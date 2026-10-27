<?php


namespace app\common\command;


use app\BaseController;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class Coupon extends Command
{
    protected function configure()
    {
        $this->setName('coupon')
            ->setDescription('会员优惠券');
    }

    protected function execute(Input $input, Output $output)
    {

        $rights=Db::name('member_rights')->where('id',1)->find();
        if($rights['status']==0 || empty($rights['value'])){
            return;
        }
        $user=Db::name('user')->where('is_vip',1)->select()->toArray();
        $coupon=Db::name('coupon')->where('id',$rights['value'])->find();
        if(empty($coupon)){
            return;
        }
        $data=[];
        $t=time();
        foreach ($user as &$v){
            $user_coupon['primary_id']=$coupon['id'];
            $user_coupon['user_id']=$v['id'];
            $user_coupon['create_time']=$t;
            unset($user_coupon['id']);
            unset($user_coupon['exchange']);
            $data[]=$user_coupon;
        }
        Db::name('coupon')->insertAll($data);


    }
}