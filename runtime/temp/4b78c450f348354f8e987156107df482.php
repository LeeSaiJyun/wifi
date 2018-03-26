<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"/home/wwwroot/wifi.8a6.cn/public/../application/admin/view/shebeimac/edit.html";i:1514425103;s:78:"/home/wwwroot/wifi.8a6.cn/public/../application/admin/view/layout/default.html";i:1514425103;s:75:"/home/wwwroot/wifi.8a6.cn/public/../application/admin/view/common/meta.html";i:1514425103;s:77:"/home/wwwroot/wifi.8a6.cn/public/../application/admin/view/common/script.html";i:1514425103;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="__CDN__/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="__CDN__/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="__CDN__/assets/js/html5shiv.js"></script>
  <script src="__CDN__/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label for="c-devmac" class="control-label col-xs-12 col-sm-2"><?php echo __('Devmac'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-devmac" data-rule="required" class="form-control" name="row[devmac]" type="text" value="<?php echo $row['devmac']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-mac" class="control-label col-xs-12 col-sm-2"><?php echo __('Mac'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mac" data-rule="required" class="form-control" name="row[mac]" type="text" value="<?php echo $row['mac']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-ssid" class="control-label col-xs-12 col-sm-2"><?php echo __('Ssid'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-ssid" data-rule="required" class="form-control" name="row[ssid]" type="text" value="<?php echo $row['ssid']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-bssid" class="control-label col-xs-12 col-sm-2"><?php echo __('Bssid'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bssid" data-rule="required" class="form-control" name="row[bssid]" type="text" value="<?php echo $row['bssid']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-ip" class="control-label col-xs-12 col-sm-2"><?php echo __('Ip'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-ip" data-rule="required" class="form-control" name="row[ip]" type="text" value="<?php echo $row['ip']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-time" class="control-label col-xs-12 col-sm-2"><?php echo __('Time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-time" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[time]" type="text" value="<?php echo $row['time']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-status" class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['status'])?$row['status']:explode(',',$row['status']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="__CDN__/assets/js/require.js" data-main="__CDN__/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>