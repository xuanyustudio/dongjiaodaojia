<?php 
/*
 module:		订单列表控制器
 create_time:	2022-03-16 17:40:00
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Order extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'order';


	function user(){
		return $this->hasOne(\app\admin\model\User::class,'id','user_id');
	}

	function technician(){
		return $this->hasOne(\app\admin\model\Technician::class,'id','technician_id');
	}

	function service(){
		return $this->hasOne(\app\admin\model\Service::class,'id','service_id');
	}

	function servicespec(){
		return $this->hasOne(\app\admin\model\Servicespec::class,'id','spec_id');
	}

	function coupon(){
		return $this->hasOne(\app\admin\model\Coupon::class,'id','coupon_id');
	}



}

