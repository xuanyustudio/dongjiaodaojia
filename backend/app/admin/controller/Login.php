<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use think\facade\Db;
use app\admin\model\AdminToken as AdminTokenModel;
use app\admin\model\Role as RoleModel;

class Login extends Admin
{
	
	
	//用户登录
    public function index(){
		$postField = 'username,password,verify,key';
		$data = $this->request->only(explode(',',$postField),'post',null);
		
// 		if(empty($data['username'])){
// 			throw new ValidateException('请输入验证码');
// 		}
		
// 		if(empty($data['username'])){
// 			throw new ValidateException('请输入密码');
// 		}
		
		$verify_status = !is_null(config('my.verify_status')) ? config('my.verify_status') : true;
		
// 		if($verify_status){
// 			if(empty($data['verify'])){
// 				throw new ValidateException('请输入验证码');
// 			}
// 			if(!captcha_check($data['key'],$data['verify'])){
// 				throw new ValidateException('验证码错误');
// 			}
// 		}
		$res = $this->checkLogin($data);
		if($res){
			return json(['status'=>200,'token'=>$this->setToken($res)]);
		}
    }
	
	//获取用户信息 菜单信息 以及菜单对应的组件
	public function getUserInfo(){
		if(!$this->userInfo['user_id']){
			throw new ValidateException('用户Id不存在');
		}
		
		$userInfo = db('admin_user')->field('role_id,user')->where('user_id',$this->userInfo['user_id'])->find();
		if(!$userInfo){
			throw new ValidateException('用户信息不存在');
		}
		
		$roleInfo = RoleModel::where('role_id',$userInfo['role_id'])->find();
		
		$menu = $this->getMyMenus($roleInfo,$this->getTotalMenus());
		
		$components = $this->getComponents($menu,$userInfo['role_id']);
		
		//如果是超级管理员则把下面两个组件挂载上
		if($userInfo['role_id'] == 1){
			$fieldComponents = [
				'name'=>'Field',
				'path'=> (string)url('admin/Field/index'),
				'meta'=>['title'=>'字段管理'],
				'component_path'=>'admin/sys/field/Index.vue'
			];
			$actionComponents = [
				'name'=>'Action',
				'path'=> (string)url('admin/Action/index'),
				'meta'=>['title'=>'方法管理'],
				'component_path'=>'admin/sys/action/Index.vue'
			];
			array_push($components,$fieldComponents,$actionComponents);
		}
		$components = array_merge($components,$this->getHiddenComponents($roleInfo)); 	//挂载菜单隐藏 但是定义了的组件
		
		$userInfo['site_title']  = config('base_config.site_title');
		$userInfo['headimg'] = config('base_config.logo');
		$userInfo['show_notice'] = !is_null(config('my.show_notice')) ? config('my.show_notice') : true;	//是否显示消息提示按钮
		
		$data['status'] = 200;
		$data['menu'] = $menu;
		$data['components'] = $components;
		$data['actions'] = explode(',',$roleInfo['access']);
		$data['data'] = $userInfo;
		return json($data);
	}
	
	
	//获取当前角色的菜单
	private function getMyMenus($roleInfo,$totalMenus){
		if($roleInfo['role_id'] == 1){
			return $totalMenus;
		}
		foreach($totalMenus as $key=>$val){
			if(in_array($val['name'],explode(',',$roleInfo['access']))){
				$tree[] = array_merge($val,['children'=>$this->getMyMenus($roleInfo,$val['children'])]);
			}
		}
		return array_values($tree);
		
	}
	
	
    //验证登录
    private function checkLogin($data){	
		$where['a.user'] = $data['username'];
		$where['a.pwd']  = md5($data['password'].config('my.password_secrect'));
// 		var_dump($where);die;
		$info = Db::name('admin_user')->alias('a')->join('role b', 'a.role_id in(b.role_id)')->field('a.user_id,a.name,a.user as username,a.status,a.role_id as user_role_ids,b.role_id,b.name as role_name,b.status as role_status')->where($where)->find();

		if(!$info){
			throw new ValidateException("请检查用户名或者密码");
		}
		if(!($info['status']) || !($info['role_status'])){
			throw new ValidateException("该账户被禁用");
		}
        
		event('LoginLog',$data['username']);	//写入登录日志
		
        return $info;
    }
	
	//验证码
	public function verify(){
		$data['rememberMe'] = true;
		$data['data'] = captcha();
		$data['site_title'] = config('base_config.site_title');
		$data['success_url'] = (string)url('admin/Home/index');		//登录成功后的跳转地址
		$data['verify_status'] = !is_null(config('my.verify_status')) ? config('my.verify_status') : true;	//验证码开关
		$data['status'] = 200;
	    return json($data);
	}

	//退出
    public function logout(){
		$token = $this->request->header('Authorization');
		AdminTokenModel::where('token',$token)->delete();
        return json(['status'=>200]);
    }
	

}
