<?php 
/*
 module:		优惠券领取控制器
 create_time:	2022-03-16 14:48:32
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Couponlog as CouponlogModel;
use think\facade\Db;

class Couponlog extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
    
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['couponlog.type'] = $this->request->post('type', '', 'serach_in');
		$where['couponlog.get_type'] = $this->request->post('get_type', '', 'serach_in');
		$where['couponlog.use'] = $this->request->post('use', '', 'serach_in');

		$field = 'id,name,money,discount,type,get_type,create_time,start_time,end_time,use,primary_id';

		$withJoin = [
			'user'=>explode(',','id,nickname,username'),
		];

		$res = CouponlogModel::where(formatWhere($where))->where('primary_id','>',0)->field($field)->withJoin($withJoin,'left')->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

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
		CouponlogModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'primary_id,name,exchange,money,discount,condition,type,get_type,user_id,service_id,create_time,start_time,end_time,status,use';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Couponlog::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);

		try{
			$res = CouponlogModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,primary_id,name,exchange,money,discount,condition,type,get_type,user_id,service_id,create_time,start_time,end_time,status,use';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Couponlog::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);

		try{
			CouponlogModel::update($data);
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
		$field = 'id,primary_id,name,exchange,money,discount,condition,type,get_type,user_id,service_id,create_time,start_time,end_time,status,use';
		$res = CouponlogModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		CouponlogModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,name,money,discount,type,get_type,create_time,start_time,end_time,use';
		$res = CouponlogModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}




}

