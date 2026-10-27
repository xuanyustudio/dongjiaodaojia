<?php


namespace app\technician\controller\my;


use app\BaseController;
use think\facade\Db;
class Index extends BaseController
{
    public function index(){


        $id=getTechnicianId();
//        $openid = input('openid', 'dsdgsdg','trim');
//        if (empty($openid)) {
//            return msg(0, 'openid不能为空');
//        }
        $info = Db::name('technician')->field('id,guid,nickname,realname,avatar,sex,mobile,balance,service,composite,credit,week,start_time,end_time,province,city,district,address,subscribe,status,cardsn,radius')->where(['id'=> $id])->find();
        if(empty($info)){
            return msg(0, '该技师不存在');
        }
        //获取技师工作时间-按小时整点
        if(!empty($info['start_time']) && !empty($info['end_time'])){
            $info['server_hour_range'] = getHourRange($info['start_time'],$info['end_time']);
            $info['start_time'] = indexToHour($info['start_time']);
            $info['end_time'] = indexToHour($info['end_time']);
        }else{
            $info['server_hour_range'] = [0];
            $info['start_time'] = '0:00';
            $info['end_time'] = '0:00';
        }

         $order_sum = Db::name('order')->where(['technician_id'=>$info['id']])->count();
        //  $service_sum = Db::name('order')->where(['technician_id'=>$info['id']])->where('order_status = 3 OR order_status = 4')->count();
          $service_sum = Db::name('order')->where(['technician_id'=>$info['id']])->whereIn('order_status',[3,4,5])->count();
        //成交率

        if(empty($order_sum)){
            $turnover_ratio = 0;
        }else{
            $turnover_ratio = intval(($service_sum/$order_sum)*100);
        }


        $comment_sum = Db::name('comment')->where('technician_id', $info['id'])->count();
        $service_index = [
            'composite'=> $info['composite'],//综合评分
            'turnover_ratio'=>$turnover_ratio, //成交率
            'service_sum' => $service_sum,//服务次数
            'credit' => $info['credit'], //信用分
            'comment_sum'=> $comment_sum//评论数
            
        ];


        $technician_album = Db::name('technician_album')->where('technician_id',$info['id'])->order('id','desc')->find();
        $technician_data = Db::name('technician_data')->where('technician_id',$info['id'])->order('id','desc')->find();
        $technician_healthy = Db::name('technician_healthy')->where('technician_id',$info['id'])->order('id','desc')->find();
        $technician_identity = Db::name('technician_identity')->where('technician_id',$info['id'])->order('id','desc')->find();
        $qualifications = Db::name('technician_qualifications')->where('technician_id',$info['id'])->order('id','desc')->find();
        $technician_tcp = Db::name('technician_tcp')->where('technician_id',$info['id'])->order('id','desc')->find();
        $data = [
            'info'=>$info,
            'service_index'=> $service_index,
            'technician_album'=> $technician_album,//头像相册
            'technician_data'=> $technician_data,//基本资料
            'technician_healthy'=> $technician_healthy, //健康信息
            'technician_identity'=> $technician_identity, //身份认证
            'qualifications'=> $qualifications, //资质信息
            'technician_tcp'=> $technician_tcp,//签署协议

        ];
       
        return msg(1,'',$data);
    }

    //技师服务协议
    public function get_technician_tcp(){
        $tcp = Db::name('base_config')->where('name', 'technician_tcp')->value('data');
        return msg(1, '', $tcp);

    }

    //签署协议
    public function sign_tcp(){
        //$openid = input('openid', '', 'trim');
        $id=getTechnicianId();
        $sign = input('sign', '', 'trim');
//
//        if(empty($openid)){
//            return msg(0, 'openid不能为空');
//        }
        $info = Db::name('technician')->field('id,nickname,realname,avatar,sex,mobile,balance,composite,credit')->where(['id' => $id])->find();
        if (empty($info)) {
            return msg(0, '该技师不存在');
        }
        if (empty($sign)) {
            return msg(0, '签名不能为空');
        }


//        $tcp_id = Db::name('technician_tcp')->where('technician_id', $info['id'])->value('id');
//        if($tcp_id){
//            Db::name('technician_tcp')->where('id',$tcp_id)->update(['sign'=> $sign]);
//        }else{
//            Db::name('technician_tcp')->insert(['sign' => $sign, 'technician_id'=> $info['id'], 'create_time'=>time()]);
//        }
//        return msg(1, '操作成功');

        $tech=Db::name('technician_tcp')->where('technician_id',$info['id'])->find();
        if(!empty($tech)&& $tech['status']<2){
            return msg(0,'请勿重复提交');
        }elseif (!empty($tech)&& $tech['status']==2){
            $res=Db::name('technician_tcp')->where('id',$tech['id'])->update(['sign' => $sign,'status'=>0,'create_time'=>time()]);
        }else{
            $res=Db::name('technician_tcp')->insert(['sign' => $sign, 'technician_id'=> $info['id'], 'status'=>0,'create_time'=>time()]);
        }



        if($res){
            return msg(1,'提交成功,请等待审核');
        }else{
            return msg(0,'网络错误');
        }

    }

