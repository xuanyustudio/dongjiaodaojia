<?php

namespace app\api\controller;

use app\BaseController;
use app\Request;
use OSS\Core\OssException;
use OSS\OssClient;
use think\facade\Db;
use think\route\Domain;
use think\validate;

class Index extends BaseController
{

    public function test()
    {

        var_dump(getHourRange(2, 23));die;
    }

    public function getkf()
    {

        return msg(1, '', '17088197887');
    }
    public function index()
    {
        $banner = Db::name('banner')->where('type', 0)->order('sort', 'desc')->field('id,image')->select();
        return msg(1, '', $banner);
    }

    //服务
    public function service()
    {

        $page = input('page/i', 1);
        $limit = 10;
        $orderBy = input('orderBy/i', 0);
        switch ($orderBy) {
            case 0;
                $orderBy = "sort";
                $order = 'desc';
                break;
            case 1;
                $orderBy = "price";
                $order = 'asc';
                break;
            case 2;
                $orderBy = "sales";
                $order = 'desc';
                break;
            case 3;
                $orderBy = "praise";
                $order = 'desc';
                break;
        }
        $where[] = ['show', '=', 1];
        $list = Db::name('service')->where($where)->field('id,name,original_price,price,cover,discount,sales,praise,duration,label')->order($orderBy, $order)
            ->order('id', 'desc')->limit(($page - 1) * $limit, $limit)->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['discount'] = $v['discount'] % 10 == 0 ? $v['discount'] / 10 : $v['discount'];
            $coupon = Db::name('coupon')->where('status', 1)->where('user_id', 0)
                ->whereRaw('service_id=:service_id or service_id=:service', ['service_id' => $v['id'], 'service' => 0])->find();
            if ($coupon) {
                $list[$k]['coupon'] = 1;
            } else {
                $list[$k]['coupon'] = 0;
            }
        }
        $count = Db::name('service')->where($where)->count();
        $total = $count == 0 ? 0 : intval($count / 10) + 1;
        return msg(1, '', array('list' => $list, 'total' => $total, 'page' => $page), $count);
    }
    //服务详情
    public function serviceDetails()
    {
        $id = input('id/i', 0);
        $data = Db::name('service')->where('id', $id)->field('id,banner,praise,introduce')->find();
        if (empty($data)) {
            return msg(0, '服务不存在');
        }
        $data['banner'] = json_decode($data['banner']);
        $data['praise'] = intval($data['praise'] * 20);
        $data['comment_count'] = Db::name('comment')->where('service_id', $id)->count();
        $spec = Db::name('service_spec')->where('service_id', $id)
            ->field('id,name,price,duration,sort')
            ->order('sort', 'desc')->order('price', 'asc')
            ->select()->toArray();
        $comment = commentType();
        $config = Db::name('order_explain')->where('service_id', $data['id'])->field('name,content')->select();
        $uid = getUserId();
        $vip['is_vip'] = Db::name('user')->where('id', $uid)->value('is_vip');
        if (!$vip['is_vip']) {
            $vip_discount = Db::name('member_rights')->where('id', 2)->value('value');
            $vip['vip_discount'] = $vip_discount % 10 == 0 ? $vip_discount / 10 : $vip_discount;
        }
        return msg(1, '', array('service' => $data, 'vip' => $vip, 'spec' => $spec, 'comment' => $comment, 'config' => $config), 0);
    }
    //优惠券
    public function coupon()
    {
        $t = time();
        $uid = getUserId();
        $id = input('id/i', 0);
        $coupon = Db::name('coupon')->where('status', 1)->whereRaw('service_id=:id or service_id=0', ['id' => $id])
            ->where('user_id', 0)->where('start_time', '<', $t)->where('end_time', '>', $t)->where('get_type', 0)
            ->field('id,name,money,discount,condition,type,start_time,end_time')->select()->toArray();
        foreach ($coupon as &$v) {
            $v['start_time'] = date('Y-m-d H:i', $v['start_time']);
            $v['end_time'] = date('Y-m-d H:i', $v['end_time']);
            $receive = Db::name('coupon')->where('user_id', $uid)->where('primary_id', $v['id'])->count();
            $v['is_receive'] = $receive > 0 ? 1 : 0;
        }
        return msg(1, '', $coupon);
    }
    //领取
    public function receive()
    {
        $post = $this->request->param();
        $validate = validate([
            'id' => 'require|max:11',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $coupon = Db::name('coupon')->where('id', $post['id'])
            ->where('status', 1)->where('get_type', 0)->find();
        if (!$coupon) {
            return msg(0, '优惠券不存在或已失效');
        }
        $uid = getUserId();
        $user_coupon = Db::name('coupon')->where('user_id', $uid)->where('primary_id', $coupon['id'])->find();
        if ($user_coupon) {
            return msg(0, '您已领取此优惠券');
        }
        $coupon['primary_id'] = $coupon['id'];
        $coupon['user_id'] = $uid;
        $coupon['create_time'] = time();
        unset($coupon['id']);
        unset($coupon['exchange']);
        $res = Db::name('coupon')->insert($coupon);
        if ($res) {
            createNews($uid, '领取优惠券成功', 1, 1, 0);
            return msg(1, '领取成功');
        } else {
            return msg(0, '网络错误，请稍后重试');
        }
    }

    //评论
    public function comment()
    {
        $post = $this->request->param();
        $validate = validate([
            'id' => 'require|number',
            'technician_id' => 'require|number',
        ], [], false, false);
        $result = $validate->check($post);
        if (!$result) {
            $error = $validate->getError();
            return msg(0, $error);
        }
        $page = input('page/i', 1);
        $limit = 10;
        if ($post['id'] > 0) {
            $where[] = ['a.service_id', '=', $post['id']];
        } else {
            $where[] = ['a.technician_id', '=', $post['technician_id']];
        }

        if (!empty($post['type'])) {
            $where[] = ['a.types', 'in', $post['type']];
        }
        $list = Db::name('comment')->alias('a')->leftJoin('user b', 'a.user_id=b.id')
            ->where($where)->order('id', 'desc')->field('a.id,a.content,a.images,a.service_score,b.nickname,b.avatar,a.create_time')
            ->limit(($page - 1) * $limit, $limit)->select()->toArray();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['images'] = json_decode($v['images']);
        }
        $count = Db::name('comment')->alias('a')->leftJoin('user b', 'a.user_id=b.id')->where($where)->count();
        $total = $count == 0 ? 0 : intval($count / 10) + 1;
        return msg(1, '', array('list' => $list, 'total' => $total, 'page' => $page), $count);
    }

    public function upload()
    {
        $config = json_decode(Db::name('base_config')->where('name', '=', 'oss')->value('data'), true);
//        var_dump($config['status']);
        //        exit();
        if (empty($config) || $config['status'] == 0) {

            $file = request()->file('file');

            try {
                validate(['image' => 'fileSize:10240|fileExt:jpg,png,jpeg,gif,bmp|image:200,200'])
                    ->check(['file' => $file]);
                $savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);
                return msg(1, '上传成功', $this->request->domain() . '/public/uploads/' . str_replace('\\', '/', $savename));

            } catch (\think\exception\ValidateException $e) {
//                echo $e->getMessage();
                return msg(0, '上传失败：' . $e->getMessage());
            }
        } else {
            $file = $_FILES['file'];
            $allowext = 'gif,GIF,jpg,JPG,jpeg,JPEG,png,PNG,bmp,BMP';
            if (strpos($file['type'], 'image') === false) {
                return msg(0, "文件格式不正确");
            }
            $temp = explode("/", $file['type']); //获取文件名按“.”进行分割
            // 获取文件后缀名 更新成功
            $extension = end($temp);

            if (strpos($allowext, $extension) === false) {
                return msg(0, "图片格式不正确");
            }
            // 阿里云账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM用户进行API访问或日常运维，请登录RAM控制台创建RAM用户。
            $accessKeyId = $config['accessKeyId'];
            $accessKeySecret = $config['accessKeySecret'];
            // yourEndpoint填写Bucket所在地域对应的Endpoint。以华东1（杭州）为例，Endpoint填写为https://oss-cn-hangzhou.aliyuncs.com。
            $endpoint = $config['endpoint'];
            // 填写Bucket名称，例如examplebucket。
            $bucket = $config['bucket'];
            // 填写Object完整路径，例如exampledir/exampleobject.txt。Object完整路径中不能包含Bucket名称。
            $object = date('YmdHis') . strval(mt_rand(1000, 9999)) . '.' . $extension;
            // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
            // 填写本地文件的完整路径，例如D:\\localpath\\examplefile.txt。如果未指定本地路径，则默认从示例程序所属项目对应本地路径中上传文件。
            $filePath = $file['tmp_name'];
            try {
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                $result = $ossClient->uploadFile($bucket, $object, $filePath);
                return msg(1, "上传成功", $result['info']['url']);
            } catch (OssException $e) {
                return msg(1, "上传失败:" . $e->getMessage());
            }
        }
    }

    public function getMapAddress()
    {
        $param = $this->request->param();
        if (!$param['lat'] || !$param['lng']) {
            return msg(0, "缺少经纬度");
        }
        $lat = $param['lat'];
        $lng = $param['lng'];
        $key = '20646167b6dcc62094655979946666d0';
        $url = 'https://restapi.amap.com/v3/geocode/regeo?output=json&location=' . $lat . ',' . $lng . '&key=' . $key . '&radius=1000&extensions=all';
        $res = httpGet($url);

        return msg(1, "获取成功", json_decode($res));
    }
}
