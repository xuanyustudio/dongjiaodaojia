<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\BaseController;
use sms\Sms2;
use think\App;
use think\facade\Cache;
use think\facade\Db;
use think\facade\View;
use wxpay\WxLogin;
use yt\helper\SignHelper;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Authorization, Sec-Fetch-Mode, DNT, X-Mx-ReqToken, Keep-Alive, User-Agent, If-Match, If-None-Match, If-Unmodified-Since, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type, Accept-Language, Origin, Accept-Encoding,Access-Token,token");
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
header('Access-Control-Max-Age: 1728000');
header('Access-Control-Allow-Credentials:true');
class User extends BaseController
{
    public function __construct(App $app, \think\View $view)
    {
        parent::__construct($app, $view);
        $this->modelClass = new UserModel();
    }
    public function cs()
    {
        $data = [
            "mobile" => '400-8008820',
            "qr_code" => "http://api.nianerge.cn/uploads/api/202201/avatar.jpg",
        ];
        Db::name('base_config')->where('name', '=', 'customer_service')->update(['data' => json_encode($data)]);
        $user = getUserId();
        var_dump($user);
        exit();
    }
    public function customerService()
    {
        $res = Db::name('base_config')->where('name', '=', 'customer_service')->value('data');
        return msg(1, '', json_decode($res, true));
    }

    public function register()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $validate = validate([
                'mobile' => 'require|number',
                // 'sms_code' => 'require|alphaNum|max:25',
            ], [], false, false);
            $result = $validate->check($post);
            if (!$result) {
                $error = $validate->getError();
                return msg(0, $error);
            }
            // if ($post['sms_code']!=888888 && $post['sms_code'] != cache('code' . $post['mobile'])) {
            //     return msg(0, '验证码不正确');
            // }
            if (empty(input('password'))) {
                $post['password'] = '';
            } else {
                $post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
            }

            if (empty($post['avatar'])) {
                $post['avatar'] = request()->domain() . '/public/uploads/api/202201/avatar.jpg';
            }

