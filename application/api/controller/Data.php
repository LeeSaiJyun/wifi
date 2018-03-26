<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Log;

class Data extends Controller
{
    public function index() {
        return view();
    }

    public function inc(){
        $code = Request::instance()->get('code');
        // score 字段加 1
        Db::table('fa_statistics')->where('code', $code)->setInc('hit');
        $hit = Db::table('fa_statistics')->where('code',$code)->value('hit');
        echo "jsonpCallback({'data':{$hit},'success':true})";
    }


    public function addRow(){
        $row['code'] = sprintf('%04x%04x%04x%04x%04x%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        try{
            Db::table('fa_statistics')->insert($row);
            echo $row['code'];
        }catch(\Exception $e){
            echo 'addOne fail';
        }
    }


    public function getHit(){
        $code = Request::instance()->get('code');
        $hit = Db::table('fa_statistics')->where('code',$code)->value('hit');
        echo $hit;
    }


}
