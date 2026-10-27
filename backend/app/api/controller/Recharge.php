<?php


namespace app\api\controller;

use think\facade\Db;
use app\BaseController;

class Recharge extends BaseController
{
    public function index(){
        $uid=getUserId();
        $user=Db::name('user')->where('id',$uid)->field('is_vip,overdue_time,balance')->find();
        $user['overdue_time']=date('Y-m-d H:i',$user['overdue_time']);
        return msg(1,'',$user);
    }
    public function rechargeList(){
        $page=input('page/i',1);
        $limit=10;
        $where[]=['status','=',1];
        $list=Db::name('recharge')
            ->where($where)->order('id','desc')->field('id,price,give')
           ->select()->toArray();
        $count=Db::name('recharge')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    public function createOrder()
    {
        $id = input('id/i', 0);
        if(empty($id)){
            $price=input('price/i',0);
            if(empty($price)){
                return msg(0,'充值金额不能为空');
            }
            $give=Db::name('recharge')->where('price','<=',$price)->order('price','desc')->value('give');
            if(empty($give)){
                $give=0;
            }
        }else{
            $recharge = Db::name('recharge')->where('id', $id)
                ->where('status', 1)->find();
            if (!$recharge) {
                return msg(0,'该充值套餐不存在');
            }
            $price=$recharge['price'];
            $give=$recharge['give'];
        }


        $uid=getUserId();
        $data = [
            'user_id' => $uid,
            'pay_money' => $price,
            'give' => $give,
            'order_sn' => order_sn('R'),
            'create_time' => time(),
        ];
        $res=Db::name('recharge_order')->insert($data);
        if($res){
            return msg(1,'创建订单成功',$data['order_sn']);
        }else{
            return msg(0,'网络错误');
        }
    }
    public function orderList(){
        $page=input('page/i',1);
        $limit=10;
        $uid=getUserId();
        $date=input('date/s','');
        if(!empty($date)){
            $start=strtotime($date);
            $end=strtotime('+1 month',$start);
            $where[]=['create_time','>',$start];
            $where[]=['create_time','<',$end];
        }
        $where[]=['user_id','=',$uid];
        $where[]=['pay_status','=',1];
        $list=Db::name('recharge_order')
            ->where($where)->order('id','desc')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();

        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('recharge_order')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    public function consumeList(){
        $page=input('page/i',1);
        $limit=10;
        $uid=getUserId();
        $date=input('date/s','');
        if(!empty($date)){
            $start=strtotime($date);
            $end=strtotime('+1 month',$start);
            $where[]=['create_time','>',$start];
            $where[]=['create_time','<',$end];
        }
        $where[]=['user_id','=',$uid];
        $where[]=['type','in','1,3,4'];
        $list=Db::name('user_log')
            ->where($where)->order('id','desc')->field('order_sn,type,money,payment,create_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();

        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('user_log')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }

    public function refundList(){
        $page=input('page/i',1);
        $limit=10;
        $uid=getUserId();
        $date=input('date/s','');
        if(!empty($date)){
            $start=strtotime($date);
            $end=strtotime('+1 month',$start);
            $where[]=['create_time','>',$start];
            $where[]=['create_time','<',$end];
        }
        $where[]=['user_id','=',$uid];
        $where[]=['type','=',2];
        $list=Db::name('user_log')
            ->where($where)->order('id','desc')->field('order_sn,type,money,payment,create_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();

        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('user_log')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    public function brokerageList(){
        $page=input('page/i',1);
        $limit=10;
        $uid=getUserId();
        $date=input('date/s','');
        if(!empty($date)){
            $start=strtotime($date);
            $end=strtotime('+1 month',$start);
            $where[]=['create_time','>',$start];
            $where[]=['create_time','<',$end];
        }
        $where[]=['user_id','=',$uid];
        $where[]=['type','=',5];
        $list=Db::name('user_log')
            ->where($where)->order('id','desc')->field('order_sn,type,money,payment,create_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();

        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('user_log')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }

}