<?php
namespace app\model;
use think\Db;
use app\model\Base as Base;
class English extends Base{
	public function saveEnglish($arr){
        $arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return $data;
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		if(isset($arr['textId'])){
			if(!empty($arr['textId'])){
				$id = $arr['textId'];
				$where['textId'] = $id;
				$update = true;
			}
			unset($arr['textId']);
		}
		$rs = $this->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $this->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return $data;
			exit;
		}
		if($update){
			$arr['textId'] = $id;
		}else{
			$arr['textId'] = $this->textId;
		}
		$data['status'] = 1;
		$data['msg'] = "保存成功";
		$data['data'] = $arr;
		return $data;
    }
    public function textList(){
        $list = $this->order("textSort Desc,textId ASC")->select();
        $err = $this->getError();
        if(!empty($err)){
            $data['status'] = -1;
            $data['msg'] = $err;
            $data['data'] = array();
            return $data;
            exit;
        }
        $data['status'] = 1;
        $data['msg'] = 'ok';
        $data['data'] = $list;
        return $data;
    }
}
?>