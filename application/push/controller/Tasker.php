<?php

namespace app\push\controller;

use app\admin\model\Shop;
use app\admin\model\Task;
use think\Db;
use think\worker\Server;
use \Workerman\Lib\Timer;

class Tasker extends Server
{
    //任务列表
    private $task_list_shopid = array();

    protected $socket    = '';
    protected $port      = '2347';

    //任务
    public function push_timer() {
        echo "----------------- function  push_timer -----------------\n";
        $task_list = Db::name('task')->where('status', '0')->select();
        if (isset($task_list) && !empty($task_list)) {
//            var_dump($task_list);
            foreach ($task_list as $task) {
                if (abs($task['running_time'] - time()) < 12999) {
                    echo "add  mission \n";
//                Timer::add(10, array($this, 'task'), array(), true);
                    if (isset($this->task_list_shopid[$task['shop_id']]))
                        array_push($this->task_list_shopid[$task['shop_id']], $task['device_id']);
                    else {
                        $this->task_list_shopid[$task['shop_id']] = array();
                        array_push($this->task_list_shopid[$task['shop_id']], $task['device_id']);
                    }
                    Db::name("task")->where('id', $task['id'])->update(['status' => '1']);
                }
            }
        }
        echo "----------------- 任务列表 -----------------\n";
        print_r($this->task_list_shopid);
    }


    public function task() {
        echo "----------------- function task -----------------\n";
        foreach ($this->task_list_shopid as $key => $task_id) {
            echo("task_id $key---------\n");

            $task = Db::name('task')->where('status', '1')->find();//运行中的任务
            if (!$task) {
                echo "miss 不存在";
//                putLog('  [ task任务不存在 ]  id=>'.$task_id);
                unset($this->task_list_shopid[$key]);
//                Db::name("task")->where('id', $task['id'])->update(['status' => '-1']);//任务出现错误
            } else{
                echo 123;
//                print_r($task);

            }

            //获取第$task['push_num']条数据
            echo("获取第{$task['push_num']}条数据");
            /*$mobile = Db::table("fa_mobile")->alias('mobile')
                ->field('mobile.*,device.id as device_id,shop.id as shop_id,shop.appid,shop.secretkey,shop.appsecret,shop.push_count')
                ->join('fa_device device','device.devmac = mobile.devmac')
                ->join('fa_shop shop','shop.id = device.shop_id')
                ->where('shop.id','in',$task_id)
                ->whereTime('mobile.createtime','between',[intval($task['start_time']),intval($task['end_time'])])
                ->limit( intval($task['push_num']),1)
                ->find();*/
            $mobile = null;
            if (count($task_id) > 1) {
                $mobile = Db::table("fa_mobile")->alias('mobile')
                    ->field('mobile.*,device.id as device_id,shop.id as shop_id,shop.shopid as shopid,shop.appid,shop.secretkey,shop.appsecret,shop.push_count')
                    ->join('fa_device device', 'device.devmac = mobile.devmac')
                    ->join('fa_shop shop', 'shop.id = device.shop_id')
                    ->where('device.id', 'in', $task_id)
                    ->whereTime('mobile.createtime', 'between', [intval($task['start_time']), intval($task['end_time'])])
                    ->limit(intval($task['push_num']), 1)
                    ->find();

            } else if (count($task_id) == 1) {
                $mobile = Db::table("fa_mobile")->alias('mobile')
                    ->field('mobile.*,device.id as device_id,shop.id as shop_id,shop.shopid as shopid,shop.appid,shop.secretkey,shop.appsecret,shop.push_count')
                    ->join('fa_device device', 'device.devmac = mobile.devmac')
                    ->join('fa_shop shop', 'shop.id = device.shop_id')
                    ->where('device.id', $task_id[0])
                    ->whereTime('mobile.createtime', 'between', [intval($task['start_time']), intval($task['end_time'])])
                    ->limit(intval($task['push_num']), 1)
                    ->find();

            }

            if ($mobile) {
                print_r($mobile);
                $shop['shop_id']= $mobile['shop_id'];
                $shop['shopid'] = $mobile['shopid'];
                $shop['appid'] = $mobile['appid'];
                $shop['appsecret'] = $mobile['appsecret'];
                $shop['secretkey'] = $mobile['secretkey'];
            } else {
                echo '没有最新数据';
                echo 'mission success';
            }

            //弹起
            //$success = $this->up($mobile,$mobile['shop_id'],$mobile['shopid'],$mobile['appid'],$mobile['appsecret'],$mobile['secretkey']);
            $success = $this->up($mobile,$shop,$key);
            if ($success) {
                $shop_model = new Task();
                $shop_model->countPush($task['id']);//推送数+1

                echo "调用成功 task : {$task['id']}---shop : {$mobile['shop_id']} -- mobile : {$mobile['id']}";
            } else {
                echo "调用失败 task : {$task['id']}---shop : {$mobile['shop_id']} -- mobile : {$mobile['id']}";
            }
        }
    }


