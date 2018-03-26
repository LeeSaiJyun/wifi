<?php

namespace app\admin\model;

use think\Model;

class Mobile extends Model
{
    // 表名
    protected $name = 'mobile';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text',
        'last_push_time_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'-1' => __('Status -1')];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getLastPushTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['last_push_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setLastPushTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


    public function device()
    {
        return $this->belongsTo('Device', 'devmac', 'devmac')->setEagerlyType(0);
    }

    public function countPush($id,$count = 1){
        $this->where('id', $id)->setInc('push_num', $count);
    }

}
