<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
class Base extends Controller{
	public function __construct(){
		parent::__construct();
		$rs = session('SH_USERS');
		if(!isset($rs) || empty($rs)){
			$this->redirect('login/index');
		}
	}
}
?>