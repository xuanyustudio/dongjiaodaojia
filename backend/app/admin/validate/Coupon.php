<?php 
/*
 module:		优惠券管理控制器
 create_time:	2022-03-15 17:02:32
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Coupon extends validate {


	protected $rule = [
		'name'=>['require'],
		'type'=>['require'],
	];

	protected $message = [
		'name.require'=>'优惠券名称不能为空',
		'type.require'=>'类型不能为空',
	];

	protected $scene  = [
		'add'=>['name','type'],
		'update'=>['name','type'],
	];



}

