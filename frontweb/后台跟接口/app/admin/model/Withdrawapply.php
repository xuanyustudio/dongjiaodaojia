<?php 
/*
 module:		提现管理控制器
 create_time:	2022-03-21 15:43:29
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Withdrawapply extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'withdraw_apply';


	function user(){
		return $this->hasOne(\app\admin\model\User::class,'id','user_id');
	}

	function technician(){
		return $this->hasOne(\app\admin\model\Technician::class,'id','user_id');
	}



}

