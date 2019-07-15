<?php
namespace app\model;
use think\Db;
use app\model\Base as Base;
class Mark extends Base{
	public function getMarkList($sid=0,$gid=0,$wh=array(),$order=""){
		if($sid >0 && $gid > 0){
			$where['m.studentId'] = $sid;
			$where['m.markGroup'] = $gid;
			$where['m.dataFlag'] = 1;
			if(!empty($wh)){
				foreach($wh as $k=>$v){
					if(!empty($v)){
						$where[$k] = $v;
					}
				}
			}
			$list = $this->alias("m")->join("__STUDENT__ s","m.studentId=s.studentId")->join("__COURSE__ c","m.courseId=c.courseId")->join("__GROUP__ g","m.markGroup=g.groupId")->where($where)->field("m.markId,s.studentId,s.studentName,m.courseId,c.courseName,m.markGroup,g.groupName,m.score,m.dataFlag")->order($order)->select();
			return $list;
		}else{
			return array();
		}
	}
	public function getClassMark($cid=0,$gid=0,$wh=array(),$order=""){
		if($cid >0 && $gid > 0){
			$where['s.classId'] = $cid;
			$where['m.markGroup'] = $gid;
			$where['m.dataFlag'] = 1;
			if(!empty($wh)){
				foreach($wh as $k=>$v){
					if(!empty($v)){
						$where[$k] = $v;
					}
				}
			}
			$list = $this->alias("m")->join("__STUDENT__ s","m.studentId=s.studentId","left")->join("__COURSE__ c","m.courseId=c.courseId","left")->join("__GROUP__ g","m.markGroup=g.groupId","left")->where($where)->field("m.markId,s.studentId,s.studentName,m.courseId,c.courseName,m.markGroup,g.groupName,m.score,m.dataFlag")->order($order)->select();
			return $list;
		}else{
			return array();
		}
	}
	public function inputMarks($arr){
		if(empty($arr)){
			return array();
		}
		if(isset($arr[0]['studentId']) && !empty(($arr[0]['studentId']))){
			$studentId = $arr[0]['studentId'];
		}else{
			$studentId = 0;
		}
		if(isset($arr[0]['markGroup']) && !empty(($arr[0]['markGroup']))){
			$markGroup = $arr[0]['markGroup'];
		}else{
			$markGroup = 0;
		}
		if(empty($studentId) || empty($markGroup)){
			return array();
		}
		$list = $this->getMarkList($studentId,$markGroup);
		if(!empty($list)){
			foreach($arr as $k=>$v){
				$tmp = $this->recInArray($v,$list);
				if(!empty($tmp)){
					$arr[$k]['markId'] = $tmp;
				}else{
					if(isset($v['markId'])){
						unset($arr[$k]['markId']);
					}
				}
			}
			if(count($list) > count($arr)){
				foreach($list as $k=>$v){
					$smp = $this->recInArray($v,$arr);
					if(empty($smp)){
						$vd['markId'] = $v['markId'];
						$vd['studentId'] = $v['studentId'];
						$vd['courseId'] = $v['courseId'];
						$vd['markGroup'] = $v['markGroup'];
						$vd['dataFlag'] = 0;
						$arr[] = $vd;
					}
				}
			}
		}else{
			foreach($arr as $k=>$v){
				if(isset($v['markId'])){
					unset($arr[$k]['markId']);
				}
			}
		}
		$rs = $this->saveAll($arr);
		return $rs;
	}

	public function recInArray($rec,$arr){
		if(empty($rec) || empty($arr)){
			return false;
		}
		foreach($arr as $k=>$v){
			if($rec['studentId'] == $v['studentId'] && $rec['courseId'] == $v['courseId'] && $rec['markGroup'] == $v['markGroup']){
				return $v['markId'];
				break;
			}
		}
		return false;
	}
}
?>