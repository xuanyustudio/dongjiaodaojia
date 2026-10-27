<?php 
/*
 module:		服务类型控制器
 create_time:	2022-03-11 16:26:24
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Service as ServiceModel;
use think\facade\Db;
use app\admin\model\Orderexplain as Orderexplain;

class Service extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['name'] = ['like',$this->request->post('name', '', 'serach_in')];
		$where['show'] = $this->request->post('show', '', 'serach_in');

		$field = 'id,name,cover,sales,sort,praise,show,original_price,price,discount,duration';

		$res = ServiceModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'id,sort,show';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['id']) throw new ValidateException ('参数错误');
		ServiceModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'name,cover,banner,sales,introduce,update_time,sort,praise,show,original_price,price,discount,duration,label,explain';
		$data = $this->request->only(explode(',',$postField),'post',null);
    $explain_data = $data['explain'];
    

    // return json(['status'=>200,'data'=>$explain_data]);
		$this->validate($data,\app\admin\validate\Service::class);
		$data['banner'] = getItemData($data['banner']);
		$data['label'] = implode(',',$data['label']);

		try{
			$res = ServiceModel::create($data);
      if($res->id&&$explain_data){
          foreach($explain_data as $key=>$value){
           $explain_data[$key]['service_id']=$res->id;
           $explain_data[$key]['create_time']=time();
           Orderexplain::create($explain_data[$key]);
          }
          //$res = Orderexplain::create($explain_data);
     }
			if($res->id && empty($data['sort'])){
				 ServiceModel::update(['sort'=>$res->id,'id'=>$res->id]);
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
		$postField = 'id,name,cover,banner,sales,introduce,update_time,sort,praise,show,original_price,price,discount,duration,label,explain';
		$data = $this->request->only(explode(',',$postField),'post',null);
    $explain_data = $data['explain'];
		$this->validate($data,\app\admin\validate\Service::class);
		$data['banner'] = getItemData($data['banner']);
		$data['update_time'] = time();
		$data['label'] = implode(',',$data['label']);

		try{
			ServiceModel::update($data);
      if($explain_data){
        foreach($explain_data as $key=>$value){
         $explain_data[$key]['service_id']=	$data['id'];
         $explain_data[$key]['create_time']=time();
         if( $explain_data[$key]['id']){
          Orderexplain::update($explain_data[$key]);
         }else{
          Orderexplain::create($explain_data[$key]);
         }
        }
        
   }
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
		$field = 'id,name,cover,banner,sales,introduce,update_time,sort,praise,show,original_price,price,discount,duration,label';
		$res = ServiceModel::field($field)->find($id);
		$res['banner'] = json_decode($res['banner'],true);
		$res['label'] = explode(',',$res['label']);
    $res['explain'] = Orderexplain::where('service_id',$id)->select();
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ServiceModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}




}