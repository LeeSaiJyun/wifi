<?php

namespace app\api\controller;

use app\admin\model\Mobile;
use app\admin\model\Shop;
use app\common\controller\Backend;
use think\Db;
use think\Log;
use think\Request;
use think\Validate;

load_trait('controller/Jump');  // 引入traits\controller\Jump

class Wechat
{
    use \traits\controller\Jump;

    private $token = '58ebcc1806a4454aa7724f583085b996';//微信后台

    public function log() {
        if($this->checkSignature()){
            $post_data = input();
            Log::info(json_encode($post_data));
        }
            return null;
    }

    public function confirm() {
        $flag = $this->checkSignature();
        if($flag){
            echo $_GET["echostr"];
        }
    }

    //wifi 认证服务端URL  (定时任务回调用)
    /** 微信弹起成功后的回调 (统计成功后的数据)
     *        array (
     *          'extend' => $shop_id,
     *          'openId' => 'opwFW0yVqHJDHilPzz-nasz-DslU',
     *          'tid' => '010002ed1b0cd270fe27c6cfc7a1abfdfea403eef69ab8f134daef',
     *          'timestamp' => '1515407390',
     *          'sign' => '2edf501f8ebb0f8550a845b408d55187',
     *          )
     */
    public function code(){
        /**/

        try{
            $info = Request::instance()->get(); // 获取所有的get变量（经过过滤的数组）
            list($shop_id,$mobile_id) = explode(',',$info['extend']);
            $userinfo = Db::name('userinfo')->where([
                'shop_id'=>$shop_id,
                'openid'=>$info['openId'],
            ])->find();
            if(!$userinfo){
                Db::name('userinfo')->insert(
                    [
                        'shop_id'=>$shop_id,
                        'openid'=>$info['openId'],
                        'tid'=>$info['tid'],
                        'createtime'=>time(),
                        'updatetime'=>time(),
                        'mobile_id'=> $mobile_id
                    ]
                );
            }else{
                Db::name('userinfo')->where('id',$userinfo['id'])->update(
                    [
                        'updatetime'=>time(),
                        'mobile_id'=> $mobile_id,
                        'success_count'=>$userinfo['success_count']+1
                    ]
                );
            }
            //统计数据
            $shop_model = new Shop();
            $shop_model->countSuccess($shop_id);//推送数+1

            $mobile_model = new Mobile();
            $mobile_model->countPush($mobile_id);//推送数+1
        }catch(\Exception $e){
            Log::error("  [ 微信弹起成功后的回调 ] ***失败***  shop_id => {$shop_id}   mobile_id => {$mobile_id}");
        }

        $this->result(null,200,'success');

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
