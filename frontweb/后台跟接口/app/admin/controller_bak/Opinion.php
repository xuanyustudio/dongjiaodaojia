<?php 
/*
 module:		用户反馈控制器
 create_time:	2022-03-21 15:20:29
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Opinion as OpinionModel;
use think\facade\Db;

class Opinion extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['opinion.user_type'] = $this->request->post('user_type', '', 'serach_in');
		$where['opinion.type'] = $this->request->post('type', '', 'serach_in');

		$field = 'id,content,mobile,images,user_type,type,create_time,reply';

		$withJoin = [
			'user'=>explode(',','nickname,username,realname,id'),
			'technician'=>explode(',','username,realname,nickname,id,mobile'),
		];

		$res = OpinionModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

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
		OpinionModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'content,reply,mobile,images,technician_id,user_id,user_type,type,create_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Opinion::class);
		$data['images'] = getItemData($data['images']);
		$data['create_time'] = strtotime($data['create_time']);

		try{
			$res = OpinionModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,content,reply,mobile,images,technician_id,user_id,user_type,type,create_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Opinion::class);
		$data['images'] = getItemData($data['images']);
		$data['create_time'] = strtotime($data['create_time']);

		try{
			OpinionModel::update($data);
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
		$field = 'id,content,reply,mobile,images,technician_id,user_id,user_type,type,create_time';
		$res = OpinionModel::field($field)->find($id);
		$res['images'] = json_decode($res['images'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		OpinionModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,content,mobile,images,user_type,type,create_time';
		$res = OpinionModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}




}

