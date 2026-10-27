<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;
class Collect extends BaseController
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

       $where[]=['a.user_id','=',getUserId()];
       $where[]=['a.status','=',1];
       $where[]=['b.status','=',1];
       $list=Db::name('collect')->alias('a')->leftJoin('technician b','a.technician_id=b.id')
           ->leftJoin('technician_data c','b.id=c.technician_id')
           ->where($where)
           ->field('a.id,a.technician_id,b.realname,b.avatar,b.sex,b.praise,b.sales,b.week,b.label,b.create_time,b.start_time,b.end_time,c.practice,ACOS(SIN('.$mlat.') * SIN((b.lat * 3.1415) / 180) + COS('.$mlat.') * COS((b.lat * 3.1415) / 180) * COS('.$mlng.' - (b.lng * 3.1415) / 180)) * 6380 as distance')
           ->order('a.id','desc')->limit(($page-1)*$limit,$limit)->select()->toArray();
       $year=intval(date('Y'));
       $t=time();
       foreach ($list as &$v){
           $v['distance']=sprintf("%.2f",$v['distance']);
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
       $count=Db::name('collect')->alias('a')->leftJoin('technician b','a.technician_id=b.id')->where($where)->count();
       $total=$count==0?0:intval($count/10)+1;
       return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
   }
   public function collect(){
       $post=$this->request->param();
       $validate = validate([
           'technician_id' => 'require|max:25',
       ], [], false, false);
       $result = $validate->check($post);
       if (!$result) {
           $error = $validate->getError();
           return $this->msg(0, $error);
       }
       $uid=getUserId();
       $collect=Db::name('collect')->where('user_id',$uid)->where('technician_id',$post['technician_id'])->find();
       if($collect){
           if($collect['status']==1){
               Db::name('collect')->where('id',$collect['id'])->update(['status'=>0]);
               return msg(1,'取消收藏成功');
           }else{
               Db::name('collect')->where('id',$collect['id'])->update(['status'=>1]);
               return msg(1,'收藏成功');
           }
       }else{
           $data=[
               'user_id'=>$uid,
               'technician_id'=>$post['technician_id'],
               'status'=>1,
           ];
           Db::name('collect')->insert($data);
           return msg(1,'收藏成功');
       }
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