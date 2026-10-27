<?php

namespace app\technician\controller;

use app\BaseController;
use OSS\Core\OssException;
use OSS\OssClient;
use sms\Sms2;
use sms\Sms;
use think\facade\Cache;
use think\facade\Db;

class Index extends BaseController
{
    public function index()
    {
        $id = getTechnicianId();
        $banner = Db::name('banner')->where('type', 1)->order('sort', 'desc')->field('id,image')->select();
        $technician = Db::name('technician')->where('id', $id)->field('id,nickname,username,mobile,realname,avatar,status,work_status,province,city,district,address')->find();
        $technician['new_order'] = Db::name('order')->where('technician_id', $id)->where('order_status', 1)->count();
        $technician['ongoing_order'] = Db::name('order')->where('technician_id', $id)->where('order_status', 3)->count();
        $technician['complete_order'] = Db::name('order')->where('technician_id', $id)->whereIn('order_status', [4, 5])->count();
        $tips = Db::name('base_config')->where('name', 'tips')->value('data');
        return msg(1, '', ['banner' => $banner, 'technician' => $technician, 'tips' => $tips]);
    }
    public function tcp()
    {

        $field = input('field/s', '');

//        var_dump($field);
        //        exit();
        $res = Db::name('base_config')->where('name', '=', $field)->value('data');
        return msg(1, '', $res);
    }

