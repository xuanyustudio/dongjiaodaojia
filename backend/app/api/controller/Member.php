<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;
class Member extends BaseController
{
    public function index(){
        $uid=getUserId();
        $user=Db::name('user')->where('id',$uid)->field('is_vip,overdue_time')->find();
        $user['overdue_time']=date('Y-m-d H:i',$user['overdue_time']);
        return msg(1,'',$user);
    }
    public function memberList(){

        $where[]=['status','=',1];
        $list=Db::name('member')
            ->where($where)->order('id','desc')->field('id,name,original_price,price')
            ->select()->toArray();
        $count=Db::name('member')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        $rights=Db::name('member_rights')->where('status',1)->field('id,icon,name,profile')->order('id','ase')->select()->toArray();
        return msg(1,'',array('rights'=>$rights,'list'=>$list,'total'=>$total,'page'=>1),$count);
    }
    public function createOrder()
    {
        $id = input('id/i', 0);
        $member = Db::name('member')->where('id', $id)
            ->where('status', 1)->find();
        if (!$member) {
           return msg(0,'该会员不存在');
        }
        $uid=getUserId();
        $data = [
            'user_id' => $uid,
            'pay_money' => $member['price'],
            'member_name' => $member['name'],
            'month' => $member['month'],
            'order_sn' => order_sn('M'),
            'create_time' => time(),
        ];
        $res=Db::name('member_order')->insert($data);
        if($res){
            return msg(1,'创建订单成功',$data['order_sn']);
        }else{
            return msg(0,'网络错误');
        }
    }
}