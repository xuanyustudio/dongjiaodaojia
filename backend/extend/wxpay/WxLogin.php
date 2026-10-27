<?php

namespace wxpay;
use think\facade\Request;
use think\facade\Db;

class WxLogin
{
    public function WxLogin($code)
    {
        $config=json_decode(Db::name('base_config')->where('name','wx_config')->value('data'),true);
        // $appid = $config['wx_app_id'];
        // $app_secret = $config['wx_app_secret'];
        // $token = json_decode($this->getCurl("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$app_secret}&js_code={$code}&grant_type=authorization_code"), true); //调用微信api接口

        $data['appid']=$config['wx_app_id'];
        $data['secret']=$config['wx_app_secret'];
        $data['code']=$code;
        $data['grant_type']='authorization_code';
        // $xml = $this->ToXml($data);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$data['appid'].'&secret='.$data['secret'].'&code='.$data['code'].'&grant_type=authorization_code'; // 调用接口

        $jsonwx=json_decode($this->getCurl($url),true);

        $openid = $jsonwx['openid']; //openid 微信用户的唯一标识对每个公众平台都不一样

        return $openid;
    }

    //发请求方法
    public function getCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        return $result;
    }
    function wtw_request($url, $data = null)
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

    function curl($param = "", $url)
    {
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost); // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, root_path() . 'public/uploads/wx/apiclient_cert.pem'); // 这个是证书cert的位置绝对路径
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, root_path() . 'public/uploads/wx/apiclient_key.pem'); // 这个是证书key的位置绝对路径
        $data = curl_exec($ch); // 运行curl
        curl_close($ch);
        return $data;
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
