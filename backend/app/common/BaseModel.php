<?php


namespace app\common;

use think\facade\Config;
use think\facade\Db;
use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    private static $errorMsg;
    private static $transaction = 0;
    private static $DbInstance = [];
    const DEFAULT_ERROR_MSG = '操作失败,请稍候再试!';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * 设置错误信息
     * @param string $errorMsg
     * @return bool
     */
    protected static function setErrorInfo($errorMsg = self::DEFAULT_ERROR_MSG, $rollback = false)
    {
        if ($rollback) self::rollbackTrans();
        self::$errorMsg = $errorMsg;
        return false;
    }

    /**
     * 获取错误信息
     * @param string $defaultMsg
     * @return string
     */
    public static function getErrorInfo($defaultMsg = self::DEFAULT_ERROR_MSG)
    {
        return !empty(self::$errorMsg) ? self::$errorMsg : $defaultMsg;
    }

    /**
     * 开启事务
     */
    public static function beginTrans()
    {
        Db::startTrans();
    }

    /**
     * 提交事务
     */
    public static function commitTrans()
    {
        Db::commit();
    }

    /**
     * 关闭事务
     */
    public static function rollbackTrans()
    {
        Db::rollback();
    }

    /**
     * 根据结果提交滚回事务
     * @param $res
     */
    public static function checkTrans($res)
    {
        if ($res) {
            self::commitTrans();
        } else {
            self::rollbackTrans();
        }
    }

    public static function getList($where = array(), $pageSize=10, $order = ['sort', 'id' => 'desc'])
    {
        return self::where($where)->where($order)->select();

    }

    public static function getOne($id)
    {

        return self::find($id);
    }

    //表前缀
    public static function get_table_prefix(){

        return Config::get('database.connections.mysql.prefix');


    }

    public function add($data){
        $result = $this->save($data);
        if($result) {
            return $result;
        } else {
            return '';
        }
    }

    public function edit($data){
        $info  = $this->find($data['id']);
        unset($data['id']);
        $result = $info->save($data);
        if($result) {
            return $result;
        } else {
            return '';
        }
    }

    public function del($ids){
        if(is_array($ids)){

            $info  = $this->where('id','in',$ids)->select();
        }else{
            $info  = $this->find($ids);
        }
        $result = $info->delete();
        if($result) {
            return $result;
        } else {
            return '';
        }
    }

    public function state($data){
        $id = $data['id'];
        $field =  $data['field'];
        if (empty($id)) {
            return '';
        }
        $info = $this->find($id);
        $info->$field = $info->$field == 1 ? 0 : 1;
        $result = $info->save();
        if($result){
            return $result;
        }else{
            return '';
        }

    }


}