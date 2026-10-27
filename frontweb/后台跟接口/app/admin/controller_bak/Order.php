<?php 
/*
 module:		订单列表控制器
 create_time:	2022-03-16 17:40:00
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Order as OrderModel;
use think\facade\Db;

class Order extends Admin {


	/*
 	* @Description  数据列表
 	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$where['id'] = $this->request->post('id', '', 'serach_in');
		$where['order.order_sn'] = $this->request->post('order_sn', '', 'serach_in');
		$where['order.order_status'] = $this->request->post('order_status', '', 'serach_in');

		$create_time = $this->request->post('create_time', '', 'serach_in');
		$where['order.create_time'] = ['between',[strtotime($create_time[0]),strtotime($create_time[1])]];
		$where['order.payment'] = $this->request->post('payment', '', 'serach_in');

		$complete_time = $this->request->post('complete_time', '', 'serach_in');
		$where['order.complete_time'] = ['between',[strtotime($complete_time[0]),strtotime($complete_time[1])]];

		$field = 'id,order_sn,pay_money,total_money,discount,technician_money,order_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time';

		$withJoin = [
			'user'=>explode(',','id,pid,spid,email,nickname,username,realname,sex,password,last_login,login_num,last_ip,qq,mobile,avatar,is_promoter,status,balance,brokerage,promo_code,qrcode,token,is_vip,overdue_time,create_time,delete_time,update_time,apply_time,praise'),
			'technician'=>explode(',','id,nickname,username,realname,service,week,start_time,end_time,sex,openid,last_login,login_num,last_ip,mobile,avatar,album,status,balance,praise,credit,composite,lat,lng,create_time,sales,update_time,work_status,address,province,city,district,label,unionid,subscribe'),
			'service'=>explode(',','id,name,cover,banner,sales,introduce,create_time,update_time,praise,sort'),
			'servicespec'=>explode(',','id,name,price,duration,create_time,update_time,sort,service_id'),
			'coupon'=>explode(',','primary_id,id,name,exchange,money,discount,condition,type,get_type,user_id,service_id,create_time,start_time,end_time,status,use'),
		];

		$res = OrderModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

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
		OrderModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'order_sn,pay_money,total_money,discount,technician_money,order_status,pay_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Order::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['appointment_time'] = strtotime($data['appointment_time']);
		$data['complete_time'] = strtotime($data['complete_time']);
		$data['receiving_time'] = strtotime($data['receiving_time']);
		$data['service_time'] = strtotime($data['service_time']);

		try{
			$res = OrderModel::create($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res->id]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'id,order_sn,pay_money,total_money,discount,technician_money,order_status,pay_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Order::class);
		$data['create_time'] = strtotime($data['create_time']);
		$data['appointment_time'] = strtotime($data['appointment_time']);
		$data['complete_time'] = strtotime($data['complete_time']);
		$data['receiving_time'] = strtotime($data['receiving_time']);
		$data['service_time'] = strtotime($data['service_time']);

		try{
			OrderModel::update($data);
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
		$field = 'id,order_sn,pay_money,total_money,discount,technician_money,order_status,pay_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time';
		$res = OrderModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		OrderModel::destroy(['id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'id,order_sn,pay_money,total_money,discount,technician_money,order_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time,refreason';
    $withJoin = [
			'user'=>explode(',','id,pid,spid,email,nickname,username,realname,sex,password,last_login,login_num,last_ip,qq,mobile,avatar,is_promoter,status,balance,brokerage,promo_code,qrcode,token,is_vip,overdue_time,create_time,delete_time,update_time,apply_time,praise'),
			'technician'=>explode(',','id,nickname,username,realname,service,week,start_time,end_time,sex,openid,last_login,login_num,last_ip,mobile,avatar,album,status,balance,praise,credit,composite,lat,lng,create_time,sales,update_time,work_status,address,province,city,district,label,unionid,subscribe'),
			'service'=>explode(',','id,name,cover,banner,sales,introduce,create_time,update_time,praise,sort'),
			'servicespec'=>explode(',','id,name,price,duration,create_time,update_time,sort,service_id'),
			'coupon'=>explode(',','primary_id,id,name,exchange,money,discount,condition,type,get_type,user_id,service_id,create_time,start_time,end_time,status,use'),
		];
    $res = OrderModel::field($field)->withJoin($withJoin,'left')->find($id);
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
		$where['order_sn'] = $this->request->post('order_sn', '', 'serach_in');
		$where['order_status'] = $this->request->post('order_status', '', 'serach_in');

		$create_time = $this->request->post('create_time', '', 'serach_in');
		$where['create_time'] = ['between',[strtotime($create_time[0]),strtotime($create_time[1])]];
		$where['payment'] = $this->request->post('payment', '', 'serach_in');

		$complete_time = $this->request->post('complete_time', '', 'serach_in');
		$where['complete_time'] = ['between',[strtotime($complete_time[0]),strtotime($complete_time[1])]];

		$field = 'id,order_sn,pay_money,total_money,discount,technician_money,order_status,pay_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time';

		$res = OrderModel::where(formatWhere($where))->field($field)->order('id desc')->limit(($page-1)*$limit,$limit)->select()->toArray();

		foreach($res as $key=>$val){
			$res[$key]['order_status'] = getItemVal($val['order_status'],'[{"val":"0","key":"待付款"},{"val":"1","key":"待接单"},{"val":"2","key":"服务中"},{"val":"3","key":"订单完成"},{"val":"4","key":"已评价"},{"val":"5","key":"退款"},{"val":"6","key":"取消"}]');
			$res[$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
			$res[$key]['appointment_time'] = date('Y-m-d H:i:s',$val['appointment_time']);
			$res[$key]['payment'] = getItemVal($val['payment'],'[{"val":"1","key":"余额","label_color":"warning"},{"val":"2","key":"微信","label_color":"success"},{"val":"3","key":"支付宝","label_color":"primary"}]');
			$res[$key]['complete_time'] = date('Y-m-d H:i:s',$val['complete_time']);
			$res[$key]['cancel_type'] = getItemVal($val['cancel_type'],'[{"key":"用户取消","val":"0"},{"key":"技师取消","val":"1"}]');
			$res[$key]['receiving_time'] = date('Y-m-d H:i:s',$val['receiving_time']);
			$res[$key]['service_time'] = date('Y-m-d H:i:s',$val['service_time']);
		}

		$data['status'] = 200;
		$data['header'] = explode(',','编号,订单号,支付金额,总金额,优惠金额,技师收入,订单状态,0待付款；1支付；2退款,用户id,技师id,服务id,规格id,优惠券id,姓名,电话,省,市,区,详细地址,创建时间,预约时间,时长,车费,退款金额,支付方式,佣金,服务完成时间,取消类型,纬度,经度,取消原因,接单时间,开始服务时间');
		$data['percentage'] = ceil($page * 100/ceil(OrderModel::where(formatWhere($where))->count()/$limit));
		$data['filename'] = '订单列表.'.config('my.dump_extension');
		$data['data'] = $res;
		return json($data);
	}
	/*
 	* @Description   统计
 	*/
   function count(){
		$res['num1'] = OrderModel::where('order_status','>','0')->count();
    $res['num2'] = OrderModel::where('order_status','>','0')->whereDay('create_time')->count();
    $res['num3'] = OrderModel::where('order_status','>','1')->sum('pay_money');
    $res['num4'] = OrderModel::where('order_status','>','0')->whereDay('create_time')->sum('pay_money');
		return json(['status'=>200,'data'=>$res]);
	}



}

