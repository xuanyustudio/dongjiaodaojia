<?php 
/*
 module:		报警信息控制器
 create_time:	2022-03-21 13:52:26
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Callpolice extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'call_police';


	function technician(){
		return $this->hasOne(\app\admin\model\Technician::class,'id','technician_id');
	}



}

