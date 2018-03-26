<?php

namespace app\admin\model;

use think\Model;

class Shop extends Model
{
    // 表名
    protected $name = 'shop';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    // 追加属性
    protected $append = [
        'expires_time_text',
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'-1' => __('Status -1')];
    }     


    public function getExpiresTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['expires_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setExpiresTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id')->setEagerlyType(0);
    }

    public function device()
    {
        return $this->hasMany('Device','shop_id')->field('id,author,content');
    }

    public function countPush($id,$count = 1){
        $this->where('id', $id)->setInc('push_count', $count);
    }

    public function countSuccess($id,$count = 1){
        $this->where('id', $id)->setInc('success_count', $count);
    }


}
