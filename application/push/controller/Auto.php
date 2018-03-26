<?php

namespace app\push\controller;

use app\admin\model\Shop;
use think\Db;
use think\worker\Server;
use \Workerman\Lib\Timer;


class Auto extends Server
{

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
        echo 'WorkerStart启动';
        Timer::add(15, array($this, 'auto'), array(), true);
    }


    //自动推送
    function auto() {
        echo "========================================= 定时器执行 =========================================  \n";
        $shop = Db::name('shop')->where("status", "1")->select();
        //print_r($shop);

        foreach ($shop as $row) {
            //echo "----当前公众号  {$row['name']}  shopid => {$row['shopid']} ----";
            //获取对应第一条数据
            $mobile = Db::table("fa_device")->alias('device')
                ->field('mobile.*,device.shop_id')
                ->join('fa_mobile mobile', 'device.devmac = mobile.devmac')
                ->where('mobile.status', '0')
                ->where('device.status', '1')
                ->where('device.shop_id', $row['id'])
//                ->fetchSql()
                ->find();
//            print_r($mobile);
            //开始 推送
            if ($mobile) {
                $res_status = $this->up($mobile, $row);
            }
        }
    }


    /**
     * 弹起
     */
    public function up($mobile, $shop) {
        if ($mobile) {
            echo "----当前    公众号 shop_id {$shop['id']} 名称 {$shop['name']}  ---- 设备 mobile_id :  {$mobile['id']} ---\n";
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
            $timestamp = getMillisecond();
            $extend = $shop['id'] . ',' . $mobile['id'];             //参数

            //$authUrl = "http://dev.codewasp.cn/1.php?code=200";        //认证服务端URL
            $authUrl = "http://wifi.8a6.cn/api/wechat/code";        //认证服务端URL

            $url = getUrl($shop['appid'], $extend, $timestamp, $shop['shopid'], $authUrl, $mobile["mac"], $mobile["ssid"], $mobile["bssid"], $shop['secretkey']);
            $res = curlGet($url);
            //try{jsonpCallback({"data":"GetAppInfo Fail","success":false} )}catch(e){};
            $res_arr = json_decode(substr($res, 18, -14), true);
            //var_dump($res_arr);
            if ($res_arr['success']) {
                Db::name("mobile")->where('id', $mobile['id'])->update(['status' => 1,'last_push_time'=>time()]);
                $shop_model = new Shop();
                $shop_model->countPush($shop['id']);//推送数+1
                echo("{$shop['id']} 计数++++++++++++++++++++++++++\n");
            } else {
                Db::name("mobile")->where('id', $mobile['id'])->update(['status' => -1,'last_push_time'=>time()]);
                echo "执行失败!!!!!!!!!\n";
                var_dump($res_arr);
                putLog('[执行微信连WiFi出错]',"门店名称{$shop['name']}  shopid => {$shop['shopid']}  extend => {$extend} \n url => $url  \n 接口返回信息 => $res");
            }
        }else if($mobile && $access_token && !$ssid_flag){
            Db::name("mobile")->where('id', $mobile['id'])->update(['status' => -1,'last_push_time'=>time()]);
            Timer::add(8, array($this, 'waitSsid'), array($mobile,$shop), false);
            echo 'waitSsid';
        }
    }

    public function waitSsid($mobile, $shop) {

        $timestamp = getMillisecond();
        $extend = $shop['id'] . ',' . $mobile['id'];             //参数

        //$authUrl = "http://dev.codewasp.cn/1.php?code=200";        //认证服务端URL
        $authUrl = "http://wifi.8a6.cn/api/wechat/code";        //认证服务端URL

        $url = getUrl($shop['appid'], $extend, $timestamp, $shop['shopid'], $authUrl, $mobile["mac"], $mobile["ssid"], $mobile["bssid"], $shop['secretkey']);
        $res = curlGet($url);
        //try{jsonpCallback({"data":"GetAppInfo Fail","success":false} )}catch(e){};
        $res_arr = json_decode(substr($res, 18, -14), true);
        if ($res_arr['success']) {
            Db::name("mobile")->where('id', $mobile['id'])->update(['status' => 1]);
            $shop_model = new Shop();
            $shop_model->countPush($shop['id']);//推送数+1
        } else {
            var_dump($res_arr);
            putLog('[执行微信连WiFi出错waitSsid]',"门店名称{$shop['name']}  shopid => {$shop['shopid']}  extend => {$extend} \n url => $url  \n 接口返回信息 => $res");
        }
    }

    private function getToken($shop) {

        //判断是否过期
        if (isset($shop['access_token']) && isset($shop['expires_time']) && $shop['expires_time'] > time()) {
            return $shop['access_token'];
        } else {
            //已过期 , 重新获取
            $token_res = getAccessToken($shop['appid'], $shop['appsecret']);
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


    //设置所有的shop的联网完成页
    /*private function finishpage() {
        $link = 'http://wifi.yawuyu.com';
        $shop = Db::name('shop')->select();//运行中的任务
        foreach ($shop as $row) {
            echo "----当前公众号 {$row['shopid']} ----\n";
            $appid = $row['appid'];
            $appsecret = $row['appsecret'];
            $access_token = getAccessToken($appid, $appsecret);
            //联网完成页
            if ($row['shopid']) {
                $sendurl = "https://api.weixin.qq.com/bizwifi/finishpage/set?access_token=" . $access_token;
                $postdata = array('shop_id' => $row['shopid'], 'finishpage_url' => $link);
                $res_data = curlPost($sendurl, json_encode($postdata));
                if ($res_data['errcode'] == 0) {
                    echo "------修改成功{$row['shopid']}------\n";
                } else {
                    print_r($res_data);
                    echo "------error{$row['shopid']}------\n";
                }
            }
            sleep(5);
        }
    }*/

}





















