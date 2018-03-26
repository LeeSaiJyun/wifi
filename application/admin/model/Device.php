<?php

namespace app\admin\model;

use think\Model;

class Device extends Model
{
    // 表名
    protected $name = 'device';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'last_upload_time_text',
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1'),'-1' => __('Status -1')];
    }     


    public function getLastUploadTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['last_upload_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setLastUploadTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


    public function shop()
    {
        return $this->belongsTo('Shop', 'shop_id', 'id')->setEagerlyType(0);
    }



    /*public function mobile()
    {
        return $this->hasMany('mobile','devmac','devmac')->field('id,devmac,mac,ssid,bssid,ip,router_ip,status,push_num');
    }*/
}
