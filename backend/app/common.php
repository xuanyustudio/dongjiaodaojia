<?php
// +----------------------------------------------------------------------
// | 应用公共文件
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
error_reporting(0);

/**
 * 随机字符
 * @param int $length 长度
 * @param string $type 类型
 * @param int $convert 转换大小写 1大写 0小写
 * @return string
 */
function random($length = 10, $type = 'letter', $convert = 0)
{
    $config = array(
        'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    );

    if (!isset($config[$type])) {
        $type = 'letter';
    }

    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $string[mt_rand(0, $strlen)];
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
}

/*
 * 生成交易流水号
 * @param char(2) $type
 */
function doOrderSn($type)
{
    return date('YmdHis') . $type . substr(microtime(), 2, 3) . sprintf('%02d', rand(0, 99));
}

//后台sql输入框语句过滤
function sql_replace($str)
{
    $farr = ["/insert[\s]+|update[\s]+|create[\s]+|alter[\s]+|delete[\s]+|drop[\s]+|load_file|outfile|dump/is"];
    $str = preg_replace($farr, '', $str);
    return $str;
}

//上传文件黑名单过滤
function upload_replace($str)
{
    $farr = ["/php|php3|php4|php5|phtml|pht|/is"];
    $str = preg_replace($farr, '', $str);
    return $str;
}

//查询方法过滤
function serach_in($str)
{
    $farr = ["/^select[\s]+|insert[\s]+|and[\s]+|or[\s]+|create[\s]+|update[\s]+|delete[\s]+|alter[\s]+|count[\s]+|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i"];
    $str = preg_replace($farr, '', $str);
    return trim($str);
}

//获取键值对信息
function getItemData($data)
{
    if (count($data) > 0) {
        foreach ($data as $key => $v) {
            if (count($v) == 0) {
                unset($data[$key]);
            }
        }
        $str = json_encode(array_values($data), JSON_UNESCAPED_UNICODE);
    } else {
        $str = '';
    }
    return $str;
}

/**
 * tp官方数组查询方法废弃，数组转化为现有支持的查询方法
 * @param array $data 原始查询条件
 * @return array
 */
function formatWhere($data)
{
    $where = [];
    foreach ($data as $k => $v) {
        if (is_array($v)) {
            if (((string) $v[1] != null && !is_array($v[1])) || (is_array($v[1]) && (string) $v[1][0] != null)) {
                switch (strtolower($v[0])) {
                    //模糊查询
                    case 'like':
                        $v[1] = '%' . $v[1] . '%';
                        break;

                    //表达式查询
                    case 'exp':
                        $v[1] = Db::raw($v[1]);
                        break;
                }
                $where[] = [$k, $v[0], $v[1]];
            }
        } else {
            if ((string) $v != null) {
                $where[] = [$k, '=', $v];
            }
        }
    }
    return $where;
}

/**
 * 生成sql查询语句
 * @access protected
 * @param  sql     原始sql语句
 * @param  $where  查询条件
 * @param  $limit  分页
 * @param  $orderby  排序
 * @return array
 */
function loadList($sql, $where = [], $limit, $orderby)
{
    $sql = str_replace('pre_', config('database.connections.mysql.prefix'), strtolower($sql));
    foreach ($where as $key => $val) {
        if (is_array($val)) {
            switch ($val[1]) {
                case 'between':
                    if (empty($val[2][0]) && !empty($val[2][1])) {
                        $map .= $val[0] . ' < ' . $val[2][1] . ' and ';
                    }
                    if (!empty($val[2][0]) && empty($val[2][1])) {
                        $map .= $val[0] . ' > ' . $val[2][1] . ' and ';
                    }
                    if (!empty($val[2][0]) && !empty($val[2][1])) {
                        $map .= $val[0] . ' between ' . $val[2][0] . ' and ' . $val[2][1] . ' and ';
                    }
                    break;

                case 'exp':
                    $map .= $val[0] . ' ' . $val[2] . ' and ';
                    break;

                case 'in':
                    $map .= $val[0] . ' in (' . $val[2] . ') and ';
                    break;

                case 'not in':
                    $map .= $val[0] . ' not in (' . $val[2] . ') and ';
                    break;

                case 'find in set':
                    $map .= ' find_in_set(\'' . $val[2] . '\',' . $val[0] . ') and ';
                    break;

                default:
                    $map .= $val[0] . ' ' . $val[1] . " '" . $val[2] . "'" . ' and ';
                    break;
            }
        }
    }
    $map .= '1=1';

    $is_where = strripos($sql, "where");
    if ($is_where === false) {
        $where = !empty($where) ? ' where ' . $map : '';
        $sql = $sql . $where;
    } else {
        $l_sql = substr($sql, 0, $is_where);
        $r_sql = substr($sql, $is_where + 5, strlen($sql) - $is_where - 5);
        $where = !empty($where) ? ' where ' . $map . ' and ' : ' where ';
        $sql = $l_sql . $where . $r_sql;
    }

    $group = preg_split('/group\s+by/', $sql);
    if (is_array($group)) {
        $is_where = strripos($group[1], "where");
        if ($is_where) {
            $l_sql = substr($group[1], 0, $is_where);
            $r_sql = substr($group[1], $is_where + 5, strlen($group[1]) - $is_where - 5);
            $where = !empty($is_where) ? 'where' : '';
            $sql = $group[0] . $where . $r_sql . ' group by ' . $l_sql;
        }
    }
    $limit = ' limit ' . $limit;

    $countWhere = 'select count(*) as count from (' . $sql . ') as tp';

    if (strripos($sql, "order by") === false && $orderby) {
        $sql .= ' order by ' . $orderby;
    }

    if (strripos($sql, "limit") === false) {
        $sql .= $limit;
    }

    $result = Db::query($sql);
    $count = Db::query($countWhere);

    return ['data' => $result, 'total' => $count[0]['count']];
}

//过滤xss
function remove_xss($string)
{
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound');
    $parm2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);
    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, '', $string);
    }
    return $string;
}

