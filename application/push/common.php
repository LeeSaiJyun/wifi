<?php

/**
 * @param $msg string 消息
 * @param $err string 错误信息
 */
function putLog($msg, $err) {
    $path = RUNTIME_PATH . '/workmanlog/';
    $Log = '    [ ' . date('Y-m-d H:i:s', time()) . ' ]    ' . $msg . "\n";
    $Log .= $err;
    if (!is_dir($path))
        @mkdir($path, 0755, true);
    file_put_contents($path . date("y-m-d") . "-log.txt", $Log . PHP_EOL, FILE_APPEND);

}


/**
 * @param $shopid 门店号
 * @param $access_token token
 * @param $ssid ssid
 * @return bool
 */
function weixinSSID($shopid, &$access_token, $ssid) {
    $flag = true;
    //查询ssid列表
    $sendurl = "https://api.weixin.qq.com/bizwifi/shop/get?access_token=" . $access_token;
    $postdata = array('shop_id' => $shopid);
    $shop = curlPost($sendurl, json_encode($postdata));
    if ($shop['errcode'] == 0) {
        $ssid_list = $shop['data']['ssid_list'];
        echo "------查询ssid列表-----" . count($ssid_list) . "----\n";
//        print_r($ssid_list);
    } elseif ($shop['errcode'] == 40001){
        echo "------需要重新获取token----   access_token is invalid or not latest  -----\n";
        //更新token
        \think\Db::name("shop")->where(['shopid' => $shopid])->update(['access_token' => '', 'expires_time' => '']);
        return false;
    }else {
        echo "------查询ssid列表----error-----\n";
        putLog("[查询ssid列表] 门店号{$shopid}", json_encode($shop));
//        print_r($shop);
        \think\Db::name("shop")->where(['shopid' => $shopid])->update(['status' => -1]);
        return false;
    }

    if (count($ssid_list) >= 90) {
        //清空ssid列表
        $sendurl = 'https://api.weixin.qq.com/bizwifi/shop/clean?access_token=' . $access_token;
        $postdata = array(
            'shop_id' => $shopid
        );
        $add = curlPost($sendurl, json_encode($postdata));
        echo "------清空ssid列表----$shopid-----\n";
        print_r($add);
    }

    if (!in_array($ssid, $ssid_list)) {
        //添加ssid
        $sendurl = 'https://api.weixin.qq.com/bizwifi/apportal/register?access_token=' . $access_token;
        $postdata = array(
            'shop_id' => $shopid,
            'ssid' => $ssid,
            'reset' => false
        );
        $add = curlPost($sendurl, json_encode($postdata));
        echo "--------添加ssid-------\n";
//        print_r($add);
        $flag = false;
    }
    return $flag;
}


function getAccessToken($appid, $appsecret) {
    $sendurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
    $data = json_decode(curlGet($sendurl), true);
    return $data;
}

function curlGet($url) {
    //初始化
    $ch = curl_init();

    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);    // 发起连接前等待超时的时间，如果设置为0，则无限等待
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);          // 设置curl允许执行的最长秒数
    curl_setopt($ch, CURLOPT_HEADER, 0);

    //执行并获取HTML文档内容
    $output = curl_exec($ch);

    //释放curl句柄
    curl_close($ch);

    return $output;
}

function curlPost($url, $post_data = array()) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);

    return json_decode($output, true);
}


//时间戳 毫秒
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

function getUrl($appId, $extend, $timestamp, $shop_id, $authUrl, $mac, $ssid, $bssid, $secretkey) {
    $sign = md5($appId . $extend . $timestamp . $shop_id . $authUrl . $mac . $ssid . $bssid . $secretkey);
    $base_url = 'http://wifi.weixin.qq.com/operator/callWechat.xhtml?' .
        'appId=' . $appId .
        '&extend=' . $extend .
        '&timestamp=' . $timestamp .
        '&sign=' . $sign .
        '&shopId=' . $shop_id .
        '&authUrl=' . $authUrl .
        '&mac=' . $mac .
        '&ssid=' . urlencode($ssid) .
        '&bssid=' . $bssid;
    return $base_url;
}
