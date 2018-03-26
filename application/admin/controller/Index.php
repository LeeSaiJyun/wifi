<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Validate;
use think\Db;


/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login','test'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 后台首页
     */
    public function index()
    {
        //
        $menulist = $this->auth->getSidebar([
            'dashboard' => 'hot',
                ], $this->view->site['fixedpage']);
        $this->view->assign('menulist', $menulist);
        $this->view->assign('title', __('Home'));
        return $this->view->fetch();
    }



    public function test() {
        if ($this->request->isPost()){
            $id = $this->request->get('id',32,'htmlspecialchars');
            $mac = $this->request->post('mac','','htmlspecialchars');
            $ssid = $this->request->post('ssid','','htmlspecialchars');
            $shop_id = $this->request->post('shop','','htmlspecialchars');
//            $bssid = '08:02:8e:d3:60:86';
            $bssid = 'B8:3A:08:b7:15:00';
            if($shop_id){
                echo $shop_id;
                $shop = Db::name('shop')
                    ->where('shopid',$shop_id)
                    ->find();
            }else{
                echo $shop_id;
                $shop = Db::name('shop')
                    ->where('id',$id)
                    ->find();
            }

            echo "\n</br>";
            if(!$shop){
                echo '门店不存在';
            }else{
                $timestamp = getMillisecond();

                $mobileid = 9999;
                $extend = $shop['id'] . ','. $mobileid;             //参数

                //$authUrl = "http://dev.codewasp.cn/1.php?code=200";        //认证服务端URL
                $authUrl = "http://wifi.8a6.cn/api/wechat/code";        //认证服务端URL

                $access_token = $this->getToken($shop);
                if ($access_token) {
                    $add_ssid = weixinSSID($shop['shopid'], $access_token, $ssid);
                    if($add_ssid){
                        sleep(2);

                        $url = getUrl($shop['appid'], $extend, $timestamp, $shop['shopid'], $authUrl, $mac, $ssid, $bssid, $shop['secretkey']);

                        $res = curlGet($url);
                        $res_arr = json_decode(substr($res, 18, -14), true);
                        if ($res_arr['success']) {
                            echo '已执行';
                        } else {
                            echo $mac . "  " . $ssid . ' 执行失败';
                        }
                    }
                } else {
                    echo "token 错误";
                }

/*
                if ($access_token && $add_ssid) {
                    $url = getUrl($shop['appid'], $extend, $timestamp, $shop['shopid'], $authUrl, $mac, $ssid, $bssid, $shop['secretkey']);

                    $res = curlGet($url);
                    $res_arr = json_decode(substr($res, 18, -14), true);
                    if ($res_arr['success']) {
                        echo '已执行';
                    } else {
                        echo $mac . "  " . $ssid . ' 执行失败';
                    }
                }*/
            }

        }
        $this->view->assign('title', __('Home'));
        return $this->view->fetch();
    }
    private function getToken($shop) {

        //判断是否过期
        if (isset($shop['access_token']) && isset($shop['expires_time']) && $shop['expires_time'] > time()) {
            return $shop['access_token'];
        } else {
            //已过期 , 重新获取
            $token_res = getAccessToken($shop['appid'], $shop['appsecret']);
            if (array_key_exists('errcode', $token_res)) {
                Log::error(' [获取token错误] ' . json_encode($token_res));
                if ($token_res['errcode'] != 0) {
                    Db::name("shop")->where(['appid' => $shop['appid'], 'appsecret' => $shop['appsecret']])->update(['status' => -1]);
                }
                return false;
            } elseif (array_key_exists('access_token', $token_res)) {
                //更新token
                Db::name("shop")->where(['appid' => $shop['appid'], 'appsecret' => $shop['appsecret']])->update(['access_token' => $token_res['access_token'], 'expires_time' => $token_res['expires_in'] + time() - 200]);
                return $token_res['access_token'];
            } else {
                Log::error(' [获取token出现未知错误] ' . $shop['id'] . json_encode($token_res));
                return false;
            }
        }
    }


    /**
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin())
        {
            $this->error(__("You've logged in, do not login again"), $url);
        }
        if ($this->request->isPost())
        {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            $validate = new Validate($rule);
            $result = $validate->check($data);
            if (!$result)
            {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            \app\admin\model\AdminLog::setTitle(__('Login'));
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true)
            {
                $this->success(__('Login successful'), $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            }
            else
            {
                $this->error(__('Username or password is incorrect'), $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin())
        {
            $this->redirect($url);
        }
        \think\Hook::listen("login_init", $this->request);
        return $this->view->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'), 'index/login');
    }

}