/**
 * 实例化数据库类
 * @param string        $name 操作的数据表名称（不含前缀）
 * @param array|string  $config 数据库配置参数
 * @param bool          $force 是否强制重新连接
 * @return \think\db\Query
 */
if (!function_exists('db')) {

    function db($name = '', $connect = '')
    {
        if (empty($connect)) {
            $connect = config('database.default');
        }
        return Db::connect($connect, false)->name($name);
    }
}
function msg($code, $msg = "", $data = '', $count = 0)
{
    return json(["code" => $code, "msg" => $msg, "data" => $data, "count" => $count]);
}
function commentType()
{
    $data = Db::name('types')->where('type', '=', 'comment')->field('id,name')->select();

    return $data;
}
function createCode($user_id)
{

    static $source_string = 'J2RSTUV67MWX89KLYZE5FCDG3HQA4B1NOPI';

    $num = $user_id + 9999;

    $code = '';

    while ($num > 0) {

        $mod = $num % 35;

        $num = ($num - $mod) / 35;

        $code = $source_string[$mod] . $code;

    }
    if (empty($code[3])) {
        $code = str_pad($code, 4, '0', STR_PAD_LEFT);
    }

    return $code;

}

function decode($code)
{

    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';

    if (strrpos($code, '0') !== false) {
        $code = substr($code, strrpos($code, '0') + 1);
    }

    $len = strlen($code);

    $code = strrev($code);

    $num = 0;

    for ($i = 0; $i < $len; $i++) {

        $num += strpos($source_string, $code[$i]) * pow(35, $i);

    }

    return $num - 9999;

}

function getUserToken()
{
    $token = trim(Request::header('token', ''), '"');
    return $token;
}
function getUserId()
{
    $token = trim(Request::header('token', ''), '"');
    return Cache::get('user_token' . $token);
}
function getTechnicianId()
{
    $token = trim(Request::header('token', ''), '"');
    return Cache::get('technician_token' . $token);
}

