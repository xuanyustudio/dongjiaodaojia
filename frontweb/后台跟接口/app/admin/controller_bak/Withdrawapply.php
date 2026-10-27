<?php 
/*
 module:		提现管理控制器
 create_time:	2022-03-21 15:43:29
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Withdrawapply as WithdrawapplyModel;
use think\facade\Db;

class Withdrawapply extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['withdrawapply.type'] = $this->request->post('type', '', 'serach_in');
		$where['withdrawapply.user_type'] = $this->request->post('user_type', '', 'serach_in');
		$where['withdrawapply.left_money'] = $this->request->post('left_money', '', 'serach_in');
		$where['withdrawapply.status'] = $this->request->post('status', '', 'serach_in');
		$where['withdrawapply.deion'] = $this->request->post('deion', '', 'serach_in');

		$field = 'id,sn,user_id,real_name,account,type,user_type,money,left_money,poundage,status,create_time';

		$withJoin = [
			'user'=>explode(',','id,nickname,username,realname,mobile'),
			'technician'=>explode(',','id,nickname,username,realname,mobile'),
		];

		$res = WithdrawapplyModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

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
		WithdrawapplyModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'sn,user_id,real_name,account,type,user_type,money,left_money,money_qr_code,poundage,status,create_time,update_time,payment_no,payment_time,deion,transfer_voucher,transfer_time,bank,subbank';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Withdrawapply::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['transfer_time'] = strtotime($data['transfer_time']);

		try{
			$res = WithdrawapplyModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,sn,user_id,real_name,account,type,user_type,money,left_money,money_qr_code,poundage,status,create_time,update_time,payment_no,payment_time,deion,transfer_voucher,transfer_time,bank,subbank';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Withdrawapply::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['transfer_time'] = strtotime($data['transfer_time']);

		try{
			WithdrawapplyModel::update($data);
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
		$field = '*';
		$res = WithdrawapplyModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		WithdrawapplyModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,sn,user_id,real_name,account,type,user_type,money,left_money,poundage,status,create_time';
		$res = WithdrawapplyModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}
 
	/*
 	* @Description   统计
 	*/
   function count(){
		$res['num1'] = WithdrawapplyModel::where('status','=','1')->sum('left_money');
    $res['num2'] = WithdrawapplyModel::sum('left_money');
    $res['num3'] = WithdrawapplyModel::where('status','=','2')->sum('left_money');
    $res['num4'] = WithdrawapplyModel::where('status','=','3')->sum('left_money');
		return json(['status'=>200,'data'=>$res]);
	}


}