    //得到服务项目
    public function get_service()
    {
        $service = Db::name('service')->field('id,name,cover,original_price,price,discount,duration')->where('show', 1)->select()->toArray();
        return msg(1, '', $service);
    }

    //设置服务项目
    public function set_service()
    {
        //$openid = input('openid', '', 'trim');
        $id=getTechnicianId();
        $service_ids = input('service_ids', '', 'trim');
//        if (empty($openid)) {
//            return msg(0, 'openid不能为空');
//        }
        if(empty($service_ids)){
            return msg(0,'请选择服务项目');
        }
        $info = Db::name('technician')->field('id,nickname,realname,avatar,sex,mobile,balance,service,composite,credit')->where(['id' => $id])->find();
        if (empty($info)) {
            return msg(0, '该技师不存在');
        }
        Db::name('technician')->where('id', $info['id'])->update(['service' => $service_ids]);
        return msg(1, '操作成功');
    }

    //设置服务时间
    public function set_service_data()
    {
        //$openid = input('openid', '', 'trim');
        $id=getTechnicianId();
        $week = input('week', '', 'trim');
        $start_time = input('start_time', '', 'trim');
        $end_time = input('end_time', '', 'trim');

//        if (empty($openid)) {
//            return msg(0, 'openid不能为空');
//        }
        $info = Db::name('technician')->field('id,nickname,realname,avatar,sex,mobile,balance,service,composite,credit')->where(['id' => $id])->find();
        if (empty($info)) {
            return msg(0,'该技师不存在');
        }

        $start_time = hourToIndex(strtotime($start_time));
        $end_time = hourToIndex(strtotime($end_time));

        Db::name('technician')->where('id', $info['id'])->update(['week' => $week, 'start_time'=> $start_time, 'end_time'=> $end_time]);
        return msg(1, '操作成功');
    }

