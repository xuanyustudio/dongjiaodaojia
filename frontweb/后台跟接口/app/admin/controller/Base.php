<?php 
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Files as FileModel;
use app\admin\model\Adminuser as AdminuserModel;
use think\facade\Db;

class Base extends Admin {


	/*
 	* @Description 图片管理列表
 	*/
	function fileList(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];

		$field = 'id,filepath,hash,create_time';

		$res = FileModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  删除图片
 	*/
	function deleteFile(){
		$filepath =  $this->request->post('filepath', '', 'serach_in');
		if(!$filepath) $this->error('请选择图片');
		
		FileModel::where('filepath','in',$filepath)->delete();
		
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	
	/*
 	* @Description  重置密码
 	*/
	public function resetPwd(){
		$password = $this->request->post('password');
		
		if(empty($password)) $this->error('密码不能为空');
		
		$data['user_id'] = $this->userInfo['user_id'];
		$data['pwd'] = md5($password.config('my.password_secrect'));
		
		$res = AdminuserModel::update($data);
		
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	
	
	/*
 	* @Description  清除缓存
 	*/
	public function clearCache(){
		$appname = app('http')->getName();
		try{
			deldir(app()->getRootPath().'runtime/cache');
		}catch(\Exception $e){
			abort(config('my.error_log_code'),$e->getMessage());
		}
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	
	
	public function getRoleMenus(){
		$menu = $this->getNodeMenus(1,0);
		$cmsMenu = include app()->getRootPath().'/app/admin/controller/Cms/access.php';	//cms菜单配置
		if($cmsMenu){
			$menu = array_merge($cmsMenu,$menu);	//合并cms菜单
		}
		
		$order_array = array_column($menu, 'sortid');			//数组排序 根据sortid 正序
		array_multisort($order_array,SORT_ASC,$menu );

		return json(['status'=>200,'menus'=>$menu]);
	}
	
	
	//权限系统获取菜单
	private function getNodeMenus($app_id,$pid){
		$field = 'menu_id,title,controller_name,status,icon,sortid';
		$list = Db::name('menu')->field($field)->where(['app_id'=>$app_id,'pid'=>$pid])->order('sortid asc')->select()->toArray();
		if($list){
			foreach($list as $key=>$val){
				$menus[$key]['sortid'] = $val['sortid'];
				$menus[$key]['access'] = $val['controller_name'];
				$menus[$key]['title'] = $val['title'].' '.'(/admin/'.$val['controller_name'].')';
				$sublist = Db::name('menu')->field($field)->where(['app_id'=>$app_id,'pid'=>$val['menu_id']])->order('sortid asc')->select()->toArray();
				if($sublist){
					if($this->getFuns($val)){
						$menus[$key]['children'] = array_merge($this->getFuns($val),$this->getNodeMenus($app_id,$val['menu_id']));
					}else{
						$menus[$key]['children'] = $this->getNodeMenus($app_id,$val['menu_id']);
					}
				}else{
					$funs = $this->getFuns($val);
					$funs && $menus[$key]['children'] = $funs;
				}
			}
			return array_values($menus);
		}
	}
	
	//获取菜单方法
	private function getFuns($info){
		$list = Db::name('action')->field('name,action_name')->where('menu_id',$info['menu_id'])->order('sortid asc')->select()->toArray();
		if($list){
			foreach($list as $key=>$val){
				$funs[$key]['title'] = $val['name'].' '.(string)url('admin/'.$info['controller_name'].'/'.$val['action_name']);
				$funs[$key]['access'] = (string)url('admin/'.$info['controller_name'].'/'.$val['action_name']);
			}
			return array_values($funs);
		}
	}

}

