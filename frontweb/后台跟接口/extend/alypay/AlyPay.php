<?php


namespace alypay;

require_once __DIR__.'/aop/AopClient.php';
require_once  __DIR__.'/aop/request/AlipayTradePayRequest.php';
require_once  __DIR__.'/aop/request/AlipayTradeAppPayRequest.php';
require_once  __DIR__.'/aop/request/AlipayTradeRefundRequest.php';

use think\facade\Db;
use think\facade\Request;
class AlyPay
{



    public function pay($post){
        $config=json_decode(Db::name('base_config')->where('name','ali_config')->value('data'),true);

        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['rsaPrivateKey'];
        $aop->alipayrsaPublicKey=$config['aliPublicKey'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='GBK';
        $aop->format='json';
        $object = new \stdClass();
        $object->out_trade_no = $post['order_sn'];
        $object->total_amount = $post['money'];
        $object->subject = '生活服务';
        $object->product_code = 'QUICK_MSECURITY_PAY';
        $object->time_expire = date('Y-m-d H:i"s',strtotime('+1 day',time()));
        $object->passback_params=urlencode($post['form']);
////商品信息明细，按需传入
// $goodsDetail = [
//     [
//         'goods_id'=>'goodsNo1',
//         'goods_name'=>'子商品1',
//         'quantity'=>1,
//         'price'=>0.01,
//     ],
// ];
// $object->goodsDetail = $goodsDetail;
// //扩展信息，按需传入
// $extendParams = [
//     'sys_service_provider_id'=>'2088511833207846',
// ];
//  $object->extend_params = $extendParams;
        $json = json_encode($object);
        $request = new \AlipayTradeAppPayRequest();
        $request->setNotifyUrl(\request()->domain().url('pay/aliNotify'));
        $request->setBizContent($json);

        $result = $aop->sdkExecute ( $request);

        return ['code'=>1,'data'=>$result];

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;

        if(!empty($resultCode)&&$resultCode == 10000){
            return ['code'=>1,'data'=>$responseNode];
        } else {
            return ['code'=>0,'data'=>$responseNode];
        }
    }
    public function transfer($name,$account,$money){
        $aop = new \AopClient ();
        $config=json_decode(Db::name('base_config')->where('name','ali_config')->value('data'),true);

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['rsaPrivateKey'];
        $aop->alipayrsaPublicKey=$config['aliPublicKey'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='GBK';
        $aop->format='json';
        $request = new \AlipayFundTransUniTransferRequest ();
        $object = new \stdClass();
        $object->out_biz_no=time()+3600;
        $object->trans_amount=$money;
        $object->product_code="TRANS_ACCOUNT_NO_PWD";
        $object->biz_scene="TRANS_ACCOUNT_NO_PWD";
        $object->product_code="DIRECT_TRANSFER";
        $object->order_title="提现";
        $payee_info=[
            'identity'=>$account,
            'identity_type'=>"ALIPAY_LOGON_ID",
            'name'=>$name
        ];
        $object->payee_info=$payee_info;
        $business_params=[
            'sub_biz_scene'=>'REDPACKET',
        ];
        $object->business_params=$business_params;
        $request->setBizContent(json_encode($object));
        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        // if(!empty($resultCode)&&$resultCode == 10000){
        //     echo "成功";
        // } else {
        //     echo "失败";
        // }
        if(!empty($resultCode)&&$resultCode == 10000){
            return ['code'=>1,'data'=>$responseNode];
        } else {
            return ['code'=>0,'data'=>$responseNode];
        }
    }

    public function Refund($out_trade_no,$total_fee,$refund_fee)
    {
        $aop = new \AopClient ();
        $config=json_decode(Db::name('base_config')->where('name','ali_config')->value('data'),true);

        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['rsaPrivateKey'];
        $aop->alipayrsaPublicKey=$config['aliPublicKey'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $object = new \stdClass();
        $object->out_trade_no = $out_trade_no;//退款商户订单号
        $object->refund_amount = $refund_fee;//退款金额
//// 返回参数选项，按需传入
//$queryOptions =[
//   'refund_detail_item_list'
//];
//$object->query_options = $queryOptions;
        $json = json_encode($object);
        $request = new \AlipayTradeRefundRequest();
        $request->setBizContent($json);

        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        // if(!empty($resultCode)&&$resultCode == 10000){
        //     echo "成功";
        // } else {
        //     echo "失败";
        // }
        if(!empty($resultCode)&&$resultCode == 10000){
            return ['code'=>1,'data'=>$responseNode];
        } else {
            return ['code'=>0,'data'=>$responseNode];
        }
    }
    public function notify(){
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = '请填写支付宝公钥，一行字符串';
        $flag = $aop->rsaCheckV2($_POST, NULL, "RSA2");
        return $flag;
    }

}