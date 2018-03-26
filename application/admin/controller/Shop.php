<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 微信公众号配置
 *
 * @icon fa fa-circle-o
 */
class Shop extends Backend
{
    /**
     * Shop模型对象
     */
    protected $model = null;
    protected $dataLimit = 'auth'; //默认基类中为false，表示不启用，可额外使用auth和personal两个值
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Shop');
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }
}
