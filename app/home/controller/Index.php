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
	public function test(){
		$where['c.dataFlag'] = 1;
		$where['c.proId'] = 1;
		/*$sql = "select c.classId,c.className,c.masterId,c.sort,c.proId,p.proName,u.userName,u.nickName from sh_classes c left join sh_program p on c.proId=p.proId left join sh_users u on c.masterId=u.userId where c.dataFlag=1 and c.proId=1 order by classId ASC";
		$rs = Db::query($sql);
		return json_encode($rs);*/
		/*$rs = Db::name("classes")->alias("c")->join("__USERS__ u","c.masterId=u.userId","left")->join("__PROGRAM__ p","c.proId=p.proId","left")->where($where)->field("c.classId,c.className,c.masterId,c.sort,c.proId,p.proName,u.userName,u.nickName")->order("sort DESC,classId ASC")->select();
		print_r($rs);*/
		//Cache::clear(); 
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
			unset($arr['userId']);
		}else{
			$arr['userPwd'] = md5("123456" . md5("123456"));
			$arr['regTime'] = time();
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
		if(isset($arr['roleId'])){
			if(!empty($arr['roleId'])){
				$id = $arr['roleId'];
				$where['roleId'] = $id;
				$update = true;
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
		if(isset($arr['proId'])){
			if(!empty($arr['proId'])){
				$id = $arr['proId'];
				$where['proId'] = $id;
				$update = true;
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
		$rs = getClassList();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
		$data['user_list'] = getUserList(0,1);
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
		if(isset($arr['classId'])){
			if(!empty($arr['classId'])){
				$id = $arr['classId'];
				$where['classId'] = $id;
				$update = true;
			}
			unset($arr['classId']);
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
		$rs = getCourseList();
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs;
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
		if(isset($arr['courseId'])){
			if(!empty($arr['courseId'])){
				$id = $arr['courseId'];
				$where['courseId'] = $id;
				$update = true;
			}
			unset($arr['courseId']);
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
			$data['status'] = 1;
			$data['msg'] = $arr;
			$data['data'] = getCourseList();
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
    	$rs = $m->where($where)->delete();
    	$err = $m->getError();
    	if(empty($rs) && !empty($err)){
    		$data['status'] = -1;
    		$data['msg'] = $err;
    		$data['data'] = input();
    		return json_encode($data);
    		exit;
    	}
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
		if(isset($arr['awardId'])){
			if(!empty($arr['awardId'])){
				$id = $arr['awardId'];
				$where['awardId'] = $id;
				$update = true;
			}
			unset($arr['awardId']);
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
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
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
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
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
		$m = new SM();
		$rs = $m->getStudentList($where,$page);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$class_list = getClassList(0,1);
    	/*$arr = array();
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
    	}*/
		$data['data'] = $rs;
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['program_list'] = getProgramList(1);
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
		$rs = $m->where($wh)->delete();
		$err  = $m->getError();
		if(empty($rs) && !empty($err)){
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
    *显示分组信息信息
    *
    ***/
    public function adminGroup(){
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
		$m = new GM();
		$rs = $m->getGroupList($where,$page);
		$err  = $m->getError();
		if(!empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = array();
			return json_encode($data);
			exit;
		}
		$data['data'] = $rs;
		$data['status'] = 1;
		$data['msg'] = "ok";
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
		$rs = $m->where($wh)->delete();
		$err  = $m->getError();
		if(empty($rs) && !empty($err)){
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
    	$data['course_list'] = getCourseList(1);
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
    	if(empty($studentId) || empty($markGroup)){
    		$data['status'] = -1;
    		$data['msg'] = "参数错误,必须提供学生ID和成绩分组";
    		$data['data'] = array();
    		return json_encode($data);
    		exit;
    	}
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
    	$data['status'] = 1;
    	$data['msg'] = "ok";
    	$data['data'] = $list;
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
    	$data['msg'] = 'ok';
		$data['program_list'] = getProgramList(1);
		$data['class_list'] = $arr;
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
    				foreach($list['scoreType'] as $kk=>$vv){
    					$students[$k][$kk] = $vv;
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
    			foreach($list['scoreType'] as $kk=>$vv){
    				$tmp[$vv] = $v[$vv];
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
		$m = new SCM();
		if(isset($arr['itemId'])){
			if(!empty($arr['itemId'])){
				$id = $arr['itemId'];
				$where['itemId'] = $id;
				$update = true;
			}
			unset($arr['itemId']);
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
		$rs = $m->where($where)->delete();
		$err = $m->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $id;
			return json_encode($data);
			exit;
		}
		$data['status'] = 1;
		$data['msg'] = "删除成功";
		$data['data'] = $id;
		return json_encode($data);
    }

    /***
    *
    *查询千分制规则列表
    *
    ***/
    public function adminScore(){
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
    	$data['msg'] = 'ok';
		$data['program_list'] = getProgramList(1);
		$data['class_list'] = $arr;
		$m = new SCM();
		$list = $m->getScoreRoleList();
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
		$m = new SCRM();
		if(isset($arr['recordId'])){
			if(!empty($arr['recordId'])){
				$id = $arr['recordId'];
				$where['recordId'] = $id;
				$update = true;
			}
			unset($arr['recordId']);
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
		$data['status'] = 1;
		$data['msg'] = "保存成功";
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
	
}
?>