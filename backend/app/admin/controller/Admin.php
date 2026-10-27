<?php

namespace app\admin\controller;
use think\exception\FuncNotFoundException;
use think\exception\ValidateException;
use app\BaseController;
use think\facade\Db;
use utils\Jwt;
use app\admin\model\AdminToken as AdminTokenModel;

class Admin extends BaseController
{
	
	protected $userInfo = []; //token解码的用户信息
	
	//建议每个项目都改变此信息
	private $jwt = [
		'jwt_expire_time'=>3600 * 24,		//token过期时间24小时
		'jwt_secrect'=>'QoCCfOVKwqTNKArT',	//签名秘钥
		'jwt_iss'=>'client.vueadmin',		//发送端
		'jwt_aud'=>'server.vueadmin'		//接收端
	];
	
	
	protected function initialize(){
		$controller = $this->request->controller();
		$action = $this->request->action();
		$app = app('http')->getName();
		
		$url =  "{$app}/{$controller}/{$action}";
		
		$list = Db::name('base_config')->cache(true,60)->select()->column('data','name');
		config($list,'base_config');
		
		if(!in_array($url,config('my.nocheck'))){
			$this->checkTokenAuth();
		}
	}
	
	
	//设置token
	protected function setToken($data){
		$jwt = Jwt::getInstance();
		$jwt->setIss($this->jwt['jwt_iss'])->setAud($this->jwt['jwt_aud'])->setSecrect($this->jwt['jwt_secrect'])->setExpTime($this->jwt['jwt_expire_time']);
		
		$username = $data['username'];
		$data['time'] = time();
		$data = json_encode($data);
		$token = $jwt->setUid($data)->encode($data)->getToken();
		
		//登录的时候把token写入数据表
		$tokenInfo = AdminTokenModel::where('token',$token)->find();
		if(!$tokenInfo){
			if(!config('my.multiple_login')){
				AdminTokenModel::field('dev_status')->where('username',$username)->update(['dev_status'=>0]);
			}
			AdminTokenModel::create(['token'=>$token,'username'=>$username]);	
		}
		
		return $token;
	}
	
	//检测token
	protected function checkTokenAuth(){
		//检测ip白名单
		$ips = config('base_config.ip_white');
		if($ips){
			if(!in_array($this->request->ip(),preg_split('/\n/',$ips))){
				throw new ValidateException('请求ip来源错误');
			}
		}
		
		$token = $this->request->header('Authorization');
	   // $token=request->header('Authorization');
	    
	   // var_dump($_SERVER,$this->request->param());die;
		if(!$token){
			abort(101,'token不能为空');
		}
		if(count(explode('.',$token)) <> 3){
			abort(101,'token格式错误');
		}
		
		$tokenInfo = AdminTokenModel::where('token',$token)->find();
		
		if(!$tokenInfo['status']){
			abort(101,'状态被禁用');
		}
		
		if(!$tokenInfo['dev_status']){
			abort(101,'你已下线,账户在其它设备登录!');
		}
		
		if(!$tokenInfo){
			abort(101,'token不存在');
		}

		$jwt = Jwt::getInstance();
		$jwt->setIss($this->jwt['jwt_iss'])->setAud($this->jwt['jwt_aud'])->setSecrect($this->jwt['jwt_secrect'])->setToken($token);
		
		if($jwt->decode()->getClaim('exp') < time()){
			abort(101,'登录状态已过期，请重新登录');
		}
		if($jwt->validate() && $jwt->verify()){
			$tokenDecode = $jwt->decode()->getClaim('uid');
			$this->userInfo = json_decode($tokenDecode,true);
			event('DoLog',$this->userInfo['username']);	//写入操作日志
		}else{
			abort(101,'token失效');
		}
	}
	
	//验证器 并且抛出异常
	protected function validate($data,$validate){
		try{
			validate($validate)->scene($this->request->action())->check($data);
		}catch(ValidateException $e){
			throw new ValidateException ($e->getError());
		}
		return true;
	}
	
	//格式化sql字段查询 转化为 key=>val 结构
	protected function query($sql){
		preg_match_all('/select(.*)from/iUs',$sql,$all);
		if(!empty($all[1][0])){
			$sqlvalue = explode(',',trim($all[1][0]));
		}
		$sql = str_replace('pre_',config('database.connections.mysql.prefix'),$sql);
		$list = Db::query($sql);
		$array = [];
		foreach($list as $k=>$v){
			$array[$k]['key'] = $v[$sqlvalue[1]];
			$array[$k]['val'] = $v[$sqlvalue[0]];
			if($sqlvalue[2]){
				$array[$k]['pid'] = $v[$sqlvalue[2]];
			}
		}
		return $array;
	}
	
	//获取所有菜单
	protected function getTotalMenus(){
		$menu =  $this->getBaseMenus();
		$cmsMenu = include app()->getRootPath().'/app/admin/controller/Cms/config.php';	//cms菜单配置
		if($cmsMenu){
			$menu = array_merge($cmsMenu,$menu);	//合并cms菜单
		}
		$order_array = array_column($menu, 'sortid');			//数组排序 根据sortid 正序
		array_multisort($order_array,SORT_ASC,$menu );
		
		return $menu;
	}

	
	//返回当前应用的菜单列表
	protected function getBaseMenus(){
		$field = 'menu_id,pid,title,controller_name,status,icon,sortid,component_path';
		$list = db("menu")->field($field)->where(['status'=>1,'app_id'=>1])->order('sortid asc')->select()->toArray();
		if($list){
			foreach($list as $key=>$val){
				$menus[$key]['name'] = $val['controller_name'];
				$menus[$key]['pid'] = $val['pid'];
				$menus[$key]['menu_id'] = $val['menu_id'];
				$menus[$key]['title'] = $val['title'];
				$menus[$key]['sortid'] = $val['sortid'];
				$menus[$key]['icon'] = $val['icon'] ? $val['icon'] : 'el-icon-menu';
				$menus[$key]['path'] = (string)url('admin/'.str_replace('/','.',$val['controller_name']).'/index');
				$menus[$key]['component_path'] = $val['component_path'];
			}
			return _generateListTree($menus,0,['menu_id','pid']);
		}
	}
	
	//获取要加载的组件
	protected function getComponents($menu){
		$components = [];
		foreach($menu as $v){
			$components[] = [
				'name'=>$v['name'],
				'path'=>$v['path'],
				'meta'=>['title'=>$v['title']],
				'component_path'=>$v['component_path']
			];
			if($v['children']){
				$components = array_merge($components,$this->getComponents($v['children']));
			}
		}
		return $components;
	}
	
	
	//获取隐藏了菜单但是也要加载的组件
	protected function getHiddenComponents($roleInfo){
		$list = db("menu")->field('controller_name,title,component_path')->where('app_id',1)->where('status',0)->where('component_path','<>','')->select()->toArray();
		$components = [];
		foreach($list as $v){
			if($roleInfo['role_id'] == 1 || in_array($v['controller_name'],explode(',',$roleInfo['access']))){
				$components[] = [
					'name'=>$v['controller_name'],
					'path'=>(string)url('admin/'.str_replace('/','.',$v['controller_name']).'/index'),
					'meta'=>['title'=>$v['title']],
					'component_path'=>$v['component_path']
				];
			}
		}
		return $components;
	}

	
	public function __call($method, $args){
        throw new FuncNotFoundException('方法不存在',$method);
    }
	
	
	
}