    /**
     *弹起
     * $key => task['shop_id']
     */
    public function up($mobile, $shop,$key) {
        $shop_id = $shop['shop_id'];
        $shopid = $shop['shopid'];
        $appid = $shop['appid'];
        $appsecret = $shop['appsecret'];
        $secretkey = $shop['secretkey'];

        echo "========================================= 定时器执行 =========================================  \n";
        if ($mobile) {
            echo "----当前    公众号 shop_id {$shop_id} ---- 设备 mobile_id :  {$mobile['id']} ---\n";
            $ssid = $mobile["ssid"];
            $token = $this->getToken($shop);

            echo "----当前    公众号 shop_id {$shop['shop_id']}  ---- 设备 mobile_id :  {$mobile['id']} ---\n";
            $ssid = $mobile["ssid"];
            $access_token = $this->getToken($shop);
            if ($access_token) {
                $ssid_flag = weixinSSID($shop['shopid'], $access_token, $ssid);
            } else {
                echo "token 错误";
            }
        }

        //开始推送
        if ($mobile && $access_token && $ssid_flag) {
            $mac = $mobile["mac"];
            $ssid = $mobile["ssid"];
            $bssid = $mobile["bssid"];

            $timestamp = getMillisecond();
            $extend = $shop_id . ',' . $mobile['id'];             //参数
            print_r($extend);

            //$authUrl = "http://dev.codewasp.cn/1.php?code=200";        //认证服务端URL
            $authUrl = "http://wifi.8a6.cn/api/wechat/code";        //认证服务端URL

            $url = getUrl($appid, $extend, $timestamp, $shopid, $authUrl, $mac, $ssid, $bssid, $secretkey);
            $res = curlGet($url);
            //try{jsonpCallback({"data":"GetAppInfo Fail","success":false} )}catch(e){};
            $res_arr = json_decode(substr($res, 18, -14), true);
            var_dump($res_arr);
            if ($res_arr['success']) {
                return true;
            } else {
                putLog($res_arr);
                return false;
            }


           /* if($res_arr['success']){
                Db::name("mobile")->where('id', $mobile['id'])->update(['status' => 1]);
            }else{
                Db::name("mobile")->where('id', $mobile['id'])->update(['status' => -1]);
                //记录错误
                putLog("  [执行微信连WiFi出错]  门店名称{$row['name']}  shopid => {$row['shopid']}  extend => {$extend} \n url => $url  \n 接口返回信息 => $res");
                echo "---------- 执行失败 -------\n";
                var_dump($res_arr);
            }*/
        }
        return false;
    }


    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data) {
        $connection->send('我收到你的信息了');
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection) {

    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection) {

    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg) {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker) {
        Db::name("task")->where('id', 1)->update(['status' => '0']);
        echo '设置定时器';
//        Timer::add(10, 'up', array(), true);
//        Timer::add(10, array($mail, 'send'), array($to, $content), false);

        Timer::add(10, array($this, 'push_timer'), array(), true);
        Timer::add(10, array($this, 'task'), array(), true);

//        Timer::add(10, array($this, 'finishpage'), array(), false);

    }


    /*public  function getToken($appid, $appsecret){
        //查询token是否过期
        $shop = Db::name('shop')->where('status', '1')->find();

        //判断是否过期
        if($shop['expires_time']>time()){
            $token = $shop['token'];
            return $token;
        }else{
            $res = getAccessToken($appid, $appsecret);
            var_dump($res);
            if(!isset($res['errorcode'])&&$res['errorcode']==0){
                $token = $res['access_token'];
                Db::name("shop")->where(['appid' => $appid , 'appsecret' => $appsecret ])->update(['status' => '1']);
            }else{
                putLog("  [获取token出错]    appid =>  {$appid}   appsecret => {$appsecret} \n 接口返回信息 => $res");
            }
        }
    }*/
    private function getToken($shop) {

        //判断是否过期
        if (isset($shop['access_token']) && isset($shop['expires_time']) && $shop['expires_time'] > time()) {
            return $shop['access_token'];
        } else {
            //已过期 , 重新获取
            $token_res = getAccessToken($shop['appid'], $shop['appsecret']);
            echo  'token已过期 , 重新获取';
            print_r($token_res);
            if (array_key_exists('errcode', $token_res)) {
                putLog(" [获取token错误] id=> {$shop['id']} 名称 => {$shop['name']}"  , json_encode($token_res));
                if ($token_res['errcode'] != 0) {
                    Db::name("shop")->where(['appid' => $shop['appid'], 'appsecret' => $shop['appsecret']])->update(['status' => -1]);
                }
                return null;
            } elseif (array_key_exists('access_token', $token_res)) {
                //更新token
                Db::name("shop")->where(['appid' => $shop['appid'], 'appsecret' => $shop['appsecret']])->update(['access_token' => $token_res['access_token'], 'expires_time' => $token_res['expires_in'] + time() - 200]);
                return $token_res['access_token'];
            } else {
                putLog(" [获取token出现未知错误]  id=> {$shop['id']} 名称 => {$shop['name']}" , json_encode($token_res));
                return null;
            }
        }
    }


    //设置联网完成页
//    function finishpage() {
//        $shop = Db::name('shop')->select();//运行中的任务
//        foreach ($shop as $row) {
//            echo "----当前公众号 {$row['shopid']} ----\n";
//            $appid = $row['appid'];
//            $appsecret = $row['appsecret'];
//            $access_token = getAccessToken($appid, $appsecret);
//            //查询ssid列表
//            if($row['shopid']){
//                $sendurl = "https://api.weixin.qq.com/bizwifi/finishpage/set?access_token=" . $access_token;
//                $postdata = array('shop_id' => $row['shopid'],'finishpage_url'=>'http://wifi.yawuyu.com');
//                $res_data = curlPost($sendurl, json_encode($postdata));
//                if ($res_data['errcode'] == 0) {
//                    echo "------修改成功{$row['shopid']}------\n";
//                }else{
//                    print_r($res_data);
//                    echo "------error{$row['shopid']}------\n";
//                }
//            }
//            sleep(5);
//        }
//    }


}
