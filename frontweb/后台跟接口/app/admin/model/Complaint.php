<?php 
/*
 module:		complaint控制器
 create_time:	2022-08-02 10:34:40
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Complaint extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'complaint';


   function user(){
        return $this->hasOne(\app\admin\model\User::class,'id','user_id');
    }

    function technician(){
        return $this->hasOne(\app\admin\model\Technician::class,'id','user_id');
    }

}

