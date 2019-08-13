<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Base extends Controller{
	public function __construct(){
		parent::__construct();
		// $rs = session('TX_USERS');
		// if(!isset($rs) || empty($rs)){
		// 	$this->redirect('login/index');
		// }
	}
}
?>