<?php

use app\common\model\Category;
use fast\Form;
use fast\Tree;
use think\Db;

/**
 * 生成下拉列表
 * @param string $name
 * @param mixed $options
 * @param mixed $selected
 * @param mixed $attr
 * @return string
 */
function build_select($name, $options, $selected = [], $attr = [])
{
    $options = is_array($options) ? $options : explode(',', $options);
    $selected = is_array($selected) ? $selected : explode(',', $selected);
    return Form::select($name, $options, $selected, $attr);
}

/**
 * 生成单选按钮组
 * @param string $name
 * @param array $list
 * @param mixed $selected
 * @return string
 */
function build_radios($name, $list = [], $selected = null)
{
    $html = [];
    $selected = is_null($selected) ? key($list) : $selected;
    $selected = is_array($selected) ? $selected : explode(',', $selected);
    foreach ($list as $k => $v)
    {
        $html[] = sprintf(Form::label("{$name}-{$k}", "%s {$v}"), Form::radio($name, $k, in_array($k, $selected), ['id' => "{$name}-{$k}"]));
    }
    return '<div class="radio">' . implode(' ', $html) . '</div>';
}

/**
 * 生成复选按钮组
 * @param string $name
 * @param array $list
 * @param mixed $selected
 * @return string
 */
function build_checkboxs($name, $list = [], $selected = null)
{
    $html = [];
    $selected = is_null($selected) ? [] : $selected;
    $selected = is_array($selected) ? $selected : explode(',', $selected);
    foreach ($list as $k => $v)
    {
        $html[] = sprintf(Form::label("{$name}-{$k}", "%s {$v}"), Form::checkbox($name, $k, in_array($k, $selected), ['id' => "{$name}-{$k}"]));
    }
    return '<div class="checkbox">' . implode(' ', $html) . '</div>';
}

/**
 * 生成分类下拉列表框
 * @param string $name
 * @param string $type
 * @param mixed $selected
 * @param array $attr
 * @return string
 */
function build_category_select($name, $type, $selected = null, $attr = [], $header = [])
{
    $tree = Tree::instance();
    $tree->init(Category::getCategoryArray($type), 'pid');
    $categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
    $categorydata = $header ? $header : [];
    foreach ($categorylist as $k => $v)
    {
        $categorydata[$v['id']] = $v['name'];
    }
    $attr = array_merge(['id' => "c-{$name}", 'class' => 'form-control selectpicker'], $attr);
    return build_select($name, $categorydata, $selected, $attr);
}

/**
 * 生成表格操作按钮栏
 * @param array $btns 按钮组
 * @param array $attr 按钮属性值
 * @return string
 */
function build_toolbar($btns = NULL, $attr = [])
{
    $auth = \app\admin\library\Auth::instance();
    $controller = str_replace('.', '/', strtolower(think\Request::instance()->controller()));
    $btns = $btns ? $btns : ['refresh', 'add', 'edit', 'del', 'import'];
    $btns = is_array($btns) ? $btns : explode(',', $btns);
    $index = array_search('delete', $btns);
    if ($index !== FALSE)
    {
        $btns[$index] = 'del';
    }
    $btnAttr = [
        'refresh' => ['javascript:;', 'btn btn-primary btn-refresh', 'fa fa-refresh', '', __('Refresh')],
        'add'     => ['javascript:;', 'btn btn-success btn-add', 'fa fa-plus', __('Add'), __('Add')],
        'edit'    => ['javascript:;', 'btn btn-success btn-edit btn-disabled disabled', 'fa fa-pencil', __('Edit'), __('Edit')],
        'del'     => ['javascript:;', 'btn btn-danger btn-del btn-disabled disabled', 'fa fa-trash', __('Delete'), __('Delete')],
        'import'  => ['javascript:;', 'btn btn-danger btn-import', 'fa fa-upload', __('Import'), __('Import')],
    ];
    $btnAttr = array_merge($btnAttr, $attr);
    $html = [];
    foreach ($btns as $k => $v)
    {
        //如果未定义或没有权限
        if (!isset($btnAttr[$v]) || ($v !== 'refresh' && !$auth->check("{$controller}/{$v}")))
        {
            continue;
        }
        list($href, $class, $icon, $text, $title) = $btnAttr[$v];
        $extend = $v == 'import' ? 'id="btn-import-' . \fast\Random::alpha() . '" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"' : '';
        $html[] = '<a href="' . $href . '" class="' . $class . '" title="' . $title . '" ' . $extend . '><i class="' . $icon . '"></i> ' . $text . '</a>';
    }
    return implode(' ', $html);
}

/**
 * 生成页面Heading
 *
 * @param string $path 指定的path
 * @return string
 */
function build_heading($path = NULL, $container = TRUE)
{
    $title = $content = '';
    if (is_null($path))
    {
        $action = request()->action();
        $controller = str_replace('.', '/', request()->controller());
        $path = strtolower($controller . ($action && $action != 'index' ? '/' . $action : ''));
    }
    // 根据当前的URI自动匹配父节点的标题和备注
    $data = Db::name('auth_rule')->where('name', $path)->field('title,remark')->find();
    if ($data)
    {
        $title = __($data['title']);
        $content = __($data['remark']);
    }
    if (!$content)
        return '';
    $result = '<div class="panel-lead"><em>' . $title . '</em>' . $content . '</div>';
    if ($container)
    {
        $result = '<div class="panel-heading">' . $result . '</div>';
    }
    return $result;
}






/**
 * @param $shopid 门店号
 * @param $access_token token
 * @param $ssid ssid
 * @return bool
 */
function weixinSSID($shopid,$access_token,$ssid){
    $flag = true;
    //查询ssid列表
    $sendurl = "https://api.weixin.qq.com/bizwifi/shop/get?access_token=" . $access_token;
    $postdata = array('shop_id' => $shopid);
    $shop = curlPost($sendurl, json_encode($postdata));
    if ($shop['errcode'] == 0) {
        $ssid_list = $shop['data']['ssid_list'];
//        print_r($ssid_list);
    }else{
        \think\Db::name("shop")->where(['shopid'=>$shopid])->update(['status' => -1]);
        return false;
    }

    if (count($ssid_list) >= 90) {
        //清空ssid列表
        $sendurl = 'https://api.weixin.qq.com/bizwifi/shop/clean?access_token=' . $access_token;
        $postdata = array(
            'shop_id' => $shopid
        );
        $add = curlPost($sendurl, json_encode($postdata));
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
//        print_r($add);
        $flag = false;
    }
    return $flag;

}


function getAccessToken($appid,$appsecret) {
    $sendurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
    $data=json_decode(curlGet($sendurl),true);
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
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}

function getUrl($appId , $extend , $timestamp , $shop_id , $authUrl , $mac , $ssid , $bssid , $secretkey){
    $sign = md5($appId . $extend . $timestamp . $shop_id . $authUrl . $mac . $ssid . $bssid . $secretkey);
    $base_url = 'http://wifi.weixin.qq.com/operator/callWechat.xhtml?'.
        'appId=' .$appId .
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