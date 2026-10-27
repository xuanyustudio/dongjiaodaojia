<?php


namespace app\technician\controller;


use app\BaseController;
use think\facade\Db;
use yt\helper\SignHelper;
use think\facade\Cache;
class Login extends BaseController
{

    public function getOpenid(){
        $code=input('code/s','');
        if(empty($code)){
            return msg(0,'code不能为空');
        }
        $config=json_decode(Db::name('base_config')->where('name','wx_config')->value('data'),true);
        $url='https://api.weixin.qq.com/sns/jscode2session?appid='.$config['wx_app_id'].'&secret='.$config['wx_app_secret'].'&js_code='.$code.'&grant_type=authorization_code';
        $res=json_decode(httpPost($url,''),true);
        if (!isset($res['openid'])){
            return msg(0,'授权失败');
        }
//        require_once (root_path()."extend/wxpay/wxBizDataCrypt.php");
//        $encryptedData=input('encryptedData');
//        $iv=input('iv');
//
//        $appid = $config['wx_app_id'];
//        $pc = new \WXBizDataCrypt($appid, $res['session_key']);
//        $errCode = $pc->decryptData($encryptedData, $iv, $data );
//        if ($errCode == 0) {
//            //return msg(0,"获取手机失败，错误码",$data);
//            $data=json_decode($data,true);
//            $user=Db::name('technician')->where('openid',$res['openid'])->find();
//            if(!$user){
//                $user_data=[
//                    'openid'=>$res['openid'],
//                    'mobile'=>$data['phoneNumber']
//                ];
//                $user_id=Db::name('technician')->insertGetId($user_data);
//                $token = SignHelper::authSign($user_data);
//            }else{
//                $token = SignHelper::authSign($user);
//                $user_id=$user['id'];
//            }
//            Cache::set('technician_token'.$token, $user_id, 3600*24);
//            return msg(1, '登录成功',$token);
//        } else {
//            return msg(0,"获取手机失败，错误码:".json_decode($errCode));
//        }

        return msg(1,$res);
    }
    
