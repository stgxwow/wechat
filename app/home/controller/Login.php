<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
class Login extends Controller{
	    /***
    *
    *登录页面
    *
    ***/
    public function index(){
    	return $this->fetch('login');
    }
    /***
    *
    *登录
    *
    ***/
    public function signin(){
		$userName = input('userName');
		$userPwd = input('userPwd');
		/*$userName = "stgxwow";
		$userPwd = "82790085228cf8a1e3bac41f45271e5f";*/
		//$userPwd = md5($userPwd . md5($userPwd));
		$rs = checkUserLogin($userName,$userPwd);
		if($rs['status'] < 0){
			return json_encode($rs);
			exit;
		}
		$tmp  = $rs['data']['rolePower'];
		$rs['data']['powerArr'] = explode(",",$tmp);
		$rs['data']['classArr'] = explode(",",$rs['data']['classList']);
		session('SH_USERS',$rs['data']);
		$data['status'] = 1;
		$data['msg'] = 'ok';
		$data['data'] = $rs['data'];
		return json_encode($data);
	}

	/***
    *
    *退出登录
    *
    ***/
	public function signOut(){
		session("SH_USERS",array());
		$data['status'] = 1;
		$data['msg'] = "ok";
		$data['data'] = array();
		return json_encode($data);
	}
}
?>