<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;

class Coupon extends BaseController
{
    public function index(){
        $post = $this->request->param();
        $validate = validate([
            'use' => 'require|number',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $page=input('page/i',1);
        $limit=10;
        $uid=getUserId();
        if ($post['use']==1){
            $where= " user_id=".$uid;
        }elseif ($post['use']==2){
            $t=time();
            $where=" `use`=0 and user_id=".$uid.' and ';
            $where.=" start_time<".$t." and end_time>".$t."";
        }elseif ($post['use']==3){
            $t=time();
            $where=" `use`=0 and user_id=".$uid.' and ';
            $where.=" (start_time>".$t." or end_time<".$t.")";
        }
        // if($post['use']<2){
        //     $where=" `use`=".$post['use']." and user_id=".$uid;
        // }else{
        //     $t=time();
        //     $where=" `use`=0 and user_id=".$uid.' and ';
        //     $where.=" (start_time>".$t." or end_time<".$t.")";
        // }

        $list=Db::name('coupon')
            ->whereRaw($where)->order('id','desc')->field('id,name,money,discount,condition,type,use,start_time,end_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();

         foreach ($list as $key=>$v){
            $list[$key]['start_time']=date('Y-m-d H:i',$v['start_time']);
            $list[$key]['end_time']=date('Y-m-d H:i',$v['end_time']);
            $now = time();
            if ($now>$v['end_time']){
                $list[$key]['chaoshi'] = 1;
            }else{
                $list[$key]['chaoshi'] = 0;
            }

        }
        $count=Db::name('coupon')->whereRaw($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    //兑换
    public function exchange(){
        $post = $this->request->param();
        $validate = validate([
            'exchange' => 'require|max:6',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $t=time();
        $coupon=Db::name('coupon')->where('exchange',$post['exchange'])->where('start_time','<',$t)->where('end_time','>',$t)
            ->where('status',1)->where('get_type',2)->find();
        if(!$coupon){
            return msg(0,'兑换码已失效');
        }
        $uid=getUserId();
        $user_coupon=Db::name('coupon')->where('user_id',$uid)->where('primary_id',$coupon['id'])->find();
        if($user_coupon){
            return msg(0,'您已兑换过此优惠券');
        }
        $coupon['primary_id']=$coupon['id'];
        $coupon['user_id']=$uid;
        $coupon['create_time']=time();
        unset($coupon['id']);
        unset($coupon['exchange']);
        $res=Db::name('coupon')->insert($coupon);
        if($res){
            createNews($uid,'兑换优惠券成功',1,1,0);
            return msg(1,'兑换成功');
        }else{
            return msg(0,'网络错误，请稍后重试');
        }
    }
}