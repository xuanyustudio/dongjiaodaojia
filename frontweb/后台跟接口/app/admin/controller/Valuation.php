<?php 
/*
 module:		出行费用控制器
 create_time:	2022-03-21 14:14:28
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Valuation as ValuationModel;
use think\facade\Db;

class Valuation extends Admin {


	/*
 	* @Description  出行费用
 	*/
	public function index(){
		$data = $this->request->post();
		$this->validate($data,\app\admin\validate\Valuation::class);
		ValuationModel::update($data);
		return json(['status'=>200]);
	}



	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getInfo(){
    $name = $this->request->post('name', '', 'serach_in');
    $data = $this->request->post();
		$res = ValuationModel::where('name','=',$name)->find();
		return json(['status'=>200,'data'=>$res]);
	}




}