<?php 
/*
 module:		技师申请控制器
 create_time:	2022-03-18 15:11:54
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Technicianapply as TechnicianapplyModel;
use think\facade\Db;
use app\admin\model\Technician;

class Technicianapply extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['realname'] = $this->request->post('realname', '', 'serach_in');
		$where['mobile'] = $this->request->post('mobile', '', 'serach_in');
		$where['status'] = $this->request->post('status', '', 'serach_in');

		$field = 'id,realname,sex,mobile,age,avatar,create_time,city,user_id,status';

		$res = TechnicianapplyModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

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
		TechnicianapplyModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'realname,sex,mobile,age,avatar,create_time,update_time,city,user_id,status';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Technicianapply::class);
		$data['create_time'] = strtotime($data['create_time']);

		try{
			$res = TechnicianapplyModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  处理
 	*/
	public function update(){
		$idx = $this->request->post('id', '', 'serach_in');
		if(empty($idx)) throw new ValidateException ('参数错误');

		$id_arr = explode(',',$idx);
        try{
            Db::startTrans();
            $apply_user = TechnicianapplyModel::with(['user'])->Where(['id'=>$id_arr])->select();
            foreach ($apply_user as $key=>$value){
                $has = Technician::where(['username'=>$value['user']['username']])->find();
                if (!$has){
                    //写入技师表
                    Technician::create([
                        'nickname'=>'用户'.$value['mobile'],
                        'username'=>$value['mobile'],
                        'realname'=>$value['realname'],
                        'sex'=>$value['sex'],
                        'mobile'=>$value['mobile'],
                        'avatar'=>$value['avatar'],
                        'create_time'=>time(),
                        'update_time'=>time(),
                    ]);
                }

                //更新申请状态
                $value->status = 1;
                $value->save();
            }
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
            throw new ValidateException ($e->getMessage());
        }
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		TechnicianapplyModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,realname,sex,mobile,age,avatar,create_time,city,user_id,status';
		$res = TechnicianapplyModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}




}