function opinionType()
{
    $data = Db::name('types')->where('type', '=', 'opinion')->field('id,name')->select();
    return $data;
}
function cancelType()
{
    $data = Db::name('types')->where('type', '=', 'cancel')->field('id,name')->select();
    return $data;
}

function order_sn($type)
{
    $order_sn = $type . date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    return $order_sn;
}
//小时转数字下标
function hourToIndex($t)
{
    $hour = intval(date('H', $t)) * 2;
    $min = intval(date('i', $t)) > 29 ? 1 : 0;
    return $hour + $min;
}
function indexToHour($index, $a = '')
{
    $hour = $index % 2;
    if ($hour == 1) {
        $index -= 1;
        $min = ":30";
    } else {
        $min = ":00";
    }
    if ($index > 19) {
        $hour = strval($index / 2);
    } else {
        $hour = $a . strval($index / 2);
    }

    return $hour . $min;
}
//上传证书
function uploadCert($file)
{

    if ($file['type'] != 'application/x-x509-ca-cert') {
        return msg('0', '非法文件格式');
    }
    if ($_FILES["file"]["error"] > 0) {
        return ['code' => 0, 'msg' => $file["error"]];
    } else {
        move_uploaded_file($file["tmp_name"], root_path('public\uploads\wx') . $file["name"]);
        return ['code' => 0, 'msg' => '上传成功'];
    }
}

function createNews($uid, $content, $user_type, $type, $technician_id = 0)
{
    $data = [
        'user_id' => $uid,
        'content' => $content,
        'user_type' => $user_type,
        'type' => $type,
        'technician_id' => $technician_id,
        'create_time' => time(),
    ];
    Db::name('news')->insert($data);
}

function getAccessToken()
{
    $access_token = cache('bdt_access_token');
    if (!$access_token) {
        $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $config['wx_app_id'] . '&secret=' . $config['wx_app_secret'];
        $res = json_decode(httpPost($url, ''), true);
        Cache::set('bdt_access_token', $res['access_token'], 7100);
        return $res['access_token'];
    }
    return $access_token;
}

function httpPost($url, $data, $header = null)
{ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话

    if (!empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 0); //返回response头部信息
    } else {
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
        )); //类型为json
    }
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    //
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回

    $result = curl_exec($curl); // 执行操作
    curl_close($curl); // 关键CURL会话
    return $result; // 返回数据
}
function httpGet($url)
{
//    $request_headers[]  = 'Authorization: '.$token;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

/**
 * 发送提现审核通知
 * @param $tech_id  技师id
 * @param $money  金额
 * @param $status  审核通过；审核拒绝
 * @param $create_time  申请时间
 */
function sendWithdrawalMsg($tech_id, $money, $status, $create_time)
{
    $openid = Db::name('technician')->where('id', $tech_id)->value('openid');
    $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
    $access_token = getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token;
    $data = [
        'touser' => $openid,
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
                    'value' => '提现审核通知',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $money,
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => '支付宝提现',
                    'color' => '#173177',
                ],
                'keyword3' => [
                    'value' => date('Y-m-d H-i', $create_time),
                    'color' => '#173177',
                ],
                'keyword4' => [
                    'value' => $status,
                    'color' => '#173177',
                ],
                'keyword5' => [
                    'value' => date('Y-m-d H-i'),
                    'color' => '#173177',
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#173177',
                ],
            ],
        ],

    ];
    $res = json_decode(httpPost($url, $data), true);
    return $res;
}

/**
 * 发送认证审核通知
 * @param $tech_id  技师id
 * @param $project  头像；身份；资质；基本资料
 * @param $status  审核通过；审核拒绝
 */
