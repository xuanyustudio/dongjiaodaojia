<?php 
/*
 module:		会员记录控制器
 create_time:	2022-03-15 14:35:55
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Memberorder as MemberorderModel;
use think\facade\Db;

class Memberorder extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['user_id'] = $this->request->post('user_id', '', 'serach_in');
		$where['payment'] = $this->request->post('payment', '', 'serach_in');

		$field = 'id,order_sn,user_id,pay_money,pay_status,member_name,month,create_time,payment';

		$res = MemberorderModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['id']) throw new ValidateException ('参数错误');
		MemberorderModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'order_sn,user_id,pay_money,pay_status,member_name,month,create_time,update_time,payment';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Memberorder::class);
		$data['create_time'] = time();

		try{
			$res = MemberorderModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,order_sn,user_id,pay_money,pay_status,member_name,month,create_time,update_time,payment';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Memberorder::class);
		$data['create_time'] = strtotime($data['create_time']);

		try{
			MemberorderModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200]);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,order_sn,user_id,pay_money,pay_status,member_name,month,create_time,update_time,payment';
		$res = MemberorderModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		MemberorderModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,order_sn,user_id,pay_money,pay_status,member_name,month,create_time,payment';
		$res = MemberorderModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}




}

