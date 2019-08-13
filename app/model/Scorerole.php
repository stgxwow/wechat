<?php
namespace app\model;
use app\model\Base as Base;
class Scorerole extends Base{
	public function getScoreRoleList($type=-1,$md=0){
		$where = array();
		if($type == 0){
			$where['itemType'] = 0;
		}else if($type > 0){
			$where['itemType'] = 1;
		}
		if($md > 0){
			$where['dataFlag'] = 1;
		}
		$list = $this->where($where)->order("itemId ASC")->select();
		return $list;
	}
	public function getRuleByName($itemName,$itemType){
		if(empty($itemName)){
			return array();
		}
		$where = "itemName like '%" . $itemName . "%'";
		if(isset($itemType)){
			$where = $where . " and itemType=" . $itemType;
		}
		$list = $this->where($where)->select();
		return $list;
	}
}
?>