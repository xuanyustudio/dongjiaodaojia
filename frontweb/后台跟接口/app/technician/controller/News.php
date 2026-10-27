<?php


namespace app\technician\controller;


use app\BaseController;
use think\facade\Db;
class News extends BaseController
{
    public function index(){

        $page=input('page/i',1);
        $limit=10;
        $uid=getTechnicianId();
        $where[]=['technician_id','=',$uid];
        $where[]=['user_type','=',2];

        $list=Db::name('news')
            ->where($where)->order('id','desc')->field('id,content,type,create_time,status')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();


        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('news')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }

}