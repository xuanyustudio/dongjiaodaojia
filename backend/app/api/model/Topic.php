<?php


namespace app\api\model;


use app\common\BaseModel;

class Topic extends BaseModel
{
    protected $table = 'zhonqi_lesson_topic';
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}