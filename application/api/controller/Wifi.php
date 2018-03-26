<?php

namespace app\api\controller;

use think\Db;
use think\Log;
use think\Request;
use think\Validate;

class Wifi
{
    private $token = '58ebcc1806a4454aa7724f583085b996';

    //wifi回调
    public function Connect(){
        $post_data = Request::instance()->only(['wxuid','hasphone','lgnid','ssid','ssid','signature','timestamp','nonce','echostr'],'post');
//        $post_data = input();
        Log::info(json_encode($post_data));
        if(!$post_data){
            return json(["state" => "0" ,"reason" => "post data is null"]);
        }else
            return json(["state" => "1" ,"data" => $post_data]);
        $check = $this->checkSignature();
        if($check) {
            $insert = Db::table('think_user')->insert($post_data);
            if($insert){
                return json([
                    "state" => "0" ,
                    "login" => "http://192.168.60.1:3990/logon?key=QgYnkgYSBwZXJzZXZlcmFuY2CBpbiB0aG",
                    "logout" => "http://192.168.60.1:3990/logoff",
                    "device_no" => "991459", //必填,AP 设备的序列号
                    "store_name" => "中基商务大厦",           //必填
                    "store_id" => "320123", //必填
                    "client_mac" => "84:3A:4B:DC:39:B8",    // 手机的 MAC 地址 //必填
                    "addition_mac" => "84:3A:4B:DC:39:B9",  // 这台被扫描 PC / PAD 的 MAC 地址
                    "login_state" => 0,                     // 0 表示已登入 -1 表示未登入状态
                    "login_remain" => 600,                  // 单位秒。用户上次连接剩余上网时间（不会有小于 0 的情况）
                    "login_allow" => 600,                   // 单位秒。此次认证通过的新增允许上网时间。
                    "echostr" => $post_data['echostr'] //输入原样返回
                ]);

            }else{
                return ["state" => "0" ,"reason" => "system error"];
            }
        }else{
            return ["state" => "0" ,"reason" => "Invalid signature"];
        }


    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


}
