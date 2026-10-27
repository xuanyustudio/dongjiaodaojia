<?php 
/*
 module:		技师傅列表控制器
 create_time:	2022-03-18 16:12:58
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Technician extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'technician';


	function technicianalbum(){
		return $this->hasOne(\app\admin\model\Technicianalbum::class,'technician_id','id');
	}

	function technicianali(){
		return $this->hasOne(\app\admin\model\Technicianali::class,'technician_id','id');
	}

	function techniciandata(){
		return $this->hasOne(\app\admin\model\Techniciandata::class,'technician_id','id');
	}

	function technicianhealthy(){
		return $this->hasOne(\app\admin\model\Technicianhealthy::class,'technician_id','id');
	}

	function technicianidentity(){
		return $this->hasOne(\app\admin\model\Technicianidentity::class,'technician_id','id');
	}

	function technicianqualifications(){
		return $this->hasOne(\app\admin\model\Technicianqualifications::class,'technician_id','id');
	}

	function techniciantcp(){
		return $this->hasOne(\app\admin\model\Techniciantcp::class,'technician_id','id');
	}



}

