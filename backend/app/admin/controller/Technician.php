<?php
/*
module:        技师傅列表控制器
create_time:    2022-03-18 16:12:58
author:
contact:
 */

namespace app\admin\controller;

use app\admin\model\Service as ServiceModel;
use app\admin\model\Technicianalbum as TechnicianalbumModel;
use app\admin\model\Technician as TechnicianModel;
use app\admin\model\Techniciandata as TechniciandataModel;
use app\admin\model\Technicianhealthy as TechnicianhealthyModel;
use app\admin\model\Technicianidentity as TechnicianidentityModel;
use app\admin\model\Technicianqualifications as TechnicianqualificationsModel;
use app\admin\model\Techniciantcp as TechniciantcpModel;
use think\exception\ValidateException;
use think\facade\Db;

class Technician extends Admin
{

    /*
     * @Description  数据列表
     */
    public function index()
    {
        $limit = $this->request->post('limit', 20, 'intval');
        $page = $this->request->post('page', 1, 'intval');

        $where = [];
        $where['id'] = $this->request->post('id', '', 'serach_in');
        $where['technician.realname'] = $this->request->post('realname', '', 'serach_in');
        $where['technician.mobile'] = $this->request->post('mobile', '', 'serach_in');
        $where['technician.status'] = $this->request->post('status', '', 'serach_in');
        $where['technician.work_status'] = $this->request->post('work_status', '', 'serach_in');

        $field = 'id,nickname,username,realname,start_time,end_time,sex,mobile,avatar,album,status,balance,praise,credit,composite,work_status,service,hframe,hrocket,hhot,fquantity,tgrade';

        $withJoin = [
            'technicianalbum' => explode(',', 'id,avatar,album,technician_id,status,create_time'),
            'technicianali' => explode(',', 'id,technician_id,realname,mobile,create_time'),
            'techniciandata' => explode(',', 'id,height,birthday,profile,technician_id,status,create_time,practice,sex'),
            'technicianhealthy' => explode(',', 'id,certificates,technician_id,status,create_time'),
            'technicianidentity' => explode(',', 'id,front,back,technician_id,status,create_time,realname,cardsn'),
            'technicianqualifications' => explode(',', 'id,certificates,technician_id,status,create_time'),
            'techniciantcp' => explode(',', 'id,sign,technician_id,status,create_time'),
        ];

        $res = TechnicianModel::where(formatWhere($where))->field($field)->withJoin($withJoin, 'left')->order('id desc')->paginate(['list_rows' => $limit, 'page' => $page])->toArray();

        $tglist = Db::name('base_config')->where('name', '=', 'tglist')->find()['data'];
        $data['tgradecount'] = count(json_decode($tglist, true));
        $data['status'] = 200;
        $data['data'] = $res;
        return json($data);
    }

    /*
     * @Description  修改排序开关
     */
    public function updateExt()
    {
        $postField = 'id,';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        if (!$data['id']) {
            throw new ValidateException('参数错误');
        }

        TechnicianModel::update($data);
        return json(['status' => 200, 'msg' => '操作成功']);
    }

	/*
     * @Description  修改技师等级
     */
	public function updateTgrade(){
		$postField = 'id,tgrade';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        if (!$data['id']) {
            throw new ValidateException('参数错误');
        }

        TechnicianModel::update($data);
        return json(['status' => 200, 'msg' => '操作成功']);
	}

	/*
     * @Description  修改技师等级
     */
	public function updateFquantity(){
		$postField = 'id,fquantity';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        if (!$data['id']) {
            throw new ValidateException('参数错误');
        }

        TechnicianModel::update($data);
        return json(['status' => 200, 'msg' => '操作成功']);
	}

    /*
     * @Description  修改排序开关
     */
    public function updateWork()
    {
        $postField = 'id,work_status';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        if (!$data['id']) {
            throw new ValidateException('参数错误');
        }

        TechnicianModel::update($data);
        return json(['status' => 200, 'msg' => '操作成功']);
    }

