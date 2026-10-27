<?php
/**
 * 阿里云短信验证码发送类
 * @author Administrator
 *
 */
namespace sms;
class Sms {

    // 保存错误信息

    public $error;

    // Access Key ID

    private $accessKeyId = '';

    // Access Access Key Secret

    private $accessKeySecret = '';

    // 签名

    private $signName = '';

    // 模版ID

    private $templateCode = '';

    public function __construct($config) {

        // 配置参数

        $this->accessKeyId = $config ['accessKeyId'];

        $this->accessKeySecret = $config ['accessKeySecret'];

        $this->signName = $config ['signName'];

        $this->templateCode = $config ['templateCode'];

    }

    private function percentEncode($string) {

        $string = urlencode ( $string );

        $string = preg_replace ( '/\+/', '%20', $string );

        $string = preg_replace ( '/\*/', '%2A', $string );

        $string = preg_replace ( '/%7E/', '~', $string );

        return $string;

    }

    /**
     * 签名
     *
     * @param unknown $parameters            
     * @param unknown $accessKeySecret            
     * @return string
     */

    private function computeSignature($parameters, $accessKeySecret) {

        ksort ( $parameters );

        $canonicalizedQueryString = '';

        foreach ( $parameters as $key => $value ) {

            $canonicalizedQueryString .= '&' . $this->percentEncode ( $key ) . '=' . $this->percentEncode ( $value );

        }

        $stringToSign = 'GET&%2F&' . $this->percentencode ( substr ( $canonicalizedQueryString, 1 ) );

        $signature = base64_encode ( hash_hmac ( 'sha1', $stringToSign, $accessKeySecret . '&', true ) );

        return $signature;

    }

    /**
     * @param unknown $mobile            
     * @param unknown $verify_code            
     *
     */

    public function send_verify($mobile, $verify_code) {

        $params = array (   //此处作了修改

                'SignName' => $this->signName,

                'Format' => 'JSON',

                'Version' => '2017-05-25',
            
                'RegionId'=> 'cn-hangzhou',

                'AccessKeyId' => $this->accessKeyId,

                'SignatureVersion' => '1.0',

                'SignatureMethod' => 'HMAC-SHA1',

                'SignatureNonce' => uniqid (),

                'Timestamp' => gmdate ( 'Y-m-d\TH:i:s\Z' ),

                'Action' => 'SendSms',

                'TemplateCode' => $this->templateCode,

                'PhoneNumbers' => $mobile,

                'TemplateParam' => '{"code":"' . $verify_code . '"}'
        );

        

        // 计算签名并把签名结果加入请求参数

        $params ['Signature'] = $this->computeSignature ( $params, $this->accessKeySecret );
        
        
        //var_dump($params);die;

        // 发送请求（此处作了修改）

        //$url = 'https://sms.aliyuncs.com/?' . http_build_query ( $params );

        $url = 'http://dysmsapi.aliyuncs.com/?' . http_build_query ( $params );



        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_URL, $url );

        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );

        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );

        $result = curl_exec ( $ch );

        curl_close ( $ch );

        $result = json_decode ( $result, true );

        return  $result;

        if (isset ( $result ['Code'] )) {

            $this->error = $this->getErrorMessage ( $result ['Code'] );

            return false;

        }

        return true;

    }

    /**
     * 获取详细错误信息
     *
     * @param unknown $status            
     */

    public function getErrorMessage($status) {

        // 阿里云的短信 乱八七糟的(其实是用的阿里大于)

        // https://api.alidayu.com/doc2/apiDetail?spm=a3142.7629140.1.19.SmdYoA&apiId=25450

        $message = array (

                'InvalidDayuStatus.Malformed' => '账户短信开通状态不正确',

                'InvalidSignName.Malformed' => '短信签名不正确或签名状态不正确',

                'InvalidTemplateCode.MalFormed' => '短信模板Code不正确或者模板状态不正确',

                'InvalidRecNum.Malformed' => '目标手机号不正确，单次发送数量不能超过100',

                'InvalidParamString.MalFormed' => '短信模板中变量不是json格式',

                'InvalidParamStringTemplate.Malformed' => '短信模板中变量与模板内容不匹配',

                'InvalidSendSms' => '触发业务流控',

                'InvalidDayu.Malformed' => '变量不能是url，可以将变量固化在模板中'

        );

        if (isset ( $message [$status] )) {

            return $message [$status];

        }

        return $status;

    }
    public function setSmsParams($scene_config, $params)
    {

        //腾讯云特殊处理
        $arr = [];
        $content = $scene_config['sms_notice']['content'];
        foreach ($params['params'] as $item => $val) {
            $search = '{' . $item . '}';
            if(strpos($content, $search) !== false
                && !in_array($item, $arr)
            ) {
                //arr => 获的数组[nickname, order_sn] //顺序可能是乱的
                $arr[] = $item;
            }
        }

        //arr2 => 获得数组[nickname, order_sn] //调整好顺序的变量名数组
        $arr2 = [];
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $key = strpos($content, $v);
                $arr2[$key] = $v;
            }
        }

        //格式化 arr2 => 以小到大的排序的数组
        ksort($arr2);
        $arr3 = array_values($arr2);

        //arr4 => 获取到变量数组的对应的值 [mofung, 123456789]
        $arr4 = [];
        foreach ($arr3 as $v2) {
            if(isset($params['params'][$v2])) {
                $arr4[] = $params['params'][$v2];
            }
        }
        return $arr4;
    }

}