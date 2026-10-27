<?php


namespace app\api\controller;


use app\BaseController;
use think\facade\Db;
class Opinion extends BaseController
{
    public function index(){
        return msg(1,'',opinionType());
    }
    public function opinionList(){
        $page=input('page/i',1);
        $limit=10;
        $uid=getUserId();
        $where[]=['user_id','=',$uid];
        $list=Db::name('opinion')
            ->where($where)->order('id','desc')->field('id,content,reply,images,create_time,type')
            ->limit(($page-1)*$limit,$limit)->select()->toArray();
        foreach ($list as &$v){
            $v['images']=json_decode($v['images']);
            $v['create_time']=date('Y-m-d H:i',$v['create_time']);
        }
        $count=Db::name('opinion')->where($where)->count();
        $total=$count==0?0:intval($count/10)+1;
        return msg(1,'',array('list'=>$list,'total'=>$total,'page'=>$page),$count);
    }
    public function createOpinion()
    {
        $post = $this->request->param();
        $validate = validate([
            'content' => 'require',
            'type' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        if(empty($post['images'])){
            $images="[]";
        }else{
            $images=json_encode($post['images']);
        }
        $uid=getUserId();
        $data = [
            'user_id' => $uid,
            'content' => $post['content'],
            'type' => $post['type'],
            'create_time' => time(),
            'images'=>$images,
            'user_type'=>1
        ];
        $res=Db::name('opinion')->insert($data);
        if($res){
            return msg(1,'意见提交成功');
        }else{
            return msg(0,'网络错误');
        }
    }
}