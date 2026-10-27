<?php


namespace app\api\model;


use app\common\BaseModel;
use yt\helper\SignHelper;
use think\facade\Cache;
use think\facade\Request;
class User extends BaseModel
{
    //protected $table = 'zhonqi_member';
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


}