    public function status()
    {
        $id = getTechnicianId();
        $technician = Db::name('technician')->where('id', $id)->field('id,work_status')->find();
        if ($technician['work_status'] == 2) {
            return msg(0, '您的账号已被禁用');
        }
        $status = input('work_status/i', 0);
        Db::name('technician')->where('id', $id)->update(['work_status' => $status]);
        return msg(1, '切换成功');
    }
    public function signIn()
    {

        $post = $this->request->param();

        $validate = validate([
            'province' => 'require|max:30',
            'city' => 'require|max:30',
            'district' => 'require|max:30',
            'address' => 'require|max:150',
            'lat' => 'require|max:30',
            'lng' => 'require|max:30',
            'radius' => 'require',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id = getTechnicianId();

        $res = Db::name('technician')->where('id', $id)->update($post);

        if ($res) {
            return msg(1, '签到成功');
        } else {
            return msg(0, '网络错误');
        }
    }
    //报警
    public function callPolice()
    {
        $post = $this->request->param();

        $validate = validate([
            'province' => 'require|max:30',
            'city' => 'require|max:30',
            'district' => 'require|max:30',
            'address' => 'require|max:150',
            'lat' => 'require|max:30',
            'lng' => 'require|max:30',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id = getTechnicianId();
        $post['technician_id'] = $id;
        $post['create_time'] = time();
        $where[] = ['a.technician_id', '=', $id];
        $order = Db::name('order')->alias('a')
            ->leftJoin('technician b', 'a.technician_id=b.id')
            ->where('a.technician_id=' . $id)
            ->where('a.order_status=3')
            ->field('b.realname tname,b.mobile tmobile,a.realname uname,a.mobile umobile')->find();
        if ($order) {
            $msg = '【紧急情况】技师' . $order['tname'] . '（手机号：' . $order['tmobile'] . '）在“' . $post['province'] . $post['city'] . $post['district'] . $post['address'] . '”遇到紧急事故发起求救，客户名称：' . $order['uname'] . '，客户手机号：' . $order['umobile'] . '。请尽快处理！';
            $umobile = Db::name('base_config')->where('name="umobile"')->field('data')->find();
            if (!$umobile) {
                return msg(0, '没有紧急联系号码');
            }

            $urgent_mobile = json_decode($umobile['data'], true)['urgent_mobile'];
            include_once app()->getRootPath() . "extend/sms/Sms2.php";
            $sms = new Sms2($urgent_mobile);
            $status = $sms->send($urgent_mobile, $msg);
            if ($status['returnstatus'] == 'Success') {
                Db::name('call_police')->insert($post);
                return msg(1, '报警成功');
            } else {
                return msg(0, '发送失败，' . $status['message']);
            }
        } else {
            return msg(0, '没有正在进行中的订单');
        }
    }

    public function msgSend()
    {
        var_dump(1222222);die;
        $host = "https://jumfixed.market.alicloudapi.com";
        $path = "/voice-notify/send";
        $method = "POST";
        $appcode = "e290fd44d3c2410e9ca24bb6e100f0df";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodys = "mobile=13883700468&param=&templateId=955762849774743552";
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //设定返回信息中是否包含响应信息头，启用时会将响应信息头作为数据流输出，true 表示输出信息头, false表示不输出信息头
        //如果想将响应结果json字符串转为json数组，建议将 CURLOPT_HEADER 设置成 false
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        curl_exec($curl);
    }

    public function smsCode()
    {
//        return msg(1, '发送成功');
        $mobile = input('mobile');
        $config = json_decode(Db::name('base_config')->where('name', 'sms')->value('data'), true);
//        var_dump($config);
        if (empty($config['accessKeyId'])) {
            return msg(0, '短信未配置');
        }
//        $config = [
        //            'accessKeyId' => 'LTAI5t7Bw4pSduHyNSuMbKqn',
        //            'accessKeySecret' => 'l95PQTQ9RCZJTEonbJweS6BKuqGT6N',
        //            'signName' => '忠企',
        //            'templateCode' => 'SMS_217926394',
        //        ];
        include_once app()->getRootPath() . "extend/sms/Sms.php";
        $sms = new Sms($config);
        $code = $this->generate_code();
        $status = $sms->send_verify($mobile, $code);

        //return msg(1, '发送成功',$status);
        if ($status['Code'] == 'OK') {
            Cache::set('code' . $mobile, $code, 300);
            return msg(1, '发送成功');
        } else {
            return msg(0, '发送失败' . $status['Message']);
        }

    }
    public function generate_code($length = 6)
    {
        $min = pow(10, ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }

    //日期
    public function busyDate()
    {
        $id = getTechnicianId();
        $technician = Db::name('technician')->where('id', $id)->field('id,week,start_time,end_time')->find();

        $i = 0;
        $j = 0;
        $t = time();
        $week = $technician['week'];
        if (empty($week)) {
            return msg(0, '请先设置您的工作时间');
        }
        $date = [];
        $start = indexToHour($technician['start_time']);
        $end = indexToHour($technician['end_time']);
        while ($i < 5) {
            $day = strtotime('+' . $j . ' day', $t);
            $j++;
            $w = date('w', $day);

            if (strpos($week, $w) === false) {
//                var_dump($week);
                //                exit();
                continue;
            }
            $date[] = [
                'detailed_date' => date('Ymd', $day),
                'date' => date('m-d', $day),
                'week' => $w,
                'start' => $start,
                'end' => $end,
            ];
            $i++;
        }

        return msg(1, '', $date);
    }

    //忙时
    public function busyHour()
    {
        $id = getTechnicianId();
        $date = input('date/s', '');
        $data = Db::name('service_date')->where('technician_id', $id)->where('date', $date)->field('id,hour,type')->select()->toArray();
        foreach ($data as &$v) {
            $v['hour'] = indexToHour($v['hour'], 0);
        }
        // $data = [];
        return msg(1, '', $data);
    }
    //设置忙时
    public function setBusyHour()
    {
        $post = $this->request->param();
        $validate = validate([
            'hour' => 'require|max:30',
            'date' => 'require|max:30',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $post_data = date('y-m-d', strtotime($post['date']));
        $id = getTechnicianId();
//        var_dump(strtotime($post_data.' '.$post['hour']));

        $hour = hourToIndex(strtotime($post_data . ' ' . $post['hour']));
        // var_dump($hour);
        // exit();
        $date = Db::name('service_date')->where('technician_id', $id)->where('date', $post['date'])->where('hour', $hour)->find();
        if (!$date) {
            $res = Db::name('service_date')->insert(
                [
                    'appointment_time' => strtotime($post['date'] . ' ' . $post['hour']),
                    'date' => $post['date'],
                    'week' => date('w', strtotime($post['date'])),
                    'hour' => $hour,
                    'technician_id' => $id,
                    'type' => 2,
                    'create_time' => time(),
                ]
            );
            if ($res) {
                return msg(1, '设置成功');
            } else {
                return msg(0, '网络错误');
            }
        } else {
            if ($date['type'] == 1) {
                return msg(0, '服务时间段不可取消');
            }

            $res = Db::name('service_date')->where('id', $date['id'])->delete();
            if ($res) {
                return msg(1, '设置成功');
            } else {
                return msg(0, '网络错误');
            }

        }

    }
    //收入
    public function incomeStatistics()
    {
        $id = getTechnicianId();
        $month = input('month/s');
        if (empty($month)) {
            $start = strtotime(date('Y-m-d'));
        } else {
            $start = strtotime($month);
        }
        $end = strtotime('+1 month', $start);

        $ongoing = Db::name('order')->where('technician_id', $id)->where('create_time', '>', $start)->where('create_time', '<', $end)->where('order_status', 'in', '1,2,3')->sum('technician_money');
        $complete = Db::name('order')->where('technician_id', $id)->where('create_time', '>', $start)->where('create_time', '<', $end)->where('order_status', '>', '3')->sum('technician_money');
        return msg(1, '', ['ongoing' => $ongoing, 'complete' => $complete]);
    }
    //收入明细
    public function incomeList()
    {
        $id = getTechnicianId();

        $month = input('month/s');
        if (empty($month)) {
            $start = strtotime(date('Y-m-d'));
        } else {
            $start = strtotime($month);
        }
        $end = strtotime('+1 month', $start);

        $page = input('page/i', 1);
        $limit = 10;
        $where[] = ['technician_id', '=', $id];
        $where[] = ['create_time', '>', $start];
        $where[] = ['create_time', '<', $end];
        $status = input('status/i', 0);
        switch ($status) {
            case 1;
                $where[] = ['order_status', 'in', '1,2,3'];
                break;
            case 2;
                $where[] = ['order_status', '>', 3];
                break;
        }
        $list = Db::name('order')
            ->where($where)->order('id', 'desc')->field('order_sn,create_time,technician_money,order_status')
            ->limit(($page - 1) * $limit, $limit)->select()->toArray();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            switch ($v['order_status']) {
                case 1;
                case 2;
                    $v['status_text'] = '进行中';
                    break;
                case 3;
                case 4;
                case 5;
                    $v['status_text'] = '已完成';
                    break;
            }
        }
        $count = Db::name('order')->where($where)->count();
        $total = $count == 0 ? 0 : intval($count / 10) + 1;
        return msg(1, '', array('list' => $list, 'total' => $total, 'page' => $page), $count);
    }

    //订单统计
    public function orderStatistics()
    {
        $status = input('status/i', 0);
        $end = time();
        $where = [];
        switch ($status) {
            case 0;
                $name = date('m/d', time());
                $start = strtotime(date('Y-m-d'));
                break;
            case 1;
                $name = '近3天';
                $start = strtotime('-3 day', $end);
                $where[] = ['create_time', '>', $start];
                break;
            case 2;
                $name = '近7天';
                $start = strtotime('-7 day', $end);
                $where[] = ['create_time', '>', $start];
                break;
            case 3;
                $name = '近30天';
                $start = strtotime('-1 month', $end);
                $where[] = ['create_time', '>', $start];
                break;
        }
        $id = getTechnicianId();
        $detailedWhere[] = ['create_time', '>', $start];
        $detailed = Db::name('order')->where('technician_id', $id)->where('order_status', 'in', '4,5')->where($detailedWhere)->field('SUM(pay_money) as money,COUNT(*) as count,SUM(fare) as fare')->find();
        if (empty($detailed['money'])) {
            $detailed['money'] = 0;
        }
        if (empty($detailed['fare'])) {
            $detailed['fare'] = 0;
        }
        $detailed['name'] = $name;

        $complete = Db::name('order')->where('technician_id', $id)->where('order_status', 'in', '4,5')->where($where)->field('SUM(pay_money) as money,COUNT(*) as count')->find();
        if (empty($complete['money'])) {
            $complete['money'] = 0;
        }
        $cancel = Db::name('order')->where('technician_id', $id)->where('order_status', '=', '6')->where($where)->field('SUM(pay_money) as money,COUNT(*) as count')->find();
        if (empty($cancel['money'])) {
            $cancel['money'] = 0;
        }
        return msg(1, '', ['complete' => $complete, 'cancel' => $cancel, 'detailed' => $detailed]);

    }
    //账户绑定
    public function aliBind()
    {
        $post = $this->request->param();
        $validate = validate([
            'realname' => 'require|max:30',
            'mobile' => 'require|max:30',

        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id = getTechnicianId();
        $ali = Db::name('technician_ali')->where('technician_id', $id)->find();
        if (!$ali) {
            $post['technician_id'] = $id;
            $res = Db::name('technician_ali')->insert(
                $post
            );
        } else {
            $res = Db::name('technician_ali')->where('id', $ali['id'])->update($post);
        }
        if ($res) {
            return msg(1, '绑定成功');
        } else {
            return msg(0, '网络错误');
        }
    }
    //钱包
    public function wallet()
    {
        $id = getTechnicianId();
        $user = Db::name('technician')->where('id', $id)->field('balance')->find();
        $user['withdraw'] = Db::name('withdraw_apply')->where('user_id', $id)->where('user_type', 2)->where('status', 2)->sum('money');
        return msg(1, '', $user);
    }
    //提现页
    public function withdraw()
    {
        $id = getTechnicianId();
        $user = Db::name('technician')->where('id', $id)->field('balance')->find();
        $realname = Db::name('technician_ali')->where('technician_id', $id)->value('realname');
        if (empty($realname)) {
            $user['ali'] = 0;
            $user['realname'] = '';
        } else {
            $user['ali'] = 1;
            $user['realname'] = $realname;
        }
        return msg(1, '', $user);
    }
    //提现申请
    public function withdrawApply()
    {
        $post = $this->request->param();
        $validate = validate([
            'money' => 'require|max:30',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $id = getTechnicianId();
        $min = Db::name('base_config')->where('name', 'withdraw_min')->value('data');
        $user = Db::name('technician')->alias('a')->leftJoin('technician_ali b', 'a.id=b.technician_id')
            ->where('a.id', $id)->field('a.balance,b.realname,b.mobile')->find();
        if ($post['money'] < $min) {
            return msg(0, '提现金额不能少于' . $min);
        }
        if ($user['balance'] < $post['money']) {
            return msg(0, '余额不足');
        }
        if (empty($user['realname'])) {
            return msg(0, '未绑定支付宝');
        }
        //手续费比例
        $technician_rate = Db::name('base_config')->where('name', 'technician_rate')->value('data');
        $rate = 1 - ($technician_rate / 100);
        $left_money = bcmul($post['money'], $rate, 2);
        $poundage = $post['money'] - $left_money;

        $order_sn = order_sn('W');
        $res = Db::name('withdraw_apply')->insert(
            [
                'sn' => order_sn('W'),
                'user_id' => $id,
                'real_name' => $user['realname'],
                'account' => $user['mobile'],
                'type' => 4,
                'user_type' => 2,
                'money' => $post['money'],
                'left_money' => $left_money,
                'poundage' => $poundage,
                'status' => 1,
                'create_time' => time(),
            ]
        );
        if ($res) {
            Db::name('technician')->where('id', $id)->dec('balance', $post['money'])->update();
            $user_log = [
                'user_id' => $id,
                'type' => 7,
                'money' => $post['money'],
                'income' => 2,
                'create_time' => time(),
                'payment' => 1,
                'user_type' => 2,
                'order_sn' => $order_sn,
            ];
            Db::name('user_log')->insert($user_log);
            return msg(1, '提现申请成功');
        } else {
            return msg(0, '网络错误');
        }

    }

    public function withdrawList()
    {
        $id = getTechnicianId();
        $month = input('month/s');
        if (empty($month)) {
            $start = strtotime(date('Y-m-d'));
        } else {
            $start = strtotime($month);
        }
        $end = strtotime('+1 month', $start);
        $where[] = ['create_time', '>', $start];
        $where[] = ['create_time', '<', $end];

        $page = input('page/i', 1);
        $limit = 10;
        $where[] = ['user_id', '=', $id];
        $where[] = ['user_type', '=', 2];
        $list = Db::name('withdraw_apply')->alias('a')
            ->where($where)->order('id', 'desc')->field('sn,money,status,description,create_time')
            ->limit(($page - 1) * $limit, $limit)->select()->toArray();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
        }
        $count = Db::name('withdraw_apply')->where($where)->count();
        $total = $count == 0 ? 0 : intval($count / 10) + 1;
        return msg(1, '', array('list' => $list, 'total' => $total, 'page' => $page), $count);
    }

    public function cs()
    {
        $sr = '0 23 * * *';
        $t = date('Y-m-d H:i');
        $srlist = explode(' ', $sr);
        switch ($srlist[0]) {
            case '*';
                break;

        }

        $post = $this->request->post();
//        $post['nickname']=urlencode($post['nickname']);
        Db::name('technician')->where('id', 12)->update($post);
        var_dump($post);
        exit();
        exit();

        //Cache::set('bdt_access_token','');
        $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
        $access_token = getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token;
        $data = [
            'touser' => 'oaYpe49G_7TJ9oFi0JBhMq8jPtRY',
            'mp_template_msg' => [
                'appid' => 'wxdb5f2028eae7f644',
                'template_id' => 'pE0sWTKZhHHQU78yzkj2-nbu5BvJoOaScFqEFsWaR9A',
                'url' => '',
                'miniprogram' => [
                    'appid' => $config['wx_app_id'],
                    'path' => 'page/page/index',
                ],
                'data' => [
                    'first' => [
                        'value' => '测试',
                        'color' => '#173177',
                    ],
                    'keyword1' => [
                        'value' => '100',
                        'color' => '#173177',
                    ],
                    'keyword2' => [
                        'value' => '支付宝提现',
                        'color' => '#173177',
                    ],
                    'keyword3' => [
                        'value' => '2022-02-03',
                        'color' => '#173177',
                    ],
                    'keyword4' => [
                        'value' => '通过',
                        'color' => '#173177',
                    ],
                    'keyword5' => [
                        'value' => '支付宝提现',
                        'color' => '#173177',
                    ],
                    'remark' => [
                        'value' => date('Y-m-d H-i'),
                        'color' => '#173177',
                    ],
                ],
            ],
//            'template_id'=>'Ytqj3Z9_0VWgjbdReTqSD7b0sBnYcsdQXjAdkN1_vNs',
            //            'page'=>'index',
            //            'miniprogram_state'=>'trial',
            //            'lang'=>'zh_CN',
            //            'data'=>[
            //                'amount1'=>[
            //                    'value'=>1
            //                ],
            //                'thing2'=>[
            //                    'value'=>'审核通过'
            //                ],
            //                'time3'=>[
            //                    'value'=>date('Y-m-d H:i',time())
            //                ]
            //
            //            ]
        ];
        $res = json_decode(httpPost($url, $data), true);
        return msg(1, '', $res);
//
        //        $res=sendOrderMsg(2,100,'新订单','sjkgjksjdgk');
        //        //$res=sendAuthenticationMsg(2,100,'新订单','sjkgjksjdgk');
        //        return msg(1,'',$res);

//       $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxdb5f2028eae7f644&secret=15a11ef8595c71ad49afaecdda547a23';
        //        $res=json_decode(httpGet($url),true);
        //$access_token=$res['access_token'];
        //        $access_token='54_juuAQVs08VRw2G1BMMR5GPP6eE4f0QZ7skAphka0saLwyui2YU8N8zmEeLr-CKuXt6X1NqoGIf3X4QGGOE2U6LiTeldKNZBclU_0GirFL0bYc3lgiikdQ41WkYbcqs5JDAMlRFzM7CQUGk8aKAEcAIABRV';
        //        $user_url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&unionid=oa8Mw5zS7Hx65d-zrHPOT3u4i0ZM&lang=zh_CN';
        //        $res=json_decode(httpGet($user_url),true);
        //        return msg(1,'',$res);

    }
    public function uploadAudio()
    {
        // $file_link = input('file');
        // $file_type = substr($file_link, -3);
        // if ($file_type != 'mp3' && $file_type != 'aac') {
        //     return msg(0, "文件格式不正确");
        // }
        // $config = json_decode(Db::name('base_config')->where('name', '=', 'oss')->value('data'), true);
        //        $config=[];
        // if (empty($config) || $config['status'] == 0) {

        $file = request()->file('file');
        // $file = file_get_contents($file_link);
        // if (isset($file) && strlen($file) > 0) {
        //     $name = date('YmdHis') . strval(mt_rand(1000, 9999)) . '.aac';
        //     $filename = root_path('public\uploads\topic') . $name;
        //     $handle = fopen($filename, 'w+');
        //     fwrite($handle, $file);
        //     fclose($handle);
        //     if (is_file($filename)) {
        //         return msg(1, '上传成功', $this->request->domain() . '/uploads/topic/' . $name);
        //     } else {
        //         return msg(0, '上传失败');
        //     }
        // } else {
        //     return msg(0, '文件为空');
        // }
        try {
            validate(['audio' => 'fileSize:10240|fileExt:mp3,wav,mip,cda'])
                ->check(['file' => $file]);
            $savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);
            return msg(1, '上传成功', $this->request->domain() . '/uploads/' . $savename);

        } catch (\think\exception\ValidateException $e) {
            echo $e->getMessage();
            return msg(0, '上传失败：' . $e->getMessage());
        }
        // } else {

        //     $file = $_FILES['file'];
        //     // 获取文件后缀名 更新成功
        //     $extension = 'aac';
        //     // 阿里云账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM用户进行API访问或日常运维，请登录RAM控制台创建RAM用户。
        //     $accessKeyId = $config['accessKeyId'];
        //     $accessKeySecret = $config['accessKeySecret'];
        //     // yourEndpoint填写Bucket所在地域对应的Endpoint。以华东1（杭州）为例，Endpoint填写为https://oss-cn-hangzhou.aliyuncs.com。
        //     $endpoint = $config['endpoint'];
        //     // 填写Bucket名称，例如examplebucket。
        //     $bucket = $config['bucket'];
        //     // 填写Object完整路径，例如exampledir/exampleobject.txt。Object完整路径中不能包含Bucket名称。
        //     $object = date('YmdHis') . strval(mt_rand(1000, 9999)) . '.' . $extension;
        //     // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        //     // 填写本地文件的完整路径，例如D:\\localpath\\examplefile.txt。如果未指定本地路径，则默认从示例程序所属项目对应本地路径中上传文件。
        //     $filePath = $file['tmp_name'];
        //     try {
        //         $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        //         $result = $ossClient->uploadFile($bucket, $object, $filePath);
        //         return msg(1, "上传成功", $result['info']['url']);
        //     } catch (OssException $e) {
        //         return msg(1, "上传失败:" . $e->getMessage());
        //     }
        // }
    }
    public function uploadImage()
    {
//         $config = json_decode(Db::name('base_config')->where('name', '=', 'oss')->value('data'), true);
// //        var_dump($config['status']);
//         //        exit();
//         if (empty($config) || $config['status'] == 0) {

            $file = request()->file('file');

            try {
                validate(['image' => 'fileSize:10240|fileExt:jpg,png,jpeg,gif,bmp|image:200,200'])
                    ->check(['file' => $file]);
                $savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);
                return msg(1, '上传成功', $this->request->domain() . '/public/uploads/' . str_replace('\\', '/', $savename));

            } catch (\think\exception\ValidateException $e) {
                echo $e->getMessage();
                return msg(0, '上传失败：' . $e->getMessage());
            }
        // } else {
        //     $file = $_FILES['file'];

        //     $allowext = 'gif,GIF,jpg,JPG,jpeg,JPEG,png,PNG,bmp,BMP';
        //     if (strpos($file['type'], 'image') === false) {
        //         return msg(0, "文件格式不正确");
        //     }
        //     $temp = explode("/", $file['type']); //获取文件名按“.”进行分割
        //     // 获取文件后缀名 更新成功
        //     $extension = end($temp);

        //     if (strpos($allowext, $extension) === false) {
        //         return msg(0, "图片格式不正确");
        //     }
        //     // 阿里云账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM用户进行API访问或日常运维，请登录RAM控制台创建RAM用户。
        //     $accessKeyId = $config['accessKeyId'];
        //     $accessKeySecret = $config['accessKeySecret'];
        //     // yourEndpoint填写Bucket所在地域对应的Endpoint。以华东1（杭州）为例，Endpoint填写为https://oss-cn-hangzhou.aliyuncs.com。
        //     $endpoint = $config['endpoint'];
        //     // 填写Bucket名称，例如examplebucket。
        //     $bucket = $config['bucket'];
        //     // 填写Object完整路径，例如exampledir/exampleobject.txt。Object完整路径中不能包含Bucket名称。
        //     $object = date('YmdHis') . strval(mt_rand(1000, 9999)) . '.' . $extension;
        //     // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        //     // 填写本地文件的完整路径，例如D:\\localpath\\examplefile.txt。如果未指定本地路径，则默认从示例程序所属项目对应本地路径中上传文件。
        //     $filePath = $file['tmp_name'];
        //     try {
        //         $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        //         $result = $ossClient->uploadFile($bucket, $object, $filePath);
        //         return msg(1, "上传成功", $result['info']['url']);
        //     } catch (OssException $e) {
        //         return msg(1, "上传失败:" . $e->getMessage());
        //     }
        // }
    }
    public function getTmplIds()
    {
        return msg(1, '', ['Ytqj3Z9_0VWgjbdReTqSD7b0sBnYcsdQXjAdkN1_vNs', 'SExtEXHmZi0U2q8AJ1tTsh3IlQa0pYWsvA6xRt3Is90', 'RHNVChxDNkBlMcUCd4SbymeIJU6Ckd7aUqn7ICrP7DU']);
    }

    private function downloadImage($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $this->saveAsImage($url, $file);
    }

    private function saveAsImage($url, $file)
    {
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $dirname = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_DIRNAME);
        $path = 'public' . $dirname . '/';
        $fullpath = 'public' . $dirname . '/' . $filename;
        // 如果目录不存在，则创建
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (file_exists($fullpath)) {
            //$this->output->writeln("【已存在】输出路径" . $fullpath);
        } else {
            $resource = fopen($fullpath, 'a');
            fwrite($resource, $file);
            fclose($resource);
            //$this->output->writeln("【已保存】输出路径" . $fullpath);
        }
    }
}
