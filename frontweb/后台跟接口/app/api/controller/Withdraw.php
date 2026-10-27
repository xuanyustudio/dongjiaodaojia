<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;

class Withdraw extends BaseController
{
    //账户绑定
    public function aliBind(){
        $post=$this->request->param();
        $validate = validate([
            'realname' => 'require|max:30',
            'mobile' => 'require|max:30',

        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getUserId();
        $ali=Db::name('user_ali')->where('user_id',$id)->find();
        if(!$ali){
            $post['user_id']=$id;
            $res=Db::name('user_ali')->insert(
                $post
            );
        }else{
            $res=Db::name('user_ali')->where('id',$ali['id'])->update($post);
        }
        return msg(1,'绑定成功');

    }
    //钱包
    public function wallet(){
        $id=getUserId();
        $user=Db::name('user')->where('id',$id)->field('brokerage')->find();
        $user['withdraw']=Db::name('withdraw_apply')->where('user_id',$id)->where('user_type',1)->where('status',2)->sum('money');
        return msg(1,'',$user);
    }
    //提现页
    public function withdraw(){
        $id=getUserId();
        $user=Db::name('user')->where('id',$id)->field('brokerage as balance')->find();
        $ali=Db::name('user_ali')->where('user_id',$id)->find();
        if(empty($ali)){
            $user['ali']=0;
            $user['realname']='';
            $user['mobile']='';
        }else{
            $user['ali']=1;
            $user['realname']=$ali['realname'];
            $user['mobile']=$ali['mobile'];
        }
        return msg(1,'',$user);
    }
    //提现申请
    public function withdrawApply(){
        $post=$this->request->param();
        $validate = validate([
            'money' => 'require|max:30',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getUserId();
        $min=Db::name('base_config')->where('name','withdraw_min')->value('data');
        $user=Db::name('user')->alias('a')->leftJoin('user_ali b','a.id=b.user_id')
            ->where('a.id',$id)->field('a.brokerage,b.realname,b.mobile')->find();

        if($post['money']<$min){
            return msg(0,'提现金额不能少于'.$min);
        }
        if($user['brokerage']<$post['money']){
            return msg(0,'佣金不足');
        }
        if(empty($user['realname'])){
            return msg(0,'未绑定支付宝');
        }
        
        //手续费比例
        $technician_rate=Db::name('base_config')->where('name','technician_rate')->value('data');
        $rate = 1-($technician_rate/100);
        $left_money = bcmul($post['money'],$rate,2);
        $poundage = $post['money'] - $left_money;

        $order_sn=order_sn('W');
        $res=Db::name('withdraw_apply')->insert(
            [
                'sn'=>$order_sn,
                'user_id'=>$id,
                'real_name'=>$user['realname'],
                'account'=>$user['mobile'],
                'type'=>4,
                'user_type'=>1,
                'money'=>$post['money'],
                'left_money'=>$left_money,
                'poundage'=>$poundage,
                'status'=>1,
                'create_time'=>time(),
            ]
        );
        if($res){
            Db::name('user')->where('id',$id)->dec('brokerage',$post['money'])->update();
            $user_log=[
                'user_id'=>$id,
                'type'=>7,
                'money'=>$post['money'],
                'income'=>2,
                'create_time'=>time(),
                'payment'=>1,
                'user_type'=>1,
                'order_sn'=>$order_sn,
            ];
            Db::name('user_log')->insert($user_log);
            return msg(1,'提现申请成功');
        }else{
            return msg(0,'网络错误');
        }


    }

    public function withdrawList(){
        $id=getUserId();
        $month=input('month/s');
        if(empty($month)){
            $start=strtotime(date('Y-m-d'));
        }else{
            $start=strtotime($month);
        }
        $end=strtotime('+1 month',$start);
        $where[]=['create_time','>',$start];
        $where[]=['create_time','<',$end];


        $page=input('page/i',1);
        $limit=10;
        $where[]=['user_id','=',$id];
        $where[]=['user_type','=',1];
        $list=Db::name('withdraw_apply')->alias('a')
            ->where($where)->order('id','desc')->field('sn,money,status,description,create_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();
        foreach ($list as &$v){
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('withdraw_apply')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
}