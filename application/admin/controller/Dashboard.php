<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    protected $dataLimit = 'auth'; //默认基类中为false，表示不启用，可额外使用auth和personal两个值
//    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段

    /**
     * 查看
     */
    public function index()
    {

        //数据图显示{$num}天前的数据量
        $num = 13;
        $seventtime = \fast\Date::unixtime('day', 1+$num*-1);
        $sql = "select count(id) as total, FROM_UNIXTIME(createtime, '%Y-%m-%d') as time 
                    from fa_userinfo where createtime>= '".$seventtime."' and createtime < '".time()."' group by time;  ";

        $dataList = Db::query($sql);
        $sendlist = array_reduce($dataList, create_function('$v,$w', '$v[$w["time"]]=$w["total"];return $v;'));

        $sql = "select count(id) as total, FROM_UNIXTIME(createtime, '%Y-%m-%d') as time 
                    from fa_mobile where createtime>= '".$seventtime."' and createtime < '".time()."' group by time;";
        $dataList = Db::query($sql);
        $mobilelist = array_reduce($dataList, create_function('$v,$w', '$v[$w["time"]]=$w["total"];return $v;'));

        $sendList = $mobileList = [];
        for ($i = 0; $i < $num; $i++)
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $sendList[$day] = array_key_exists($day,$sendlist) ? $sendlist[$day] : 0;
            $mobileList[$day] = array_key_exists($day,$mobilelist)  ? $mobilelist[$day] : 0;
//            $createlist[$day] = Db::table('shebeimac')->where('time','between time',[$day,$nextday])->count();
//            $paylist[$day] = Db::table('errorlog')->where('time','between time',[$day,$nextday])->fetchSql()->count();
        }

        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';

//        SELECT COUNT(*) AS tp_count FROM `fa_mobile` WHERE `devmac` IN ( SELECT `devmac` FROM `fa_device` WHERE `admin_id` = 1 GROUP BY `devmac` ) LIMIT 1
        $totaluser = Db::name('mobile')
            ->where('devmac','IN',function($query){
                $query->name('device')->where('admin_id','IN',$this->getDataLimitAdminIds())->group('devmac')->field('devmac');
            })
            ->count();

        $totalsends = Db::name('push_log')->count();
        $totalsends = Db::name('mobile')
            ->where('devmac','IN',function($query){
                $query->name('device')->where('admin_id','IN',$this->getDataLimitAdminIds())->group('devmac')->field('devmac');
            })
            ->where('status','IN','-1,1')
            ->count();
        $ids = $this->auth->getChildrenAdminIds();
        array_push($ids,$this->auth->id);
        $totaldev = Db::name('device')->where('admin_id','IN',$ids)->group('devmac')->count();

        $todaysends = Db::name('push_log')->whereTime('createtime', 'today')->count();
        $this->view->assign([
            'totaluser'        => $totaluser,
            'totalsends'       => $totalsends,
            'totaldev'         => $totaldev,
            'todaysends'       => $todaysends,
            'sendlist'         => $sendList,
            'mobilelist'       => $mobileList,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }


}
