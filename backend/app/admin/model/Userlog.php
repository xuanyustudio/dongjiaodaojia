<?php 
/*
 module:		user_log控制器
 create_time:	2022-08-01 16:35:38
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Userlog extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'user_log';


   function user(){
        return $this->hasOne(\app\admin\model\User::class,'id','user_id');
    }

    function technician(){
        return $this->hasOne(\app\admin\model\Technician::class,'id','user_id');
    }

}

