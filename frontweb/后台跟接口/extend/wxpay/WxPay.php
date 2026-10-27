<?php

namespace wxpay;

use think\facade\Db;
use think\facade\Request;

class WxPay
{
    public function wxappPay($post)
    {
        $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
        $openid = 'otZVP5Ptqw7YsL3avS0fEQKeFeQM';
        $price = input('price');
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

        $nonce_str = $this->getNonceStr(32);
        $send_data = array(
            'appid' => $post['wxapp_app_id'],
            'mch_id' => $post['mch_id'],
            'nonce_str' => $nonce_str,
            'body' => "支付",
            'out_trade_no' => $post['order_sn'],
            'total_fee' => $post['money'] * 100,
            'notify_url' => url('index/index'),
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'trade_type' => 'JSAPI',
            'attach' => '支付',
            'openid' => $post['openid'],
        );

        $sign = $this->MakeSign($send_data, $config['mch_secret']);
        $send_data['sign'] = $sign;
        $result = $this->FromXml($this->wtw_request($url, $this->ToXml($send_data)));
        if ($result['return_code'] == 'SUCCESS') {
            $ret = $this->sign_pay($result, $config['wxapp_app_id'], $config['mch_secret']);
            return msg($ret, 1, "支付参数");
        } else {
            return msg($result, 0, '');

        }
    }

    public function appPay($post)
    {
        $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $nonce_str = $this->getNonceStr(32);
        $appid = $config['app_id'];

        $send_data = array(
            'appid' => $appid,
            'mch_id' => $config['mch_id'],
            'nonce_str' => $nonce_str,
            'body' => "享享到家",
            'out_trade_no' => $post['order_sn'],
            'total_fee' => $post['money'] * 100,
            'notify_url' => \request()->domain() . url('pay/wxNotify'),
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'trade_type' => "APP",
            'attach' => $post['form'],
        );
        //($post['brow']==1?'MWEB':'APP')
        if ($post['brow'] == 1) {
            $send_data['appid'] = $config['wx_app_id'];
            $send_data['openid'] = $post['openid'];
            $send_data['detail'] = '享享到家上门服务';
            $send_data['body'] = '享享到家上门服务';
            $send_data['trade_type'] = 'JSAPI';
        }

        $sign = $this->MakeSign($send_data, $config['mch_secret']);
        $send_data['sign'] = $sign;
        $result = $this->FromXml($this->wtw_request($url, $this->ToXml($send_data)));
        if ($result['return_code'] == 'SUCCESS') {
            $ret = $this->app_sign_pay($post['brow'], $result, $appid, $config['mch_id'], $config['mch_secret'],$config['wx_app_id']);
            return ['code' => 1, 'data' => $ret];
        } else {
            return ['code' => 0, 'data' => $result];

        }
    }
    //转账
    public function transfer($money, $openid)
    {
        $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
        $total_amount = 100 * $money;
        $data = array(
            'mch_appid' => $config['app_id'], // 商户账号appid
            'mchid' => $config['mch_id'], // 商户号
            'nonce_str' => $this->getNonceStr(), // 随机字符串
            'partner_trade_no' => date('YmdHis') . rand(1000, 9999), // 商户订单号
            'openid' => $openid, // 用户openid
            'check_name' => 'NO_CHECK', // 校验用户姓名选项,
            'amount' => $total_amount, // 金额
            'desc' => "测试", // 企业付款描述信息
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
        ); // Ip地址

        $data = array_filter($data);
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $str .= 'key=' . $config['mch_secret'];
        $data['sign'] = md5($str);
        $xml = $this->ToXml($data);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; // 调用接口
        $res = $this->curl($xml, $url);
        $responseObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;
    }
    public function Refund($out_trade_no, $total_fee, $refund_fee)
    {
        $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
        $send_data = array(
            'appid' => $config['app_id'],
            'mch_id' => $config['mch_id'],
            'nonce_str' => time() + 3600,
            'out_trade_no' => $out_trade_no,
            'out_refund_no' => time() - 177878,
            'total_fee' => $total_fee * 100,
            'refund_fee' => $refund_fee * 100,
        );
        $sign = $this->MakeSign($send_data, $config['mch_secret']);
        $send_data['sign'] = $sign;
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $result = $this->FromXml($this->curl($this->ToXml($send_data), $url));
        return $result;

    }

