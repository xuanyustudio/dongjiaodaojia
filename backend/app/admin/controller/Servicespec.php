<?php 
/*
 module:		服务项目控制器
 create_time:	2022-03-11 18:43:32
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Servicespec as ServicespecModel;
use think\facade\Db;

class Servicespec extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['servicespec.price'] = $this->request->post('price', '', 'serach_in');
		$where['servicespec.duration'] = $this->request->post('duration', '', 'serach_in');
		$where['servicespec.service_id'] = $this->request->post('service_id', '', 'serach_in');

		$field = 'id,name,price,duration,sort,technician_rate';

		$withJoin = [
			'service'=>explode(',','id,name'),
		];

		$res = ServicespecModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		$page == 1 && $data['sql_field_data'] = $this->getSqlField('service_id');
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'id,sort';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['id']) throw new ValidateException ('参数错误');
		ServicespecModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'name,price,duration,create_time,update_time,sort,service_id,technician_rate';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Servicespec::class);
		$data['create_time'] = time();

		try{
			$res = ServicespecModel::create($data);
			if($res->id && empty($data['sort'])){
				 ServicespecModel::update(['sort'=>$res->id,'id'=>$res->id]);
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,name,price,duration,create_time,update_time,sort,service_id,technician_rate';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Servicespec::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['update_time'] = time();

		try{
			ServicespecModel::update($data);
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
		$field = 'id,name,price,duration,create_time,update_time,sort,service_id,technician_rate';
		$res = ServicespecModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ServicespecModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,name,price,duration,sort';
		$res = ServicespecModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('service_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('service_id',explode(',',$list))){
			$data['service_ids'] = $this->query('select id,name from cd_service');
		}
		return $data;
	}



}

