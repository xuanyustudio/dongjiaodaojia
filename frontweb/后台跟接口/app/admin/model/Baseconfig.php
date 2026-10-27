<?php 
/*
 module:		基本配置控制器
 create_time:	2022-03-21 17:28:56
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Baseconfig extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'base_config';




}

