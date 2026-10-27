<?php

namespace app\api\controller;

use alypay\AlyPay;
use app\BaseController;
use sms\Sms2;
use think\facade\Db;
use think\facade\Log;
use wxpay\WxPay;
use yifenzhang\YfzPay;

class Pay extends BaseController
{
    public function pay()
    {
        $uid = getUserId();
        $post = $this->request->param();
        $message = [
            'order_sn.require' => 'order_sn不能为空',
            'pay_way.require' => 'pay_way不能为空',
            'form.require' => 'form不能为空',
        ];
        $validate = validate([
            'order_sn' => 'require|max:250',
            'pay_way' => 'require|max:10',
            'form' => 'require|max:20',
        ], $message, false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        switch ($post['form']) {
            case 'order';
                $order = 'order';
                break;
            case 'recharge';
                $order = 'recharge_order';
                break;
            case 'member';
                $order = 'member_order';
                break;
        }
        $post['money'] = Db::name($order)->where('order_sn', $post['order_sn'])->value('total_money');
        if ($post['form'] == 'order') {
            $order = Db::name($order)->where('order_sn', $post['order_sn'])->find();
            $order['form'] = 'order';
            $order['goodsList'] = Db::name('service')->where('id', $order['service_id'])->find();
        } elseif ($post['form'] == 'recharge') {
            $order = Db::name('recharge_order')->where('order_sn', $post['order_sn'])->find();
            $order['form'] = 'recharge_order';
            $order['goodsList'] = [
                'name' => '充值',
                'price' => $order['pay_money'],
            ];
        } elseif ($post['form'] == 'member') {
            $order = Db::name('member_order')->where('order_sn', $post['order_sn'])->find();
            $order['form'] = 'member_order';
            $order['goodsList'] = [
                'name' => '会员-' . $order['member_name'],
                'price' => $order['pay_money'],
            ];
        }
        switch ($post['pay_way']) {
            case 1;
                $res = $this->balancePay($uid, $post);
                return $res;
                break;
            case 2;

                $order = Db::name($order['form'])->where('order_sn', $post['order_sn'])->find();
                $order['form'] = $post['form'];
//                 $order['goodsList'] = Db::name('service')->where('id', $order['service_id'])->find();
                // //                $alipay=new AlyPay();
                //                 //                $res=$alipay->Pay($post);
                //                 $YfzPay = new YfzPay();
                //                 $res = $YfzPay->UnifiedPayment($order);
                // //                dump($res);
                //                 if ($res['returnCode'] == '0000') {
                //                     return msg(1, '微信下单', $res);
                //                 } else {
                //                     return msg(0, '微信下单错误', $res);
                //                 }
                if ($post['brow'] == 1) {
                    $post['openid'] = Db::name('user')->where('id', $uid)->value('openid');
                }
                $wxpay = new WxPay();
                $res = $wxpay->appPay($post);

                if ($res['code'] == 1) {
                    return msg(2, '微信支付', $res['data']);
                } else {
                    return msg(1, $res['return_msg']);
                }
                break;
            case 3;

                // $order = Db::name($order)->where('order_sn',$post['order_sn'])->find();
                // $order['goodsList'] = Db::name('service')->where('id',$order['service_id'])->find();
                $alipay = new AlyPay();
                $res = $alipay->Pay($post);
                // $YfzPay = new YfzPay();
                // $res = $YfzPay->ali($order);
                //                dump($res);
                // if ($res['returnCode']=='0000'){
                //     return msg(1,'支付宝下单',$res);
                // }else{
                //     Log::info($res);
                //     return msg(0,'支付宝下单错误',$res);
                // }
                //                die;
                if ($res['code'] == 1) {
                    return msg(3, '支付宝支付', $res['data']);
                } else {
                    return msg(1, '支付宝下单错误');
                }
                break;
        }
    }
    /*
     * 微信公众号/小程序支付——收银台发起
     * */
    public function wechatpay()
    {
//        $uid=getUserId();
        $post = $this->request->param();
        $message = [
            'order_sn.require' => 'order_sn不能为空',
            'open_id.require' => 'app_id不能为空',
            'order_token.require' => 'order_token不能为空',
        ];
        $validate = validate([
            'order_sn' => 'require|max:250',
            'open_id' => 'require|max:250',
            'order_token' => 'require|max:250',
        ], $message, false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }

        $YfzPay = new YfzPay();
        $res = $YfzPay->wechat($post);
//                dump($res);
        if ($res['returnCode'] == '0000') {
            return msg(1, '微信下单', $res);
        } else {
            return msg(0, '微信下单错误', $res['returnMsg']);
        }
    }

    public function payWay()
    {
        $uid = getUserId();
        $post = $this->request->param();
        $message = [
            'order_sn.require' => 'order_sn不能为空',
            'form.require' => 'form不能为空',
        ];
        $validate = validate([
            'order_sn' => 'require|max:250',
            'form' => 'require|max:20',
        ], $message, false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $money = Db::name($post['form'] . '_order')->where('order_sn', $post['order_sn'])->value('pay_money');
        if (empty($money)) {
            return msg(0, '订单不存在');
        }
        $balance = Db::name('user')->where('id', $uid)->field('balance')->find();
        $config = Db::name('base_config')->where('name', 'in', 'ali_config,wx_config')->column('data', 'name');
        if ($post['form'] == 'recharge') {
            $pay_way_list = [
                2 => '微信支付', 3 => '支付宝',
            ];
        } else {
            $pay_way_list = [
                1 => '余额支付', 2 => '微信支付', 3 => '支付宝',
            ];
        }
        $wx_config = json_decode($config['wx_config'], true);
        if (empty($wx_config) || $wx_config['status'] == 0) {
            unset($pay_way_list[2]);
        }
        $ali_config = json_decode($config['ali_config'], true);
        if (empty($ali_config) || $ali_config['status'] == 0) {
            unset($pay_way_list[3]);
        }
        return msg(1, '', ['money' => $money, 'balance' => $balance['balance'], 'pay_way_list' => $pay_way_list]);
    }

    //退款
    public function refund($order, $refund_fee)
    {
        switch ($order['payment']) {
            case 1;
                Db::name('user')->where('id', $order['user_id'])->inc('balance', $refund_fee)->update();
                $res = ['code' => 1, 'msg' => '', 'data' => ''];
                break;
            case 2;
                $wxpay = new WxPay();
                $refund = $wxpay->Refund($order['order_sn'], $order['pay_money'], $refund_fee);

                if ($refund['return_code'] == 'SUCCESS' && $refund['result_code'] == 'SUCCESS') {
                    $res = ['code' => 1, 'msg' => '', 'data' => ''];
                } else {
                    $res = ['code' => 0, 'msg' => $refund['return_msg'], 'data' => ''];
                }
                break;
            case 3;
                $alipay = new AlyPay();
                $refund = $alipay->Refund($order['order_sn'], $order['pay_money'], $refund_fee);
                if ($refund['code'] == 1) {
                    $res = ['code' => 1, 'msg' => '', 'data' => ''];
                } else {
                    $res = ['code' => 0, 'msg' => '退款失败', 'data' => $refund['data']];
                }
                break;
        }
        if ($res['code'] == 1) {
            $user_log = [
                'user_id' => $order['user_id'],
                'type' => 2,
                'money' => $refund_fee,
                'income' => 1,
                'create_time' => time(),
                'payment' => $order['payment'],
                'user_type' => 1,
                'order_sn' => $order['order_sn'],
            ];
            Db::name('user_log')->insert($user_log);
        }

        return $res;
    }

    public function isPay()
    {
        $post = $this->request->param();
        $message = [
            'order_sn.require' => 'order_sn不能为空',
            'pay_way.require' => 'pay_way不能为空',
            'form.require' => 'form不能为空',
        ];
        $validate = validate([
            'order_sn' => 'require|max:250',
            'pay_way' => 'require|max:10',
            'form' => 'require|max:20',
        ], $message, false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        switch ($post['form']) {
            case 'order';
                $order = Db::name('order')->where('order_sn', $post['order_sn'])->where('pay_status', 1)->find();
                break;
            case 'member';
                $order = Db::name('member_order')->where('order_sn', $post['order_sn'])->where('pay_status', 1)->find();
                break;
            case 'recharge';
                $order = Db::name('recharge_order')->where('order_sn', $post['order_sn'])->where('pay_status', 1)->find();
                break;
        }
        if ($order) {
            return msg(1, '支付成功');
        } else {
            return msg(1, '支付失败');
        }
    }

    //余额支付
    protected function balancePay($uid, $post)
    {
        $user = Db::name('user')->where('id', $uid)->field('balance')->find();

//        if($user['pay_password']!=md5($post['pay_password'])){
        //            return $this->msg(0,'支付密码不对');
        //        }
        if ($user['balance'] < $post['money']) {
            return msg(0, '余额不足');
        }

        switch ($post['form']) {
            case 'order';
                return $this->order($post, 1);
                break;
            case 'member';
                return $this->memberorder($post, 1);
                break;
            case 'recharge';
                return $this->rechargeOrder($post, 1);
                break;
        }
    }

    // public function test()
    // {
    //     $config = Db::name('base_config')->where('name', 'in', 'rate,technician_rate,brokerage,tglist')->column('data', 'name');
    //     $tglist = json_decode($config['tglist'], true);
    //     $val = $tglist[1]['value'];
    //     return msg(0, count($tglist));
    // }

    //订单修改
    public function order($post, $pay_way)
    {

//        $order=Db::name('order')->alias('a')->leftJoin('user b','a.user_id=b.id')
        //            ->field('a.id,a.pay_status,a.pay_money,b.pid,a.user_id,a.technician_id')
        //            ->where('a.order_sn',$post['order_sn'])->find();
        $order = Db::name('order')->alias('a')
            ->leftJoin('service_spec b', 'a.spec_id=b.id')
            ->leftJoin('technician t', 'a.technician_id=t.id')
            ->field('a.*,b.name as spec_name,t.mobile as tech_mobile,t.tgrade')
            ->where('a.order_sn', $post['order_sn'])->find();

        if (!$order) {
            return msg(0, '该订单不存在');
        }

        if ($order['pay_status'] == 1) {
            return msg(0, '该订单已支付');
        }
        $config = Db::name('base_config')->where('name', 'in', 'rate,technician_rate,brokerage,tglist')->column('data', 'name');
        //$technician_rate = Db::name('service_spec')->where('id', $order['spec_id'])->value('technician_rate');
        if ($order['tgrade'] == '') {
            $technician_rate = 0;
        } else {
            $tglist = json_decode($config['tglist'], true);
            if ($order['tgrade'] > count($tglist)) {
                $technician_rate = 0;
            } else {
                $technician_rate = $tglist[$order['tgrade'] - 1]['value'];
            }
        }
        // $technician_money = sprintf("%.2f", ($order['pay_money'] - $order['fare']) * $technician_rate / 100);//技师分成比例
        $technician_money = sprintf("%.2f", $order['total_money'] * $technician_rate / 100);
        $brokerage_rate = json_decode($config['brokerage'], true);
        // $brokerage = ($order['pay_money'] - $order['fare']) * $brokerage_rate['first'] / 100;//佣金比例
        // $second_brokerage = ($order['pay_money'] - $order['fare']) * $brokerage_rate['second'] / 100;
        $brokerage = $order['pay_money'] * $brokerage_rate['first'] / 100;
        $second_brokerage = $order['pay_money'] * $brokerage_rate['second'] / 100;
        $order_status = 1;
        if (substr($order['order_sn'], 0, 1) == 'J') {
            $order_status = 3;
        }

        $res = Db::name('order')->where('order_sn', $post['order_sn'])
            ->update(['payment' => $pay_way, 'pay_status' => 1, 'order_status' => $order_status, 'pay_time' => time(), 'technician_money' => $technician_money, 'brokerage' => $brokerage, 'second_brokerage' => $second_brokerage]);

        if ($res) {
            $user_log = [
                'user_id' => $order['user_id'],
                'type' => 1,
                'money' => $order['pay_money'],
                'income' => 2,
                'create_time' => time(),
                'payment' => $pay_way,
                'user_type' => 1,
                'order_sn' => $post['order_sn'],
            ];
            Db::name('user_log')->insert($user_log);
            createNews(0, '您有新的订单，请尽快开始服务', 2, 1, $order['technician_id']);
            $this->SendSMS($order);
            $this->SendSMS2($order);
            // sendOrderMsg($order['technician_id'],$order['spec_name'],'新订单',$post['order_sn'],$order['appointment_time']);
            if ($pay_way == 1) {
                Db::name('user')->where('id', $order['user_id'])->dec('balance', $post['money'])->update();
            }
        }
        if ($res) {
            // $this->msgSend($order['tech_mobile']);

            return msg(1, '支付成功');
        } else {
            return msg(1, '支付失败');
        }
    }
    public function msgSend($mobile)
    {
        $host = "https://jumfixed.market.alicloudapi.com";
        $path = "/voice-notify/send";
        $method = "POST";
        $appcode = "e290fd44d3c2410e9ca24bb6e100f0df";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodys = "mobile=" . $mobile . "&param=&templateId=955765337621966848";
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

    //充值订单
    public function rechargeOrder($post, $pay_way)
    {

        $order = Db::name('recharge_order')->where('order_sn', $post['order_sn'])->find();
        if (!$order) {
            return msg(0, '该订单不存在');
        }
        if ($order['pay_status'] == 1) {
            return msg(0, '该订单已支付');
        }
        $res = Db::name('recharge_order')->where('order_sn', $post['order_sn'])->update(['payment' => $pay_way, 'pay_status' => 1, 'pay_time' => time()]);
        if ($res) {
            Db::name('user_log')->insert(
                [
                    'user_id' => $order['user_id'],
                    'type' => '4',
                    'money' => $post['money'],
                    'income' => 1,
                    'create_time' => time(),
                    'payment' => $pay_way,
                    'user_type' => 1,
                    'order_sn' => $post['order_sn'],
                ]
            );
            Db::name('user')->where('id', $order['user_id'])->inc('balance', $order['pay_money'] + $order['give'])->update();
        }
        if ($res) {
            return msg(1, '支付成功');
        } else {
            return msg(1, '支付失败');
        }
    }
    //会员订单
    public function memberOrder($post, $pay_way)
    {

        $order = Db::name('member_order')->where('order_sn', $post['order_sn'])->find();
        if (!$order) {
            return msg(0, '该订单不存在');
        }
        if ($order['pay_status'] == 1) {
            return msg(0, '该订单已支付');
        }
        $res = Db::name('member_order')->where('order_sn', $post['order_sn'])->update(['payment' => $pay_way, 'pay_status' => 1, 'pay_time' => time()]);
        if ($res) {
            Db::name('user_log')->insert(
                [
                    'user_id' => $order['user_id'],
                    'type' => 3,
                    'money' => $post['money'],
                    'income' => 2,
                    'create_time' => time(),
                    'payment' => $pay_way,
                    'user_type' => 1,
                    'order_sn' => $post['order_sn'],
                ]
            );
            $user = Db::name('user')->where('id', $order['user_id'])->field('balance,is_vip,overdue_time')->find();
            if ($pay_way == 1) {
                if ($user['is_vip'] == 0) {
                    Db::name('user')->where('id', $order['user_id'])->update(['balance' => $user['balance'] - $post['money'], 'is_vip' => 1, 'overdue_time' => strtotime('+ ' . $order['month'] . ' month', time())]);
                } else {
                    Db::name('user')->where('id', $order['user_id'])->update(['balance' => $user['balance'] - $post['money'], 'is_vip' => 1, 'overdue_time' => strtotime('+ ' . $order['month'] . ' month', $user['overdue_time'])]);
                }
            } else {
                if ($user['is_vip'] == 0) {
                    Db::name('user')->where('id', $order['user_id'])->update(['is_vip' => 1, 'overdue_time' => strtotime('+ ' . $order['month'] . ' month', time())]);
                } else {
                    Db::name('user')->where('id', $order['user_id'])->update(['is_vip' => 1, 'overdue_time' => strtotime('+ ' . $order['month'] . ' month', $user['overdue_time'])]);
                }
            }
        }
        if ($res) {
            return msg(1, '支付成功');
        } else {
            return msg(1, '支付失败');
        }
    }

    //微信回调
    public function wxNotify()
    {
        libxml_disable_entity_loader(true);
        $testxml = file_get_contents("php://input");
        // cache('wxNotify',$testxml);

        $result = json_decode(json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        Log::info('微信回调');
        Log::info($result);
        if ($result) {
            //如果成功返回了
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $post['form'] = $result['attach'];
                $post['money'] = $result['total_fee'];
                $post['order_sn'] = $result['out_trade_no'];
                switch ($result['attach']) {
                    case 'order';
                        $this->order($post, 2);
                        break;
                    case 'member';
                        $this->memberOrder($post, 2);
                        break;
                    case 'recharge';
                        $this->rechargeOrder($post, 2);
                        break;

                }
                return 'success';
            } else {
                return;
            }

        }

    }

    //微信回调
    public function wxNotify2()
    {
//        libxml_disable_entity_loader(true);
        //        $testxml = file_get_contents("php://input");
        //        $result = json_decode(json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        // cache('wxNotify',$testxml);

        $wxPost = $this->request->post();
        Log::info('微信回调-充值订单');
        Log::info($wxPost);
        if ($wxPost) {
            //如果成功返回了
            if ($wxPost['payState'] == '00') {
//                $post['form'] = $result['attach'];
                $post['money'] = $wxPost['amount'];
                $post['order_sn'] = $wxPost['out_trade_no'];
                $this->rechargeOrder($post, 2);
            } else {
                return;
            }
        }

    }

//支付宝回调
    public function aliNotify()
    {

//        require_once root_path().'extend/alypay/aop/AopClient.php';
        //        $aop = new \AopClient;
        //
        //        $config=json_decode(Db::name('base_config')->where('name','ali_config')->value('data'),true);
        //        $aop->alipayrsaPublicKey = $config['aliPublicKey'];
        //        $flag = $aop->rsaCheckV1($this->request->post(), NULL, "RSA2");
        // var_dump($flag);
        // exit();
        //cache('aliNotify',$this->request->post());

//        if (!empty($flag)) {
        $aliPost = $this->request->post();
        Log::info('支付宝回调');
        Log::info($aliPost);
        //如果成功返回了
        // if ($aliPost['payState'] == '00') {
        // $post['form'] = $aliPost['passback_params'];
        switch ($aliPost['passback_params']) {
            // case 'battery';
            //     $this->battery($post, 3);
            //     break;
            // case 'motorcycle';
            //     $this->motorcycle($post, 3);
            //     break;
            case 'recharge';
                $post['money'] = $aliPost['total_amount'];
                $post['order_sn'] = $aliPost['out_trade_no'];
                $this->rechargeOrder($post, 3);
                break;
            case 'order';
                $post['money'] = $aliPost['total_amount'];
                $post['order_sn'] = $aliPost['out_trade_no'];
                $this->order($post, 3);
                break;
                // case 'deposit';
                //     $this->depositOrder($post, 2);
                //     break;
        }
        // } else {
        //     return;
        // }

//        }

    }

    //支付宝回调-充值订单
    public function aliNotify2()
    {

        $aliPost = $this->request->post();
        Log::info('支付宝回调-充值订单');
        Log::info($aliPost);
        //如果成功返回了
        if ($aliPost['payState'] == '00') {
//                $post['form'] = $aliPost['passback_params'];
            $post['money'] = $aliPost['amount'] / 100;
            $post['order_sn'] = $aliPost['outTradeNo'];
            $this->rechargeOrder($post, 3);

        } else {
            return;
        }

    }

    //支付宝回调-会员订单
    public function aliNotify3()
    {

        $aliPost = $this->request->post();
        Log::info('支付宝回调-会员订单');
        Log::info($aliPost);
        //如果成功返回了
        if ($aliPost['payState'] == '00') {
//                $post['form'] = $aliPost['passback_params'];
            $post['money'] = $aliPost['amount'] / 100;
            $post['order_sn'] = $aliPost['outTradeNo'];
            $this->memberOrder($post, 3);

        } else {
            return;
        }

    }

    public function SendSMS($order)
    {
        $res = Db::name('technician')->where('id', $order['technician_id'])->field('mobile')->find();
        $service_spec = Db::name('service_spec')->where('id', $order['spec_id'])->field('name')->find();
        $sms = new Sms2($res['mobile']);
        $sms->send($res['mobile'], '【享享到家】您有一个新的订单，请及时处理。服务项目：' . $service_spec['name'] . '，订单号：' . $order['order_sn'] . '，客户资料如下：姓名：' . $order['realname'] . '，手机号：' . $order['mobile'] . '，地址：' . $order['province'] . $order['city'] . $order['district'] . $order['address']);
    }

    public function SendSMS2($order)
    {
        $res = Db::name('technician')->where('id', $order['technician_id'])->field('realname,mobile')->find();
        $service_spec = Db::name('service_spec')->where('id', $order['spec_id'])->field('name')->find();
        $sms = new Sms2();
        $sms->send('14705843033', '【享享到家】您好，平台有一个新的订单。服务项目：' . $service_spec['name'] . '，订单号：' . $order['order_sn'] . '用户选择技师：' . $res['realname'] . '，手机号为：' . $res['mobile'] . '。客户资料如下：姓名：' . $order['realname'] . '，手机号：' . $order['mobile'] . '，地址：' . $order['province'] . $order['city'] . $order['district'] . $order['address']);
        $sms->send('13484283888', '【享享到家】您好，平台有一个新的订单。服务项目：' . $service_spec['name'] . '，订单号：' . $order['order_sn'] . '用户选择技师：' . $res['realname'] . '，手机号为：' . $res['mobile'] . '。客户资料如下：姓名：' . $order['realname'] . '，手机号：' . $order['mobile'] . '，地址：' . $order['province'] . $order['city'] . $order['district'] . $order['address']);
    }

}
