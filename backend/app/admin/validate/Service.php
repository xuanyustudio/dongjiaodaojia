<?php 
/*
 module:		服务类型控制器
 create_time:	2022-03-11 16:26:24
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Service extends validate {


	protected $rule = [
		'sales'=>['regex'=>'/^[0-9]*$/'],
		'original_price'=>['regex'=>'/^[0-9]*$/'],
		'price'=>['regex'=>'/^[0-9]*$/'],
		'discount'=>['regex'=>'/^[0-9]*$/'],
		'duration'=>['regex'=>'/^[0-9]*$/'],
	];

	protected $message = [
		'sales.regex'=>'销量格式错误',
		'original_price.regex'=>'原价格式错误',
		'price.regex'=>'现价格式错误',
		'discount.regex'=>'折扣格式错误',
		'duration.regex'=>'时长（分）格式错误',
	];

	protected $scene  = [
		'add'=>['sales','original_price','price','discount','duration'],
		'update'=>['sales','original_price','price','discount','duration'],
	];



}

