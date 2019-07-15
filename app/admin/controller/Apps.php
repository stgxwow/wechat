<?php
namespace app\admin\controller;
use app\model\Article as AM;
class Apps extends Base{
    public function test(){
        
    }
    /***
     * 
     * 
     * 
    ***/
     /***
     * 
     * 文章相关接口
     * 
    ***/
    /***
     * 
     * 文章列表
     * @$cid，int,文章分类列表，
     * @$dataFlag,1-0,是否显示带删除标记的文章
     * 
    ***/
    public function getArtilist($cid=0,$dataFlag=0){
        $m = new AM();
        if(empty($cid) && empty($dataFlag)){
            $where = array();
        }else{
            $where['dataFlag'] = 1;
        }
        $list = $m->where($where)->order("navSort DESC,navId ASC")->select();
        if(!empty($list)){
            if(empty($cid)){
                return $list;
            }else{
                $arr = array();
                foreach($list as $k => $v){
                    $tmp = explode(',',$v['catList']);
                    if(in_array($cid,$tmp)){
                        $v['idpath'] = $cid;
                        $arr[] = $v;
                    }
                }
            }
        }else{
            return array();
        }
        if(emtpy($cid)){
            $where = array();
        }else{
            $where['cat']
        }
    }

    /***
     * 
     * 保存文章
     * 
    ***/
    public function artSave(){
        $pd= input();
        if(empty($arr)){
            $data['status'] = -1;
            $data['msg'] = "参数错误";
            $data['data'] = $arr;
            return $data;
            exit;
        }
        $artId = 0;
        if(isset($pd['artId']) && !empty($pd['artId'])){
            $artId = $pd['artId'];
        }
        $m = new AM();
        unset($pd['artId']);
        if(empty($artId)){
            $model = 0;
            $rs = $m->create($pd);
            if(empty($rs)){
                $data['status'] = -1;
                $data['msg'] = $m->getError();
                $data['data'] = $pd;
                return $data;
                exit;
            }
            $data['status'] = 1;
            $data['msg'] = 'ok';
            $data['data'] = $rs;
            $data['model'] = $model;
            return $data;
            exit;
        }else{
            $model = 1;
            $rs = $m->where(array('artId'=>$artId))->update($pd);
            $pd['artId'] = $houseId;
            $err = $m->getError();
            if(empty($rs) && !empty($err)){
                $data['status'] = -1;
                $data['msg'] = $err;
                $data['data'] = '';
                return $data;
                exit;
            }
            $data['status'] = 1;
            $data['msg'] = 'ok';
            $data['data'] = $pd;
            $data['model'] = $model;
            return $data;
            exit;
        }
    }
}