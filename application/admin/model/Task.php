<?php

namespace app\admin\model;

use think\Model;

class Task extends Model
{
    // 表名
    protected $name = 'task';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'start_time_text',
        'end_time_text',
        'running_time_text',
        'finish_time_text',
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'2' => __('Status 2'),'-1' => __('Status -1')];
    }     


    public function getStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['start_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['end_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getRunningTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['running_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFinishTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['finish_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setStartTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setEndTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setRunningTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setFinishTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


    public function shop()
    {
        return $this->belongsTo('Shop', 'shop_id', 'id')->setEagerlyType(0);
    }

    public function countPush($id,$count = 1){
        $this->where('id', $id)->setInc('push_num', $count);
    }


}
