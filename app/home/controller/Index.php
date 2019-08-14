<?php
namespace app\home\controller;
use think\Db;
use think\Controller;
use think\Loader;
use think\Cache;
use app\model\Menu as MM;
use app\model\Users as UM;
use app\model\Role as RM;
use app\model\Program as PM;
use app\model\Classes as CM;
use app\model\Course as COM;
use app\model\Award as AM;
use app\model\Student as SM;
use app\model\Group as GM;
use app\model\Mark as MAM;
use app\model\Scorerole as SCM;
use app\model\Scorerecord as SCRM;
use app\model\Editscore as EM;
use app\model\Info as IM;
use app\model\Infogroup as IGM;
use app\model\Msg;
use app\model\Logs;
class Index extends Base
{
	/*public function __construct(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        parent::__construct();
    }*/
    public function index()
    {
		return $this->fetch('index');
    }
   	public function main(){
   		return $this->fetch("main");
   	}
	public function listMenu(){
		$m = new MM();
		$list = $m->menuList(); 
	}
	public function users(){
		return $this->fetch("users");
	}
	public function role(){
		return $this->fetch("role");
	}
	public function program(){
		return $this->fetch("program");
	}
	public function classes(){
		return $this->fetch("class");
	}
	public function course(){
		return $this->fetch("course");
	}
	public function student(){
		return $this->fetch("student");
	}
	public function mark(){
		return $this->fetch("mark");
	}
	public function group(){
		return $this->fetch("group");
	}
	public function award(){
		return $this->fetch("award");
	}
	public function menu(){
		return $this->fetch("menu");
	}
	public function markInput(){
		return $this->fetch("mark_input");
	}
	public function scorerole(){
		return $this->fetch("scorerole");
	}
	public function scoreinput(){
		return $this->fetch("scoreinput");
	}
	public function score(){
		return $this->fetch("score");
	}
	public function scoreExamine(){
		return $this->fetch('score_examine');
	}
	public function messages(){
		return $this->fetch('messages');
	}
	public function logs(){
		return $this->fetch('logs');
	}
	public function showWorks(){
		return $this->fetch('showWorks');
	}
	public function infoGroup(){
		return $this->fetch('infoGroup');
	}
	public function uploadfile(){
		/* $rs = uploadFiles();
		print_r($rs); */
	}
	public function phpinfo(){
		phpinfo();
	}
	public function test(){
		// $t = getClassesByUserLogin();
		// print_r($t);
		// $where['c.dataFlag'] = 1;
		// $where['c.proId'] = 1;
		// $m = new EM();
		// $list = $m->getListByUser(1);
		// print_r($list);
		//$rs = getClassList(0,1);
		//print_r($rs);
		/*$sql = "select c.classId,c.className,c.masterId,c.sort,c.proId,p.proName,u.userName,u.nickName from sh_classes c left join sh_program p on c.proId=p.proId left join sh_users u on c.masterId=u.userId where c.dataFlag=1 and c.proId=1 order by classId ASC";
		$rs = Db::query($sql);
		return json_encode($rs);*/
		/*$rs = Db::name("classes")->alias("c")->join("__USERS__ u","c.masterId=u.userId","left")->join("__PROGRAM__ p","c.proId=p.proId","left")->where($where)->field("c.classId,c.className,c.masterId,c.sort,c.proId,p.proName,u.userName,u.nickName")->order("sort DESC,classId ASC")->select();
		print_r($rs);*/
		//Cache::clear(); 
		//getExcelData('public/upload/excel/b.xlsx');
		//return $this->fetch('test11');
		/* $rs = getExcelData('/public/upload/common/2019-07-23/b.xlsx');
		print_r($rs); */
		//print_r(getBirthDay('211481198003025614'));
		//\app\model\Logs::saveLog('test','添加','主页',1,'修改前');
		//$s = uploadFile();
		//print_r($s);
		/* $str = 'https://v.qq.com/x/cover/3fvg46217gw800n.htmlhttps://v.qq.com/x/page/e09064uhfp0.html';
		$url = getVedioInfo('https://v.qq.com/x/page/e09064uhfp0.html');
		print_r($url); */
		//$rs=getInfoByClass(1,1);


		/* 按照班级导出所有成绩 */
		$classId=input('classId/d');
		if(empty($classId)){
			echo "请输入班级Id";
			exit;
		}
		$m=new MAM();
		$list = collection($m->alias('m')->join('__STUDENT__ s','m.studentId=s.studentId','left')->join('__COURSE__ c','m.courseId=c.courseId','left')->join('__GROUP__ g','m.markGroup=g.groupId','left')->field('m.studentId,s.studentNumber,s.studentName,m.courseId,c.courseName,m.markGroup,g.groupName,m.score')->where('s.classId=' . $classId)->order('groupId ASC')->select())->toArray();
		$arr = array();
		$course = array();
		$data = array();
		if(!empty($list)){
			foreach($list as $k=>$v){
				$keys = array_keys($arr);
				if(!in_array($v['markGroup'],$keys)){
					$arr[$v['markGroup']] = array();
					//$data[$v['markGroup']] = array();
				}
				$md = $arr[$v['markGroup']];
				$tempkey = array_keys($md);
				if(!in_array($v['studentId'],$tempkey)){
					$subItem = array();
					$subItem['studentNumber'] = $v['studentNumber'];
					$subItem['studentName'] = $v['studentName'];
					$subItem['markGroup'] = $v['markGroup'];
					$subItem['groupName'] = $v['groupName'];
					$subItem[$v['courseName']] = $v['score'];
					$arr[$v['markGroup']][$v['studentId']] = $subItem;
				}else{
					$arr[$v['markGroup']][$v['studentId']][$v['courseName']] = $v['score'];
				}
				if(!in_array($v['markGroup'],array_keys($course))){
					$course[$v['markGroup']] = array();
				}
				if(!in_array($v['courseName'],$course[$v['markGroup']])){
					$course[$v['markGroup']][] = $v['courseName'];
				}
			}
		}
		foreach($arr as $k=>$v){
			$temp = array();
			foreach($v as $kk=>$vv){
				$temp[] = $vv;
			}
			$data[] = $temp;
		}
		$tar = $course;
		$course = array();
		if(!empty($tar)){
			foreach($tar as $k=>$v){
				$course[] = $v;
			}
		}
		Loader::import('phpexcel.PHPExcel.IOFactory');
		if(!empty($data)){
			$r = 1;
			$l = 0;
			$excel = new \PHPExcel();
			foreach($data as $kl=>$vl){
				$titleDesc = array();
				$titleDesc = ['学号','学生姓名','分组Id','分组名称'];
				$titleDesc = array_merge($titleDesc,$course[$kl]);
				$title = array();
				$title = ['studentNumber','studentName','markGroup','groupName'];
				$title = array_merge($title,$course[$kl]);
				$info = '成线表' . $kl;
				$arrs = $vl;
				
				$mj = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
				$letter = array();
				$letterDesc = array();
				for($s=0;$s<count($titleDesc);$s++){
					$letter[] = $mj[$s];
					$letterDesc[] = $title[$s];
				}
				$excel->getActiveSheet()->setCellValue('A' . $r,'班级ID为' . $classId . '分组Id为' . $kl);
				for($i=0;$i< count($letter);$i++){
					$excel->getActiveSheet()->setCellValue($letter[$i] . ($r+1),$titleDesc[$i]);
				}
				$row = $r+2;
				for($m=($r+2);$m < $row + count($vl);$m++){
					$n = 0;
					foreach($vl[$m - ($r+2)] as $k => $v){
						if(isset($vl[$m - ($r+2)][$letterDesc[$n]])){
							$values = $vl[$m - ($r+2)][$letterDesc[$n]];
						}else{
							$values = '';
						}
						$excel->getActiveSheet()->setCellValueExplicit($letter[$n] . $m,$values,\PHPExcel_Cell_DataType::TYPE_STRING);
						$n++;
					}
				}
				
				$r = $r + count($vl)+1;
				$r++;
			}
			$filename = $info . date('Ymd',time()) . '.xls';
			$write = new \PHPExcel_Writer_Excel5($excel);
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
			header("Content-Type:application/force-download");
			header("Content-Type:application/vnd.ms-execl");
			header("Content-Type:application/octet-stream");
			header("Content-Type:application/download");;
			header('Content-Disposition:attachment;filename="' . $filename .'"');
			header("Content-Transfer-Encoding:binary");
			$write->save('php://output');
			exit;
		}
	}
	public function getmarklist(){
		$classId = input('classId/d');
    	$groupId = input('groupId/d');
    	if(empty($classId) || empty($groupId)){
    		print_r("必须提供班级和成绩分组信息");
    		exit;
    	}
    	$s = new SM();
    	$where['classId'] = $classId;
    	$where['dataFlag'] = 1;
    	$students = $s->where($where)->field("studentId,studentName,studentNumber")->order("studentId ASC")->select();
		$err = $s->getError();
    	if(!empty($err)){
    		print_r("获取学生列表失败");
    		exit;
    	}
    	$m = new MAM();
    	$list = $m->getClassMark($classId,$groupId);
    	$list = getMarkGroup($list);
    	if(isset($list['scoreType']) && !empty($list['scoreType'])){
    		$st = $list['scoreType'];
    		$scoreList = array();
    		foreach($st as $k=>$v){
    			$slr = array();
    			$slr['courseName'] = $v;
    			$slr['score'] = 0;
    			$scoreList[] = $slr;
    		}
    	}else{
    		$scoreList = array();
    	}
		$ls = $list['list'];
    	foreach($students as $k=>$v){
    		$bl = inMarkList($v['studentId'],$ls);
    		if($bl >= 0){
    			$tmpList = $ls[$bl]['scoreList'];
    			$sum = 0;
    			foreach($tmpList as $kk=>$vv){
    				$sum = $sum + $vv['score'];
    			}
    			$students[$k]['scoreList'] = $ls[$bl]['scoreList'];
    			$students[$k]['scoreSum'] = $sum;
    			$ln = count($scoreList);
    			if($ln > 0){
    				$students[$k]['avg'] = sprintf("%.2f", $sum/$ln);
    			}else{
    				$students[$k]['avg'] = 0;
    			}
    		}else{
    			$students[$k]['scoreList'] = $scoreList;
    			$students[$k]['scoreSum'] = 0;
    			$students[$k]['avg'] = 0;
    		}
    	}
    	$tplist = array_column($students,'scoreSum');
    	array_multisort($tplist,SORT_DESC,$students);
    	if(!empty($students)){
    		$i = 1;
    		foreach($students as $k=>$v){
    			$tmprs = makeMarkList($list['scoreType'],$v['scoreList']);
    			if(count($tmprs) > 0){
    				foreach($tmprs as $kk=>$vv){
    					$students[$k][$kk] = $vv;
    				}
    			}else{
					if(!empty($list['scoreType'])){
						foreach($list['scoreType'] as $kk=>$vv){
							$students[$k][$kk] = $vv;
						}
					}
    			}
    			$students[$k]['sort'] = $i;
    			$i++;
    			unset($students[$k]['studentId']);
    			unset($students[$k]['scoreList']);
    		}
    		$tmparr = array();
    		foreach($students as $k=>$v){
    			$tmp['studentNumber'] = $v['studentNumber'];
				$tmp['studentName'] = $v['studentName'];
				if(!empty($list['scoreType'])){
					foreach($list['scoreType'] as $kk=>$vv){
						$tmp[$vv] = $v[$vv];
					}
				}else{
					$list['scoreType'] = array();
				}
    			$tmp['scoreSum'] = $v['scoreSum'];
    			$tmp['avg'] = $v['avg'];
    			$tmp['sort'] = $v['sort'];
    			$tmparr[] = $tmp;
    		}
    		$students = $tmparr;
		}
    	$field = ["studentNumber","studentName"];
    	$field = array_merge($field,$list['scoreType']);
    	$field[] = "scoreSum";
    	$field[] = "avg";
    	$field[] = "sort";
    	$desc = ['学号','姓名'];
    	$desc = array_merge($desc,$list['scoreType']);
    	$desc[] = "总分";
    	$desc[] = "平均分";
    	$desc[] = "排名";
    	$fileName = getReportInfo($classId);
    	makeRport($students,$desc,$field,$fileName);
	}

