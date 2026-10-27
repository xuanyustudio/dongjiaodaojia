<?php 
/*
 module:		优惠券管理控制器
 create_time:	2022-03-15 17:02:32
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Coupon as CouponModel;
use think\facade\Db;
use app\admin\model\Service as ServiceModel;

class Coupon extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
    $where['primary_id'] = 0;
		$where['name'] = $this->request->post('name', '', 'serach_in');
		$where['type'] = $this->request->post('type', '', 'serach_in');

		$field = 'id,name,exchange,type,money,discount,condition,start_time,end_time,status';

		$res = CouponModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'id,status';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['id']) throw new ValidateException ('参数错误');
		CouponModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'primary_id,name,type,money,discount,condition,service_id,create_time,start_time,end_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Coupon::class);
		$data['create_time'] = time();
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);

		try{
			$res = CouponModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,primary_id,name,type,money,discount,condition,service_id,create_time,start_time,end_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Coupon::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);

		try{
			CouponModel::update($data);
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
		$field = 'id,primary_id,name,type,money,discount,condition,service_id,create_time,start_time,end_time';
		$res = CouponModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		CouponModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,name,exchange,type,money,discount,condition,start_time,end_time,status';
		$res = CouponModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}

	/*
 	* @Description  获取服务类目
 	*/
   function getService(){
	
		$res = ServiceModel::field("name as value,id")->select();
		return json(['status'=>200,'data'=>$res]);
	}


}

