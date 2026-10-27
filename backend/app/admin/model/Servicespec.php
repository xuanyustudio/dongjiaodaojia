<?php 
/*
 module:		服务项目控制器
 create_time:	2022-03-11 18:43:32
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Servicespec extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'service_spec';


	function service(){
		return $this->hasOne(\app\admin\model\Service::class,'id','service_id');
	}



}