    /*
     * @Description  添加
     */
    public function add()
    {
        $postField = 'nickname,username,realname,service,week,start_time,end_time,sex,openid,last_login,login_num,last_ip,mobile,avatar,album,status,balance,praise,credit,composite,lat,lng,create_time,update_time,sales,work_status,address,province,city,district,label,unionid,subscribe,hframe,hrocket,hhot,fquantity,tgrade';
        $data = $this->request->only(explode(',', $postField), 'post', null);

        $this->validate($data, \app\admin\validate\Technician::class);
        $data['album'] = getItemData($data['album']);

        try {
            $res = TechnicianModel::create($data);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        return json(['status' => 200, 'data' => $res->id]);
    }

    /*
     * @Description  修改
     */
    public function update()
    {
        $postField = 'id,nickname,username,realname,service,week,start_time,end_time,sex,openid,last_login,login_num,last_ip,mobile,avatar,album,status,balance,praise,credit,composite,lat,lng,create_time,update_time,sales,work_status,address,province,city,district,label,unionid,subscribe,hframe,hrocket,hhot,fquantity,tgrade';
        $data = $this->request->only(explode(',', $postField), 'post', null);

        $this->validate($data, \app\admin\validate\Technician::class);
        $data['album'] = getItemData($data['album']);

        try {
            TechnicianModel::update($data);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        return json(['status' => 200]);
    }

    /*
     * @Description  修改信息之前查询信息的 勿要删除
     */
    public function getUpdateInfo()
    {
        $id = $this->request->post('id', '', 'serach_in');
        if (!$id) {
            throw new ValidateException('参数错误');
        }

        $field = 'id,nickname,username,realname,service,week,start_time,end_time,sex,openid,last_login,login_num,last_ip,mobile,avatar,album,status,balance,praise,credit,composite,lat,lng,create_time,update_time,sales,work_status,address,province,city,district,label,unionid,subscribe,hframe,hrocket,hhot,fquantity,tgrade';
        $res = TechnicianModel::field($field)->find($id);
        $res['album'] = json_decode($res['album'], true);
        return json(['status' => 200, 'data' => $res]);
    }

    /*
     * @Description  删除
     */
    public function delete()
    {
        $idx = $this->request->post('id', '', 'serach_in');
        if (!$idx) {
            throw new ValidateException('参数错误');
        }

        TechnicianModel::destroy(['id' => explode(',', $idx)], true);
        return json(['status' => 200, 'msg' => '操作成功']);
    }

    /*
     * @Description  查看详情
     */
    public function detail()
    {
        $id = $this->request->post('id', '', 'serach_in');
        if (!$id) {
            throw new ValidateException('参数错误');
        }

        $withJoin = [
            'technicianalbum' => explode(',', 'id,avatar,album,technician_id,status,create_time'),
            'technicianali' => explode(',', 'id,technician_id,realname,mobile,create_time'),
            'techniciandata' => explode(',', 'id,height,birthday,profile,technician_id,status,create_time,practice,sex'),
            'technicianhealthy' => explode(',', 'id,certificates,technician_id,status,create_time'),
            'technicianidentity' => explode(',', 'id,front,back,technician_id,status,create_time,realname,cardsn'),
            'technicianqualifications' => explode(',', 'id,certificates,technician_id,status,create_time'),
            'techniciantcp' => explode(',', 'id,sign,technician_id,status,create_time'),
        ];

        $field = 'id,nickname,username,realname,start_time,end_time,sex,mobile,avatar,album,status,balance,praise,credit,composite,work_status,sales,service,week,start_time,end_time,tgrade';
        $res = TechnicianModel::field($field)->withJoin($withJoin, 'left')->find($id);
        $service_id = explode(',', $res['service']);
        $res['service'] = ServiceModel::where('id', 'in', $service_id)->select();
        $tglist = Db::name('base_config')->where('name', '=', 'tglist')->find()['data'];
        $res['tgradecount'] = count(json_decode($tglist, true));
        return json(['status' => 200, 'data' => $res]);
    }

    /*
     * @Description  更新状态
     */
    public function updateAuth()
    {
        $data['technician_id'] = $this->request->post('id', '', 'serach_in');
        $model = $this->request->post('model', '', 'serach_in');
        $data['status'] = $this->request->post('status', '', 'serach_in');
        if ($model == "techniciandata") {
            //更新
            $res = TechniciandataModel::where('technician_id', '=', $data['technician_id'])->find();
            $TechnicianModel = TechnicianModel::find($data['technician_id']);
            $TechnicianModel->sex = $res['sex'];
            $TechnicianModel->save();
            TechniciandataModel::where('technician_id', '=', $data['technician_id'])->update($data);
        } elseif ($model == "technicianidentity") {
//更新
            $res = TechnicianidentityModel::where('technician_id', '=', $data['technician_id'])->find();
            $TechnicianModel = TechnicianModel::find($data['technician_id']);
            $TechnicianModel->realname = $res['realname'];
            $TechnicianModel->save();
            TechnicianidentityModel::where('technician_id', '=', $data['technician_id'])->update($data);
        } elseif ($model == "technicianqualifications") {
            TechnicianqualificationsModel::where('technician_id', '=', $data['technician_id'])->update($data);
        } elseif ($model == "technicianhealthy") {
            TechnicianhealthyModel::where('technician_id', '=', $data['technician_id'])->update($data);
        } elseif ($model == "techniciantcp") {
            TechniciantcpModel::where('technician_id', '=', $data['technician_id'])->update($data);
        } elseif ($model == "auth") {
            //通过审核
            sendAuthenticationMsg($data['technician_id'], "", "审核通过");
            TechnicianModel::where('id', '=', $data['technician_id'])->update($data);
        } elseif ($model == "technicianalbum") {
            //更新
            $res = TechnicianalbumModel::where('technician_id', '=', $data['technician_id'])->find();
            $TechnicianModel = TechnicianModel::find($data['technician_id']);
            $TechnicianModel->avatar = $res['avatar'];
            $TechnicianModel->album = $res['album'];
            $TechnicianModel->save();
            TechnicianalbumModel::where('technician_id', '=', $data['technician_id'])->update($data);
        }
        return json(['status' => 200]);
    }

    /*
     * @Description   统计
     */
    public function count()
    {
        $res['num1'] = TechnicianModel::where('status', '=', '1')->count();
        $res['num2'] = TechnicianModel::where('status', '=', '0')->count();
        $res['num3'] = TechnicianModel::where('work_status', '=', '1')->count();
        $res['num4'] = TechnicianModel::where('work_status', '=', '0')->count();
        return json(['status' => 200, 'data' => $res]);
    }

}
