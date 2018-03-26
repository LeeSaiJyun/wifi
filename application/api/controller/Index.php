<?php

namespace app\api\controller;

use app\admin\model\Mobile;
use fast\Random;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;

load_trait('controller/Jump');  // 引入traits\controller\Jump

class Index
{

    use \traits\controller\Jump;

    private $key = 'api_8a6_cn';

    public function index() {
        $input = file_get_contents("php://input");
        //最大10KB
        if (strlen($input) / 1024 <= 10) {
            $input = rc4($this->key, $input);
            $time = date('Y-m-d H:i:s', time());
            Db::table('fa_device_log')->insert(['text' => $input, 'time' => $time]);
            $data = json_decode($input, true);

            $flag = 1;
            $time = date('Y-m-d H:i:s', time());

            /*if (!$input) {
                $input = '{"devmac":"90:50:5A:55:2C:08","devip":"10.143.109.174","time":211,"data":[{"mac":"ff:ff:ff:ff:ff:ff","ssid":"test","bssid":"ff:ff:ff:ff:ff:ff"}]}';
                $data = json_decode($input, true);
            }*/

            try {
                Db::name('device')->where(['devmac' => $data['devmac']])->update(['last_upload_time' => time()]);
            } catch (\Exception $e) {
                Log::error("  [ 设备更新状态 ] =失败= devmac => {$data['devmac']}");
            }


            if (is_array($data['data'])) {
//                $allow_ssid = Db::name('device_filter')->where(['devmac' => $data['devmac'], 'status' => '1'])->value('allow');
//                $allow_ssid_arr = explode(',', $allow_ssid);
                foreach ($data['data'] as $value) {
                    $result = Db::table('fa_mobile')->insert(
                        [
                            'devmac' => $data['devmac'],
                            'ip' => $data['devip'],
                            'router_ip' => Request::instance()->ip(),
                            'time' => $time,
                            'mac' => $value['mac'],
                            'ssid' => $value['ssid'],
                            'bssid' => $value['bssid'],
                            'createtime' => time(),
                            'updatetime' => time(),
                        ]
                    );
                    if ($result == 1) {
                        $flag = 1;
                    };

                }

//                foreach ($data['data'] as $value) {
//                    $data = [
//                        'devmac' => $data['devmac'],
//                        'ip' => $data['devip'],
//                        'router_ip' => Request::instance()->ip(),
//                        'time' => $time,
//                        'mac' => $value['mac'],
//                        'ssid' => $value['ssid'],
//                        'bssid' => $value['bssid'],
//                        'createtime' => time(),
//                        'updatetime' => time(),
//                    ];
//                    if (is_array($allow_ssid_arr) && count($allow_ssid_arr) > 0 ) {
//                        if(in_array($value['ssid'], $allow_ssid_arr)){
//                            $data['status'] = '0';
//                        }else{
//                            $data['status'] = '1';
//                        }
//                        $result = Db::table('fa_mobile')->insert($data);
//                        if(!$result)
//                            $flag = 0;
//                    } else {
//                        $result = Db::table('fa_mobile')->insert($data);
//                        if(!$result)
//                            $flag = 0;
//                    }
//                }
            }

            if ($flag == 1) {
                $this->api(0, 'success', 'json');
            } else {
                $this->api(1, 'error', 'json');
            }
        }
    }

    public function test() {
        /*echo $count = Db::name("mobile")->count();
        for($i =1 ;$i<$count;$i++){
            $data = Db::name("mobile")->field('id,time')->where('id',$i)->find();
            if($data['id'])
                Db::name("mobile")->where('id',$data['id'])->update(['createtime' => strtotime($data['time'])]);
        }*/
        $shop_id = 1;
        $mobile['id'] = 11235;
        $extend = $shop_id . ',' . $mobile['id'];
        echo $extend;
        list($shop_id, $mobile_id) = explode(',', $extend);
        var_dump(explode(',', $extend));
        var_dump($shop_id, $mobile_id);
    }


/*    public function count() {
        echo $count = Db::name("mobile")->where('devmac', 'c8:ee:a6:35:14:67')->count() + 100;
    }*/

    public function insert() {
        $count = 0;
        for ($i = 0; $i <= 200; $i++) {
            $res = Db::name('mobile')->insert([
                'devmac' => '90:50:5a:55:2c:58',
                'mac' => 'c0:ee:fb:d6:eb:78',
                'ssid' => '888888',
                'bssid' => '08:02:8e:d3:60:86',
                'ip' => '223.74.180.20',
                'router_ip' => '',
                'time' => '2017-12-23 09:37:47',
                'status' => '1',
            ]);
            if ($res) {
                $count++;
            }
        }
        echo $count;
    }

    /*//从dblog日志导入数据
    public function import() {
        $log = Db::table('dblog')->where('time','>','2018-01-05 02:00:00')->select();
        $num = $row =  0;
        foreach ($log as $row){
            $time = $row['time'];
            $data = json_decode($row['text'], true);
            if (is_array($data['data'])) {
                foreach ($data['data'] as $value) {
//                    $sql = "INSERT INTO shebeimac(devmac,ip,time,mac,ssid,bssid) VALUES ('{$data['devmac']}','{$data['devip']}','{$time}','{$value['mac']}','{$value['ssid']}','{$value['bssid']}');";
                    $result = Db::table('shebeimac')->insert(
                        [
                            'devmac' => $data['devmac'],
                            'ip' => $data['devip'],
                            'router_ip' => Request::instance()->ip(),
                            'time' => $time,
                            'mac' => $value['mac'],
                            'ssid' => $value['ssid'],
                            'bssid' => $value['bssid']
                        ]
                    );
                    if ($result == 1) {
                        $row ++;
                    };
                }
            }
            $num ++;
        }
    }*/

    /*public function checkToken() {
//        $post_data = Request::instance()->only(['wxuid','hasphone','lgnid','ssid','ssid','signature','timestamp','nonce','echostr'],'post');
        $post_data = input();
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
        
    }*/


}

