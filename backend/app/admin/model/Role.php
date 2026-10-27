<?php 
/*
 module:		角色管理控制器
 create_time:	2021-07-27 18:11:24
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Role extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'role_id';

 	protected $name = 'role';




}

