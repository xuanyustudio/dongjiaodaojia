<?php 
/*
 module:		技师申请控制器
 create_time:	2022-03-18 15:11:54
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Technicianapply extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'technician_apply';




    function user(){
        return $this->belongsTo(\app\admin\model\User::class,'user_id','id');
    }
}

