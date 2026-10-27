<?php 
/*
 module:		优惠券领取控制器
 create_time:	2022-03-16 14:48:32
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Couponlog extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'coupon';


	function user(){
		return $this->hasOne(\app\admin\model\User::class,'id','user_id');
	}



}