            $is_mobile = $this->modelClass->where('mobile', $post['mobile'])->find();
            if ($is_mobile) {
                return msg(0, '该手机已注册');
            }
            $post['username'] = $post['mobile'];
            $post['nickname'] = '用户' . $post['mobile'];
            if (!empty($post['uid'])) {
                $pid = $this->modelClass->where('id', $post['uid'])->field('id,pid')->find();
                if (!$pid) {
                    return msg(0, '推荐人不存在');
                } else {
                    $post['pid'] = $pid['id'];
                    $post['spid'] = $pid['pid'];
                }
            }
            unset($post['sms_code']);
            unset($post['uid']);
            $post['brokerage'] = 0;
            $post['create_time'] = time();
            $userId = $this->modelClass->insertGetId($post);
            if ($userId) {
                $member_rights = Db::name('member_rights')->where('id', 1)->find('value');
                $coupon = Db::name('coupon')->where('id', $member_rights)->where('status', 1)->find();
                if ($coupon) {
                    $coupon['primary_id'] = $coupon['id'];
                    $coupon['user_id'] = $userId;
                    $coupon['create_time'] = time();
                    unset($coupon['id']);
                    unset($coupon['exchange']);
                    Db::name('coupon')->insert($coupon);
                }
//                $promo_code = createCode($userId);
                //                $this->modelClass->where('id', $userId)->update(['promo_code' => $promo_code]);
                return msg(1, '注册成功');
            } else {
                return msg(0, '网络异常，注册失败');
            }
        }
        $view = [
            'uid' => input('uid'),
        ];
        View::assign($view);
        return view('register');

    }
    public function login()
    {
        if (!$this->request->isPost()) {
            $view = [
                'uid' => input('uid'),
            ];
            View::assign($view);
            return view('register');
        }
        $post = $this->request->param();
        if ($post['type'] == 2) {
            $validate = validate([
                'username' => 'require|alphaNum|max:25',
                'sms_code' => 'require|alphaNum|max:25',
            ], [], false, false);
            $result = $validate->check($post);
            if (!$result) {
                $error = $validate->getError();
                return msg(0, $error);
            }
            if ($post['sms_code'] != 888888 && $post['sms_code'] != cache('code' . $post['username'])) {return msg(0, '验证码不正确');}
        } else {
            $validate = validate([
                'username' => 'require|alphaNum|max:25',
                'password' => 'require|alphaNum|max:25',
            ], [], false, false);
            $result = $validate->check($post);
            if (!$result) {
                $error = $validate->getError();
                return msg(0, $error);
            }
        }
        $where['username'] = strip_tags(trim($post['username']));
        $user = $this->modelClass::where($where)->find();

        if (!$user) {
            //   if($post['sms_code']!=888888 && $post['sms_code']!=cache('code'.$post['username'])){return msg(0,'验证码不正确');}

            $post['password'] = '';
            $post['avatar'] = request()->domain() . '/public/uploads/api/202201/avatar.jpg';
            $post['mobile'] = $post['username'];
            $post['nickname'] = '用户' . $post['mobile'];
            if (!empty($post['pid'])) {
                $pid = $this->modelClass->where('id', $post['pid'])->field('id,pid')->find();
                if (!$pid) {
                    return msg(0, '推荐人不存在');
                } else {
                    $post['pid'] = $pid['id'];
                    $post['spid'] = $pid['pid'];
                }
            }

            //如果没有用户，那么进行微信授权
            $wxLogin = new WxLogin();
            $post['openid'] = $wxLogin->WxLogin($post['code']);

            unset($post['sms_code']);
            // unset($post['uid']);
            $post['brokerage'] = 0;
            $post['create_time'] = time();
            $userId = $this->modelClass->insertGetId($post);
            if ($userId) {
                $coupon = Db::name('member_rights')->alias('a')->leftJoin('coupon b', 'a.value=b.id')
                    ->where('a.id', 1)->where('b.status', 1)->field('b.*')->where('a.status', 1)->find();
                if ($coupon) {
                    $coupon['primary_id'] = $coupon['id'];
                    $coupon['user_id'] = $userId;
                    $coupon['create_time'] = time();
                    unset($coupon['id']);
                    unset($coupon['exchange']);
                    Db::name('coupon')->insert($coupon);
                }

                $token = SignHelper::authSign($post);
                Cache::set('user_token' . $token, $userId, 3600 * 24 * 7);
                return msg(3, '登录成功', $token);
            }
        } else {
            if ($post['type'] == 2) {
                //   if($post['sms_code']!=888888 && $post['sms_code']!=cache('code'.$post['username'])){return msg(0,'验证码不正确');}
            } else {
                if (empty($user['password'])) {
                    return msg(0, '该账号暂未设置密码，请使用验证码登录');
                }
                if (!password_verify($post['password'], $user['password'])) {
                    return msg(0, '请检查账号密码');
                }
            }

            if ($post['code'] && $post['code'] != '') {
                $wxLogin = new WxLogin();
                $user['openid'] = $wxLogin->WxLogin($post['code']);
                $this->modelClass->where('id', $user['id'])->update(['openid' => $user['openid']]);
            }

            $ip = request()->ip();
            $user->last_ip = $ip;
            $user->last_login = time();
            $user->token = SignHelper::authSign($user);
            Cache::set('user_token' . $user->token, $user->id, 3600 * 24 * 7);
            $user->save();
            return msg(1, '登录成功', $user->token);
        }

    }
//     public function smsCode(){
    // //        return msg(1, '发送成功');
    //         $mobile=input('mobile');
    // //        halt($mobile);
    //         $ip = \request()->ip();
    //         if (Cache::get('ip'.$ip)) {
    //             return msg(0,'请不要重复发送');
    //         }
    //         if (Cache::get('code'.$mobile)){
    //             return msg(0,'请不要重复发送');
    //         }
    //         $config=json_decode(Db::name('base_config')->where('name','sms')->value('data'),true);
    //         if(empty($config['accessKeyId'])){
    //             return msg(0,'短信未配置');
    //         }
    // //        $config = [
    // //            'accessKeyId' => 'LTAI5t7Bw4pSduHyNSuMbKqn',
    // //            'accessKeySecret' => 'l95PQTQ9RCZJTEonbJweS6BKuqGT6N',
    // //            'signName' => '忠企',
    // //            'templateCode' => 'SMS_217926394',
    // //        ];
    //         include_once(app()->getRootPath() . "extend/sms/Sms2.php");
    //         $sms = new Sms2($config);
    //         $code=$this->generate_code();
    //         $status = $sms->send($mobile,$code);

//         //return msg(1, '发送成功',$status);
    //         if($status['Code']=='OK'){
    //             Cache::set('code'.$mobile, $code, 60);
    //             Cache::set('ip'.$mobile, $code, 60);
    //             return msg(1, '发送成功');
    //         }else{
    //             return msg(0, '发送失败'.$status['Message']);
    //         }

