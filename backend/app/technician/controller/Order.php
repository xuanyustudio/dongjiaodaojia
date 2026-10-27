<?php

namespace app\technician\controller;

use app\BaseController;
use think\facade\Db;

class Order extends BaseController
{
    public function orderList()
    {
        $page = input('page/i', 1);
        $limit = 10;
        $uid = getTechnicianId();

        $where[] = ['a.technician_id', '=', $uid];
        if (input('order_status') != '') {
            if (input('order_status') == 4) {
                $where[] = ['a.order_status', 'in', '4,5'];
            } else {
                $where[] = ['a.order_status', '=', input('order_status')];
            }

        }
        if (input('order_sn') != '') {
            $where[] = ['a.order_sn', '=', input('order_sn')];
        }
        $list = Db::name('order')->alias('a')
            ->leftJoin('service_spec b', 'a.spec_id=b.id')
            ->leftJoin('service d', 'a.service_id=d.id')
            ->leftJoin('user c', 'a.user_id=c.id')
            ->where($where)->order('id', 'desc')->field('a.id,a.order_sn,a.technician_money,a.order_status,a.appointment_time,a.duration,a.create_time,b.name,d.name as service_name,c.username,c.avatar,c.mobile,c.praise,a.province,a.city,a.district,a.address,a.fare')
            ->limit(($page - 1) * $limit, $limit)->select()->toArray();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['appointment_time'] = date('Y-m-d H:i', $v['appointment_time']);
        }
        $count = Db::name('order')->alias('a')->where($where)->count();
        $total = $count == 0 ? 0 : intval($count / 10) + 1;
        return msg(1, '', array('list' => $list, 'total' => $total, 'page' => $page), $count);
    }
    public function orderDetails()
    {
        $order_sn = input('order_sn/s', '');
        $order = Db::name('order')->alias('a')
            ->leftJoin('service_spec b', 'a.spec_id=b.id')
            ->leftJoin('user c', 'a.user_id=c.id')
            ->leftJoin('service d', 'a.service_id=d.id')
            ->where('order_sn', $order_sn)->order('id', 'desc')
            ->field('a.id,a.order_sn,a.technician_money,a.order_status,a.appointment_time,a.fare,a.discount,a.refund,a.total_money,a.lat,a.lng,a.create_time,a.service_time,a.complete_time,b.name,b.duration,d.cover,d.name as service_name,c.username,c.praise,c.avatar,a.province,a.city,a.district,a.address,a.fare')->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }
        $mlat = ($order['lat'] * 3.1415) / 180;
        $mlng = ($order['lng'] * 3.1415) / 180;
        $uid = getTechnicianId();
        $user = Db::name('technician')->where('id', $uid)->field('lat,lng')->find();

        $distance = ACOS(SIN($mlat) * SIN((floatval($user['lat']) * 3.1415) / 180) + COS($mlat) * COS((floatval($user['lat']) * 3.1415) / 180) * COS($mlng - (floatval($user['lng']) * 3.1415) / 180)) * 6380;
        $order['distance'] = sprintf("%.2f", $distance);
        $order['appointment_time'] = date('Y-m-d H:i', $order['appointment_time']);
        $order['create_time'] = date('Y-m-d H:i', $order['create_time']);
        $order['complete_time'] = date('Y-m-d H:i:s', $order['complete_time']);
        return msg(1, '', $order);

    }
    //接取订单
    public function orderReceiving()
    {
        $order_sn = input('order_sn/s', '');

        $order = Db::name('order')->where('order_sn', $order_sn)->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }
        if ($order['order_status'] > 1) {
            return msg(0, '该订单已被接取');
        }
        $res = Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 2, 'receiving_time' => time()]);
        if ($res) {
            createNews($order['user_id'], $order_sn . '订单已被接取', 1, 2, 0);
            return msg(1, '接取订单成功');
        } else {
            return msg(0, '网络错误');
        }
    }
    //开始服务订单
    public function orderService()
    {
        $order_sn = input('order_sn/s', '');

        $order = Db::name('order')->where('order_sn', $order_sn)->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }
        if ($order['order_status'] > 2) {
            return msg(0, '该订单已开始服务');
        }
        $res = Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 3, 'service_time' => time()]);
        if ($res) {
            createNews($order['user_id'], $order_sn . '订单已开始服务', 1, 2, 0);
            return msg(1, '开始服务成功');
        } else {
            return msg(0, '网络错误');
        }
    }

    public function cancelReason()
    {
        return msg(1, '', cancelType());
    }

    public function cancelOrder()
    {
        $order_sn = input('order_sn/s', '');
        $cancel_reason = input('cancel_reason/s', '');
        if (empty($cancel_reason)) {
            return msg(0, '原因不能为空');
        }
        $order = Db::name('order')->where('order_sn', $order_sn)->find();if (!$order) {
            return msg(0, '订单不存在');
        }
        if ($order['order_status'] > 1) {
            return msg(0, '该订单已无法取消');
        }
        $res = Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6, 'cancel_type' => 1, 'cancel_reason' => $cancel_reason, 'pay_status' => 2, 'complete_time' => time()]);
        if ($res) {
            Db::name('technician')->where('id', $order['technician_id'])->dec('credit', 1)->update();
            Db::name('service_date')->where('order_sn', $order_sn)->delete();
            createNews($order['user_id'], $order_sn . '订单技师已取消', 1, 2, 0);
            return msg(1, '取消订单成功');
        } else {
            return msg(0, '网络错误');
        }
    }

    public function completeOrder()
    {
        $order_sn = input('order_sn/s', '');
        $order = Db::name('order')->alias('a')->leftJoin('user b', 'a.user_id=b.id')->field('a.*,b.pid,b.spid')->where('a.order_sn', $order_sn)->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }
        Db::startTrans();
        try {
            Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 4, 'complete_time' => time()]);
            Db::name('service')->where('id', $order['service_id'])->inc('sales', 1)->update();
            Db::name('technician')->where('id', $order['technician_id'])->inc('balance', $order['technician_money'])
                ->inc('credit', 1)
                ->inc('sales', 1)
                ->update();
            $user_log = [];
            $user_log[] = [
                'user_id' => 0,
                'technician_id' => $order['technician_id'],
                'type' => 6,
                'money' => $order['technician_money'] + $order['fare'],
                'income' => 1,
                'create_time' => time(),
                'payment' => 1,
                'user_type' => 2,
                'order_sn' => $order_sn,
            ];
            if (!empty($order['pid'])) {
                Db::name('user')->where('id', $order['pid'])->inc('brokerage', $order['brokerage'])->update();
                $user_log[] = [
                    'user_id' => $order['pid'],
                    'technician_id' => 0,
                    'type' => 5,
                    'money' => $order['brokerage'],
                    'income' => 1,
                    'create_time' => time(),
                    'payment' => 1,
                    'user_type' => 1,
                    'order_sn' => $order_sn,
                ];
                if (!empty($order['spid'])) {
                    Db::name('user')->where('id', $order['spid'])->inc('brokerage', $order['second_brokerage'])->update();
                    $user_log[] = [
                        'user_id' => $order['spid'],
                        'technician_id' => 0,
                        'type' => 5,
                        'money' => $order['second_brokerage'],
                        'income' => 1,
                        'create_time' => time(),
                        'payment' => 1,
                        'user_type' => 1,
                        'order_sn' => $order_sn,
                    ];
                }
            }

            Db::name('user_log')->insertAll($user_log);
            Db::commit();
            return msg(1, '完成订单成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return msg(0, '网络错误');
        }

    }

    //评论
    public function comment()
    {
        $post = $this->request->param();

        $validate = validate([
            'order_id' => 'require|max:100',
            'score' => 'require|max:5',

        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $order = Db::name('order')->where('order_sn', $post['order_id'])->field('id,user_id')->find();
        $comment = Db::name('comment')->where('order_id', $order['id'])->find();
        if ($comment) {
            $res = Db::name('comment')->where('id', $comment['id'])->update(['user_score' => $post['score']]);
        } else {
            $res = Db::name('comment')->insert(
                [
                    'user_score' => $post['score'],
                    'order_id' => $order['id'],
                    'user_id' => $order['user_id'],
                ]
            );
        }
        // return msg(0,json_decode(json_encode($comment)));
        if ($res) {
            $comms = Db::name('comment')->where('user_id', $order['user_id'])->field('SUM(user_score) as user_score,COUNT(*) as count')->find();
            // $comms['user_score'] = $comms['user_score'] > 0 ? $comms['user_score'] : 5;
            $score = sprintf("%.2f", $comms['user_score'] / $comms['count'] + 1);
            Db::name('user')->where('id', $order['user_id'])->update(['praise' => $score]);
            Db::name('order')->where('order_sn', $post['order_id'])->update(['order_status' => 4]);
            return msg(1, '评论成功');
        } else {
            return msg(0, '网络错误');
        }

    }

    //投诉
    public function complaint()
    {
        $post = $this->request->param();
        $validate = validate([
            'order_id' => 'require|max:11',
            'content' => 'require|max:255',

        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        if (empty($post['images'])) {
            $images = "[]";
        } else {
            $images = json_encode($post['images']);
        }
        $order = Db::name('order')->where('id', $post['order_id'])->field('id,user_id')->find();
        $TechnicianId = getTechnicianId();
        $data = [
            'technician_id' => $TechnicianId,
            'user_id' => $order['user_id'],
            'order_id' => $post['order_id'],
            'content' => $post['content'],
            'create_time' => time(),
            'images' => $images,
            'user_type' => 2,
        ];
        $res = Db::name('complaint')->insert($data);
        if ($res) {
            return msg(1, '投诉提交成功');
        } else {
            return msg(0, '网络错误');
        }
    }
}
