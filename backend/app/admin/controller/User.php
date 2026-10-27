<?php 
/*
 module:		用户列表控制器
 create_time:	2022-03-15 13:56:49
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\User as UserModel;
use think\facade\Db;

class User extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['username'] = $this->request->post('username', '', 'serach_in');
		$where['mobile'] = $this->request->post('mobile', '', 'serach_in');

		$field = 'id,pid,nickname,username,realname,id_card,real_status,sex,mobile,avatar,status,is_promoter,balance,is_vip,create_time,praise';

		$res = UserModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

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
		UserModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,nickname,username,realname,id_card,real_status,sex,mobile,avatar,status,balance,praise';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\User::class);

		try{
			UserModel::update($data);
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
		$field = 'id,nickname,username,realname,id_card,real_status,sex,mobile,avatar,status,balance,praise';
		$res = UserModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,pid,nickname,username,realname,sex,mobile,avatar,status,is_promoter,balance,is_vip,create_time,praise';
		$res = UserModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}
	/*
 	* @Description  导出
 	*/
   function dumpdata(){
		$page = $this->request->post('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['username'] = $this->request->post('username', '', 'serach_in');
		$where['mobile'] = $this->request->post('mobile', '', 'serach_in');

		$field = 'id,nickname,username,realname,sex,mobile,avatar,status,balance,praise';

		$res = UserModel::where(formatWhere($where))->field($field)->order('id desc')->limit(($page-1)*$limit,$limit)->select()->toArray();

		foreach($res as $key=>$val){
			$res[$key]['sex'] = getItemVal($val['sex'],'[{"key":"保密","val":"0","label_color":"primary"},{"key":"男","val":"1","label_color":"primary"},{"key":"女","val":"2","label_color":"primary"}]');
			$res[$key]['status'] = getItemVal($val['status'],'[{"val":"0","key":"禁止","label_color":"danger"},{"key":"正常","val":"1","label_color":"success"}]');
		}

		$data['status'] = 200;
		$data['header'] = explode(',','编号,昵称,用户名,真名,性别,手机号码,头像,状态,余额,好评率');
		$data['percentage'] = ceil($page * 100/ceil(UserModel::where(formatWhere($where))->count()/$limit));
		$data['filename'] = '用户列表.'.config('my.dump_extension');
		$data['data'] = $res;
		return json($data);
	}



}