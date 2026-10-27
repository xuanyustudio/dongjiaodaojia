<?php 
/*
 module:		轮播图配置控制器
 create_time:	2022-03-07 20:59:50
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Banner as BannerModel;
use think\facade\Db;

class Banner extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['type'] = $this->request->post('type', '', 'serach_in');

		$field = 'id,type,image,sort,create_time,update_time';

		$res = BannerModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'id,sort';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['id']) throw new ValidateException ('参数错误');
		BannerModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'type,image,sort,create_time,update_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Banner::class);
		$data['create_time'] = time();

		try{
			$res = BannerModel::create($data);
			if($res->id && empty($data['sort'])){
				 BannerModel::update(['sort'=>$res->id,'id'=>$res->id]);
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
		$postField = 'id,type,image,sort,create_time,update_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Banner::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['update_time'] = time();

		try{
			BannerModel::update($data);
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
		$field = 'id,type,image,sort,create_time,update_time';
		$res = BannerModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		BannerModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  导入
 	*/
	public function importData(){
		$data = $this->request->post();
		$list = [];
		foreach($data as $key=>$val){
			$list[$key]['type'] = getValByKey($val['类型'],'[{"key":"用户端","val":"0"},{"key":"技术端","val":"1"}]');
			$list[$key]['image'] = $val['图片'];
			$list[$key]['sort'] = $val['排序'];
			$list[$key]['create_time'] = time();
			$list[$key]['update_time'] = strtotime($val['更新时间']);
		}
		(new BannerModel)->saveAll($list);
		return json(['status'=>200]);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->post('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['type'] = $this->request->post('type', '', 'serach_in');

		$field = 'id,type,image,sort,create_time,update_time';

		$res = BannerModel::where(formatWhere($where))->field($field)->order('id desc')->limit(($page-1)*$limit,$limit)->select()->toArray();

		foreach($res as $key=>$val){
			$res[$key]['type'] = getItemVal($val['type'],'[{"key":"用户端","val":"0"},{"key":"技术端","val":"1"}]');
			$res[$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
			$res[$key]['update_time'] = date('Y-m-d H:i:s',$val['update_time']);
		}

		$data['status'] = 200;
		$data['header'] = explode(',','编号,类型,图片,排序,创建时间,更新时间');
		$data['percentage'] = ceil($page * 100/ceil(BannerModel::where(formatWhere($where))->count()/$limit));
		$data['filename'] = '轮播图配置.'.config('my.dump_extension');
		$data['data'] = $res;
		return json($data);
	}




}

