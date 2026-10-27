<?php
/**
 * 短信验证码发送类
 * @author Administrator
 *
 */
namespace sms;

use DOMDocument;

class Sms2
{
//Luosimao api key
    private $key = '';

    private $_last_error = array();

    private $url = 'http://101.201.41.194:9999/v2sms.aspx';
    private $userid = '';
    private $username = '';
    private $pwd = '';

    //触发，单发，适用于验证码，订单触发提醒类
    public function send($mobile, $message = '')
    {
        $datetime = date('Ymdhis');
        $param = array(
            'action' => 'send',
            'userid' => $this->userid,
            'timestamp' => $datetime,
            'sign' => md5($this->username . $this->pwd . $datetime),
            'mobile' => $mobile,
            'content' => $message,
            'sendTime' => '',
            'extno' => '',
        );
        $xml = $this->http_post($this->url, $param);
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $returnsms = $doc->getElementsByTagName('returnsms');
        $res = array();
        foreach ($returnsms as $dom) {
            $res['returnstatus'] = $dom->getElementsByTagName('returnstatus')->item(0)->nodeValue;
            $res['message'] = $dom->getElementsByTagName('message')->item(0)->nodeValue;
        }
        return $res;
    }

    /**
     * @param string $api_url 接口地址
     * @param array $param post参数
     * @param int $timeout 超时时间
     * @return bool
     */
    private function http_post($api_url = '', $param = array(), $timeout = 5)
    {

        if (!$api_url) {
            die("error api_url");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if (parse_url($api_url)['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            $this->_last_error[] = $error;
            return false;
        }
        return $res;
    }
    /**
     * @param string $type 接收类型，用于在服务器端接收上行和发送状态，接收地址需要在luosimao后台设置
     * @param array $param 传入的参数，从推送的url中获取，官方文档：https://luosimao.com/docs/api/
     */
    public function recv($type = 'status', $param = array())
    {
        if ($type == 'status') {
            if ($param['batch_id'] && $param['mobile'] && $param['status']) { //状态
                // do record
            }
        } elseif ($type == 'incoming') { //上行回复
            if ($param['mobile'] && $param['message']) {
                // do record
            }
        }
    }

}
