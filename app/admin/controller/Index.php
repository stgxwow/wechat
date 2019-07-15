<?php
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {
        $arr = array("parentId"=>0,"menuId"=>0);
        $menu = getMenuList();
        $this->assign('menuItem',$arr);
        $this->assign("menu",$menu);
        return $this->fetch('index/index');
    }

    public function artList(){
        $tag = "文章列表";
        $arr = getMenuByTag($tag);
        $menu = getMenuList();
        $this->assign('menuItem',$arr);
        $this->assign("menu",$menu);
        return $this->fetch('index/art_list');
    }
    public function artAdd(){
        $tag = "发布文章";
        $arr = getMenuByTag($tag);
        $menu = getMenuList();
        $this->assign('menuItem',$arr);
        $this->assign("menu",$menu);
        return $this->fetch('index/art_add');
    }

}