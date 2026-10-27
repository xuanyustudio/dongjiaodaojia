<?php

namespace app\api\controller;

use app\BaseController;
use sms\Sms2;
use think\facade\Cache;
use think\facade\Db;

class Order extends BaseController
{
    public function settlement()
    {
        $post = $this->request->param();
        $validate = validate([
            'service_id' => 'require|max:11',
            'spec_id' => 'require|max:11',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $service = Db::name('service_spec')->alias('a')->leftJoin('service b', 'b.id=a.service_id')
            ->where('a.id', $post['spec_id'])
            ->field('a.name as spec_name,a.price,a.duration,b.name,b.cover,b.sales,b.discount')->find();
//       var_dump($service);
        //       exit();
        $service['discount'] = $service['discount'] % 10 == 0 ? $service['discount'] / 10 : $service['discount'];
        $valuation = Db::name('valuation')->order('id', 'desc')->select()->toArray();
        if (!empty($post['technician_id'])) {
            $technician = Db::name('technician')->where('id', $post['technician_id'])->field('id,nickname,realname,lat,lng')->find();
        } else {
            $technician = new \stdClass();
        }
        $uid = getUserId();
        $user = Db::name('user')->where('id', $uid)->field('id,is_vip,balance')->find();
        $user['vip_discount'] = Db::name('member_rights')->where('id', 2)->value('value');
        return msg(1, '', ['service' => $service, 'technician' => $technician, 'valuation' => $valuation, 'user' => $user]);
    }
    public function userInstructions()
    {
        $res = Db::name('base_config')->where('name', '=', 'user_instructions')->value('data');
        return msg(1, '', $res);
    }

    /**
     * Lee 根据两地经纬度 获取摩托车、骑行、驾车、步行距离
     * 注意：骑行或者步行方式如果距离太远则会计算不出来
     * @param int $lat1 始发地纬度
     * @param int $lng1 始发地经度
     * @param int $lat2 目的地纬度
     * @param int $lng2 目的地经度
     * @param int $type 1:驾车;2:骑行;3:步行;4:摩托车,摩托车批量算路为开放平台高级服务，需申请开通权限后才能访问服务
     * @return array
     */
    public function getDistance()
    {
        $post = $this->request->param();
        $validate = validate([
            'address_id' => 'require|max:30',
            'technician_lat' => 'require|max:30',
            'technician_lng' => 'require|max:30',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $address = Db::name('address')->where('id', $post['address_id'])->find();
//        var_dump($address);
        //        exit();
        $res['distance'] = 0.00; //距离 米
        $res['distance_text'] = ''; //距离 文本
        $res['duration'] = 0.00; //时间 分钟
        $type = 1;
        switch ($type) {
            case 1:
                $way = 'driving';
                break;
            case 2:
                $way = 'riding';
                break;
            case 3:
                $way = 'walking';
                break;
            case 4:
                $way = 'motorcycle';
                break;
            default:
                $way = 'driving';
                break;
        }
        $ak = 'RHSdk1iLH4uoqMidbtFQjhIMGYqVvrYQ';
        //Lee 百度地图批量算路服务
        $url = 'http://api.map.baidu.com/routematrix/v2/' . $way . '?output=json&origins=' . $address['lat'] . ',' . $address['lng'] . '&destinations=' . $post['technician_lat'] . ',' . $post['technician_lng'] . '&ak=' . $ak;
        $data = file_get_contents($url);

        $data = json_decode($data, true);
        if (!empty($data) && $data['status'] == 0) {
            $res['distance'] = $data['result'][0]['distance']['value'];
            $res['distance_text'] = $data['result'][0]['distance']['text'];
            $res['duration'] = $data['result'][0]['duration']['text'];
        } else {
            return msg(0, '百度接口错误');
        }
        return msg(1, '', $res['distance'] / 1000);

    }

    public function serviceDate()
    {
        $id = input('id/i', 0);
        $technician = Db::name('technician')->where('id', $id)
            ->where('status', 1)->field('id,realname,avatar,sex,praise,sales,week,start_time,end_time')->find();
        if (empty($technician)) {
            return msg(0, '该技师不存在');
        }
        $date = [];
        $t = time();
        for ($i = 0; $i < 5; $i++) {
            $day = strtotime('+' . $i . ' day', $t);
            $w = date('w', $day);
            if (strpos($technician['week'], $w) === false) {
                continue;
            }
            $date[] = ['date' => date('m-d', $day), 'week' => $w];
        }
        return msg(1, '', $date);
    }
    public function dateList()
    {
        $data = input('date/s');
        $id = input('id/i', 0);
        $data = date('Y') . '-' . $data;

        $start = strtotime($data);
        $end = strtotime('+1 day', $start);

        //   var_dump(date('Y-m-d H:i:s',$start),date('Y-m-d H:i:s',$end));die;
        $list = Db::name('service_date')
            ->where('type', 'IN', '1,2')
        //->where('hour', 'IN', '0,2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48')
            ->where('technician_id', $id)
            ->where('appointment_time', '>=', $start)
            ->where('appointment_time', '<=', $end)->field('hour')
            ->order('hour ASC')
            ->select()->toArray();
        // $str = '';
        foreach ($list as &$v) {
            //   $srr=explode(':',indexToHour($v['hour']));
            //   if ($srr[1] == 00) {
            //       $str .=$v['hour'].',';
            //   }
            $v['hour'] = indexToHour($v['hour']);
        }
        //   var_dump($str);die;
        //获取技师工作时间-按小时整点
        $info = Db::name('technician')->field('id,start_time,end_time')->where(['id' => $id])->find();
        $server_hour_range = getHourRange($info['start_time'], $info['end_time']);
        $data = [
            'list' => $list,
            'server_hour_range' => $server_hour_range,
        ];
        return msg(1, 0, $data);
    }
    public function coupon()
    {
        $post = $this->request->param();
        $validate = validate([
            'service_id' => 'require|max:11',
            'money' => 'require|max:11',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $uid = getUserId();
        $coupon = Db::name('coupon')->where('user_id', $uid)->where('service_id',$post['service_id'])->field('id,name,money,discount,condition,type,service_id,start_time,end_time')
            ->where('end_time', '>', time())->where('use', 0)
            ->select()->toArray();
//      var_dump($coupon);
        //      exit();
        $available = [];
        $noAvailable = [];
        foreach ($coupon as &$v) {

            //   if($v['service_id']!=$post['service_id'] && $v['service_id']!=0){
            //       $v['reason']='非此服务优惠券';
            //       $noAvailable[]=$v;
            //       continue;
            //   }
            if ($v['start_time'] > time()) {
                $v['reason'] = '不在使用时间';
                $v['start_time'] = date('Y-m-d H:i', $v['start_time']);
                $v['end_time'] = date('Y-m-d H:i', $v['end_time']);
                $noAvailable[] = $v;
                continue;
            }
            if ($v['end_time'] < time()) {
                $v['reason'] = '不在使用时间';
                $v['start_time'] = date('Y-m-d H:i', $v['start_time']);
                $v['end_time'] = date('Y-m-d H:i', $v['end_time']);
                $noAvailable[] = $v;
                continue;
            }

            if ($v['condition'] > $post['money']) {
                $v['reason'] = '不满足使用条件';
                $noAvailable[] = $v;
                continue;
            }
            $v['start_time'] = date('Y-m-d H:i', $v['start_time']);
            $v['end_time'] = date('Y-m-d H:i', $v['end_time']);
            $v['reason'] = '';
            $available[] = $v;
        }
        return msg(1, '', ['available' => $available, 'noAvailable' => $noAvailable]);
    }

    public function createOrder()
    {
        $post = $this->request->param();
        $validate = validate([
            'service_id' => 'require|max:11',
            'spec_id' => 'require|max:11',
            'pay_money' => 'require',
            'total_money' => 'require',
            'fare' => 'require',
            'discount' => 'require', 'technician_id' => 'require',
            'appointment_time' => 'require', 'address_id' => 'require',
            'travelTypeValue' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        if ($post['travelTypeValue'] != 1 && $post['travelTypeValue'] != 2) {
            return msg(0, '参数错误');
        }

        if (Cache::get('order' . getUserId())) {
            return msg(0, '请不要重复下单');
        } else {
            Cache::set('order' . getUserId(), $post['spec_id'], 10);
        }

        $address = Db::name('address')->where('id', $post['address_id'])->find();
        $tech = Db::name('technician')->where('id', $post['technician_id'])->field('lat,lng,radius,mobile')->find();
        $mlat = ($address['lat'] * 3.1415) / 180;
        $mlng = ($address['lng'] * 3.1415) / 180;
        $distance = ACOS(SIN($mlat) * SIN(($tech['lat'] * 3.1415) / 180) + COS($mlat) * COS(($tech['lat'] * 3.1415) / 180) * COS($mlng - ($tech['lng'] * 3.1415) / 180)) * 6380;
        if ($distance > $tech['radius']) {
            return msg(0, '当前技师不在服务距离内');
        }

        $distance = $this->getDistance2($address, $tech);
        $valuation = Db::name('valuation')->order('id', 'desc')->select()->toArray();
        $starting_price = 0; //起步价
        $average_price = 0;
        $kilometre = 0;
        foreach ($valuation as &$v) {
            if ($v['id'] == $post['travelTypeValue']) {
                $starting_price = $v['starting_price'];
                $average_price = $v['average_price'];
                $kilometre = $v['kilometre'];
            }
        }
        if ($distance - $kilometre > 0) {
            $post['fare'] = $starting_price + ($distance - $kilometre) * $average_price;
        } else {
            $post['fare'] = $starting_price;
        }
        $coupon = Db::name('coupon')->where('id', $post['coupon_id'])->find();

        $spec = Db::name('service_spec')->where('id', $post['spec_id'])->find();
        $post['province'] = $address['province'];
        $post['city'] = $address['city'];
        $post['district'] = $address['district'];
        $post['address'] = $address['address'];
        $post['realname'] = $address['realname'];
        $post['mobile'] = $address['mobile'];
        $post['lat'] = $address['lat'];
        $post['lng'] = $address['lng'];
        $post['duration'] = $spec['duration'];
        $post['user_id'] = getUserId();
        $post['create_time'] = time();
        $post['order_sn'] = order_sn('O');
        $post['appointment_time'] = strtotime($post['appointment_time']);
        $post['pay_money'] = $spec['price'];
        $post['total_money'] = $post['pay_money'] + $post['fare'];
        if ($coupon) {
            $post['total_money'] -= $coupon['money'];
            if ($post['total_money'] < 0) {
                return msg(0, '操作失败');
            }

        }
        $res = Db::name('order')->insert($post);
        if ($res) {
            if (!empty($post['coupon_id'])) {
                Db::name('coupon')->where('id', $post['coupon_id'])->update(['use' => 1]);
            }

            $data = [];
            if ($spec['duration'] == 0) {
                $count = 0;
            } elseif ($spec['duration'] <= 30) {
                $count = 1;
            } else {
                $duration = $spec['duration'] % 30;
                if ($duration == 0) {
                    $count = $spec['duration'] / 30;
                } else {
                    $count = ($spec['duration'] - $duration) / 30 + 1;
                }
            }

            for ($i = 0; $i < $count; $i++) {
                $appointment_time = strtotime('+' . ($i * 30) . ' min', $post['appointment_time']);
                $data[] = [
                    'appointment_time' => $appointment_time,
                    'technician_id' => $post['technician_id'],
                    'order_sn' => $post['order_sn'],
                    'type' => 1,
                    'date' => date('Ymd', $appointment_time),
                    'week' => date('w', $appointment_time),
                    'hour' => hourToIndex($appointment_time),
                ];
            }
            if (!empty($data)) {
                Db::name('service_date')->insertAll($data);
            }

            Cache::delete('order' . getUserId());
            // $sms = new Sms2($tech['mobile']);
            // $sms->send($tech['mobile'], '【享享到家】您有一个新的订单，请及时处理。');
            return msg(1, '创建订单成功', $post['order_sn']);
        } else {
            Cache::delete('order' . getUserId());
            return msg(0, '网络错误');
        }
    }

    public function createOrderJZ()
    {
        $post = $this->request->param();
        $validate = validate([
            'spec_id' => 'require',
            'spec_count' => 'require',
            'technician_id' => 'require',
            'appointment_time' => 'require',
            'address_id' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }

        if (Cache::get('order' . getUserId())) {
            return msg(0, '请不要重复下单');
        } else {
            Cache::set('order' . getUserId(), $post['spec_id'], 10);
        }

        $address = Db::name('address')->where('id', $post['address_id'])->find();
        $post['province'] = $address['province'];
        $post['city'] = $address['city'];
        $post['district'] = $address['district'];
        $post['address'] = $address['address'];
        $post['realname'] = $address['realname'];
        $post['mobile'] = $address['mobile'];
        $post['lat'] = $address['lat'];
        $post['lng'] = $address['lng'];
        $post['user_id'] = getUserId();
        $post['create_time'] = time();
        $post['appointment_time'] = time();
        $post['order_sn'] = order_sn('J');

        $spec_id = [];
        $spec_count = [];
        $spec_id = explode(',', $post['spec_id']);
        $spec_count = explode(',', $post['spec_count']);
        $totalduration = 0; //获取加时项目总服务时长
        $totalmoney = 0; //获取项目总金额
        $spec_name = [];
        for ($i = 0; $i < count($spec_id); $i++) {
            $spec = Db::name('service_spec')->where('id', $spec_id[$i])->find();
            $count = 0;
            if ($spec['duration'] <= 30) { //服务时长
                $count = 1;
            } else {
                $duration = $spec['duration'] % 30;
                if ($duration == 0) {
                    $count = $spec['duration'] / 30;
                } else {
                    $count = ($spec['duration'] - $duration) / 30 + 1;
                }
            }
            $totalduration += $count;
            $totalmoney += ($spec['price'] * $spec_count[$i]);
            array_push($spec_name, $spec['name'] . '*' . $spec_count[$i]);
        }
        $spec_name_str = implode(',', $spec_name);
        $post['duration'] = $totalduration * 30;
        $post['pay_money'] = $totalmoney;
        $post['total_money'] = $totalmoney;
        $post['remark'] = $spec_name_str;
        $post['service_id'] = Db::name('service')->where('name', '加单')->field('id')->find()['id'];
        $post['spec_id'] = Db::name('service_spec')->where('name', '加单')->field('id')->find()['id'];
        $res = Db::name('order')->insert($post);
        if (!$res) {
            Cache::delete('order' . getUserId());
            return msg(0, '网络错误');
        }

        $hour = date('G'); //获取当前小时
        $min = date('i');
        $start = $hour * 2;
        if ($min < 30) {
            $start++;
        } else {
            $start = $start + 2;
        }
        $where[] = ['date', '=', date('Ymd')];
        $where[] = ['hour', '>=', $start];
        $service_date = Db::name('service_date')
            ->where('technician_id', $post['technician_id'])
            ->where($where)
            ->select()->toArray();
        $surplusduration = 0; //剩余可用时长
        $hour = [];
        foreach ($service_date as &$v) {
            $hour[] = $v['hour'];
        }
        $surplusdurationarray = []; //剩余可用时长数组
        for ($i = $start; $i < 48; $i++) {
            if (!in_array($i, $hour)) {
                $surplusduration++;
                if (count($surplusdurationarray) < $totalduration) {
                    $surplusdurationarray[] = $i;
                }
            }
        }
        for ($i = 0; $i < count($surplusdurationarray); $i++) {
            $appointment_time = time();
            $data[] = [
                'appointment_time' => $appointment_time,
                'technician_id' => $post['technician_id'],
                'order_sn' => $post['order_sn'],
                'type' => 1,
                'date' => date('Ymd', $appointment_time),
                'week' => date('w', $appointment_time),
                'hour' => $surplusdurationarray[$i],
            ];
        }
        if (!empty($data)) {
            Db::name('service_date')->insertAll($data);
        }
        return msg(1, '创建订单成功', $post['order_sn']);
    }

    public function speclist()
    {
        $slist = Db::name('service_spec')->alias('a')->leftJoin('service b', 'a.service_id=b.id')->where('b.show', 1)->field('a.*,b.cover,b.original_price,b.id service_id')->select()->toArray();
        return msg(1, '返回成功', $slist);
    }

    public function GetHour()
    {
        echo date('G');
    }

    public function orderList()
    {
        $page = input('page/i', 1);
        $limit = 10;
        $uid = getUserId();
        $where[] = ['a.user_id', '=', $uid];
        if (input('order_status') != '') {
            if (input('order_status') == 4) {
                $where[] = ['a.order_status', 'in', '4,5'];
            } else {
                $where[] = ['a.order_status', '=', input('order_status')];
            }

        }
        $list = Db::name('order')->alias('a')
            ->leftJoin('service_spec b', 'a.spec_id=b.id')
            ->leftJoin('technician c', 'a.technician_id=c.id')
            ->leftJoin('service d', 'a.service_id=d.id')
            ->where($where)->order('id', 'desc')->field('a.id,a.order_sn,a.pay_money,a.order_status,a.appointment_time,a.create_time,b.name,c.nickname,c.realname,d.cover')
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
            ->leftJoin('technician c', 'a.technician_id=c.id')
            ->leftJoin('service d', 'a.service_id=d.id')
            ->leftjoin('address e', 'a.address=e.address')
            ->where('order_sn', $order_sn)->order('id', 'desc')
            ->field('a.id,a.order_sn,a.pay_money,a.order_status,a.appointment_time,a.fare,a.discount,a.refund,a.total_money,a.create_time,a.duration,e.id as address_id,a.service_id,a.spec_id,a.technician_id,a.realname as user_realname,a.mobile as user_mobile,a.province,a.city,a.district,a.address,b.name,d.cover,c.nickname,c.realname,c.praise,c.avatar,c.mobile as technician_mobile')->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }
        $order['appointment_time'] = date('Y-m-d H:i', $order['appointment_time']);
        $order['create_time'] = date('Y-m-d H:i', $order['create_time']);
        return msg(1, '', $order);

    }
    public function cancelOrder()
    {
        $order_sn = input('order_sn/s', '');
        $order = Db::name('order')->where('order_sn', $order_sn)->find();if (!$order) {
            return msg(0, '订单不存在');
        }
        if ($order['order_status'] > 2) {
            return msg(0, '该订单已无法取消');
        }

        if ($order['payment'] == 1 && $order['pay_status'] == 1) {
            $res = Db::name('user')->where('id', $order['user_id'])->inc('balance', $order['pay_money'])->update();
        }

        Db::startTrans();
        try {
            if ($order['payment'] == 1 && $order['pay_status'] == 1) {
                $user_log = [
                    'user_id' => $order['user_id'],
                    'type' => 2,
                    'money' => $order['pay_money'],
                    'income' => 1,
                    'create_time' => time(),
                    'payment' => 1,
                    'user_type' => 1,
                    'order_sn' => $order['order_sn'],
                ];
                Db::name('user_log')->insert($user_log);
            }

            //有优惠券
            if ($order['coupon_id'] > 0) {
                Db::name('coupon')->where('id', $order['coupon_id'])->update(['use' => 0]);
            }
            //更新订单
            if ($order['pay_status'] == 1) {
                Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6, 'pay_status' => 2]);
            } else {
                Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6]);
            }
            Db::name('service_date')->where('order_sn', $order_sn)->delete();
            Db::commit();
            return msg(1, '取消成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return msg(0, '网络错误');
        }

        return msg(0, '网络错误');

        //   if($order['order_status']<2){
        //       return msg(1,'可取消订单');
        //   }
        //   $config=json_decode(Db::name('base_config')->where('name','refund_config')->value('data'),true);

        //   if($order['appointment_time']-$config['time']*60<time()){
        //       //return msg(2,"当前离预约时间不足".$config['time'].'分钟,需扣除'.$config['rate'].'%手续费');
        //       return msg(1,"可取消订单");
        //   }else{
        //       return msg(1,"可取消订单");
        //   }
    }
    public function confirmCancel()
    {
//       $data=[
        //           'app_id'=>'sdgsdgsdgsdf',
        //           'app_secret'=>'dsgdgse',
        //           'mch_id'=>'146245487',
        //           'mch_secret'=>'setwet',
        //           'wxapp_app_id'=>'wxdsgsdgdg',
        //           'wxapp_secret'=>'sjgksndnhjk'
        //       ];
        //       Db::name('base_config')->where('name','wx_config')->update(['data'=>json_encode($data)]);
        //       exit();

        $order_sn = input('order_sn/s', '');
        $refreason = input('refreason');
        $order = Db::name('order')->alias('a')->leftJoin('service_spec b', 'a.spec_id=b.id')->leftjoin('technician t', 'a.technician_id=t.id')
            ->field('a.*,b.name as spec_name,t.mobile as tmobile')
            ->where('a.order_sn', $order_sn)->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }

        // $config = json_decode(Db::name('base_config')->where('name', 'refund_config')->value('data'), true);
        if ($order['order_status'] == 1 || $order['order_status'] == 2 || $order['order_status'] == 0) {
            Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6, 'cancel_type' => 0, 'pay_status' => 2, 'complete_time' => time(), 'technician_money' => 0, 'refreason' => $refreason]);
            include_once app()->getRootPath() . "extend/sms/Sms2.php";
            $customer_service = Db::name('base_config')->where('name', '=', 'customer_service')->value('data'); //客服电话
            $sms = new Sms2();
            $sms->send($customer_service, '【享享到家】平台上有一个退款订单，客户名：' . $order['realname'] . '，手机号：' . $order['mobile'] . '。请尽快联系客户处理');
            $sms->send($order['tmobile'], '您有一个订单已被取消，单号:' . $order_sn);
            Db::name('service_date')->where('order_sn', $order_sn)->delete();
            return msg(1, '取消订单成功');
        } else {
            return msg(0, '该订单无法取消');
        }
//         switch ($order['order_status']) {
        //             case 0;
        //                 Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6, 'cancel_type' => 0, 'pay_status' => 2, 'complete_time' => time(), 'technician_money' => 0]);
        //                 Db::name('service_date')->where('order_sn', $order_sn)->delete();
        //                 //sendOrderMsg($order['technician_id'], $order['spec_name'], '取消订单', $order_sn, $order['appointment_time']);
        //                 return msg(1, '取消订单成功');
        //                 break;
        //             case 1;
        //                 $pay = new \app\api\controller\Pay($this->app, $this->view);
        //                 $result = $pay->refund($order, $order['pay_money']);
        //                 if ($result['code'] == 1) {
        //                     Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6, 'cancel_type' => 0, 'pay_status' => 2, 'refund' => $order['pay_money'], 'technician_money' => 0, 'complete_time' => time()]);
        //                     Db::name('service_date')->where('order_sn', $order_sn)->delete();
        //                     return msg(1, '取消订单成功');
        //                 } else {
        //                     return json($result);
        //                 }
        //                 break;
        //             case 2;
        //                 $pay = new \app\api\controller\Pay($this->app, $this->view);
        //                 $refund_money = sprintf("%.2f", ($order['pay_money'] - $order['fare']) * (100 - $config['rate']) / 100);
        //                 $technician_money = ($order['pay_money'] - $order['fare'] - $refund_money) * 0.5;
        // //               if($order['appointment_time']-$config['time']*60<time()){
        //                 //                   $refund_money=sprintf("%.2f",($order['pay_money']-$order['fare'])*(100-$config['rate'])/100);
        //                 //                   $technician_money=sprintf("%.2f",($order['pay_money']-$order['fare'])*$config['technician_rate']/100);
        //                 //               }else{
        //                 //                   $technician_money=0;
        //                 //                   $refund_money=$order['pay_money']-$order['fare'];
        //                 //               }
        //                 $result = $pay->refund($order, $refund_money);
        //                 if ($result['code'] == 1) {
        //                     Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 6, 'cancel_type' => 0, 'pay_status' => 2, 'refund' => $refund_money, 'technician_money' => $technician_money, 'complete_time' => time()]);
        //                     Db::name('technician')->where('id', $order['technician_id'])->inc('balance', $order['fare'] + $technician_money)->update();
        //                     Db::name('service_date')->where('order_sn', $order_sn)->delete();
        //                     $user_log = [
        //                         'user_id' => 0,
        //                         'technician_id' => $order['technician_id'],
        //                         'type' => 6,
        //                         'money' => $technician_money + $order['fare'],
        //                         'income' => 1,
        //                         'create_time' => time(),
        //                         'payment' => 1,
        //                         'user_type' => 2,
        //                         'order_sn' => $order_sn,
        //                     ];
        //                     Db::name('user_log')->insert($user_log);
        //                     return msg(1, '取消订单成功');
        //                 } else {
        //                     return json($result);
        //                 }
        //                 break;
        //             default;
        //                 return msg(0, '该订单无法取消');
        //                 break;
        //         }

    }

    public function completeOrder()
    {
        $order_sn = input('order_sn/s', '');
        $order = Db::name('order')->alias('a')->leftJoin('user b', 'a.user_id=b.id')->field('a.*,b.pid')->where('a.order_sn', $order_sn)->find();
        if (!$order) {
            return msg(0, '订单不存在');
        }
        // $orderCount = 0;
        // if (!empty($order['pid'])) {
        //     $orderCount = Db::name('order')->field('count(*) a')->where('user_id', $order['user_id'])->where('order_status','in','3,4')->find();
        // }
        Db::startTrans();
        try {
            Db::name('order')->where('order_sn', $order_sn)->update(['order_status' => 4, 'complete_time' => time()]);
            Db::name('technician')->where('id', $order['technician_id'])->inc('balance', $order['technician_money'])->inc('credit', 1)->update();
            Db::name('service_date')->where('order_sn', $order_sn)->delete();
            // Db::name('user')->where('id', $order['pid'])->inc('balance', 20)->update();
            // if (!empty($order['pid']) && $orderCount < 2) {
            //     if ($orderCount == 0) {
            //         Db::name('user')->where('id', $order['pid'])->inc('balance', 20)->update();
            //     } else {
            //         Db::name('user')->where('id', $order['pid'])->inc('balance', 10)->update();
            //     }
            // }
            // $user_log = [];
            // $user_log[] = [
            //     'user_id' => 0,
            //     'technician_id' => $order['technician_id'],
            //     'type' => 6,
            //     'money' => $order['technician_money'],
            //     'income' => 1,
            //     'create_time' => time(),
            //     'payment' => 1,
            //     'user_type' => 2
            // ];

            // if (!empty($order['pid'])) {
            //     Db::name('user')->where('id', $order['pid'])->inc('balance', $order['brokerage'])->update();
            //     $user_log[] = [
            //         'user_id' => $order['pid'],
            //         'technician_id' => 0,
            //         'type' => 5,
            //         'money' => $order['brokerage'],
            //         'income' => 1,
            //         'create_time' => time(),
            //         'payment' => 1,
            //         'user_type' => 1
            //     ];
            // }
            // Db::name('user_log')->insertAll($user_log);
            Db::commit();
            return msg(1, '完成订单成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
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
        $order = Db::name('order')->where('id', $post['order_id'])->field('id,user_id,technician_id')->find();
        $uid = getUserId();
        $data = [
            'technician_id' => $order['technician_id'],
            'user_id' => $uid,
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

    public function commentType()
    {
        $data = Db::name('types')->where('type', '=', 'comment')->field('id,name')->select();
        return msg(1, '', $data);
    }

    //评价
    public function comment()
    {
        $post = $this->request->param();
        $validate = validate([
            'order_sn' => 'require|max:100',
            'content' => 'require|max:255',
            'types' => 'require',
            'service_score' => 'require',
//            'service_id'=>'require',
            'technician_score' => 'require',
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
        $uid = getUserId();
        $order = Db::name('order')->where('order_sn', $post['order_sn'])->field('id,user_id,technician_id,service_id')->find();
        $data = [
            'technician_id' => $order['technician_id'],
            'user_id' => $uid,
            'order_id' => $order['id'],
            'content' => $post['content'],
            'create_time' => time(),
            'images' => $images,
            'types' => $post['types'],
            'service_score' => $post['service_score'],
            'technician_score' => $post['technician_score'],
            'service_id' => $order['service_id'],
        ];
        $res = Db::name('comment')->insert($data);
        if ($res) {
            Db::name('order')->where('order_sn', $post['order_sn'])->update(['order_status' => 5]);
            return msg(1, '评论提交成功');
        } else {
            return msg(0, '网络错误');
        }
    }

    public function getDistance2($address, $tech)
    {
        $res['distance'] = 0.00; //距离 米
        $res['distance_text'] = ''; //距离 文本
        $res['duration'] = 0.00; //时间 分钟
        $type = 1;
        switch ($type) {
            case 1:
                $way = 'driving';
                break;
            case 2:
                $way = 'riding';
                break;
            case 3:
                $way = 'walking';
                break;
            case 4:
                $way = 'motorcycle';
                break;
            default:
                $way = 'driving';
                break;
        }
        $ak = 'RHSdk1iLH4uoqMidbtFQjhIMGYqVvrYQ';
        //Lee 百度地图批量算路服务
        $url = 'http://api.map.baidu.com/routematrix/v2/' . $way . '?output=json&origins=' . $address['lat'] . ',' . $address['lng'] . '&destinations=' . $tech['lat'] . ',' . $tech['lng'] . '&ak=' . $ak;
        $data = file_get_contents($url);

        $data = json_decode($data, true);
        if (!empty($data) && $data['status'] == 0) {
            $res['distance'] = $data['result'][0]['distance']['value'];
            $res['distance_text'] = $data['result'][0]['distance']['text'];
            $res['duration'] = $data['result'][0]['duration']['text'];
        } else {
            return 0;
        }
        return $res['distance'] / 1000;
    }

    public function cleanGarbageData()
    {
        $orderlist = Db::name('order')->where('order_status', 'in', '(0,4,5,6)')->where('order_sn', 'like', '%' . date('Ymd') . '%')->field('id,order_sn,order_status,create_time')->select();
        if ($orderlist != null) {
            foreach ($orderlist as &$order) {
                if (abs(time() - $order['create_time']) >= 60) {
                    if ($order['order_status'] == 0) {
                        Db::name('order')->where('order_sn', $order['order_sn'])->update(['order_status' => 6]);
                    }
                    Db::name('service_date')->where('order_sn', $order['order_sn'])->delete();
                }
            }
        }
    }

}
