<?php
namespace app\home\controller;
use think\Controller;
use think\Loader;
class Manager extends Controller{
	public function index(){
		return $this->fetch('index');
	}

}
?>