<?php

namespace app\admin\model;

use think\Model;

class PushLog extends Model
{
    // 表名
    protected $name = 'push_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [

    ];
    

    







    public function shop()
    {
        return $this->belongsTo('Shop', 'shop_id', 'id')->setEagerlyType(0);
    }


    public function countPush($shop_id,$count = 1){
        $this->where('shop_id', $shop_id)->where()->setInc('push_num', $count);
    }

    public function countSuccess($shop_id,$count = 1){
        $this->where('shop_id', $shop_id)->setInc('success_num', $count);
    }



}
