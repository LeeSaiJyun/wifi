<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Controller;
use think\Request;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Device extends Backend
{
    
    /**
     * Device模型对象
     */
    protected $model = null;
    protected $dataLimit = 'auth'; //默认基类中为false，表示不启用，可额外使用auth和personal两个值
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Device');
        $this->view->assign("statusList", $this->model->getStatusList());
    }


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
//                ->with('shop')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
//                ->with('shop')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $key => &$row){
//                list($row['shop_id'],$row['name']) = model('Shop')->field('id,name')->where('id',$row['shop_id'])->find();
                $row['name'] = model('Shop')->where('id',$row['shop_id'])->value('name');
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    public function selectpage()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $device = $this->request->param('name');
            $total = Db::name('device')
                ->where($where)
                ->where('devmac','like',"%{$device}%")
                ->order($sort, $order)
                ->group('devmac')
                ->count();

            $list =  Db::name('device')
                ->where($where)
                ->where('devmac','like',"%{$device}%")
                ->field('id,devmac as name')
                ->order($sort, $order)
                ->group('devmac')
                ->limit($offset, $limit)
                ->select();


            $result = array("total" => $total, "list" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 编辑
     */
/*    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }*/
}