    public function notify($postStr)
    {
        //允许从外部加载XML实体(防止XML注入攻击)

//        $postStr='<xml>
        //  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
        //  <attach><![CDATA[支付测试]]></attach>
        //  <bank_type><![CDATA[CFT]]></bank_type>
        //  <fee_type><![CDATA[CNY]]></fee_type>
        //  <is_subscribe><![CDATA[Y]]></is_subscribe>
        //  <mch_id><![CDATA[10000100]]></mch_id>
        //  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
        //  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
        //  <out_trade_no><![CDATA[1409811653]]></out_trade_no>
        //  <result_code><![CDATA[SUCCESS]]></result_code>
        //  <return_code><![CDATA[SUCCESS]]></return_code>
        //  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
        //  <time_end><![CDATA[20140903131540]]></time_end>
        //  <total_fee>1</total_fee>
        //  <coupon_fee><![CDATA[10]]></coupon_fee>
        //  <coupon_count><![CDATA[1]]></coupon_count>
        //  <coupon_type><![CDATA[CASH]]></coupon_type>
        //  <coupon_id><![CDATA[10000]]></coupon_id>
        //  <trade_type><![CDATA[JSAPI]]></trade_type>
        //  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
        //</xml>';

        $arr = json_decode(json_encode(simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        ksort($arr); // 对数据进行排序
        $str = $this->ToUrlParams($arr); //对数据拼接成字符串
        $user_sign = strtoupper(md5($str));
        if ($user_sign == $arr['sign']) { //验证签名
            return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        } else {
            return '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }
    }
    public function app_sign_pay($brow, $arr, $appid, $mch_id, $pay_key,$wxappid)
    {
        $time = (int) time();
        if ($brow == 1) {
            $array = array(
                'appId' => $wxappid,
                'nonceStr' => $arr['nonce_str'],
                'package' => 'prepay_id='.$arr['prepay_id'],
                'timeStamp' => (string) $time,
                'signType' => 'MD5',
            );
        } else {
            $array = array(
                'appid' => $appid,
                'partnerid' => $mch_id,
                'noncestr' => $arr['nonce_str'],
                'prepayid' => $arr['prepay_id'],
                'package' => 'Sign=WXPay',
                'timestamp' => (string) $time,
            );
        }
        $sign_str = $this->MakeSign($array, $pay_key);
        $array['sign'] = $sign_str;
        return $array;
    }

    /**
     *
     * 产生随机字符串，不长于32位
     *
     * @param int $length
     * @return 产生的随机字符串
     */
    public function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public function MakeSign($array, $key)
    {
        // 签名步骤一：按字典序排序参数
        ksort($array);

        $string = $this->ToUrlParams($array);
        // 签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        // 签名步骤三：MD5加密
        $string = md5($string);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    public function ToUrlParams($arr)
    {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
    public function FromXml($xml)
    {
        // 将XML转为array
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }
    public function ToXml($array)
    {

        $xml = "<xml>";
        foreach ($array as $key => $val) {
            if ($key != 'body') {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    public function sign_pay($arr, $appid, $pay_key)
    {
        $package = "prepay_id=" . $arr['prepay_id'];
        $signType = "MD5";
        $time = (int) time();
        $array = array(
            'appId' => $appid,
            'nonceStr' => $arr['nonce_str'],
            'package' => $package,
            'signType' => $signType,
            'timeStamp' => (string) $time,
        );
        $sign_str = $this->MakeSign($array, $pay_key);

        $array['paySign'] = $sign_str;
        return $array;
    }

    public function wtw_request($url, $data = null)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        if ($data != null) {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 300); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $info = curl_exec($curl); // 执行操作
        curl_close($curl);
        return $info;
    }

    public function curl($param = "", $url)
    {
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost); // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, root_path() . 'public/uploads/wx/apiclient_cert.pem'); // 这个是证书cert的位置绝对路径
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, root_path() . 'public/uploads/wx/apiclient_key.pem'); // 这个是证书key的位置绝对路径
        $data = curl_exec($ch); // 运行curl
        curl_close($ch);
        return $data;
    }

}
