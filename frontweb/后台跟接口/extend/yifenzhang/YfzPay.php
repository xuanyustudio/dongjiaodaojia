<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/7/22
 * Time: 9:49
 */

namespace yifenzhang;


class YfzPay
{

    /**
     * 构造方法
     * @access public
     */
    public function __construct()
    {
//        $this->base     = 'http://test-efps.epaylinks.cn';//测试环境
        $this->base     = 'https://efps.epaylinks.cn';//正式环境
        $this->sign_no = '562597003874410001'; //序列号
        $this->customer_code = '562597003874410';
        $this->app_id = 'wx76e473d7e692eab2';
        $this->user_rsa =  $_SERVER["DOCUMENT_ROOT"].'/extend/yifenzhang/user-rsa.pfx';
        $this->user_rsa =  $_SERVER["DOCUMENT_ROOT"].'/extend/yifenzhang/user-rsa.pfx';
        $this->public_rsa =  $_SERVER["DOCUMENT_ROOT"].'/extend/yifenzhang/public-rsa.cer';
//        $this->user_rsa =  $_SERVER["DOCUMENT_ROOT"].'/extend/yifenzhang/private-rsa.key';
    }
    /*
       *统一下单
       * */
    public function UnifiedPayment($order)
    {
        $orderInfo = [
            'Id' => $order['order_sn'],
            'businessType' => '100007',
            'goodsList' => [
                [
                    'name'=>$order['goodsList']['name'],
                    'number'=>'1',
                    'amount'=>intval($order['goodsList']['price'] * 100),
                ]
            ],
        ];
        
        $url = $this->base.'/api/txs/pay/UnifiedPayment';

        if ($order['form']=='order'){
            $notifyUrl = \request()->domain().'/public/index.php/api/pay/wxNotify';
        }elseif ($order['form']=='recharge'){
            $notifyUrl = \request()->domain().'/public/index.php/api/pay/wxNotify2';
        }

        $timestamp = date('YmdHis',time());
        $NonceStr = $this->getNonceStr();
        $params = [
            'outTradeNo' => $order['order_sn'],
            'customerCode' => $this->customer_code,
            'orderInfo' => $orderInfo,
//            'payMethod' => '7',
            'payAmount' => intval($order['pay_money']*100),
            'payCurrency' => 'CNY',
            'notifyUrl' => $notifyUrl,
            'transactionStartTime' => $timestamp,
            'nonceStr' => $NonceStr,
            'areaInfo' => '230404',
        ];
  
        //请求参数转换
        $sign_params = json_encode($params);
//        dump($sign_params);
        //生成签名
        $sign = $this->getSign3($sign_params,$this->user_rsa);
//        halt($sign);
//        dump($sign);

        $header = [
            "x-efps-sign-no:$this->sign_no",
            "x-efps-sign-type:SHA256withRSA",
            "x-efps-sign:$sign",
            "x-efps-timestamp:$timestamp",
            "content-type: application/json; charset=utf-8"
        ];
        $result = httpPost($url,$params,$header);
        return json_decode($result,true);
    }
    /*
     *微信公众号/小程序支付——收银台发起
     * */
    public function wechat($post)
    {
        $params = [
            'outTradeNo' => $post['order_sn'],
            'customerCode' => $this->customer_code,
            'appId' => $this->app_id,
            'openId' => $post['open_id'],
            'orderToken' => $post['order_token'],
        ];
//        dump($params);
        $url = $this->base.'/api/txs/pay/WxJSAPICashier';

        $timestamp = date('YmdHis',time());

        //请求参数转换
        $sign_params = json_encode($params);
//        dump($sign_params);
        //生成签名
        $sign = $this->getSign3($sign_params,$this->user_rsa);

        $header = [
            "x-efps-sign-no:$this->sign_no",
            "x-efps-sign-type:SHA256withRSA",
            "x-efps-sign:$sign",
            "x-efps-timestamp:$timestamp",
            "content-type: application/json; charset=utf-8"
        ];
        $result = httpPost($url,$params,$header);
        return json_decode($result,true);
    }

