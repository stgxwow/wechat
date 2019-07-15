<?php
namespace app\model;
use think\Db;
use app\model\Base as Base;
class Menu extends Base{
	public function getMenuInfo($tag=''){
        if(empty($tag)){
            return array();
        }else{
            $where['menuTag'] = $tag;
        }
        $list = $this->where($where)->order("menuSort DESC,menuId ASC")->find();
        return $list;
    }
    public function menuList($where=array()){
        $list = $this->where($where)->order("menuSort DESC,menuId ASC")->select();
        if(!empty($list)){
            $arr = array();
            foreach($list as $k => $v){
                if($v['parentId'] == 0){
                    $arr[] = $v;
                }
            }
            if(!empty($arr)){
                foreach($arr as $k => $v){
                    $tmp =array();
                    foreach($list as $kk => $vv){
                        if($vv['parentId'] == $v['menuId']){
                            $tmp[] = $vv;
                        }
                    }
                    $arr[$k]['sub'] = $tmp;
                }
                return $arr;
            }else{
                return array();
            }
        }else{
            return $list;
        }        
    }
    public function haveSons($id){
        if(empty($id)){
            return true;
        }
        $where['parentId'] = $id;
        $list = $this->where($where)->count();
        if($list > 0){
            return true;
        }else{
            return false;
        }
    }
    public function getSons($id){
        if(strlen($id) == 0){
            return array();
        }
        $where['parentId'] = $id;
        $list = $this->where($where)->select();
        return $list;
    }
    public function getMenuList(){
        $list = $this->order("menuId ASC")->select();
        if(empty($list)){
            return array("top_list"=>array(),"menu_list"=>array(),"sub_list"=>array());
            exit;
        }
        $top_list = array();
        $sub_list = array();
        $menu_list = array();
        foreach($list as $k=>$v){
            $v['checked'] = false;
            if($v['parentId'] == 0){
                $top_list[] = $v;
            }else{
                $sub_list[] = $v;
            }
        }
        if(empty($top_list)){
            if(empty($sub_list)){
                $menu_list = array();
            }else{
                $menu_list = $sub_list;
            }
            return array("top_list"=>array(),"sub_list"=>$sub_list,"menu_list"=>$menu_list);
            exit;
        }
        $m = 0;
        $n = 1;
        foreach($top_list as $k=>$v){
            $i = 1;            
            $v['select'] = false;
            $v['sub'] = 0;
            $v['id'] = $n;
            $v['isSub'] = false;
            $v['display'] = true;
            $menu_list[] = $v;
            foreach($sub_list as $kk=>$vv){
                if($v['menuId'] == $vv['parentId']){
                    $vv['id'] = $i;
                    $vv['isSub'] = true;
                    $vv['select'] = false;
                    $vv['display'] = false;
                    $menu_list[] = $vv;
                    $i++;
                }
            }
            $n++;
            $menu_list[$m]['sub'] = $i - 1;
            $m = $m + $i;
        }
        $rs['top_list'] = $top_list;
        $rs['menu_list'] = $menu_list;
        $rs['sub_list'] = $sub_list;
        return $rs;
    }
}
?>