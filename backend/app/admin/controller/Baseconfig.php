<?php
/*
module:        基本配置控制器
create_time:    2022-03-21 17:28:56
author:
contact:
 */

namespace app\admin\controller;

use app\admin\model\Baseconfig as BaseconfigModel;

class Baseconfig extends Admin
{

    /*
     * @Description  基本配置
     */
    public function index()
    {
        $data = $this->request->post();
        $this->validate($data, \app\admin\validate\Baseconfig::class);
        $data['keyword'] = implode(',', $data['keyword']);

        $info = BaseconfigModel::column('data', 'name');
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $info)) {
                BaseconfigModel::field('data')->where(['name' => $key])->update(['data' => $value]);
            } else {
                BaseconfigModel::create(['name' => $key, 'data' => $value, 'remarks' => '']);
            }
        }
        return json(['status' => 200]);
    }

    /*
     * @Description  修改信息之前查询信息的 勿要删除
     */
    public function getInfo()
    {
        $res = BaseconfigModel::column('data', 'name');
        $res['keyword'] = explode(',', $res['keyword']);
        $res['water_alpha'] = (int) $res['water_alpha'];
        return json(['status' => 200, 'data' => $res]);
    }

}