function sendAuthenticationMsg($tech_id, $project, $status)
{
    $tech = Db::name('technician')->where('id', $tech_id)->field('openid,nickname')->find();
    $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
    $access_token = getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token;
    $data = [
        'touser' => $tech['openid'],
        'mp_template_msg' => [
            'appid' => 'wxdb5f2028eae7f644',
            'template_id' => '-Z17hvT-mRmNCSdyDLNWTiBk6O9ZZKxLycZ6gsZjKf0',
            'url' => '',
            'miniprogram' => [
                'appid' => $config['wx_app_id'],
                'path' => 'page/page/index',
            ],
            'data' => [
                'first' => [
                    'value' => $project . '认证审核通知',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $tech['nickname'],
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => $status,
                    'color' => '#173177',
                ],
                'keyword3' => [
                    'value' => date("Y-m-d H:i:s", time()),
                    'color' => '#173177',
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#173177',
                ],
            ],
        ],

    ];
    $res = json_decode(httpPost($url, $data), true);
    return $res;
}

/**
 * 发送订单状态通知
 * @param $tech_id  技师id
 * @param $service  服务名称
 * @param $status  下单；新订单；取消订单
 * @param $order_sn  订单号
 * @param $appointment_time  服务时间
 */
function sendOrderMsg($tech_id, $service, $status, $order_sn, $appointment_time)
{
    $tech = Db::name('technician')->where('id', $tech_id)->field('openid,nickname')->find();
    $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
    $access_token = getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token;
    $data = [
        'touser' => $tech['openid'],
        'mp_template_msg' => [
            'appid' => 'wxdb5f2028eae7f644',
            'template_id' => '8v-xo8kX8LOOqs-Tx5koYwD4YPMK1NO5uxjxg_s2bPM',
            'url' => '',
            'miniprogram' => [
                'appid' => $config['wx_app_id'],
                'path' => 'page/page/index',
            ],
            'data' => [
                'first' => [
                    'value' => '订单通知',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $service,
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => $order_sn,
                    'color' => '#173177',
                ],
                'keyword3' => [
                    'value' => $status,
                    'color' => '#173177',
                ],
                'keyword4' => [
                    'value' => date('Y-m-d H:i', $appointment_time),
                    'color' => '#173177',
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#173177',
                ],
            ],
        ],

    ];
    $res = json_decode(httpPost($url, $data), true);
    return $res;
}

/**
 * 发送关注状态通知
 * @param $tech_id  技师id
 */
function sendFollowMsg($tech_id)
{
    $tech = Db::name('technician')->where('id', $tech_id)->field('openid,nickname')->find();
    $config = json_decode(Db::name('base_config')->where('name', 'wx_config')->value('data'), true);
    $customer_service = json_decode(Db::name('base_config')->where('name', 'customer_service')->value('data'), true);
    $access_token = getAccessToken();
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token;
    $data = [
        'touser' => $tech['openid'],
        'mp_template_msg' => [
            'appid' => 'wxdb5f2028eae7f644',
            'template_id' => 'jcNH9cL7rfdjxroOsLq0aLUBmJzCwBhWO5BzwSNNVxE',
            'url' => '',
            'miniprogram' => [
                'appid' => $config['wx_app_id'],
                'path' => 'page/page/index',
            ],
            'data' => [
                'first' => [
                    'value' => '关注公众号通知',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => '通过公众号消息通知',
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => $customer_service['mobile'],
                    'color' => '#173177',
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#173177',
                ],
            ],
        ],

    ];
    $res = json_decode(httpPost($url, $data), true);
    return $res;
}

/*
 * 获取时间段内每个整点小时
 * */
function getHourRange($start_time, $end_time)
{
    // $start_hour=$start_time%2;
    // if($start_hour==1){
    //     $start_time+=1;
    //     $start = $start_time/2;
    // }else{
    //     $start_time-=2;
    //     $start = $start_time/2;
    // }
    // $end_hour=$end_time%2;
    // if($end_hour==1){
    //     $end_time-=1;
    // }else{
    //     $end_time+=2;
    // }
    // $end = $end_time/2;
    $start = $start_time;
    $end = $end_time;

//    dump($start);
    //    dump($end);
    $returnData = [];
    
    for ($i = 0; $start <= $end; $start++) {
        $tempDate = $start / 2 + $i;
        $returnData[] = $tempDate;
    }
    // unset($returnData[0]);
    // array_pop($returnData);

    $ret = [];
    foreach ($returnData as $v) {
        $ret[] = $v;
    }
    return $ret;

}
