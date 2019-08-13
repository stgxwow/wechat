<?php
namespace app\model;
use app\model\Base as Base;
class Infogroup extends Base{
	public function getGroupList($arr=array(),$page=array(),$order="",$m=0){
		if(empty($page)){
			$page['per_page'] = 20;
			$page['current_page'] = 1;
		}
		$keys = $this->getEModel("infogroup");
		$wh = array();
		foreach($keys as $k=>$v){
			if(isset($arr[$k]) && !empty($arr[$k])){
				$wh[$k] = $arr[$k];
			}
		}
		if(empty($order)){
			$order = "groupId ASC";
		}
		$sum = $this->where($wh)->count();
		if($sum == 0){
			$num = 1;
		}else{
			$num = $sum % $page['per_page'] == 0 ? floor($sum / $page['per_page']) : floor($sum / $page['per_page']) + 1;
		}    
		if($page['current_page'] > $num){
			$page['current_page'] = $num;
		}
		$list = $this->where($wh)->order($order)->paginate($page['per_page'],false,['page'=>$page['current_page']]);
		return $list;
    }
	public function saveGroup($arr){
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的数据";
			$data['data'] = $arr;
			return $data;
			exit;
		}
		$sid = 0;
		$update = false;
		$where = array();
		$type = '添加';
		if(isset($arr['groupId'])){
			if(!empty($arr['groupId'])){
				$update = true;
				$sid = $arr['groupId'];
				$where['groupId'] = $sid;
			}
			unset($arr['groupId']);
			$type = "编辑";
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
		$strs = implode('-',$arr);
		\app\model\Logs::saveLog($strs,$type,'展示分组');
		$data['status'] = 1;
		$data['msg'] = "ok";
		if($update){
			$arr['groupId'] = $sid;
		}else{
			$arr['groupId'] = $this->groupId;
		}
		$data['data'] = $arr;
		return $data;
	}
}
?>