<?php


namespace app\technician\controller\my;


use app\BaseController;
use think\facade\Db;
class Opinion extends BaseController
{
    public function index(){
        return msg(1,'',opinionType());
    }
    //反馈记录
    public function list(){
        //$openid = input('openid', '', 'trim');
        $id=getTechnicianId();
        $info = Db::name('technician')->field('id,nickname,realname,avatar,sex,mobile,balance,service,composite,credit')->where(['id' => $id])->find();
        if (empty($info)) {
            return msg(0, '该技师不存在');
        }
        $page=input('page/i',1);
        $limit=10;
        $where[]=['technician_id','=', $info['id']];
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
    //意见反馈提交
    public function add()
    {

        $post = $this->request->param();

        $validate = validate([
//            'openid' => 'require',

            'content' => 'require',
            'type' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id=getTechnicianId();
        $info = Db::name('technician')->field('id,nickname,realname,avatar,sex,mobile,balance,service,composite,credit')->where(['id' => $id])->find();
        if (empty($info)) {
            return msg(0, '该技师不存在');
        }
        if (empty($post['image'])) {
            $images = "{}";
        } else {
            $images =json_encode(explode(',',$post['image']));
        }

        $data = [
            'technician_id' => $info['id'],
            'content' => $post['content'],
            'type' => $post['type'],
            'create_time' => time(),
            'images' => $images,
            'mobile' => $post['mobile'],
            'user_type' => 2
        ];
        $res = Db::name('opinion')->insert($data);
        if ($res) {
            return msg(1, '意见提交成功');
        } else {
            return msg(0, '网络错误');
        }
    }

}