	/***
	*
	*接口
	*
	***/

	
	/***
	*
	*保存用户信息
	*
	***/

	public function saveUser(){
		$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$wh = array();
		$page = array();
		if(isset($arr['search'])){
			if(!empty($arr['search'])){
				$tmp = $arr['search'];
				if(empty($tmp['search_key'])){
					$wh = array();
				}else{
					$wh['nickName'] = $tmp['search_key'];
				}				
				$page = array();
				$page['per_page'] = $tmp['per_page'];
				$page['current_page'] = $tmp['current_page'];
				unset($arr['search']);
			}
		}
		$where = array();
		$userId = 0;
		$update = false;
		if(isset($arr['userId']) && !empty($arr['userId'])){
			$userId = $arr['userId'];
			$where['userId'] = $userId;
			$update = true;
			$type = '编辑';
			unset($arr['userId']);
		}else{
			$arr['userPwd'] = md5("123456" . md5("123456"));
			$arr['regTime'] = time();
			$type = '添加';
		}
		$m = new UM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'用户');
			$data['status'] = 1;
			$data['msg'] = $wh;
			$data['data'] = getUserFullInfo($wh,$page);
			return json_encode($data);
			exit;
		}
		return json_encode($arr);
	}

	/***
	*
	*修改密码
	*
	***/


	public function editPwd(){
		$userId = input('userId/d');
		$userName = input('userName');
		$oldPwd = input('oldPwd');
		$userPwd = input('userPwd');
		if(empty($userId) || empty($userName) || empty($oldPwd) || empty($userPwd)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		/*$oldPwd = md5($oldPwd . md5($oldPwd));
		$userPwd = md5($userPwd . md5($userPwd));*/
		$rs = checkUserLogin($userName,$oldPwd);
		if($rs['status'] < 0){
			return json_encode($rs);
			exit;
		}
		$arr['userPwd'] =  $userPwd;
		$m = new UM();
		$where['userId'] = $userId;
		$rs = $m->allowField(true)->isUpdate(true)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = "对用户密码机型了修改";
			\app\model\Logs::saveLog($strs,'编辑','用户');
			$data['status'] = 1;
			$data['msg'] = $where;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
	}
	/***
	*
	*删除用户
	*
	***/
	public function delUsers(){
		$id = input("userId/d");
		$flag = input("dataFlag/d");
		if(empty($id)){
			$data['status'] = -1;
			$data['msg'] = "用户ID错误";
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		$arr['dataFlag'] = $flag;
		$where['userId'] = $id;
		$m = new UM();
		$rs = $m->allowField(true)->isUpdate(true)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = '删除了一个用户,ID为' . $id;
			\app\model\Logs::saveLog($strs,'删除','用户');
			$data['status'] = 1;
			$data['msg'] = "ok";
			$field = array("userId"=>$id,"dataFlag"=>$flag);
			$data['data'] = $field;
			return json_encode($data);
			exit;
		}
	}



	/***
    *
    *获取角色信息
    *
    ***/

	public function adminRole(){
		$arr = input();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = getRoleList();
		$data['menu_list'] = getMenuList(false);
		return json_encode($data);
	}


	/***
    *
    *保存角色信息
    *
    ***/
	public function saveRole(){
		$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type = '添加';
		if(isset($arr['roleId'])){
			if(!empty($arr['roleId'])){
				$id = $arr['roleId'];
				$where['roleId'] = $id;
				$update = true;
				$type = '编辑';
			}
			unset($arr['roleId']);
		}
		$m = new RM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'角色');
			$data['status'] = 1;
			$data['msg'] = 'ok';
			if($update){
				$arr['roleId'] = $id;
			}else{
				$arr['roleId'] = $m->roleId;
			}
			$data['data'] = $arr;
			return json_encode($data);
		}
	}

	/***
    *
    *删除角色信息
    *
    ***/
	public function delRole(){
		$id = input('roleId/d');
		if(empty($id)){
			$data['status'] = -1;
			$data['msg'] = '角色ID错误';
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$where['roleId'] = $id;
		$m = new RM();
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$strs = "删除了一个千分制规则,ID为" . $id;
		\app\model\Logs::saveLog($strs,'删除','千分制');
		$data['status'] = 1;
		$data['msg'] = $rs;
		$data['data'] = $id;
		return json_encode($data);
	}


	/***
    *
    *获取菜单信息
    *
    ***/

	public function adminLoading(){
		$list = getMenuList(1);
		$program = Db::name("program")->where(array("dataFlag"=>1))->count();
		$classes = Db::name("classes")->where(array("dataFlag"=>1))->count();
		$student = Db::name("student")->where(array("dataFlag"=>1))->count();
		$course = Db::name("course")->where(array("dataFlag"=>1))->count();
		$award = Db::name("award")->where(array("dataFlag"=>1))->count();
		$scorerole = Db::name("scorerole")->where(array("dataFlag"=>1))->count();
		$arr = array();
		$arr[] = array('id'=>1,"sum"=>$program,"name"=>"项目数量","url"=>"/public/static/img/img_info1.png");
		$arr[] = array('id'=>2,"sum"=>$classes,"name"=>"班级数量","url"=>"/public/static/img/img_info2.png");
		$arr[] = array('id'=>3,"sum"=>$student,"name"=>"学员数量","url"=>"/public/static/img/img_info3.png");
		$arr[] = array('id'=>3,"sum"=>$course,"name"=>"课程数量","url"=>"/public/static/img/img_info4.png");
		$arr[] = array('id'=>3,"sum"=>$award,"name"=>"奖励数量","url"=>"/public/static/img/img_info5.png");
		$arr[] = array('id'=>3,"sum"=>$scorerole,"name"=>"千分制规则数量","url"=>"/public/static/img/img_info6.png");
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data']['menu_list'] = $list;
		$data['userInfo'] = session("SH_USERS");
		$data['item_list'] = $arr;
		return json_encode($data);
	}

	public function adminUsers(){
		$arr = input();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data']['program_list'] = getProgramList();
		$data['data']['class_list'] = $class = getClassList();
		$data['data']['role_list'] = getRoleList();
		$where = array();
		if(isset($arr['search_key']) && !empty($arr['search_key'])){
			$where['nickName'] = $arr['search_key'];
		}
		$page = array();
		if(isset($arr['per_page']) && !empty($arr['per_page'])){
			$page['per_page'] = $arr['per_page'];
		}else{
			$page['per_page'] = 20;
		}
		if(isset($arr['current_page']) && !empty($arr['current_page'])){
			$page['current_page'] = $arr['current_page'];
		}else{
			$page['current_page'] = 1;
		}
		$data['data']['list'] = getUserFullInfo($where,$page);
		return json_encode($data);
	}


	/***
    *
    *项目相关
    *
    ***/

	/***
    *
    *获取项目相关信息
    *
    ***/
	public function adminProgram(){
		$rs = getProgramList();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
		$data['manager_list'] = getManagerList();
		return json_encode($data);
	}


	/***
    *
    *保存项目相关信息
    *
    ***/
    public function saveProgram(){
    	$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type = '添加';
		if(isset($arr['proId'])){
			if(!empty($arr['proId'])){
				$id = $arr['proId'];
				$where['proId'] = $id;
				$update = true;
				$type = "编辑";
			}
			unset($arr['proId']);
		}
		$m = new PM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'项目');
			$data['status'] = 1;
			$data['msg'] = 'ok';
			$data['data'] = getProgramList();
			return json_encode($data);
		}
    }



    /***
    *
    *获取项目相关信息
    *
    ***/
	public function adminClass(){
		$proId = input('proId/d');
		if(empty($proId)){
			$rs = array();
		}else{
			$rs = getClassList($proId,1);
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
		$data['user_list'] = getUserList($proId,1);
		$data['program_list'] = getProgramList();
		return json_encode($data);
	}


	/***
    *
    *保存项目相关信息
    *
    ***/
    public function saveClass(){
    	$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type = '添加';
		if(isset($arr['classId'])){
			if(!empty($arr['classId'])){
				$id = $arr['classId'];
				$where['classId'] = $id;
				$update = true;
			}
			unset($arr['classId']);
			$type = '编辑';
		}
		$m = new CM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'班级');
			$data['status'] = 1;
			$data['msg'] = $arr;
			$data['data'] = getClassList();
			return json_encode($data);
		}
    }


    /***
    *
    *删除班级
    *
    ***/
    public function delClass(){
    	$id = input("classId/d");
    	if(empty($id)){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误";
    		$data['data'] = $input;
    		return json_encode($data);
    		exit;
    	}
    	$where['classId'] = $id;
    	$m = new CM();
    	$rs = $m->where($where)->delete();
    	$err = $m->getError();
    	if(empty($rs) && !empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
		}
		$strs = '删除了一个用户,ID为' . $id;
		\app\model\Logs::saveLog($strs,'删除','班级');
    	$data['status'] = 1;
    	$data['msg'] = $rs;
    	$data['data'] = $id;
    	return json_encode($data);
    }


    /***
    *
    *课程相关
    *
    ***/
     /***
    *
    *获取课程相关信息
    *
    ***/
	public function adminCourse(){
		$classId = input("classId/d");
		$groupId = input('groupId/d');
		if(empty($classId)){
			$rs = array();
		}else{
			$rs = getCourseList($classId,0,$groupId);
		}
		if(empty($groupId)){
			$user = session('SH_USERS');
			if(isset($user['proId']) && !empty($user['proId'])){
				$group_list = getGroupByProgram($user['proId']);
			}else{
				$group_list = getAllGroup(1);
			}
			$md = 1;
		}else{
			$group_list = array();
			$md = 0;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
		$data['classId'] = $classId;
		$data['group_list'] = $group_list;
		$data['class_list'] = getClassesByUserLogin();
		$data['model'] = $md;
		return json_encode($data);
	}

     /***
    *
    *保存课程相关信息
    *
    ***/
     public function saveCourse(){
     	$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type='添加';
		if(isset($arr['courseId'])){
			if(!empty($arr['courseId'])){
				$id = $arr['courseId'];
				$where['courseId'] = $id;
				$update = true;
			}
			unset($arr['courseId']);
			$type = '编辑';
		}
		$m = new COM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			if(isset($arr['groupId'])){
				$groupId = $arr['groupId'];
			}else{
				$groupId = 0;
			}
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'课程');
			$data['status'] = 1;
			$data['msg'] = $arr;
			$data['data'] = getCourseList($arr['classId'],0,$groupId);
			return json_encode($data);
		}
     }


     /***
    *
    *删除课程
    *
    ***/
    public function delCourse(){
    	$id = input("courseId/d");
    	if(empty($id)){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误";
    		$data['data'] = $input;
    		return json_encode($data);
    		exit;
    	}
    	$where['courseId'] = $id;
		$m = new COM();
		$info = $m->where(array('courseId'=>$id))->find();
    	$rs = $m->where($where)->delete();
    	$err = $m->getError();
    	if(empty($rs) && !empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
		}
		if(empty($info)){
			$strs = '删除了一个课程,ID为'. $id;
		}else{
			$strs = '删除了一门叫做《' . $info['courseName'] . '》的课程';
		}		
		\app\model\Logs::saveLog($strs,'删除','课程');
    	$data['status'] = 1;
    	$data['msg'] = $rs;
    	$data['data'] = $id;
    	return json_encode($data);
    }


    /***
    *
    *获奖信息相关
    *
    ***/
     /***
    *
    *获取奖励列表
    *
    ***/
	public function adminAward(){
		$tmp = input();
		$md = 1;
		$arrs = getInput($tmp);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(!empty($where)){
			$rs = getAwardList($where,$page);
			if(count($rs) > 0){
				foreach($rs as $k=>$v){
					$rs[$k]['awardTime'] = date("Y-m-d",$v['awardTime']);
				}
			}
		}else{
			$rs = array();
			$md = 0;
		}
		$class_list = getClassList(0,1);
    	$arr = array();
    	$ses = session("SH_USERS");
    	$car = $ses['classArr'];
    	if(empty($car) || empty($class_list)){
    		$arr = array();
    	}else{
			foreach($class_list as $k=>$v){
				if(in_array($v['className'],$car)){
					$arr[] = $v;
				}
			}
    	}
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		$data['model'] = $md;
		$data['program_list'] = getProgramList();
		$data['class_list'] = $arr;
		$data['user_list'] = getUserList(0,1);
		return json_encode($data);
	}

	 /***
    *
    *保存获奖信息
    *
    ***/
	public function saveAward(){
		$tmp = input();
		$arrs = getInput($tmp);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$wh = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$wh[$k] = $v;
				}
			}			
		}else{
			$wh = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['data'])){
			$arr = $arrs['data'];
		}else{
			$arr = array();
		}
		$id = 0;
		$update = false;
		$type = "添加";
		if(isset($arr['awardId'])){
			if(!empty($arr['awardId'])){
				$id = $arr['awardId'];
				$where['awardId'] = $id;
				$update = true;
			}
			unset($arr['awardId']);
			$type = '编辑';
		}else{
			$where = array();
		}
		if(!empty($arr)){
			if(isset($arr['awardTime']) && !empty($arr['awardTime'])){
				$arr['awardTime'] = strtotime($arr['awardTime']);
			}else{
				$arr['awardTime'] = strtotime(date("Y-m-d",time()));
			}
		}
		$m = new AM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'获奖');
			$data['status'] = 1;
			$data['where'] = $wh;
			$data['msg'] = $page;
			$list = getAwardList($wh,$page);
			if(!empty($list)){
				foreach($list as $k=>$v){
					$list[$k]['awardTime'] = date("Y-m-d",$v['awardTime']);
				}
			}
			$data['data'] = $list;
			return json_encode($data);
		}
	}


	public function delAward(){
		$id = input('awardId/d');
		if($id <= 0){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$m = new AM();
		$where['awardId'] = $id;
		$info = $m->where($where)->find();
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($info)){
			$strs = '删除了一个ID为' . $id . '的奖励';
		}else{
			$strs = '删除了一个关于"' . $info['proName'] . $info['className'] . $info['studentName'] . $info['awardInfo'] . '"的奖励信息';
		}
		$strs = implode('-',$arr);
		\app\model\Logs::saveLog($strs,'删除','获奖');
		$data['status'] = 1;
		$data['msg'] = "删除成功";
		$data['data'] = $id;
		return json_encode($data);
	}



	/***
    *
    *菜单相关信息
    *
    ***/
    /***
    *
    *菜单列表
    *
    ***/
    public function adminMenu(){
	 	$m = new MM();
		$list = $m->getMenuList(0,1);
		$top_list = array();
		$menu_list = array();
		$sub_list = array();
		if(isset($list['top_list'])){
			$top_list = $list['top_list'];
		}
		if(isset($list['menu_list'])){
			$menu_list = $list['menu_list'];
		}
		if(isset($list['sub_list'])){
			$sub_list = $list['sub_list'];
		}
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data']['top_list'] = $top_list;
		$data['data']['menu_list'] = $menu_list;
		$data['data']['sub_list'] = $sub_list;
		$data['data']['list'] = $list;
		return json_encode($data);
	}

    /***
    *
    *保存菜单
    *
    ***/

	public function saveMenu(){
		$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type='添加';
		$m = new MM();
		if(isset($arr['menuId'])){
			if(!empty($arr['menuId'])){
				$id = $arr['menuId'];
				$where['menuId'] = $id;
				$update = true;
				if(isset($arr['parentId']) && $id == $arr['parentId']){
					$data['status'] = -1;
					$data['msg'] = "不能把自身做为上级菜单";
					$data['data'] = $arr;
					return json_decode($data);
					exit;
				}
			}
			unset($arr['menuId']);
			$type = "编辑";
		}
		if(empty($arr['isSub']) || empty($arr['isSub'])){
			$isSub = false;
		}else{
			$isSub = true;
		}
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$strs = implode('-',$arr);
		\app\model\Logs::saveLog($strs,$type,'菜单');
		$data['status'] = 1;
		$data['msg'] = $arr;
		$list = $m->getMenuList();
    	$top_list = array();
    	$menu_list = array();
    	if(isset($list['top_list'])){
    		$top_list = $list['top_list'];
    	}
    	if(isset($list['menu_list'])){
    		$menu_list = $list['menu_list'];
    	}
    	$data['data']['top_list'] = $top_list;
    	$data['data']['menu_list'] = $menu_list;
		return json_encode($data);
    }

	/***
    *
    *删除菜单
    *
    ***/
	public function delMenu(){
		$id = input("menuId/d");
		if($id <= 0){
			$data['status'] = -1;
			$data['msg'] = "参数错误，请指定一个菜单ID";
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		$m = new MM();
		$tmp = $m->haveSons($id);
		if($tmp){
			$data['status'] = -1;
			$data['msg'] = "当前菜单下还存在子菜单，请先删除所有子菜单。";
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		$where['menuId'] = $id;
		$info = $m->where($where)->find();
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		if(empty($info)){
			$strs = '删除了ID为' . $id . '的菜单';
		}else{
			$strs = '删除了名为' . $info['menuName'] . '的菜单';
		}
		\app\model\Logs::saveLog($strs,'删除','菜单');
		$data['status'] = 1;
		$data['msg'] = "删除成功";
		$data['data'] = $id;
		return json_encode($data);
	}


	/***
    *
    *学生管理
    *
    ***/

    /***
    *
    *学生列表
    *
    ***/
	public function adminStudent(){
		$program_list = getProgramList(1);
		$class_list = getClassList(0,1);
		$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			$data['program_list'] = $program_list;
			$data['class_list'] = $class_list;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(empty($where)){
			$pages['current_page'] = 1;
			$pages['last_page'] = 1;
			$per_page = 20;
			if(isset($arrs['page']['per_page']) && !empty($arrs['page']['per_page'])){
				$per_page = $arrs['page']['per_page'];
			}
			$pages['per_page'] = $arrs['page']['per_page'];
			$pages['data'] = array();
			$data['status'] = 1;
			$data['msg'] = "请输入查询条件";
			$data['data'] = $pages;
			$data['program_list'] = $program_list;
			$data['class_list'] = $class_list;
			return json_encode($data);
			exit;
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		$m = new SM();
		$rs = $m->getStudentList($where,$page);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			$data['program_list'] = $program_list;
			$data['class_list'] = $class_list;
			return json_encode($data);
			exit;
		}
		$data['data'] = $rs;
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['program_list'] = $program_list;
		$data['class_list'] = $class_list;
		$data['arrs'] = $page;
		$data['where'] = $where;
		return json_encode($data);
	}


	/***
    *
    *保存学生信息
    *
    ***/
	public function saveStudent(){
		$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		if(!isset($arrs['data']) || empty($arrs['data'])){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$dt = $arrs['data'];
		$m = new SM();
		$rs = $m->saveStudent($dt);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$rs = $m->getStudentList($where,$page);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		return json_encode($data);
	}

	/***
    *
    *删除学生信息
    *
    ***/

    public function delStudent(){
    	$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		$dt = $arrs['data'];
		if(isset($dt['birthDay'])){
			$dt['birthDay'] = strtotime($dt['birthDay']);
		}
		if(!isset($dt['studentId']) || empty($dt['studentId'])){
			$data['status'] = -1;
			$data['msg'] = "学生ID错误.请正确选择!";
			$data['data'] = $data;
			return json_encode($data);
			exit;
		}
		$wh["studentId"] = $dt['studentId'];
		$m = new SM();
		$info = $m->where(array('studentId'=>$dt['studentId']))->find();
		$rs = $m->where($wh)->delete();
		$err  = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($info)){
			$strs = '删除了ID为' . $dt['studentId'] . '的学生';
		}else{
			$strs = '删除了名为"' . $info['studentName'] . '"的学生';
		}
		\app\model\Logs::saveLog($strs,$type,'学生');
		$rs = $m->getStudentList($where,$page);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		return json_encode($data);
	}
	

	/***
    *
    *根据姓名查找学员信息
    *
    ***/

    public function searchStudent(){
    	$str = input('studentName');
    	if(!isset($str)){
    		$data['status'] = 1;
    		$data['msg'] = 'ok';
    		$data['data'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$m = new SM();
    	$list = $m->getStudent($str);
    	$err = $m->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['data'] = $list;
    	return json_encode($data);
    }

	/***
    *
    *根据班级Id查询学员信息
    *
    ***/
    public function listStudent(){
    	$id = input("classId/d");
    	if(empty($id)){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误,请选择正确的班级ID";
    		$data['data'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$rs = getStudentListByClass($id,1);
    	$data['status'] = 1;
    	$data['msg'] = 'ok';
    	$data['data'] = $rs;
    	return json_encode($data);
	}
	
	/***
    *
    *由excel批量导入学员信息
    *
    ***/
	public function importStudent(){
		$proId = input('proId/d');
		$classId = input('classId/d');
		if(empty($proId) || empty($classId)){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$up = uploadFile();
		if(empty($up)){
			$data['status'] = -1;
			$data['msg'] = '在上传文件时发生致命错误,请联系管理员';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if($up['status'] != 1){
			$data['msg'] = -1;
			$data['msg'] = '上传文件失败,代码为:' . $up['msg'];
			$data['data'] = $up['data'];
			return json_encode($data);
			exit;
		}
		$fileName = $up['data'];
		$list = getExcelData($fileName);
		if($list == -1){
			$data['msg'] = -1;
			$data['msg'] = '您上传的文件格式有部题无法获取有效数据,请重新下载文件模板';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($list)){
			$list = array();
		}else{
			foreach($list as $k=>$v){
				$list[$k]['proId'] = $proId;
				$list[$k]['classId'] = $classId;
				$list[$k]['birthDay'] = getBirthDay($v['IDCard']);
			}
		}
		$m = new SM();
		$rs = $m->allowField(true)->saveAll($list);
		$err = $m->getError();
		if(empty($rs) || !empty($err)){
			$data['status'] = -1;
			$data['msg'] = '保存学员信息时失败,错误代码为:' . $err;
			$data['data'] = $list;
			return json_encode($data);
			exit;
		}
		$strs = '从文件' . $fileName . '中导出学生信信并保存到数据库之中';
		\app\model\Logs::saveLog($strs,'添加','学生');
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $list;
		return json_encode($data);
	}


	public function exportStudent(){
		$where = input();
		$m = new SM();
		$rs = collection($m->where($where)->field("studentNumber,studentName,gender,IDCard,birthDay,address,mobile")->select())->toArray();
		if(empty($rs)){
			$rs = array();
		}else{
			foreach($rs as $k=>$v){
				$rs[$k]['gender'] = $v['gender'] > 0 ? '男' : '女';
			}
		}
		$field = ['studentNumber','studentName','gender','IDCard','birthDay','address','mobile'];
		$desc = ['学号','姓名','性别','身份证','出生日期','家庭住址','电话号码'];
		$fileName = "学生信息";
		makeRport($rs,$desc,$field,$fileName);
	}



    /***
    *
    *显示分组信息信息
    *
    ***/
    public function adminGroup(){
    	$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			$data['class_list'] = getClassesByUserLogin();
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}
			if(!isset($where['classId']) || empty($where['classId'])){
				/*classId为空时,指定一个不可能查到的值 */
				$where['classId'] = "asdf";
			}
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		$m = new GM();
		$rs = $m->getGroupList($where,$page);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			$data['class_list'] = getClassesByUserLogin();
			return json_encode($data);
			exit;
		}
		$data['data'] = $rs;
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['class_list'] = getClassesByUserLogin();
		return json_encode($data);
    }


    /***
    *
    *保存分组信息
    *
    ***/
	public function saveGroup(){
		$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		if(!isset($arrs['data']) || empty($arrs['data'])){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$dt = $arrs['data'];
		$m = new GM();
		$rs = $m->saveGroup($dt);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$rs = $m->getGroupList($where,$page);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		return json_encode($data);
	}


	/***
    *
    *删除分组信息
    *
    ***/

    public function delGroup(){
    	$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		$dt = $arrs['data'];
		if(!isset($dt['groupId']) || empty($dt['groupId'])){
			$data['status'] = -1;
			$data['msg'] = "分组ID错误.请正确选择!";
			$data['data'] = $data;
			return json_encode($data);
			exit;
		}
		$wh["groupId"] = $dt['groupId'];
		$m = new GM();
		$info = $m->where(array('groupId'=>$dt['groupId']))->find();
		$rs = $m->where($wh)->delete();
		$err  = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($info)){
			$strs = '删除了一个ID为' . $dt['groupId'] . '的分组';
		}else{
			$strs = '删除了一个名为' . $info['groupName'] . '的分组';
		}
		\app\model\Logs::saveLog($strs,'删除','学生');
		$rs = $m->getGroupList($where,$page);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		return json_encode($data);
	}

	

    /***
    *
    *批量导入学员成绩
    *
    ***/
	public function importMark(){
		$err_list = array();
		$classId = input('classId/d');
		$groupId = input('groupId/d');
		if(empty($classId) || empty($groupId)){
			$data['status'] = -1;
			$data['msg'] = '参数错误-01';
			$data['data'] = input();
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$up = uploadFile();
		if(empty($up)){
			$data['status'] = -1;
			$data['msg'] = '在上传文件时发生致命错误,请联系管理员-02';
			$data['data'] = array();
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		if($up['status'] != 1){
			$data['msg'] = -1;
			$data['msg'] = '上传文件失败,代码为:' . $up['msg'];
			$data['data'] = $up['data'];
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$fileName = $up['data'];
		$list = getExcelDataToArray($fileName,3);
		if(empty($list)){
			$data['data'] = -1;
			$data['msg'] = '没有查到可用数据-03';
			$data['data'] = $list;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$course = new COM();
		$courseList = $course->where(array('classId'=>$classId,'dataFlag'=>1))->select();
		if(empty($courseList)){
			$data['status'] = -1;
			$data['msg'] = '你的班级还没有设置课程,请先添加相应课程再次尝试.-04';
			$data['data'] = array();
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$temp_course = array();
		$tar = $list[0];
		if(count($tar)<2){
			$data['status'] = -1;
			$data['msg'] = '在EXCEL表中没有找到课程,请您确保数据完成性-05';
			$data['data'] = $list;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		unset($tar[0]);
		unset($tar[1]);
		if(count($tar) > count($courseList)){
			$data['status'] = -1;
			$data['msg'] = 'EXCEL表中的课程数量要多于您所在班级中设置的课程数量,请确认课程后再次尝试!-06';
			$data['data'] = $list;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$course_list = array();
		foreach($tar as $k=>$v){
			foreach($courseList as $kk=>$vv){
				if($v == $vv['courseName']){
					$course_list[] = $vv['courseId'];
					break;
				}
			}
		}
		if(count($course_list) != count($tar)){
			$data['status'] = -1;
			$data['msg'] = 'EXCEL表中的课程与班级所设置的课程不一致,请确认后再次尝试';
			$data['data'] = $course_list;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		unset($list[0]);
		$arr = array();
		foreach($list as $k=>$v){
			$temp = array();
			$studentId = getStudentIdByName($v[1],$classId,$v[0]);
			if(empty($studentId)){
				$err_list[] = '在学员表中没有查到"' . $v[1] . '",成绩无法添加';
			}else{
				for($i=2;$i<count($v);$i++){
					$temp['studentId'] = $studentId;
					$temp['courseId'] = $course_list[$i-2];
					$temp['markGroup'] = $groupId;
					$temp['score'] = $v[$i];
					$temp['dataFlag'] = 1;
					$arr[] = $temp;
				}
			}
		}
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = '在处理数据时出现错误,没有可以写入成绩的数据';
			$data['data'] = $arr;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$m = new MAM();
		$rs = collection($m->allowField(true)->saveAll($arr))->toArray();
		$err = $m->getError();
		if(!empty($err) || empty($rs)){
			$data['ststus'] = -1;
			$data['msg'] = '在保存数据时失败,错误代码为:' . $err;
			$data['data'] = $arr;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$strs = '从文件' . $fileName . '导入成绩信息';
		\app\model\Logs::saveLog($strs,'添加','成绩');
		$data['status'] = 1;
		$data['msg'] = '批量添加成功';
		$data['data'] = $rs;
		$data['err'] = $err_list;
		return json_encode($data);
	}

    /***
    *
    *查询成绩录入相关信息
    *
    ***/
    public function adminMarkInput(){
    	$input = input();
    	if(isset($input['studentId']) && !empty($input['studentId']) && isset($input['markGroup']) && !empty($input['markGroup'])){
    		$list = getMarkList($input['studentId'],$input['markGroup']);
    	}else{
    		$list = array();
    	}
    	$class_list = getClassList(0,1);
    	$arr = array();
    	$ses = session("SH_USERS");
    	$car = $ses['classArr'];
    	if(empty($car) || empty($class_list)){
    		$arr = array();
    	}else{
			foreach($class_list as $k=>$v){
				if(in_array($v['className'],$car)){
					$arr[] = $v;
				}
			}
    	}
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['data'] = $list;
    	$data['course_list'] = getCourseList(0,1);
    	$data['program_list'] = getProgramList(1);
		$data['class_list'] = $arr;
		$data['group_list'] = getAllGroup(1);
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
		$classId = input('classId/d');
		$course_list = getCourseList($classId,1,$markGroup);
    	if(empty($studentId) || empty($markGroup)){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误,必须提供学生ID和成绩分组";
			$data['data'] = array();
			$data['course_list'] = $course_list;
    		return json_encode($data);
    		exit;
		}
		if(empty($classId)){
			$tmparr = Db::name('student')->where(array('studentId'=>$studentId))->find();
			if(!empty($tmparr)){
				$classId = $tmparr['classId'];
			}
		}
    	$m = new MAM();
    	$list = $m->getMarkList($studentId,$markGroup);
    	$err = $m->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
			$data['data'] = array();
			$data['course_list'] = $course_list;
    		return json_encode($data);
    		exit;
    	}
    	$data['status'] = 1;
    	$data['msg'] = "ok";
		$data['data'] = $list;
		$arr['classId'] = $classId;
		$arr['groupId'] = $markGroup;
		$data['arr'] = $arr;
		$data['course_list'] = $course_list;
    	return json_encode($data);
    }

    /***
    *
    *成绩录入
    *
    ***/
    public function saveMark(){
		$input = input();
		if(empty($input)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $input;
			return json_encode($data);
    		exit;
		}
		$arr = getInput($input);
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "数据错误";
			$data['data'] = $arr;
			return json_encode($data);
    		exit;
		}
		$arg = [];
		foreach($arr as $k=>$v){
			$arg[] = $v;
		}
		$update = array();
		$add = array();
		foreach($arg as $k=>$v){
			if(empty($v['markId'])){
				unset($arg[$k]['markId']);
			}
		}
		$err_list = array();
		$m = new MAM();
		$list = $m->inputMarks($arg);
		if(empty($list)){
			$data['status'] = -1;
			$data['msg'] = $m->getError();
			$data['data'] = $arg;
			return json_encode($data);
    		exit;
		}
		if(count($list) < count($arg)){
			$err_list = array();
			foreach($arg as $k=>$v){
				$tmp = $m->recInArray($v,$list);
				if(empty($tmp)){
					$err_list[] = $v;
				}
			}
			$data['status'] = 2;
			$data['msg'] = "部分记录添加失败.错误代码为" . $m->getError();
			$data['data'] = $list;
			$data['err_list'] = $err_list;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $list;
		$data['add'] = $arg;
		return json_encode($data);
		exit;
    }

    /***
    *
    *成绩列表
    *
    ***/
    public function adminMark(){
    	$class_list = getClassList(0,1);
    	
		$ses = session("SH_USERS");
		if($ses['isManager'] != 1){
			$arr = array();
			$car = $ses['classArr'];
			if(empty($car) || empty($class_list)){
				$arr = array();
			}else{
				foreach($class_list as $k=>$v){
					if(in_array($v['className'],$car)){
						$arr[] = $v;
					}
				}
			}
			$class_list = $arr;
		}    	
    	$data['status'] = 1;
    	$data['msg'] = 'ok';
		$data['program_list'] = getProgramList(1);
		$data['class_list'] = $class_list;
		$data['group_list'] = getAllGroup(1);
		return json_encode($data);
    }


    /***
    *
    *根据班级和成绩分组查询成绩
    *
    ***/
    public function getMarkListByClass(){
    	$classId = input('classId/d');
    	$groupId = input('groupId/d');
    	if(empty($classId) || empty($groupId)){
    		$data['status'] = -1;
    		$data['msg'] = "必须提供班级和成绩分组信息";
    		$data['data'] = input();
    		$data['scoreList'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$s = new SM();
    	$where['classId'] = $classId;
    	$where['dataFlag'] = 1;
    	$students = $s->where($where)->field("studentId,studentName,studentNumber")->order("studentId ASC")->select();
    	$err = $s->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = "获取学生列表失败";
    		$data['data'] = array();
    		$data['scoreList'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$m = new MAM();
    	$list = $m->getClassMark($classId,$groupId);
    	$list = getMarkGroup($list);
    	if(isset($list['scoreType']) && !empty($list['scoreType'])){
    		$st = $list['scoreType'];
    		$scoreList = array();
    		foreach($st as $k=>$v){
    			$slr = array();
    			$slr['courseName'] = $v;
    			$slr['score'] = 0;
    			$scoreList[] = $slr;
    		}
    	}else{
    		$scoreList = array();
    	}
    	$ls = $list['list'];
    	foreach($students as $k=>$v){
    		$bl = inMarkList($v['studentId'],$ls);
    		if($bl >= 0){
    			$tmpList = $ls[$bl]['scoreList'];
    			$sum = 0;
    			foreach($tmpList as $kk=>$vv){
    				$sum = $sum + $vv['score'];
    			}
    			$students[$k]['scoreList'] = $ls[$bl]['scoreList'];
    			$students[$k]['scoreSum'] = $sum;
    			$ln = count($scoreList);
    			if($ln > 0){
    				$students[$k]['avg'] = sprintf("%.2f", $sum/$ln);
    			}else{
    				$students[$k]['avg'] = 0;
    			}
    		}else{
    			$students[$k]['scoreList'] = $scoreList;
    			$students[$k]['scoreSum'] = 0;
    			$students[$k]['avg'] = 0;
    		}
    	}
    	$tplist = array_column($students,'scoreSum');
    	array_multisort($tplist,SORT_DESC,$students);
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['scoreList'] = $scoreList;
    	$data['data'] = $students;
    	return json_encode($data);
    }
    /***
    *
    *根据班级和成绩分组查询并导出Excel
    *
    ***/
    public function exportMarkByClass(){
    	$classId = input('classId/d');
    	$groupId = input('groupId/d');
    	if(empty($classId) || empty($groupId)){
    		print_r("必须提供班级和成绩分组信息");
    		exit;
    	}
    	$s = new SM();
    	$where['classId'] = $classId;
    	$where['dataFlag'] = 1;
    	$students = $s->where($where)->field("studentId,studentName,studentNumber")->order("studentId ASC")->select();
		$err = $s->getError();
    	if(!empty($err)){
    		print_r("获取学生列表失败");
    		exit;
    	}
    	$m = new MAM();
    	$list = $m->getClassMark($classId,$groupId);
    	$list = getMarkGroup($list);
    	if(isset($list['scoreType']) && !empty($list['scoreType'])){
    		$st = $list['scoreType'];
    		$scoreList = array();
    		foreach($st as $k=>$v){
    			$slr = array();
    			$slr['courseName'] = $v;
    			$slr['score'] = 0;
    			$scoreList[] = $slr;
    		}
    	}else{
    		$scoreList = array();
    	}
		$ls = $list['list'];
    	foreach($students as $k=>$v){
    		$bl = inMarkList($v['studentId'],$ls);
    		if($bl >= 0){
    			$tmpList = $ls[$bl]['scoreList'];
    			$sum = 0;
    			foreach($tmpList as $kk=>$vv){
    				$sum = $sum + $vv['score'];
    			}
    			$students[$k]['scoreList'] = $ls[$bl]['scoreList'];
    			$students[$k]['scoreSum'] = $sum;
    			$ln = count($scoreList);
    			if($ln > 0){
    				$students[$k]['avg'] = sprintf("%.2f", $sum/$ln);
    			}else{
    				$students[$k]['avg'] = 0;
    			}
    		}else{
    			$students[$k]['scoreList'] = $scoreList;
    			$students[$k]['scoreSum'] = 0;
    			$students[$k]['avg'] = 0;
    		}
    	}
    	$tplist = array_column($students,'scoreSum');
    	array_multisort($tplist,SORT_DESC,$students);
    	if(!empty($students)){
    		$i = 1;
    		foreach($students as $k=>$v){
    			$tmprs = makeMarkList($list['scoreType'],$v['scoreList']);
    			if(count($tmprs) > 0){
    				foreach($tmprs as $kk=>$vv){
    					$students[$k][$kk] = $vv;
    				}
    			}else{
					if(!empty($list['scoreType'])){
						foreach($list['scoreType'] as $kk=>$vv){
							$students[$k][$kk] = $vv;
						}
					}
    			}
    			$students[$k]['sort'] = $i;
    			$i++;
    			unset($students[$k]['studentId']);
    			unset($students[$k]['scoreList']);
    		}
    		$tmparr = array();
    		foreach($students as $k=>$v){
    			$tmp['studentNumber'] = $v['studentNumber'];
				$tmp['studentName'] = $v['studentName'];
				if(!empty($list['scoreType'])){
					foreach($list['scoreType'] as $kk=>$vv){
						$tmp[$vv] = $v[$vv];
					}
				}else{
					$list['scoreType'] = array();
				}
    			$tmp['scoreSum'] = $v['scoreSum'];
    			$tmp['avg'] = $v['avg'];
    			$tmp['sort'] = $v['sort'];
    			$tmparr[] = $tmp;
    		}
    		$students = $tmparr;
		}
    	$field = ["studentNumber","studentName"];
    	$field = array_merge($field,$list['scoreType']);
    	$field[] = "scoreSum";
    	$field[] = "avg";
    	$field[] = "sort";
    	$desc = ['学号','姓名'];
    	$desc = array_merge($desc,$list['scoreType']);
    	$desc[] = "总分";
    	$desc[] = "平均分";
    	$desc[] = "排名";
		$fileName = getReportInfo($classId);
    	makeRport($students,$desc,$field,$fileName);
    }

    /***
    *
    *查询千分制规则列表
    *
    ***/
    public function adminScoreRole(){
    	$m = new SCM();
    	$list = $m->getScoreRoleList();
    	$data['status'] = 1;
    	$data['msg'] = 'ok';
    	$data['data'] = $list;
    	return json_encode($data);
    }


    /***
    *
    *保存千分制规则
    *
    ***/

    public function saveScoreRole(){
    	$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type = '添加';
		$m = new SCM();
		if(isset($arr['itemId'])){
			if(!empty($arr['itemId'])){
				$id = $arr['itemId'];
				$where['itemId'] = $id;
				$update = true;
			}
			unset($arr['itemId']);
			$type = '编辑';
		}
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$strs = implode('-',$arr);
		\app\model\Logs::saveLog($strs,$type,'千分制');
		if($update){
			$arr['itemId'] = $id;
		}else{
			$arr['itemId'] = $m->itemId;
		}
		$data['status'] = 1;
		$data['msg'] = "保存成功";
		$data['data'] = $arr;
		return json_encode($data);
	}
	


    /***
    *
    *删除千分制规则
    *
    ***/

    public function delScoreRole(){
    	$id = input("itemId/d");
		if($id <= 0){
			$data['status'] = -1;
			$data['msg'] = "参数错误，请指定一个千分制规则ID";
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		$m = new SCM();
		$where['itemId'] = $id;
		$info = $m->where(array('itemId'=>$id))->find();
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		if(empty($info)){
			$strs = '删除了ID为' . $id . '的千分制规则';
		}else{
			$strs = '删除了名为' . $info['itemName'] . '千分制规则';
		}
		\app\model\Logs::saveLog($strs,'删除','千分制');
		$data['status'] = 1;
		$data['msg'] = "删除成功";
		$data['data'] = $id;
		return json_encode($data);
	}
	

	/***
    *
    *从EXCEL表中批量导入千分制规则
    *
    ***/
	public function importScoreRule(){
		$up = uploadFile();
		if(empty($up)){
			$data['status'] = -1;
			$data['msg'] = '在上传文件时发生致命错误,请联系管理员';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if($up['status'] != 1){
			$data['msg'] = -1;
			$data['msg'] = '上传文件失败,代码为:' . $up['msg'];
			$data['data'] = $up['data'];
			return json_encode($data);
			exit;
		}
		$fileName = $up['data'];
		$list = getExcelData($fileName);
		if($list == -1){
			$data['msg'] = -1;
			$data['msg'] = '您上传的文件格式有部题无法获取有效数据,请重新下载文件模板';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($list)){
			$list = array();
		}
		$m = new SCM();
		$rs = $m->allowField(true)->saveAll($list);
		$err = $m->getError();
		if(empty($rs) || !empty($err)){
			$data['status'] = -1;
			$data['msg'] = '保存千分制规则时失败,错误代码为:' . $err;
			$data['data'] = $list;
			return json_encode($data);
			exit;
		}
		$strs = '从文件' . $fileName . '中导出千分制规则并保存到数据库之中';
		\app\model\Logs::saveLog($strs,'添加','千分制');
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $list;
		return json_encode($data);
	}

	/***
    *
    *导出千分制规则到EXCEL
    *
    ***/
	public function exportScoreRule(){
		$m = new SCM();
		$list = collection($m->field('itemName,itemText,itemType,score,dataFlag')->select())->toArray();
		if(empty($list)){
			$list = array();
		}else{
			foreach($list as $k=>$v){
				if($v['dataFlag'] == 1){
					$list[$k]['dataFlag'] = '有效';
				}else{
					$list[$k]['dataFlag'] = '无效';
				}
			}
		}
		$field = ['itemName','itemText','itemType','score','dataFlag'];
		$desc = ['规则名称','说明文本','规则类型(1为加分,0为减分)','分值','是否有效'];
		$fileName = "千分制规则信息";
		makeRport($list,$desc,$field,$fileName);
	}

    /***
    *
    *查询千分制规则列表
    *
    ***/
    public function adminScore(){
		$class_list = getClassList(0,1);
		$ses = session("SH_USERS");
		if($ses['isManager'] != 1){
			$arr = array();
			$car = $ses['classArr'];
			if(empty($car) || empty($class_list)){
				$arr = array();
			}else{
				foreach($class_list as $k=>$v){
					if(in_array($v['className'],$car)){
						$arr[] = $v;
					}
				}
			}
			$class_list = $arr;
		}    	
    	$data['status'] = 1;
    	$data['msg'] = 'ok';
		$data['program_list'] = getProgramList(1);
		$data['class_list'] = $class_list;
		$m = new SCM();
		$list = $m->getScoreRoleList();
		$data['rule_list'] = $list;    	
		return json_encode($data);
	}
	

    /***
    *
    *查询千分制规则列表(录入用)
    *
    ***/
    public function adminScoreInput(){
		$class_list = getClassList(0,1);
		$ses = session("SH_USERS");
		$arr = array();
		$car = $ses['classArr'];
		if(empty($car) || empty($class_list)){
			$arr = array();
		}else{
			foreach($class_list as $k=>$v){
				if(in_array($v['className'],$car)){
					$arr[] = $v;
				}
			}
		}
		$class_list = $arr;   	
    	$data['status'] = 1;
    	$data['msg'] = 'ok';
		$data['program_list'] = getProgramList(1);
		$data['class_list'] = $arr;
		$m = new SCM();
		$list = $m->getScoreRoleList(-1,1);
		$data['rule_list'] = $list;    	
		return json_encode($data);
	}

    /***
    *
    *根据班级查询千分制总分
    *
    ***/
    public function getScoreListByClass(){
    	$classId = input('classId/d');
    	if(empty($classId)){
    		$data['status'] = -1;
    		$data['msg'] = "必须提供班级信息";
    		$data['data'] = input();
    		$data['scoreList'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$s = new SM();
    	$where['classId'] = $classId;
    	$where['dataFlag'] = 1;
    	$students = $s->where($where)->field("studentId,studentName,studentNumber")->order("studentId ASC")->select();
    	$err = $s->getError();
    	if(!empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = "获取学生列表失败";
    		$data['data'] = array();
    		$data['scoreList'] = array();
    		return json_encode($data);
    		exit;
    	}
    	$m = new SCRM();
    	$list = $m->getScoreByClass($classId);
    	if(empty($list)){
    		foreach($students as $k=>$v){
    			$students[$k]['score'] = 1000;
    			$students[$k]['points'] = 0;
    		}
    	}else{
    		foreach($students as $k=>$v){
	    		$bl = inArrayByKey($v['studentId'],'studentId',$list);
				if($bl >= 0){
					$points = $list[$bl]['score'];
					$students[$k]['score'] = 1000 + $list[$bl]['score'];
					$students[$k]['points'] = $points;
	    		}else{
	    			$students[$k]['score'] = 0;
	    			$students[$k]['points'] = 0;
	    		}
	    	}
	    	$tplist = array_column($students,'score');
	    	array_multisort($tplist,SORT_DESC,$students);
	    	
    	}
    	
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['data'] = $students;
    	return json_encode($data);
	}

	
    /***
    *
    *批量导入千分制信息
    *
    ***/
	public function importScoreInput(){
		$err_list = array();
		$classId = input('classId/d');
		if(empty($classId)){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = input();
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$up = uploadFile();
		if(empty($up)){
			$data['status'] = -1;
			$data['msg'] = '在上传文件时发生致命错误,请联系管理员-';
			$data['data'] = array();
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		if($up['status'] != 1){
			$data['msg'] = -1;
			$data['msg'] = '上传文件失败,代码为:' . $up['msg'];
			$data['data'] = $up['data'];
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$fileName = $up['data'];
		$list = getExcelData($fileName);
		if($list == -1){
			$data['msg'] = -1;
			$data['msg'] = '您上传的文件格式有部题无法获取有效数据,请重新下载文件模板';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($list)){
			$data['data'] = -1;
			$data['msg'] = '没有查到可用数据';
			$data['data'] = $list;
			$data['err'] = $err_list;
			return json_encode($data);
			exit;
		}
		$arr = array();
		if(empty($list)){
			$data['status'] = -1;
			$data['msg'] = '在Excel中没有读取到有效的数据';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		foreach($list as $k=>$v){
			$studentId = getStudentIdByName($v['studentName'],$classId,$v['studentNumber']);
			if(empty($studentId)){
				$err_list[] = "在学员表中,没有查到" . $arr['studentName'] . "(" . $arr['studentNumber'] . ')的信息,添加千分制信息失败';
				continue;
			}
			if(empty($v['opTime'])){
				$opTime = time();
			}else{
				$opTime = strtotime($v['opTime']);
			}
			$arr[] = array('studentId'=>$studentId,'scoreRule'=>$v['scoreRule'],'recordMem'=>$v['recordMem'],'score'=>$v['score'],'opTime'=>$opTime);
		}
		$m = new SCRM();
		$rs = $m->allowField(true)->saveAll($arr);
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = '在保存数据时失败,错误代码为:' . $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		if(empty($rs)){
			$data['status'] = -1;
			$data['msg'] = '在保存数据时失败,出现未知错误';
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$strs = '从文件' . $fileName . '中导入千分制扣分信息';
		\app\model\Logs::saveLog($strs,'添加','千分制');
		$data['status'] = 1;
		$data['msg'] = '批量添加成功';
		$data['data'] = $rs;
		$data['err'] = $err_list;
		return json_encode($data);
	}
	/***
    *
    *把千分制记录的详细信息导出到Excel
    *
    ***/
	public function exportScoreInput(){

	}

    /***
    *
    *根据学生ID查询千分制记录的详细信息
    *
    ***/
    public function getScoreListBystudent(){
    	$studentId = input('studentId/d');
    	if(empty($studentId)){
    		$data['status'] = -1;
    		$data['msg'] = "必须提供班级信息";
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
    	}
    	$m = new SCRM();
    	$list = $m->getScoreListByStudent($studentId);
    	$data['msg'] = 'ok';
    	$data['status'] = 1;
    	$data['data'] = $list;
    	return json_encode($data);
    }

    /***
    *
    *根据ID查询千分制记录
    *
    ***/
    public function getScoreById(){
    	$id = input('recordId/d');
		if(empty($id)){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$m = new SCRM();
		$list = $m->getScoreById($id);
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $list;
		return json_encode($data);
	}


	/***
    *
    *根据千分制规则名称模糊查询详细信息
    *
    ***/
	public function getRuleByName(){
		$str = input('str');
		$itemType = input('itemType/d');
		if(empty($str)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$m = new SCM();
		$list = $m->getRuleByName($str,$itemType);
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $list;
		return json_encode($data);
	}

	/***
    *
    *保存千分制信息
    *
    ***/
	public function saveRoleInput(){
		$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type = '添加';
		$m = new SCRM();
		if(isset($arr['recordId'])){
			if(!empty($arr['recordId'])){
				$id = $arr['recordId'];
				$where['recordId'] = $id;
				$update = true;
			}
			unset($arr['recordId']);
			$type = '编辑';
		}
		if(isset($arr['opTime'])){
			if(!empty($arr['opTime'])){
				$arr['opTime'] = strtotime($arr['opTime']);
			}else{
				$arr['opTime'] = strtotime(date('Y-m-d',time()));
			}
		}
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		if($update){
			$arr['recordId'] = $id;
		}else{
			$arr['recordId'] = $m->recordId;
		}
		$strs = implode('-',$arr);
		\app\model\Logs::saveLog($strs,$type,'千分制');
		$data['status'] = 1;
		$data['msg'] = "保存成功";
		$data['data'] = $arr;
		return json_encode($data);
	}

	/***
    *
    *对需审核的千分制信息进行确认
    *
	***/
	public function enterScoreEdit(){
		$scoreId = input('scoreId/d');
		if(empty($scoreId)){
			$ata['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$m = new EM();
		$user = session('SH_USERS');
		$userId = $user['userId'];
		$where['scoreId'] = $scoreId;
		$where['isComplete'] = 0;
		$where['userId'] = $userId;
		$arr = $m->where($where)->find();
		$err = $m->getError();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "获取千分制记录失败,或者是该条千分制修改信息己经审核过了,请联系管理员进行确认";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$recordId = $arr['recordId'];
		$item['scoreRule'] = $arr['targetRule'];
		$item['recordMem'] = $arr['targetMem'];
		$item['score'] = $arr['targetScore'];
		$esm = new SCRM();
		$wh['recordId'] = $recordId;
		$rs = $esm->allowField(true)->isUpdate(true)->save($item,$wh);
		$err = $esm->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = '写入千分制信息失败,代码为:' . $err;
			$data['data'] = $item;
			return json_encode($data);
			exit;
		}
		$tmp['isComplete'] = 1;
		$temp['scoreId'] = $scoreId;
		$ks = $m->allowField(true)->isUpdate(true)->save($tmp,$temp);
		$error = $m->getError();
		if(!empty($error)){
			$data['status'] = -1;
			$data['msg'] = '千分制修改己经完成,请刷新网页查看,但是在写入审核信息状态时出错,请联系管理员' . $error;
			$data['data'] = $rs;
			$data['error'] = 1;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = array();
		$data['error'] = 0;
		return json_encode($data);
	}


	/***
    *
    *拒绝修改千分制分数
    *
	***/
	public function denyScoreEdit($scoreId){
		$scoreId = input('scoreId/d');
		if(empty($scoreId)){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$m = new EM();
		$where['scoreId'] = $scoreId;
		$arr['isComplete'] = 2;
		$rs = $m->allowField(true)->isUpdate(true)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$ata['status'] = -1;
			$data['msg'] = '保存千分制修改时错误,代码为:' . $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $arr;
		return json_encode($data);
		exit;
	}

	/***
    *
    *修改千分制后向项目负责人发起审核
    *
	***/
	public function examinedScoreInput(){
		$arr['recordId'] = input('recordId/d');
		if(empty($arr['recordId'])){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$arr['studentId'] = input('studentId/d');
		$arr['studentName'] = input('studentName');
		$arr['targetRule'] = input('scoreRule');
		$arr['targetMem'] = input('recordMem');
		$arr['targetScore'] = input('score');
		$arr['targetTime'] = time();
		$user = session('SH_USERS');
		$pid = $user['proId'];
		$arr['userId'] = getManagerByProId($pid);
		$arr['description'] = input('description');
		$arr['isComplete'] = 0;
		$arr['initiator'] = $user['userId'];
		$tmp =  getRecordScoreById($arr['recordId']);
		if(empty($tmp)){
			$data['status'] = -1;
			$data['msg'] = '获取源千分制操作记录失败';
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$arr['sourceRule'] = $tmp['scoreRule'];
		$arr['sourceMem'] = $tmp['recordMem'];
		$arr['sourceScore'] = $tmp['score'];
		$arr['sourceTime'] = $tmp['opTime'];
		$m = new EM();
		$rs = $m->allowField(true)->insert($arr);
		$err = $m->getError();
		if(!$rs){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$info['sender'] = $user['userId'];
		$info['receiver'] = $arr['userId'];
		$info['sendTime'] = time();
		$info['msgText'] = $user['nickName'] . '对' . $arr['studentName'] . '的千分制进行了修改,需要您的审核,请到学生管理-千分制审核中进行操作.';
		$ks = inputMsg($info);
		if(empty($ks)){
			$data['status'] = 1;
			$data['msg'] = "在为项目负责人发送千分制审核消息时失败,请联系项目负责人进行手动审核";
			$data['data'] = $info;
			$data['error'] = 1;
			return json_encode($data);
			exit;
		}
		$strs = '发起千分制修改审核,记录ID为' . $arr['recordId'] . '学生姓名为:' . $arr['studentName'];
		\app\model\Logs::saveLog($strs,'编辑','千分制');
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $arr;
		return json_encode($data);
	}

	public function checkStudentNumber(){
		$studentNumber = input('studentNumber');
		$proId = input('proId/d');
		$classId = input('classId/d');
		if(empty($studentNumber) || $proId * $classId <= 0){
			$data['status'] = 2;
			$data['msg'] = '请选指定学号、项目和班级！';
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$m = new SM();
		$where['studentNumber'] = $studentNumber;
		$where['proId'] = $proId;
		$where['classId'] = $classId;
		$rs = $m->where($where)->select();
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = 2;
			$data['msg'] = $err;
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		if(count($rs) > 0){
			$data['status'] = 1;
			$data['msg'] = $where;
			$data['data'] = $rs;
			return json_encode($data);
		}else{
			$data['status'] = -1;
			$data['msg'] = $where;
			$data['data'] = $rs;
			return json_encode($data);
		}
	}

	public function getEXScoreList(){
		$user = session('SH_USERS');
		if(!isset($user['userId']) || empty($user['userId'])){
			$data['status'] = -1;
			$data['msg'] = '获取用户信息失败';
			$data['data'] = $user;
			return json_encode($data);
			exit;
		}
		$userId = $user['userId'];
		$m = new EM();
		$list = $m->getListByUser($userId);
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = '获取千分制审核信息失败:' . $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($list)){
			$list = array();
		}
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $list;
		return json_encode($data);
	}
	


	/***
    *
    *发送消息
    *
	***/
	public function sendMsg(){
		$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$arr['sendTime'] = time();
		$m = new Msg();
		$rs = $m->saveMsg($arr);
		$err = $m->getError();
		if(empty($rs)){
			$data['status'] = -1;
			$data['msg'] = '发送消息失败';
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = array();
		return json_encode($data);
	}


	/***
    *
    *引发消息状态
    *
	***/
	public function changeMsgInvi(){
		$msgId = input('msgId/d');
		$haveRead = input('haveRead/d');
		if(empty($msgId) || empty($haveRead)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$where['msgId'] = $msgId;
		$arr['haveRead'] = $haveRead;
		$m = new Msg();
		$rs = $m->allowField(true)->isUpdate(true)->save($arr,$where);
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = '修改消息失败,代码为:' . $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$user = session('SH_USER');
		$userId = $user['userId'];
		$data['data'] = $m->getMsgByUser($userId);
		return json_encode($data);		
	}


	/***
    *
    *根据用户获取消息列表
    *
	***/
	public function getMsgListByUser(){
		$user = session('SH_USERS');
		$userId = $user['userId'];
		if(empty($userId)){
			$data['status'] = -1;
			$data['msg'] = '获取用户信息失败.';
			$data['data'] = $user;
			return json_encode($data);
			exit;
		}
		$m = new Msg();
		$rs = $m->getMsgByUser($userId);
		$err = $m->getError();
		if(!empty($err) || empty($rs)){
			$data['status'] = -1;
			$data['msg'] = '获取消息列表失败,代码为:' . $err;
			$data['data'] = array();
			return json_encode($data);
		}else{
			foreach($rs as $k=>$v){
				$rs[$k]['sendTimeDesc'] = date('Y-m-d H:i:s',$v['sendTime']);
			}
			$data['status'] = 1;
			$data['msg'] = "ok";
			$data['data'] = $rs;
			return json_encode($data);
		}
	}


	/***
    *
    *查询日志列表
    *
	***/
	public function adminLogs(){
		$list = getUserList(0,1);
		$pages['current_page'] = 1;
		$pages['last_page'] = 1;
		$pages['per_page'] = 50;
		$pages['data'] = array();
		$user_list = array();
		if(!empty($list)){
			foreach($list as $k=>$v){
				$temp = array();
				$temp['userId'] = $v['userId'];
				$temp['userName'] = $v['userName'];
				$temp['nickName'] = $v['nickName'];
				$user_list[] = $temp;
			}
		}
		$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$pages['data'] = $arg;
			$data['data'] = $pages;
			$data['user_list'] = $user_list;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		$whereStr = '';
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}
			$timestr = '';
			if(isset($where['logTimeStart']) && !empty($where['logTimeStart']) && isset($where['logTimeEnd']) && !empty($where['logTimeEnd'])){
				//$timestr = 'logTime >=' . strtotime($where['logTimeStart']) . " and logTime < " . strtotime($where['logTimeEnd']);
				$timestr = "logTime between '" .strtotime($where['logTimeStart']) . "' and '" . strtotime($where['logTimeEnd']) . "'";
				unset($where['logTimeStart']);
				unset($where['logTimeEnd']);
			}else{
				if(isset($where['logTimeStart']) && !empty($where['logTimeStart'])){
					//$timestr = 'logTime >= ' . strtotime() . ' and logTime < ' . (date('Y-m-d',strtotime($where['logTimeStart'])) + 86400);
					$timestr = 'logTime between ' . strtotime($where['logTimeStart']) . ' and ' . (strtotime(date('Y-m-d',strtotime($where['logTimeStart']))) + 86400);
					unset($where['logTimeStart']);
				}else if(isset($where['logTimeEnd']) && !empty($where['logTimeEnd'])){
					//$timestr = 'logTime > ' . (date('Y-m-d',strtotime($where['logTimeEnd'])) - 86400) . ' and logTime <= ' . $where['logTimeEnd'];
					$timestr = 'logTime between ' . strtotime(date('Y-m-d',strtotime($where['logTimeEnd']))) . ' and ' . strtotime($where['logTimeEnd']);
					unset($where['logTimeEnd']);
				}else{
					if(isset($where['logTimeStart'])){
						unset($where['logTimeStart']);
					}

				}	if(isset($where['logTimeEnd'])){
					unset($where['logTimeEnd']);
				}
			}
			if(empty($where)){
				$whereStr = $timestr;
			}else{
				$str = '';
				foreach($where as $k => $v){
					if(!empty($v)){
						$str = $str . $k . " = '" . $v . "' and ";
					}
				}
				if(empty($timestr)){
					$str = substr($str,0,-5);
				}
				$whereStr = $str .  $timestr;
			}
		}else{
			$whereStr = '';
		}
		if(empty($whereStr)){			
			$data['status'] = 1;
			$data['msg'] = '请输入筛选条件';
			$data['data'] = $pages;
			$data['user_list'] = $user_list;
			return json_encode($data);
			exit;
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "logTime DESC";
		}
		$m = new Logs();
		$rs = $m->getLogList($whereStr,$page);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $pages;
			$data['user_list'] = $user_list;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = "ok";
		if(!empty($rs)){
			foreach($rs as $k=>$v){
				$rs[$k]['logTimeDesc'] = date('Y-m-d H:i:s',$v['logTime']);
			}
		}
		$data['data'] = $rs;
		$data['user_list'] = $user_list;
		$data['arrs'] = $page;
		$data['where'] = $whereStr;
		return json_encode($data);
	}


	public function getMsgNum(){
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = getNoReadMsgNum();
		return json_encode($data);
	}

	/*
	*
	*展示学员作品
	*@userId学员Id
	*
	*/
	public function adminShowWorks(){
		$classId = input('classId/d');
		$groupId = input('groupId/d');
		$class_list = getClassList(0,1);
		$ses = session("SH_USERS");
		if($ses['isManager'] != 1){
			$arr = array();
			$car = $ses['classArr'];
			if(empty($car) || empty($class_list)){
				$arr = array();
			}else{
				foreach($class_list as $k=>$v){
					if(in_array($v['className'],$car)){
						$arr[] = $v;
					}
				}
			}
			$class_list = $arr;
		}
		if(empty($classId) || empty($groupId)){
			$data['status'] = 1;
			$data['msg'] = '参数错误,请提供班级ID和分组ID';
			$data['program_list'] = getProgramList(1);
			$data['class_list'] = $class_list;
			$data['group_list'] = getInfoList();
			$data['student_list'] = array();
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$student_list = getInfoByClass($classId,$groupId);
		if(!empty($student_list)){
			foreach($student_list as $k=>$v){
				$imgstr = $v['imgList'];
				if(empty($imgstr)){
					$student_list[$k]['img_list'] = array();
				}else{
					$tkp= explode('|',$v['imgList']);
					$arr = array();
					foreach($tkp as $kk=>$vv){
						$ttp = explode('^',$vv);
						if(empty($ttp)){
							$ars = array('src'=>'','title'=>'');
						}else{
							if(count($ttp) == 1){
								$ars = array('src'=>$ttp[0],'title'=>'');
							}else{
								$ars = array('src'=>$ttp[0],'title'=>$ttp[1]);
							}
						}
						$arr[] = $ars;
					}
					$student_list[$k]['img_list'] = $arr;
				}
				$tempstr = $v['vedioList'];
				if(empty($tempstr)){
					$student_list['$k']['vedio_list'] = array();
				}else{
					$ls = explode(',',$tempstr);
					$student_list[$k]['vedio_list'] = $ls;
					$student_list[$k]['showVedio'] = false;
				}
				/* $tempstr = $v['vedioList'];
				if(empty($tempstr)){
					$student_list['$k']['vedio_list'] = array();
				}else{
					$ls = explode(',',$tempstr);
					if(empty($ls)){
						$student_list[$k]['vedio_list'] = array();
					}else{
						$tmp = array();
						foreach($ls as $kkk=>$vvv){
							$tmp[] = getVedioInfo($vvv);
						} 
						$student_list[$k]['vedio_list'] = $ls;
						$student_list[$k]['showVedio'] = false;
					}
				} */
			}
		}
    	$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $student_list;
		$data['program_list'] = getProgramList(1);
		$data['class_list'] = $class_list;
		$data['group_list'] = getInfoList();
		$data['student_list'] = getStudentListByClass($classId,1);
		return json_encode($data);
	}


	/*
	*
	*
	*保存学生作品展示信息
	*
	*
	*/
	public function saveShowWorks(){
		$arr = input();
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误";
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}
		$id = 0;
		$where = array();
		$update = false;
		$type = '添加';
		if(isset($arr['infoId'])){
			if(!empty($arr['infoId'])){
				$id = $arr['infoId'];
				$where['infoId'] = $id;
				$update = true;
				$type = '编辑';
			}
			unset($arr['infoId']);
		}
		$m = new IM();
		$rs = $m->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return json_encode($data);
			exit;
		}else{
			$strs = implode('-',$arr);
			\app\model\Logs::saveLog($strs,$type,'作品展示');
			$data['status'] = 1;
			$data['msg'] = 'ok';
			if($update){
				$arr['infoId'] = $id;
			}else{
				$arr['infoId'] = $m->infoId;
			}
			$data['data'] = $arr;
			return json_encode($data);
		}
	}

	/*
	*
	*
	*保存学生作品展示信息
	*
	*
	*/
	public function delShowWorks(){
		$infoId = input('infoId/d');
		if(empty($infoId)){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = input();
			return json_encode($data);
			exit;
		}
		$m = new IM();
		$rs = $m->where(array('infoId'=>$infoId))->delete();
		$err = $m->getError();
		if(empty($rs) || !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $rs;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = '删除成功';
		$data['data'] = $infoId;
		return json_encode($data);
		exit;
	}

	/*
	*
	*
	*获取直实的视频地址
	*
	*/
	public function getTrueUrl(){
		$infoId = input('infoId');
		if(empty($infoId)){
			$data['status'] = -1;
			$data['msg'] = '参数错误';
			$data['data'] = input();
			$data['infoId'] = $infoId;
			return json_encode($data);
			exit;
		}
		$m = new IM();
		$where['infoId'] = $infoId;
		$rs = $m->where($where)->find();
		$err = $m->getError();
		if(empty($rs) || !empty($err)){
			$data['status'] = -1;
			$data['msg'] = '查询数据失败,' . $err;
			$data['data'] = array();
			$data['infoId'] = $infoId;
			return json_encode($data);
			exit;
		}
		if(isset($rs['vedioList']) && !empty($rs['vedioList'])){
			$vedio_list = [];
			$tempstr = $rs['vedioList'];
			if(!empty($tempstr)){
				$ls = explode(',',$tempstr);
				if(!empty($ls)){
					$tmp = array();
					foreach($ls as $k=>$v){
						$vedio_list[] = getVedioInfo($v);
					}
				}
			}
			$data['status'] = 1;
			$data['msg'] = 'ok';
			$data['data'] = $vedio_list;
			$data['infoId'] = $infoId;
			return json_encode($data);
		}else{
			$data['status'] = 1;
			$data['msg'] = '';
			$data['data'] = array();
			$data['infoId'] = $infoId;
			return json_encode($data);
		}
	}
	/*
	*
	*
	*多文件上传,反回一个文件路径列表.
	*
	*/
	public function uploadMultipleFiles(){
		$rs = uploadFiles();
		return json_encode($rs);
	}
	    /***
    *
    *显示作品展示分组信息信息
    *
    ***/
    public function adminShowGroup(){
    	$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			$data['class_list'] = getClassesByUserLogin();
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}
			if(!isset($where['classId']) || empty($where['classId'])){
				/*classId为空时,指定一个不可能查到的值 */
				$where['classId'] = "asdf";
			}
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		$m = new IGM();
		$rs = $m->getGroupList($where,$page);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			$data['class_list'] = getClassesByUserLogin();
			return json_encode($data);
			exit;
		}
		$data['data'] = $rs;
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['class_list'] = getClassesByUserLogin();
		return json_encode($data);
    }


    /***
    *
    *保存作品展示分组信息
    *
    ***/
	public function saveShowGroup(){
		$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		if(!isset($arrs['data']) || empty($arrs['data'])){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$dt = $arrs['data'];
		$m = new IGM();
		$rs = $m->saveGroup($dt);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$rs = $m->getGroupList($where,$page);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		return json_encode($data);
	}


	/***
    *
    *删除作品展示分组信息
    *
    ***/

    public function delShowGroup(){
    	$arg = input();
		if(empty($arg)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的参数";
			$data['data'] = $arg;
			return json_encode($data);
			exit;
		}
		$arrs = getInput($arg);
		if(isset($arrs['where'])){
			$sd = $arrs['where'];
			$where = array();
			foreach($sd as $k=>$v){
				if(strlen($v) > 0){
					$where[$k] = $v;
				}
			}			
		}else{
			$where = array();
		}
		if(isset($arrs['page'])){
			$page = $arrs['page'];
		}else{
			$page = array();
		}
		if(isset($arrs['order'])){
			$order = $arrs['order'];
		}else{
			$order = "";
		}
		$dt = $arrs['data'];
		if(!isset($dt['groupId']) || empty($dt['groupId'])){
			$data['status'] = -1;
			$data['msg'] = "分组ID错误.请正确选择!";
			$data['data'] = $data;
			return json_encode($data);
			exit;
		}
		$wh["groupId"] = $dt['groupId'];
		$m = new IGM();
		$info = $m->where(array('groupId'=>$dt['groupId']))->find();
		$rs = $m->where($wh)->delete();
		$err  = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		if(empty($info)){
			$strs = '删除了一个ID为' . $dt['groupId'] . '的分组';
		}else{
			$strs = '删除了一个名为' . $info['groupName'] . '的分组';
		}
		\app\model\Logs::saveLog($strs,'删除','展示分组');
		$rs = $m->getGroupList($where,$page);
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = $rs;
		return json_encode($data);
	}
}
?>