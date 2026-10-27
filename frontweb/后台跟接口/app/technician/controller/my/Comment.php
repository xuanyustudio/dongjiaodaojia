<?php


namespace app\technician\controller\my;


use app\BaseController;
use think\facade\Db;
class Comment extends BaseController
{
   
    

    //得到评论类型
    public function get_type()
    {
        $type = commentType();
        return msg(1, '', $type);
    }

    //评论列表
    public function list()
    {
        //$openid = input('openid', '', 'trim');
        $id=getTechnicianId();
        $types = input('types', '', 'trim');
        $info = Db::name('technician')->field('id,nickname,realname,avatar,sex,mobile,balance,service,composite,credit')->where(['id' => $id])->find();
        if (empty($info)) {
            return msg(0, '该技师不存在');
        }
        $page = input('page/i', 1);
        $limit = 10;
        $where[] = ['A.technician_id', '=', $info['id']];
        if(!empty($types)){
            $where[] = ['A.types','in', $types];
        }
        $list = Db::name('comment')->alias('A')
            ->join('user B','A.user_id=B.id')
            ->field('A.content,A.images,A.create_time,A.service_score,A.technician_score,A.types,B.nickname')

            ->where($where)
            ->order('A.id', 'desc')
            ->limit(($page - 1) * $limit, $limit)->select()->toArray();
        foreach ($list as &$v) {
            $v['images'] = json_decode($v['images']);
            $v['create_time'] = date('m月d日', $v['create_time']);
        }
        $count = Db::name('comment')->alias('A')->where($where)->count();
        $total = $count == 0 ? 0 : intval($count / 10) + 1;
        return msg(1, '', array('list' => $list, 'total' => $total, 'page' => $page), $count);
    }


   



    


}