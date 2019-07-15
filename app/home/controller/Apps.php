<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use app\model\Program as PM;
use app\model\Mark as MAM;
use app\model\Scorerecord as SRM;
use app\model\Award as AM;
class Apps extends Controller
{
	public function adminProgram(){
		$rs = getProgramList();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
		return json_encode($data);
	}
	public function adminClass(){
		$rs = getClassList();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
		$data['user_list'] = getUserList(0,1);
		$data['program_list'] = getProgramList();
		return json_encode($data);
	}
	public function adminWechat(){
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data']['pro_list'] = getProgramList();
		$data['data']['class_list'] = getClassList();
		return json_encode($data);
	}
	public function getStudents(){
		$classId = input('classId/d');
		if($classId <= 0){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$student_list = getStudentListByClass($classId,1);
		$group = getAllGroup(1);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data']['student_list'] = $student_list;
		$data['data']['group_list'] = $group;
		return json_encode($data);
	}

	/***
    *
    *根据学生Id和成绩分组查询成绩
    *
    ***/

    public function getMarkByStudentId(){
    	$studentId = input("studentId/d");
    	$markGroup = input('markGroup/d');
    	if(empty($studentId) || empty($markGroup)){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误,必须提供学生ID和成绩分组";
    		$data['data'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$sum = 0;
    	$avg = 0;
    	$m = new MAM();
    	$list = $m->getMarkList($studentId,$markGroup);
    	$err = $m->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = array();
    		return json_encode($data);
    		exit;
    	}
    	if(!empty($list)){
    		foreach($list as $k=>$v){
    			$sum += $v['score'];
    		}
    		$avg = round($sum / count($list),2);
    	}
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['data']['data'] = $list;
    	$data['data']['sum'] = $sum;
    	$data['data']['avg'] = $avg;
    	return json_encode($data);
    }

    public function getScoreList(){
    	$id = input('studentId/d');
    	if($id <= 0){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误";
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
    	}
    	$m = new SRM();
    	$where['studentId'] = $id;
    	$where['dataFlag'] = 1;
    	$list = $m->where($where)->select();
    	$err = $m->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
    	}
    	$sum = 0;
    	$deduction = 0;
    	$reward = 0;
    	if(!empty($list)){
    		foreach($list as $k=>$v){
    			if($v['score'] >= 0){
    				$reward += $v['score'];
    			}else{
    				$deduction += $v['score'];
    			}
    		}
    		$sum = 1000 + $deduction + $reward;
    	}
    	$data['status'] = 1;
    	$data['msg'] = 'ok';
    	$data['data']['data'] = $list;
    	$data['data']['sum'] = $sum;
    	$data['data']['reward'] = $reward;
    	$data['data']['deduction'] = $deduction;
    	return json_encode($data);
    }

    public function getAwardList(){
    	$names = input('studentName');
    	if(empty($names)){
    		$data['status'] = 1;
    		$data['msg'] = '参数错误';
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
    	}
    	$m = new AM();
    	$where['studentName'] = $names;
    	$where['dataFlag'] = 1;
    	$list = $m->where($where)->select();
    	$err = $m->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
    	}
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['data'] = $list;
    	return json_encode($data);
    }
}
?>