<?php 
/*
 module:		应用秘钥控制器
 create_time:	2021-06-01 18:46:45
 author:		
 contact:		
*/

namespace app\admin\controller\Sys;
use think\exception\ValidateException;
use app\admin\controller\Admin;
use think\facade\Db;

class Secrect extends Admin {


	/*
 	* @Description  应用秘钥
 	*/
	public function index(){
		$data = $this->request->post();
		$info = Db::name('secrect')->column('data','name');
		foreach ($data as $key => $value) {
			if(array_key_exists($key,$info)){
				Db::name('secrect')->where(['name'=>$key])->update(['data'=>$value]);
			}else{
				Db::name('secrect')->insert(['name'=>$key,'data'=>$value]);
			}
		}
		return json(['status'=>200]);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getInfo(){
		$res = Db::name('secrect')->column('data','name');
		return json(['status'=>200,'data'=>$res]);
	}




}