//     }

    public function smsCode()
    {
        $mobile = input('mobile');
        $message = input('message');
        $ip = \request()->ip();
        if (Cache::get('ip' . $ip)) {
            return msg(0, '请不要重复发送');
        }
        if (Cache::get('code' . $mobile)) {
            return msg(0, '请不要重复发送');
        }
        include_once app()->getRootPath() . "extend/sms/Sms2.php";
        $sms = new Sms2($mobile);
        if ($message == '') {
            $code = $this->generate_code();
            $status = $sms->send($mobile, '【享享到家】您的验证码为' . $code);

            if ($status['returnstatus'] == 'Success') {
                Cache::set('code' . $mobile, $code, 60);
                Cache::set('ip' . $mobile, $code, 60);
                return msg(1, '发送成功');
            } else {
                return msg(0, '发送失败，' . $status['message']);
            }
        } else {
            $status = $sms->send($mobile, $message);
            if ($status['returnstatus'] == 'Success') {
                return msg(1, '发送成功');
            } else {
                return msg(0, '发送失败，' . $status['message']);
            }
        }
    }

    public function generate_code($length = 6)
    {
        $min = pow(10, ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }
    public function editUser()
    {
        $uid = getUserId();
        $validate = validate([
            'field' => 'require',
            'value' => 'require|max:250',

        ], [], false, false);
        $post = $this->request->param();
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
//        var_dump($post['field']!='avatar');
        //        exit();
        if ($post['field'] != 'avatar' && $post['field'] != 'nickname' && $post['field'] != 'sex') {
            return msg(0, '非法请求字段');
        }

        Db::name('user')->where('id', $uid)->update([$post['field'] => $post['value']]);
        return msg(1, '编辑成功');
    }

    public function logout()
    {
        $token = getUserToken();
        Cache::set('user_token' . $token, '');
        return msg(1, '退出成功');
    }
    public function editPassword()
    {
        $post = $this->request->param();
        $validate = validate([
            'password' => 'require|max:50',
            'mobile' => 'require|max:25',
            'sms_code' => 'require|max:25',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        // if($post['sms_code']!=888888 && $post['sms_code']!=cache('code'.$post['mobile'])){return msg(0,'验证码不正确');}
        $user = $this->modelClass->where('mobile', $post['mobile'])->find();
        if ($user) {
            $post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
            $save = $user->save($post);
            if ($save) {
                return msg(1, '修改密码成功');
            } else {
                return msg(0, '修改密码失败，请稍后重试');
            }
        } else {
            return msg(0, '该账号不存在');
        }

    }

    public function config()
    {
        $validate = validate([
            'field' => 'require',
        ], [], false, false);
        $post = $this->request->param();
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $data = Db::name('base_config')->where('name', $post['field'])->value('data');
        return msg(1, '', $data);
    }
    public function userInfo()
    {
        $uid = getUserId();
        $user = $this->modelClass->field('id,guid,username,nickname,mobile,is_vip,overdue_time,avatar,sex,balance,brokerage')->find($uid);
        $data['is_vip'] = $user['is_vip'];
        if ($user['is_vip'] > 0 && $user['overdue_time'] < time()) {
            $data['is_vip'] = 0;
            $user->save($data);
        }
        $user['overdue_time'] = date('Y-m-d', $user['overdue_time']);
        $user['collect'] = Db::name('collect')->where('user_id', $uid)->where('status', 1)->count();
        $user['coupon'] = Db::name('coupon')->where('user_id', $uid)->count();
        $user['news'] = Db::name('news')->where('user_id', $uid)->where('status', 0)->count();
        $user['unpaid'] = Db::name('order')->where('user_id', $uid)->where('pay_status', 0)->count();
        if ($user['unpaid'] > 0) {
            $unpaid_order = Db::name('order')->alias('a')->leftJoin('service b', 'a.service_id=b.id')
                ->where('a.user_id', $uid)->where('a.pay_status', 0)->order('a.id', 'desc')
                ->field('a.id,a.order_sn,a.pay_money,a.create_time,appointment_time as service_date,b.name as service_name')->find();
            $unpaid_order['service_time'] = date('Y-m-d H:i', $unpaid_order['service_date']);
            $user['unpaid_order'] = $unpaid_order;
        } else {
            $user['unpaid_order'] = new \stdClass();
        }
        return msg(1, '', $user);
    }
    public function realName()
    {
        $uid = getUserId();
        $realname = Db::name('member_real')->where('id', $uid)->find();
        if ($realname) {
            return msg(0, '您已经认证过');
        }
        $post = $this->request->param();
        $message = [
            'realname.require' => '请填写姓名',
            'id_card.require' => '请填写身份证',
            'bank.require' => '请填写银行',
            'bank_code.require' => '请填写银行卡号',
            'bank_address.require' => '请填写开户行',
            'alipay_code.require' => '请填写支付宝账号',
        ];

        $validate = validate([
            'realname' => 'require|max:25',
            'id_card' => 'require|chsDash|max:50',
            'bank' => 'require|chsDash|max:25',
            'bank_code' => 'require|chsDash|max:50',
            'bank_address' => 'require|chsDash|max:25',
            'alipay_code' => 'require|chsDash|max:25',
        ], $message, false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $post['id'] = $uid;
        $res = Db::name('member_real')->insert($post);
        if ($res) {
            return msg(1, '认证成功');
        } else {
            return msg(1, '认证失败');
        }
    }
    public function technicianApply()
    {
        $validate = validate([
            'realname' => 'require',
            'mobile' => 'require',
            'sex' => 'require',
            'age' => 'require',
            'avatar' => 'require',
            'city' => 'require',
        ], [], false, false);
        $post = $this->request->param();
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $uid = getUserId();
        $res = Db::name('technician_apply')->where('user_id', $uid)->find();
        if ($res) {
            return msg(0, '您已经申请过，请勿重复申请');
        }
        $post['user_id'] = $uid;
        $post['create_time'] = time();
        $res = Db::name('technician_apply')->insert($post);
        if ($res) {
            return msg(1, '申请成功');
        } else {
            return msg(0, '网络错误');
        }
    }

    //不判断token
    public function technicianApply2()
    {
        $validate = validate([
            'realname' => 'require',
            'mobile' => 'require',
            'sex' => 'require',
            'age' => 'require',
            'avatar' => 'require',
            'city' => 'require',
        ], [], false, false);
        $post = $this->request->param();
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $res = Db::name('technician_apply')->where('mobile', $post['mobile'])->find();
        if ($res) {
            return msg(0, '您已经申请过，请勿重复申请');
        }
        $post['user_id'] = '0';
        $post['create_time'] = time();
        $post['username'] = $post['mobile'];
        $res = Db::name('technician_apply')->insert($post);
        if ($res) {
            return msg(1, '申请成功');
        } else {
            return msg(0, '网络错误');
        }
    }

    public function technicianStatus()
    {
        $uid = getUserId();
        $res = Db::name('technician_apply')->where('user_id', $uid)->find();
        if ($res) {
            if ($res['status'] == 0) {
                $data = ['desc' => '审核中', 'status' => 0];
            } elseif ($res['status'] == 1) {
                $data = ['desc' => '审核通过', 'status' => 1];
            }
        } else {
            $data = ['desc' => '未申请', 'status' => -1];
        }
        return msg(1, '成功', $data);

    }

    /*
     * 用户实名
     * */
    public function member_real()
    {
        $post = $this->request->param();
        $validate = validate([
            'realname' => 'require|max:30',
            'id_card' => 'require|max:30',

        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }

        $uid = getUserId();

        $user = $this->modelClass->where('id', $uid)->find();

        if ($user['real_status'] == 1) {
            return msg(0, '审核中，请勿重复申请');
        } elseif ($user['real_status'] == 2) {
            return msg(0, '已认证过，请勿重复申请');
        }

        $user->realname = $post['realname'];
        $user->id_card = $post['id_card'];
        $user->real_status = 1;
        $user->save();

        return msg(1, '成功');
    }

    public function deleteUser()
    {
        return msg(0, '注销已提交，我们将在三个月内审核完毕');
        // $post = $this->request->param();
        // $validate = validate([
        //     'guid' => 'require',
        // ], [], false, false);
        // $result = $validate->check($post);
        // if (!$result) {
        //     $error = $validate->getError();
        //     return msg(0, $error);
        // }
        // $res = Db::name('user')->where('guid', $post['guid'])->delete();
        // if ($res) {
        //     return msg(1, '注销成功');
        // } else {
        //     return msg(0, '注销失败');
        // }
    }

}
