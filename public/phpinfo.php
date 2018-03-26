<?php
//$shopid = '991459';
//$appid = 'wx2c8781dda56b9370';
//$appsecret = '61263a5a22022bced6db7d857c6baef6';
//$secretkey = 'ca161a61d6fecb1d3e64d505e51f5903';
//
$shopid = '880472';
$appid = 'wx75236167258e1c1e';
//$appsecret = '93f92b93be05021ae802b51ff1f99336';
$secretkey = '93d52e4245b20a95b43e359699902ea1';


/*$shopid = '1035059';
$appid = 'wxb9f3491519a66d33';
$appsecret = '2f782370228d2b1fbca4f11b9d657573';
$secretkey = '432a3324f3c6f7df7b56b3748ef7e9a2';*/


$ssid = "888888";


$timestamp=getMillisecond();
$extend = 123;
$mac = "c0:ee:fb:d6:eb:78";
$bssid = "c0:ee:fb:d6:eb:78";
$authUrl = "http://baidu.com";
$url = getUrl($appid , $extend , $timestamp , $shopid , $authUrl , $mac , $ssid , $bssid , $secretkey);
$res = curlGet($url);
//try{jsonpCallback({"data":"GetAppInfo Fail","success":false} )}catch(e){};
$res_arr = json_decode(substr($res,18,-14),true);
var_dump($res_arr);





function getUrl($appid , $extend , $timestamp , $shop_id , $authUrl , $mac , $ssid , $bssid , $secretkey){
    $sign = md5($appid . $extend . $timestamp . $shop_id . $authUrl . $mac . $ssid . $bssid . $secretkey);
    $base_url = 'http://wifi.weixin.qq.com/operator/callWechat.xhtml?'.
        'appId=' .$appid .
        '&extend=' .$extend.
        '&timestamp=' .$timestamp.
        '&sign=' .$sign.
        '&shopId=' .$shop_id.
        '&authUrl=' .$authUrl.
        '&mac=' .$mac.
        '&ssid=' .urlencode($ssid).
        '&bssid='.$bssid ;
    return $base_url;
}



/*
//清空ssid列表
$sendurl = 'https://api.weixin.qq.com/bizwifi/shop/clean?access_token=' . $access_token;
$postdata = array(
    'shop_id' => $shopid
);
$add = curlPost($sendurl, json_encode($postdata));
echo "------清空ssid列表----$shopid-----\n";
print_r($add);


//查询ssid列表
$sendurl = "https://api.weixin.qq.com/bizwifi/shop/list?access_token=" . $access_token;
//$postdata = array('shop_id' => $shopid);
$postdata = array('pageindex' => 1,'pagesize' => 20);
$shop = curlPost($sendurl, json_encode($postdata));

if ($shop['errcode'] == 0) {
    echo "------查询ssid列表---------\n";
    print_r($shop['data']);
}*/

/*
//修改ssid
$sendurl = "https://api.weixin.qq.com/bizwifi/shop/update?access_token=" . $access_token;
$postdata = array('shop_id' => $shopid,'old_ssid' => '8B','ssid' => '123',);
$shop = curlPost($sendurl, json_encode($postdata));
echo "------修改ssid---------\n";
print_r($shop);*/



function weixinSSID($shopid,$appid,$appsecret,$secretkey,$ssid){
    $access_token = getAccessToken($appid, $appsecret);
    //查询ssid列表
    $sendurl = "https://api.weixin.qq.com/bizwifi/shop/get?access_token=" . $access_token;
    $postdata = array('shop_id' => $shopid);
    $shop = curlPost($sendurl, json_encode($postdata));
    if ($shop['errcode'] == 0) {
        print_r($shop);
        $ssid_list = $shop['data']['ssid_list'];
        echo "------查询ssid列表---------\n";
        print_r($ssid_list);
    }



    /*
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
    */

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
        print_r($add);
    }


}


function getAccessToken1($appid,$appsecret) {
    $sendurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
    $data=json_decode(curlGet($sendurl),true);
    return $data['access_token'];
}

function curlGet($url) {
    //初始化
    $ch = curl_init();

    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);    // 发起连接前等待超时的时间，如果设置为0，则无限等待
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




//时间戳 毫秒级
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}



/*function curlRequest($url , array $headers=array())
{
    // 1.初始化一个curl会话资源
    $ch = curl_init();

    // 2.设置curl会话的选项
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);    // 强制使用 HTTP/1.0
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);    // 发起连接前等待超时的时间，如果设置为0，则无限等待
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);          // 设置curl允许执行的最长秒数
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // 是否将curl_exec()获取的信息返回，而不是直接输出
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');    // 设置HTTP请求头中"Accept-Encoding: "的值
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    // 启用时会将服务器返回的"Location: "放在header中递归的返回给服务器
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);         // 设置HTTP重定向的最大数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 是否需要进行服务端的SSL证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // 是否验证服务器SSL证书中的公用名
    curl_setopt($ch, CURLOPT_HEADER, false);        // 是否抓取头文件的信息
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        // 设置HTTP请求头
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);



    curl_setopt($ch, CURLOPT_URL, $url);    // 设置需要请求的URL地址，也可以在 curl_init()函数中设置

    // 3.执行curl会话
    $response = curl_exec($ch);

    // 4.关闭curl会话，释放资源
    curl_close($ch);

    return $response;
}*/
