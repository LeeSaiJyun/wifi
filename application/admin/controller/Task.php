<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use fast\Random;
use think\Controller;
use think\Request;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Task extends Backend
{
    
    /**
     * Task模型对象
     */
    protected $model = null;
//    protected $dataLimit = 'auth'; //默认基类中为false，表示不启用，可额外使用auth和personal两个值
//    protected $dataLimitField = 'shop.admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段



    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Task');
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    //批量添加账号
    /*public function test(){
        if($this->auth->isSuperAdmin()){
            $password = ['552f78', '5530f8', '552e68', '552dcc', '552c64',
                '5530c4', '552de8', '552e00', '553158', '552eb8',
                '552e18', '552c3c', '552e6c', '552de0', '552ed0',
                '553120', '5530e8', '5530a0', '553144', '552e34',
                '552e7c', '552ecc', '552ea0', '552ee8', '552f68',
                '552c5c', '552d44', '552c60', '552c20', '553124'];
            for($i=0;$i<31;$i++){
                $salt= Random::alnum();
                echo $i. "   ".$password[$i] ."  ".$salt."  ".md5(md5($password[$i]) . $salt)."\n";
            }
        }
    }*/





    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                ->field('fa_shop.name ,device.devmac,fa_task.* ')
                ->join('fa_shop shop' , 'shop.id = fa_task.shop_id')
                ->join('fa_device device' , 'device.id = fa_task.device_id')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
