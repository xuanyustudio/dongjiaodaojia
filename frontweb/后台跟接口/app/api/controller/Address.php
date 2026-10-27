<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;
class Address extends BaseController
{
    public function index(){

        $uid=getUserId();
        $where[]=['user_id','=',$uid];
        $list=Db::name('address')
            ->where($where)->order('id','desc')->field('id,realname,mobile,province,city,district,address,is_default')->
            select()->toArray();

        return msg(1,'',$list);
    }
    public function details(){
        $id=input('id/i',0);
        $data=Db::name('address')->where('id',$id)->find();
        if(!$data){
            return msg(0,'地址不存在');
        }
        return msg(1,'',$data);
    }
    public function editAddress(){
        $post = $this->request->param();
        $validate = validate([
            'realname' => 'require|max:25',
            'mobile' => 'require|max:25',
            'province' => 'require|max:50',
            'city' => 'require|max:50',
            'district' => 'require|max:50',
            'address' => 'require|max:150',
            'lat' => 'require|max:50',
            'lng' => 'require|max:50',
            'is_default'=>'require|max:1'
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        if($post['is_default']==1){
            Db::name('address')->where('user_id',getUserId())->update(['is_default'=>0]);
        }
        $post['lat']=str_replace(' ','',$post['lat']);
        $post['lat']=str_replace('-','',$post['lat']);
        $post['lng']=str_replace(' ','',$post['lng']);
        $post['lng']=str_replace('-','',$post['lng']);
        if(empty($post['id'])){
            $post['user_id']=getUserId();
            $res=Db::name('address')->insert($post);
        }else{
            Db::name('address')->where('id',$post['id'])->update($post);
            $res=true;
        }
        if($res){
            return msg(1,'编辑成功');
        }else{
            return msg(0,'网络错误');
        }

    }
    public function del(){
        $id=input('id/i',0);
        $data=Db::name('address')->where('id',$id)->find();
        if(!$data){
            return msg(0,'地址不存在');
        }
        $res=Db::name('address')->where('id',$id)->delete();
        return msg(1,'删除成功');
    }
}