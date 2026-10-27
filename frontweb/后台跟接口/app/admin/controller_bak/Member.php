<?php 
/*
 module:		会员等级控制器
 create_time:	2022-03-15 14:17:51
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Member as MemberModel;
use think\facade\Db;

class Member extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['status'] = $this->request->post('status', '', 'serach_in');

		$field = 'id,name,original_price,price,month,sort,status';

		$res = MemberModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'id,sort,status';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['id']) throw new ValidateException ('参数错误');
		MemberModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'name,original_price,price,month,sort,status,update_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Member::class);

		try{
			$res = MemberModel::create($data);
			if($res->id && empty($data['sort'])){
				 MemberModel::update(['sort'=>$res->id,'id'=>$res->id]);
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
		$postField = 'id,name,original_price,price,month,sort,status,update_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Member::class);
		$data['update_time'] = time();

		try{
			MemberModel::update($data);
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
		$field = 'id,name,original_price,price,month,sort,status,update_time';
		$res = MemberModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		MemberModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,name,original_price,price,month,sort,status';
		$res = MemberModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}




}

