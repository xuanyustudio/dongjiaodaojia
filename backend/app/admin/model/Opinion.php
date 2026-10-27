<?php 
/*
 module:		用户反馈控制器
 create_time:	2022-03-21 15:20:29
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Opinion extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'opinion';


	function user(){
		return $this->hasOne(\app\admin\model\User::class,'id','user_id');
	}

	function technician(){
		return $this->hasOne(\app\admin\model\Technician::class,'id','technician_id');
	}



}

