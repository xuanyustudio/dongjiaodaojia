<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;
class Share extends BaseController
{
    public function index(){

        $uid=getUserId();
        $money=Db::name('user_log')->where('user_id',$uid)->where('type',5)->sum('money');
        $user=Db::name('user')->where('pid',$uid)->count();
        return msg(1,'',['money'=>$money,'user'=>$user]);
    }
    public function team(){
        $uid=getUserId();
        $page=input('page/i',1);
        $limit=10;
        $where[]=['pid','=',$uid];


        $list=Db::name('user')
            ->where($where)->order('id','desc')->field('id,nickname,mobile,avatar,create_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();
        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('user')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }

}