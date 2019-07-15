<?php
namespace app\model;
use think\Db;
use app\model\Base as Base;
class Scorerecord extends Base{
	public function getScoreByClass($classId=0,$md=1){
		$where = array();
		if($classId <= 0){
			return array();
		}else{
			$where['s.classId'] = $classId;
		}		
		if($md > 0){
			$where['s.dataFlag'] = 1;
		}
		$where['sc.dataFlag'] = 1;
		$list = Db::name('student')->alias('s')->join("__SCORERECORD__ sc","s.studentId=sc.studentId","left")->where($where)->field('s.studentId,s.studentName,s.studentNumber,sum(sc.score) as score')->group("s.studentId")->order("score ASC")->select();
		return $list;
	}

	public function getScoreListByStudent($studentId=0,$md=1){
		if($studentId <= 0){
			return array();
		}
		$where['studentId'] = $studentId;
		$where['dataFlag'] = 1;
		$list = $this->where($where)->order("opTime DESC,recordId ASC")->select();
		return $list;
	}
	public function getSumScoreByStudent($studentId=0){
		if($studentId <= 0){
			return 0;
		}
		$where['studentId'] = $studentId;
		$where['dataFlag'] = 1;
		$list = $this->where($where)->field("sum(score) as score")->select();
		return $list;
	}
	public function getScoreById($recordId=0){
		if(empty($recordId)){
			return array();
		}else{
			$where['sc.recordId'] = $recordId;
		}
		$where['sc.dataFlag'] = 1;
		$list = $this->alias('sc')->join("__STUDENT__ s","sc.studentId=s.studentId")->field("sc.recordId,sc.studentId,s.studentName,sc.scoreRule,sc.recordMem,sc.score,sc.opTime,sc.dataFlag")->where($where)->find();
		return $list;
	}
}
?>