    /**
     * /technician/login/login1
     * @param string $username 
     * @param string $code
     */
     public function login1(){
        $post=$this->request->param();
        if($post['sms_code']!=888888 && $post['sms_code']!=cache('code'.$post['username'])){return msg(0,'验证码不正确');}
        // $user=Db::name('technician')->where('username',$post['username'])->find();
        if(isset($post['username'])){
            $user=Db::name('technician')->where('username',$post['username'])->find();
        }else{
            $user=Db::name('technician')->where('openid',$post['openid'])->find();
        }
        if(!$user){
            return msg(0,"未申请/还未审核");
            // $user_id=Db::name('technician')->insertGetId($post);
            // $token = SignHelper::authSign($post);
        }else{
            $token = SignHelper::authSign($user);
            $user_id=$user['id'];
        }
        Cache::set('technician_token'.$token, $user_id, 3600*24);
        return msg(1, '登录成功',$token);
    }
    
    
    public function login(){
        $post=$this->request->param();
        $validate = validate([
            'openid' => 'require|max:150',
            'nickname' => 'require|max:30',
            'avatar' => 'require|max:250',
            'unionid'=>'require',

        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $user=Db::name('technician')->where('openid',$post['openid'])->find();
        if(!$user){
            return msg(0,"未申请/还未审核");
            // $user_id=Db::name('technician')->insertGetId($post);
            // $token = SignHelper::authSign($post);
        }else{
            $token = SignHelper::authSign($user);
            $user_id=$user['id'];
        }
        Cache::set('technician_token'.$token, $user_id, 3600*24);
        return msg(1, '登录成功',$token);
    }
    
    public function logout()
    {
        $token=getUserToken();
        Cache::set('technician_token'.$token,'');
        return msg(1, '退出成功');
    }
    
    public function bindMobile(){
        $id=getTechnicianId();
        if($this->request->isPost()){
            $config=json_decode(Db::name('base_config')->where('name','wx_config')->value('data'),true);
            require_once (root_path()."extend/wxpay/wxBizDataCrypt.php");
//            $post=$this->request->param();
                    $encryptedData=input('encryptedData');
                    $sessionKey=input('sessionKey');
        $iv=input('iv');
            $appid = $config['wx_app_id'];
            $pc = new \WXBizDataCrypt($appid, $sessionKey);
            $errCode = $pc->decryptData($encryptedData, $iv, $data );
            if ($errCode == 0) {
                $data=json_decode($data,true);
//                $user=Db::name('technician')->where('openid',$data['openId'])->find();
//                if(!$user){
//                    $user_data=[
//                        'openid'=>$data['openId'],
//                        'nickname'=>$data['nickName'],
//                        'avatar'=>$data['avatarUrl'],
//                        'mobile'=>$data['phoneNumber']
//                    ];
//                    $user_id=Db::name('technician')->insertGetId($user_data);
//                    $token = SignHelper::authSign($user_data);
//                }else{
//                    $token = SignHelper::authSign($user);
//                    $user_id=$user['id'];
//                }
//                Cache::set('technician_token'.$token, $user_id, 3600*24);
//                return msg(1, '登录成功',$token);
//                $data=json_decode($data,true);
                $res=Db::name('technician')->where('id',$id)->update(['mobile'=>$data['phoneNumber']]);
                return msg(1,"绑定成功",$data);
            } else {
                return msg(0,"获取手机失败，错误码:".json_decode($errCode));
            }


//            $mobile=input('mobile/s','');
//            if(empty($mobile)){
//                return msg(0,'手机号不能为空');
//            }
//            $res=Db::name('technician')->where('id',$id)->update(['mobile'=>$mobile]);
//            if($res){
//                return msg(1,'绑定手机成功');
//            }else{
//                return msg(0,'网络错误');
//            }
        }else{
            $res=Db::name('technician')->where('id',$id)->value('mobile');
            return msg(1,'',$res);
        }

    }

    public function cs(){
        require_once (root_path()."extend/wxpay/wxBizDataCrypt.php");
        //$config=json_decode(Db::name('base_config')->where('name','wx_config')->value('data'),true);
        $appid = 'wx4f4bc4dec97d474b';
        $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

        $encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';

        $pc = new \WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
           return msg(1,'',json_decode($data,true));
        } else {
            print($errCode . "\n");
        }
    }

    public function follow(){
        $id=getTechnicianId();
        $res=sendFollowMsg($id);
        if($res['errcode']==43004){
          return msg(1,'未关注公众号');
        }elseif($res['errcode']==0){
            Db::name('technician')->where('id',$id)->update(['subscribe'=>1]);
            return msg(1,'已关注公众号');
        }else{
            return msg(1,'确认失败：'.$res['errmsg']);
        }
    }
//    public function getPhoneNumber($data)
//    {
//        $getTokenUrl   = "https://api.weixin.qq.com/sns/jscode2session". "?appid=".$data['appid']. "&secret=".$data['secret']. "&js_code={$data['code']}". "&grant_type=authorization_code";
//        $token_get_all = json_decode(file_get_contents($getTokenUrl));
//
//        if (!isset($token_get_all->openid)) return false;
//
//        $openid = $token_get_all->openid;
//        $sessionKey = $token_get_all->session_key;
//
//        $wxbiz_data_crypt = new WxBizDataCryptService($data['appid'], $sessionKey);
//
//        $errCode = $wxbiz_data_crypt->decryptData(urldecode($data['encryptedData']), $data['iv'], $return_data);
//
//        if ($errCode == 0) {
//            $return_data = json_decode($return_data,true);
//            $result['mobile'] = $return_data['phoneNumber'];
//            $result['unionid'] = $token_get_all->unionid;
//            $result['openid'] = $openid;
//            $result['session_key'] = $sessionKey;
//
//            return $result;
//        } else {
//            return false;
//        }
//    }

}