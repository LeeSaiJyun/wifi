<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 *
 *
 * @icon mobile
 */
class Mobile extends Backend
{

    /**
     * Mobile模型对象
     */
    protected $model = null;

    public function _initialize() {
        parent::_initialize();
        $this->model = model('Mobile');
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index() {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('pkey_name')) {
                return $this->selectpage();
            }

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('devmac', 'IN', function ($query) {
                    $query->name('device')->where('admin_id', 'in', $this->auth->getChildrenAdminIds(true))->group('devmac')->field('devmac');
                })
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->field('id, devmac, mac, ssid, time, status,last_push_time')
                ->where($where)
                ->where('devmac', 'IN', function ($query) {
                    $query->name('device')->where('admin_id', 'in', $this->auth->getChildrenAdminIds(true))->group('devmac')->field('devmac');
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
//                ->fetchSql()
                ->select();

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }


    public function change($ids = '') {
        $num = $this->model
            ->where('id', $ids)
            ->update(['status' => 0]);
        if ($num) {
            $this->success("设置重发成功");
        }
    }

}
