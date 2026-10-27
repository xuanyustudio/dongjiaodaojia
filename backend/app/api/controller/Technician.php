<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;
class Technician extends BaseController
{
    public function index(){

        $post=$this->request->param();
        $message = [
            'lng.require' => 'lng不能为空',
            'lat.require' => 'lat不能为空',
        ];
        $validate = validate([
            'lat' => 'require|max:25',
            'lng' => 'require|max:25',
        ], $message, false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return $this->msg(0, $error);
        }
        $page=input('page/i',1);
        $limit=5;
        $mlat=($post['lat']*3.1415)/180;
        $mlng=($post['lng']*3.1415)/180;
        $where=[];
        $where[]=['a.status','=',1];
        if(!empty($post['realname'])){
            $where[]=['a.realname','like','%'.$post['realname'].'%'];
        }
        if(!empty($post['service_id'])){
            $where[]=['','EXP',Db::raw("FIND_IN_SET(".$post['service_id'].",service)")];
        }
        //  $list=Db::name('technician')->alias('a')
        //      ->leftJoin('technician_data b','a.id=b.technician_id')
        //      ->where($where)
        //      ->where('a.work_status',1)
        //      ->field('a.id,a.realname,a.avatar,a.sex,a.praise,a.sales,a.week,a.start_time,b.practice,a.label,a.end_time,a.create_time,ACOS(SIN('.$mlat.') * SIN((a.lat * 3.1415) / 180) + COS('.$mlat.') * COS((a.lat * 3.1415) / 180) * COS('.$mlng.' - (a.lng * 3.1415) / 180)) * 6380 as distance')
        //      ->where('ACOS(SIN('.$mlat.') * SIN((a.lat * 3.1415) / 180) + COS('.$mlat.') * COS((a.lat * 3.1415) / 180) * COS('.$mlng.' - (a.lng * 3.1415) / 180)) * 6380','<',50)
        //      ->order('distance','asc')->limit(($page-1)*$limit,$limit)->select()->toArray();
        $list=Db::name('technician')->alias('a')
            ->leftJoin('technician_data b','a.id=b.technician_id')
            ->where($where)
            ->where('a.work_status','in','(0,1)')
            // ->where('a.work_status',1)
            ->field('a.id,a.realname,a.avatar,a.sex,a.praise,a.sales+a.fquantity sales,a.week,a.start_time,b.practice,a.label,a.end_time,a.create_time,a.lat,a.lng,a.hframe,a.hrocket,a.hhot,work_status,
            ROUND(
        6378.138 * 2 * ASIN(
            SQRT(
                POW(
                    SIN(
                        (
                            '.$post['lat'].' * PI() / 180 - lat * PI() / 180
                        ) / 2
                    ),
                    2
                ) + COS('.$post['lat'].' * PI() / 180) * COS(lat * PI() / 180) * POW(
                    SIN(
                        (
                            '.$post['lng'].' * PI() / 180 - lng * PI() / 180
                        ) / 2
                    ),
                    2
                )
            )
        ) * 1000
    ) AS distance')
//            ->where('st_distance(point(lng,lat),point('.$post['lat'].','.$post['lng'].'))*111195','<',50000)
            ->order('work_status','desc')->order('distance','asc')->order('create_time','asc')->limit(($page-1)*$limit,$limit)->select()->toArray();

        $year=intval(date('Y'));
        $t=time();
        foreach ($list as &$v){
            $v['distance']=sprintf("%.2f",$v['distance']/1000);
            $appointment_time=$this->makeTime($v['week'],$v['start_time'],$v['end_time'],$v['id']);
            $v['appointment_time']=$appointment_time[1];
            $v['business']=$appointment_time[0];
            $v['practice']=$year-intval(substr($v['practice'], 0, 4))>0?$year-intval(substr($v['practice'], 0, 4)):1;
            $v['comment']=Db::name('comment')->where('technician_id',$v['id'])->count();
            if($t-$v['create_time']<86400){
                $v['new']=1;
            }else{
                $v['new']=0;
            }
        }
        $count=Db::name('technician')->alias('a')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    public function details(){
        $id=input('id/i',0);
        $technician=Db::name('technician')->alias('a')
            ->leftJoin('technician_data b','b.technician_id=a.id')
            ->leftJoin('technician_album c','c.technician_id=a.id')
            ->field('a.nickname,a.realname,a.avatar,c.album,a.praise,a.sales+a.fquantity sales,a.credit,a.service,b.height,b.birthday,b.practice,b.profile,a.province,a.sex,a.hframe,a.hrocket,a.hhot')
            ->where('a.id',$id)->find();
        if(empty($technician)){
            return msg(0,'该技师不存在');
        }

        $technician['birthday']=intval(date('Y'))-intval(substr($technician['birthday'], 0, 4));
        $technician['practice']=intval(date('Y'))-intval(substr($technician['practice'], 0, 4))>0?intval(date('Y'))-intval(substr($technician['practice'], 0, 4)):1;
        $technician['album']=json_decode($technician['album']);
        $service=Db::name('service')->where('find_in_set(id,:serviceIds)',['serviceIds'=>$technician['service']])->field('id,name,price,original_price,cover,discount,sales,praise,duration,label')->order('sort','desc')
            ->order('id','desc')->select()->toArray();
        $technician['collect']=Db::name('collect')->where('technician_id',$id)->where('status',1)->count();
       foreach ($service as $k=>$v){
           $service[$k]['discount']=$v['discount']%10==0?$v['discount']/10:$v['discount'];
           $coupon=Db::name('coupon')->where('status',1)->where('user_id',0)
               ->whereRaw('service_id=:service_id or service_id=:service',['service_id'=>$v['id'],'service'=>0])->find();
           if($coupon){
               $service[$k]['coupon']=1;
           }else{
               $service[$k]['coupon']=0;
           }
       }
       $collect=Db::name('collect')->where('user_id',getUserId())->where('technician_id',$id)->where('status',1)->find();
       if($collect){
           $collect=1;
       }else{
           $collect=0;
       }
       $comment=Db::name('comment')->alias('a')->leftJoin('types b','a.types=b.id')
           ->where('a.technician_id',$id)
           ->group('a.types')->field('b.id,b.name')->select();
       return msg(1,'',['technician'=>$technician,'service'=>$service,'collect'=>$collect,'comment'=>$comment]);
    }



    function makeTime($week,$start_time,$end_time,$technician_id){
        $t=time();
        $w=date('w');

        $hour_index=hourToIndex($t)+1;
        $service_date=Db::name('service_date')->where('appointment_time','>',$t)->order('appointment_time','asc')->field('appointment_time,hour')->select()->toArray();
        if(empty($service_date)){
           for ($i=0;$i<5;$i++){
               $day=strtotime('+'.$i.' day',$t);
               $w=date('w',$day);
               if(strpos($week,$w) === false){
                   continue;
               }
               if($i==0){
                   return ['1',indexToHour($start_time)];
               }else{
                   return ['1',date('m-d',$day).' '.indexToHour($start_time)];
               }
               return ['0','休息中'];
           }
        }

        foreach ($service_date as $k=>$v){
            if($k==0){
                if(strpos($week,$w) === false){
                    continue;
                }
                if($hour_index<$start_time && $hour_index>$end_time){
                    continue;
                }
                if($v['appointment_time']-$t<1799){
                     continue;
                }
                return ['1',indexToHour($hour_index)];
            }else{
                $day=strtotime('+30 min',$service_date[$k-1]['appointment_time']);
                $w=date('w',$day);
                if(strpos($week,$w) === false){
                    continue;
                }
                if($service_date[$k-1]['hour']+1<$start_time && $service_date[$k-1]['hour']+1>$end_time){
                    continue;
                }
                if($v['appointment_time']-$service_date[$k-1]['appointment_time']<1799){
                    continue;
                }
                if(date('m-d',$t)==date('m-d',$day)){
                    return ['1',date('H:i',$day)];
                }else{
                    return ['1',date('m-d H:i',$day)];
                }
            }
        }
        $count=count($service_date);
        if($count==0){
            return ['0','休息中'];
        }
        $day=strtotime('+30 min',$service_date[$count-1]['appointment_time']);
        $w=date('w',$day);
        if(strpos($week,$w) === false){
            return ['0','休息中'];
        }
        if($service_date[$count-1]['hour']+1<$start_time && $service_date[$count-1]['hour']+1>$end_time){
            return ['0','休息中'];
        }
        if(date('m-d',$t)==date('m-d',$day)){
            return ['1',date('H:i',$day)];
        }else{
            return ['1',date('m-d H:i',$day)];
        }
    }


}