<?php

namespace app\index\controller;

use app\common\controller\Frontend;


class Index extends Frontend
{

    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
//        return $this->view->fetch();
        $this->redirect('admin/index/login');
    }


    public function news()
    {
        $newslist = [];
        return jsonp(['newslist' => $newslist, 'new' => count($newslist), 'url' => 'http://www.fastadmin.net?ref=news']);
    }

}