    /*
     *支付宝主扫
     * */
    public function ali($order)
    {

//        halt($order);
        $orderInfo = [
            'Id' => $order['order_sn'],
            'businessType' => '100007',
            'goodsList' => [
                [
                    'name'=>$order['goodsList']['name'],
                    'number'=>'1',
                    'amount'=>intval($order['goodsList']['price'] * 100),
                ]
            ],
        ];
        $url = $this->base.'/api/txs/pay/NativePayment';
        if ($order['form']=='order'){
            $notifyUrl = \request()->domain().'/public/index.php/api/pay/aliNotify';
        }elseif ($order['form']=='recharge'){
            $notifyUrl = \request()->domain().'/public/index.php/api/pay/aliNotify2';
        }elseif ($order['form']=='member'){
            $notifyUrl = \request()->domain().'/public/index.php/api/pay/aliNotify3';
        }

        $timestamp = date('YmdHis',time());
        $NonceStr = $this->getNonceStr();
        $params = [
            'outTradeNo' => $order['order_sn'],
            'customerCode' => $this->customer_code,
            'orderInfo' => $orderInfo,
            'payMethod' => '7',
            'payAmount' => intval($order['pay_money']*100),
            // 'payAmount' => intval(2),
            'payCurrency' => 'CNY',
            'notifyUrl' => $notifyUrl,
            'transactionStartTime' => $timestamp,
            'nonceStr' => $NonceStr,
        ];
//        halt($params);
        //请求参数转换
        $sign_params = json_encode($params);
//        dump($sign_params);
        //生成签名
        $sign = $this->getSign3($sign_params,$this->user_rsa);
//        halt($sign);
//        dump($sign);
        //验证签名
//        $verify = $this->verify($sign_params,$sign);
//        dump($verify);
//        die;
        $header = [
            "x-efps-sign-no:$this->sign_no",
            "x-efps-sign-type:SHA256withRSA",
            "x-efps-sign:$sign",
            "x-efps-timestamp:$timestamp",
            "content-type: application/json; charset=utf-8"
        ];
        $result = httpPost($url,$params,$header);
        return json_decode($result,true);

    }
    //生成 sha256WithRSA 签名
    function getSign3($content,$filePath) {
//        $filePath = 'test-pfx.pfx';
        if ( !file_exists( $filePath ) ) {
            return false;
        }
        $pkcs12 = file_get_contents( $filePath );
        if ( openssl_pkcs12_read( $pkcs12, $certs, '123456' ) ) {
            $pkey = $certs['pkey'];
            $pi_key =  openssl_pkey_get_private($pkey);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id，别名函数openssl_get_privatekey
            if(!$pi_key){
                //var_dump($pi_key);
                return false;
            }
//            print_r($certs);
            //print_r($certs); 可以查看里面是啥
            //根据实际情况键值可能不同
            //最后一个参数签名算法还有其他值OPENSSL_ALGO_SHA1
            openssl_sign($content, $signature, $pkey, OPENSSL_ALGO_SHA256);
            openssl_free_key($pi_key);
            $sign = base64_encode($signature);
            return $sign;
        }
    }

    //验证 sha256WithRSA 签名
    function verify( $content, $sign ) {
        $publicKey = file_get_contents($this->public_rsa); //验证接口方传过来数据用使用这个公钥,使用这个。
        $pu_key = openssl_pkey_get_public($publicKey);//这个函数可用来判断公钥是否是可用的，可用返回资源id Resource id
        if(!$pu_key){
            //var_dump( $pu_key);
            return false;
        }
        $key = openssl_get_publickey( $publicKey ); //返回资源型Resource id #9，和上面函数openssl_pkey_get_public，效果一致
        $ok = openssl_verify( $content, base64_decode($sign) , $key, OPENSSL_ALGO_SHA256);
        openssl_free_key( $key);
        return $ok;
    }

    /**
     *
     * 产生随机字符串，不长于32位
     *
     * @param int $length
     * @return 产生的随机字符串
     */
    function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    function MakeSign($array, $key)
    {
        // 签名步骤一：按字典序排序参数
        ksort($array);

        $string =$this->ToUrlParams($array);
        // 签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        // 签名步骤三：MD5加密
        $string = md5($string);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    function ToUrlParams($arr)
    {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && ! is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    function ToXml($array)
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
}