    public function setAlbum(){
        
        $post=$this->request->param();
        $validate = validate([
            'album' => 'require',
            'avatar' => 'require|max:250',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getTechnicianId();
        $post['technician_id']=$id;
        $post['status']=0;
        $post['album']=json_encode(explode(',',$post['album']));
        $post['create_time']=time();
        $tech=Db::name('technician_album')->where('technician_id',$id)->find();
//        if(!empty($tech)&& $tech['status']<2){
//            return msg(0,'请勿重复提交');
//        }else

        if (!empty($tech)){
            $res=Db::name('technician_album')->where('id',$tech['id'])->update(['album'=>$post['album'],'avatar'=>$post['avatar']]);
        }else{
            $res=Db::name('technician_album')->insert(['album'=>$post['album'],'avatar'=>$post['avatar'],'technician_id'=>$id]);
        }



        if($res){
            //同步技师主表头像
            Db::name('technician')->where('id',$id)->update(['avatar'=>$post['avatar']]);
            return msg(1,'提交成功,请等待审核');
        }else{
            return msg(0,'网络错误');
        }
    }
    public function setData(){
        $post=$this->request->param();
        $validate = validate([
            'height' => 'require',
            'birthday' => 'require|max:30',
            'profile' => 'require|max:30',
            'practice' => 'require|max:30',
            'sex'=>'require'
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getTechnicianId();
        $post['technician_id']=$id;
        $post['status']=0;
        $post['create_time']=time();

        $post['sex']=intval($post['sex'])+1;


        $tech=Db::name('technician_data')->where('technician_id',$id)->find();
        if(!empty($tech)&& $tech['status']<2){
            return msg(0,'请勿重复提交');
        }elseif (!empty($tech)&& $tech['status']==2){
            $res=Db::name('technician_data')->where('id',$tech['id'])->update($post);
        }else{
            $res=Db::name('technician_data')->insert($post);
        }
        if($res){
            return msg(1,'提交成功,请等待审核');
        }else{
            return msg(0,'网络错误');
        }
    }
    public function setHealthy(){
        $post=$this->request->param();
        $validate = validate([
            'certificates' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getTechnicianId();

        $post['technician_id']=$id;
        $post['status']=0;
        $post['create_time']=time();

        $tech=Db::name('technician_healthy')->where('technician_id',$id)->find();
        
        if (!empty($tech)){
            $res=Db::name('technician_healthy')->where('id',$tech['id'])->update($post);
        }else{
            $res=Db::name('technician_healthy')->insert($post);
        }
        
        // if(!empty($tech)&& $tech['status']<2){
        //     return msg(0,'请勿重复提交');
        // }elseif (!empty($tech)&& $tech['status']==2){
        //     $res=Db::name('technician_healthy')->where('id',$tech['id'])->update($post);
        // }else{
        //     $res=Db::name('technician_healthy')->insert($post);
        // }
        if($res){
            return msg(1,'提交成功,请等待审核');
        }else{
            return msg(0,'网络错误');
        }
    }

    public function setIdentity(){
        $post=$this->request->param();
        $validate = validate([
            'front' => 'require',
            'back' => 'require',
            'realname'=>'require',
            'cardsn'=>'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getTechnicianId();

        $post['technician_id']=$id;
        $post['status']=0;
        $post['create_time']=time();

        $tech=Db::name('technician_identity')->where('technician_id',$id)->find();
        if(!empty($tech)&& $tech['status']<2){
            return msg(0,'请勿重复提交');
        }elseif (!empty($tech)&& $tech['status']==2){
            $res=Db::name('technician_identity')->where('id',$tech['id'])->update($post);
        }else{
            $res=Db::name('technician_identity')->insert($post);
        }
        if($res){
            return msg(1,'提交成功,请等待审核');
        }else{
            return msg(0,'网络错误');
        }

    }
    public function setQualifications(){
        $post=$this->request->param();
        $validate = validate([
            'certificates' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getTechnicianId();

        $post['technician_id']=$id;
        $post['status']=0;
        $post['create_time']=time();
        $tech=Db::name('technician_qualifications')->where('technician_id',$id)->find();
        // unset($post['/technician/my_Index/setQualifications']);
        // var_dump($post);die;
        if(!empty($tech)&& $tech['status']<2){
            return msg(0,'请勿重复提交');
        }elseif (!empty($tech)&& $tech['status']==2){
            $res=Db::name('technician_qualifications')->where('id',$tech['id'])->update($post);
        }else{
            $res=Db::name('technician_qualifications')->insert($post);
        }
        if($res){
            return msg(1,'提交成功,请等待审核');
        }else{
            return msg(0,'网络错误');
        }
    }
    public function creditLog(){
        $id=getTechnicianId();
        $page=input('page/i',1);
        $limit=10;
        $where[]=['technician_id','=',$id];
        $where[]=['order_status','>','3'];
        $list=Db::name('order')
            ->where($where)->order('id','desc')->field('order_status,complete_time')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();
        foreach ($list as &$v){
            if($v['order_status']==6){
                $v['name']='取消订单';
                $v['num']='-1';
            }else{
                $v['num']='+1';
                $v['name']='完成订单';
            }
            $v['complete_time']=date('Y/m/d',$v['complete_time']);
        }
        $count=Db::name('order')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        $info = Db::name('technician')->where(['id'=> $id])->value('credit');

        return msg(1,'',array('credit'=>$info,'list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    public function bindMobile(){
        $id=getTechnicianId();
        if($this->request->isPost()){
            $config=json_decode(Db::name('base_config')->where('name','wx_config')->value('data'),true);
            require_once (root_path()."extend/wxpay/wxBizDataCrypt.php");
            $post=$this->request->param();
            $validate = validate([
                'sessionKey' => 'require',
                'encryptedData' => 'require',
                'iv' => 'require',
            ], [], false, false);
            $result = $validate->check($post);
            if (!$result) {
                $error = $validate->getError();
                return msg(0, $error);
            }
            $appid = $config['wx_app_id'];
            $pc = new \WXBizDataCrypt($appid, $post['sessionKey']);
            $errCode = $pc->decryptData($post['encryptedData'], $post['iv'], $data );
            if ($errCode == 0) {
                $res=Db::name('technician')->where('id',$id)->update(['mobile'=>'123456']);
                return msg(1,"绑定成功",json_decode($data));
            } else {
                return msg(0,"绑定失败",json_decode($errCode));
            }


//            $mobile=input('mobile/s','');
//            if(empty($mobile)){
//                return msg(0,'手机号不能为空');
//            }
//            $res=Db::name('technician')->where('id',$id)->update(['mobile'=>$mobile]);
//            if($res){
//                return msg(1,'绑定手机成功');
//            }else{
//                return msg(0,'网络错误');
//            }
        }else{
            $res=Db::name('technician')->where('id',$id)->value('mobile');
            return msg(1,'',$res);
        }


    }


    public function deleteTechnician(){
        $post=$this->request->param();
        $validate = validate([
            'guid' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $res=Db::name('technician')->where('guid',$post['guid'])->delete();
        if($res){
            return msg(1,'注销成功');
        }else{
            return msg(0,'注销失败');
        }
    }



    


}