<?php
namespace app\student\controller;
use think\Controller;
use app\model\English as EM;
class Index extends controller{
    public function index(){
        return $this->fetch('index');
    }
    public function toSave(){
        return $this->fetch('edit');
    }
    public function saveEnglish(){
        $arr = input();        
        if(empty($arr)){
            $data['status'] = -1;
            $data['msg'] = "参数错误";
            $data['data'] = $arr;
            return json_encode($data);
            exit;
        }
        // if(isset($arr['textSound'])){
        //     $arr['textSound'] = urldecode($arr['textSound']);
        // }
        $m = new EM();
        $rs = $m->saveEnglish($arr);
        return json_encode($rs);
    }
    public function getList(){
        $m = new EM();
        $rs = $m->textList();
        return json_encode($rs);
    }

}
?>