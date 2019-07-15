<?php
use think\Db;
use think\Loader;
use app\model\Program as PM;
use app\model\Menu as MM;
use app\model\Classes as CM;
use app\model\Student as SM;
use app\model\Users as UM;
use app\model\Role as RM;
use app\model\Course as COM;
use app\model\Award as AM;
/***
 * 
 * 用户内相关信息
 * 
 ***/

/***
 * 
 * 获取用户列表
 * 
 ***/

function getUserList($md=0){
    $m = new UM();
    if(empty($md)){
        $where = array();
    }else{
        $where['dataFlag'] = 1;
    }
    $list = $m->where($where)->field("userId,userName,nickName,mobile,gender,userEmail,classList,userPower")->select();
    return $list;
}

/***
 * 
 * 根据用户ID获取用户信息
 * 
 ***/
function getUserInfo($id=0){
    if(!is_numeric($id) && $id > 0){
        $where['userId'] = $id;
    }
    $m = new UM();
    $rs = $m->where($where)->field("userId,userName,nickName,mobile,gender,userEmail,classList,userPower")->find();
    return $rs;
}
function getUserFullInfo($wh=array(),$page =array('per_page'=>20,'current_page'=>1)){
    $sum = Db::name("users")->where($wh)->count();
    if($sum == 0){
        $num = 1;
    }else{
        $num = $sum % $page['per_page'] == 0 ? floor($sum / $page['per_page']) : floor($sum / $page['per_page']) + 1;
    }    
    if($page['current_page'] > $num){
        $page['current_page'] = $num;
    }
    $rs = Db::name("users")->alias('u')->join("__PROGRAM__ p","u.proId=p.proId","left")->join("__ROLE__ r","u.userRole=r.roleId","left")->where($wh)->field("u.userId,u.userName,u.nickName,u.gender,u.mobile,u.proId,p.proName as proDesc,u.userRole,u.userEmail,r.roleName as roleDesc,u.dataFlag,u.classList,u.userPower,r.rolePower as powerDesc,p.proAddress")->order("u.userId ASC")->paginate($page['per_page'],false,['page'=>$page['current_page']]);
    return $rs;
}

/***
 * 
 * 获取角色列表
 * 
 ***/
function getRoleList($md=0){
    if(empty($md)){
        $where = array();
    }else{
        $where['dataFlag'] = 1;
    }
    $list = Db::name("role")->where($where)->order("roleId ASC")->select();
    return $list;
}

function getClassListByUser($class,$arr){
    if(empty($class) || empty($arr)){
        return '';
    }
    $akr = array();
    $vd = explode(',',$arr);
    foreach($vd as $k=>$v){
        foreach($class as $kk=>$vv){
            if($v == $vv['classId']){
                $akr[] = $vv['className'];
                break;
            }
        }
    }
    if(empty($akr)){
        return '';
    }else{
        $str = implode(',', $akr);
        return $str;
    }
}

/***
*
*检查用户登录信息
*
***/
function checkUserLogin($userName,$userPwd){
    $where['u.userName'] = $userName;
    $where['u.userPwd'] = $userPwd;
    $where['u.dataFlag'] = 1;
    $user = Db::name('users')->alias("u")->join("__PROGRAM__ p","u.proId=p.proId","left")->join("__ROLE__ r","u.userRole=r.roleId","left")->where($where)->field("u.userId,u.userName,u.nickName,u.userPwd,u.mobile,u.gender,u.logTime,u.userRole,u.userEmail,u.classList,u.dataFlag,p.proName,r.roleName,r.rolePower")->find();
    if(empty($user)){
        $rs['status'] = -1;
        $rs['msg'] = '用户不存在或是用户名和密码错误';
        $rs['data'] = $where;
    }else{
        if(empty($rs['dataFlag'])){
            $rs['status'] = -1;
            $rs['msg'] = "用户己注销,请联系管理员";
            $rs['data'] = array();
        }
        $rs['status'] = 1;
        $rs['msg'] = 'ok';
        unset($user['userPwd']);
        $rs['data'] = $user;
    }
    return $rs;
}



/***
 * 
 * 根据用户ID获取用户权限
 * 
 ***/
function getUserPowerByID($id = 0){
    if(!is_numeric($id) && $id <= 0){
        return false;
        exit;
    }
    $where['userId'] = $id;
    $where['dataFlag'] = 1;
    $m = new UM();
    $rs = $m->where($where)->find();
    if(empty($rs)){
        return false;
        exit;
    }
    if(isset($rs['userPower'])){
        return $rs['userPower'];
    }else{
        return false;
    }
}



/***
 * 
 * 项目相关信息
 * 
 ***/
/***
 * 
 * 获取项目列表
 * 
 ***/
function getProgramList($position=0){
    $m = new PM();
    $where['dataFlag'] = 1;
    if(empty($position)){
        $where = array();
    }
    $list=$m->where($where)->select();
    return $list;
}
/***
 * 
 * 班级相关信息
 * 
 ***/
/***
 * 
 * 获取班级列表
 * 
 ***/
function getClassList($proId=0,$md=0){
    $where = array();
    if($md > 0){
        $where['c.dataFlag'] = 1;
    }
    if($proId > 0){
        $where['c.proId'] = $proId;
    }
    $list = Db::name("classes")->alias("c")->join("__USERS__ u","c.masterId=u.userId","left")->join("__PROGRAM__ p","c.proId=p.proId","left")->where($where)->field("c.classId,c.className,c.masterId,c.dataFlag,c.sort,c.proId,p.proName,u.userName,u.nickName")->order("sort DESC,classId ASC")->select();
    return $list;
}

/***
*
*课程相关信息
*
***/

/***
*
*课获取课程列表
*
***/
function getCourseList($md=0){
    if($md > 0){
        $where['dataFlag'] = 1;
    }else{
        $where = array();
    }
    $m = new COM;
    $list = $m->where($where)->order("courseId ASC")->select();
    return $list;
}



/***
*
*获奖相关信息
*
***/

/***
*
*获取奖励列表
*
***/
function getAwardList($arr=array(),$page=array(),$md=0){
    $where = array();
    $m = new AM();
    $keys = $m->getEModel("award");
    foreach($keys as $k=>$v){
        if(isset($arr[$k]) && !empty($arr[$k])){
            $where[$k] = $arr[$k];
        }
    }
    if(empty($page) || !is_array($page)){
        $page = array("per_page"=>20,"current_page"=>1);
    }
    if($md > 0){
        $where['dataFlag'] = 1;
    }
    $sum = $m->where($where)->count();
    $num = $sum % $page['per_page'] == 0 ? floor($sum / $page['per_page']) : floor($sum / $page['per_page']) + 1;
    if($page['current_page'] > $num){
        $page['current_page'] = $num;
    }
    $list = $m->where($where)->order("awardId ASC")->paginate($page['per_page'],false,['page'=>$page['current_page']]);
    return $list;
}


/***
*
*根据前缀分类获取input数据
*
***/
function getInput($arr){    
    $rs = array();
    foreach($arr as $k=>$v){
        $tmp = strpos($k,"_");
        if(gettype($tmp) == "integer"){
            $str = substr($k,$tmp+1);
            $front = substr($k,0,$tmp);
            $rs[$front][$str] = $v;
        }
    }
    return $rs;
}


/***
*
*学员相关信息
*
***/
/***
*
*根据班级Id获取学员列表
*
*@classId 班级Id
*
***/
function getStudentListByClass($classId=0,$dataFlag=0){
    $where = array();
    if($dataFlag >0){
        $where['dataFlag'] = 1;
    }
    if($classId > 0){
        $where['classId'] = $classId;
    }
    $m = new SM();
    $list = $m->where($where)->order("studentId ASC")->select();
    return $list;
}




/***
*
*根据班级Id获取学员列表
*
*@programId 项目Id
*
***/
function getStudentListByProgram($proId=0){

}

/***
*
*后台菜单相关函数
*
***/
function getMenuByTag($tag=""){
    if(empty($tag)){
        return array();
    }else{
        $m = new MM();
        $rs = $m->getMenuInfo($tag);
        return $rs;
    }
}
/***
*
*获取菜单列表
*
***/
function getMenuList($all=0,$md=1){
    if($md > 0){
        $where = "dataFlag=1";
    }else{
        $where = '';
    }
    if($all > 0){
        $arr = session("SH_USERS.powerArr");
        if(!empty($arr)){
            foreach($arr as $k=>$v){
                $arr[$k] = "'" . $v . "'";
            }
            $str = implode(',',$arr);
            if(strlen($where) > 0){
                $where = $where . " and menuTag in (" . $str . ")";
            }else{
                $where = "menuTag in (" . $str . ")";
            }
        }
    }
    $m = new MM();
    $list = $m->menuList($where);
    /*if($all > 0){
        if(!empty($list)){
            $arr = session("SH_USERS.powerArr");
            $rs = array();
            foreach($list as $k=>$v){
                $rs[] = $v;
                $i = 0;
                foreach($v['sub'] as $kk=>$vv){
                    if(!in_array($vv['menuTag'],$arr)){
                        unset($rs['sub'][$kk]);
                        $i++;
                    }
                }
                if($i == 0){
                    unset($rs[$k]);
                }
            }
            $k = 0;
            $list = array();
            foreach($rs as $k=>$v){
                $list[] = $v;
            }
        }
    }*/
    return $list;
}
/***
*
*学生相关函数
*
***/

/***
*
*学生成绩分组
*
***/
function getMarkGroup($arr){
    if(empty($arr)){
        return false;
    }
    $scoreType = array();
    $list = array();
    foreach($arr as $k=>$v){
        if(!in_array($v['courseName'],$scoreType)){
            $scoreType[] = $v['courseName'];
        }
        $bl = inMarkList($v['studentId'],$list);
        if($bl >= 0){
            $lmp = array();
            $lmp['markId'] = $v['markId'];
            $lmp['courseId'] = $v['courseId'];
            $lmp['courseName'] = $v['courseName'];
            $lmp['score'] = $v['score'];
            array_push($list[$bl]['scoreList'],$lmp);
        }else{
            $tmp["studentId"] = $v["studentId"];
            $tmp["studentName"] = $v["studentName"];
            $tmp['markGroup'] = $v['markGroup'];
            $tmp['groupName'] = $v['groupName'];
            $tmp['scoreList'] = array();
            $smp = array();
            $smp['markId'] = $v['markId'];
            $smp['courseId'] = $v['courseId'];
            $smp['courseName'] = $v['courseName'];
            $smp['score'] = $v['score'];
            array_push($tmp['scoreList'],$smp);
            array_push($list,$tmp);
        }
    }
    $data['scoreType'] = $scoreType;
    $data['list'] = $list;
    return $data;
}

/***

制作成绩列表
@$item为考试科目列表
@$list为成绩列表

***/
function makeMarkList($item,$list){
    if(empty($list)){
        return array();
    }
    $arr = array();
    foreach($item as $k => $v){
        $arr[$v] = 0;
        foreach($list as $kk => $vv){
            if($v == $vv['courseName']){
                $arr[$v] = $vv['score'];
                break;
            }
        }
    }
    return $arr;
}

/***

获取分组列表

***/
function getAllGroup($m = 0){
    if($m > 0){
        $where['dataFlag'] = 1;
    }else{
        $where = array();
    }
    $list = Db::name("group")->where($where)->order("groupId DESC")->select();
    return $list;
}

/***
*
*判断字符串是否是数组的键值
*
***/
function inMarkList($id,$arr){
    if(empty($id)){
        return -1;
    }
    if(empty($arr)){
        return -1;
    }
    for($i=0;$i<count($arr);$i++){
        if($id == $arr[$i]['studentId']){
            return $i;
            break;
        }
    }
    return -1;
}



/***
*
*判断某一直是否在数组组
*
*@$val为需要判断的修值
*@$keys为数组的键值
*@$arr为数组
*
***/
function inArrayByKey($val,$keys,$arr){
    if(empty($val)){
        return -1;
    }
    if(empty($keys)){
        return -1;
    }
    if(empty($arr)){
        return -1;
    }
    for($i=0;$i<count($arr);$i++){
        if($val == $arr[$i][$keys]){
            return $i;
            break;
        }
    }
    return -1;
}





/***
*
*判断字符串是否是数组的键值
*
***/
function is_key($keys,$arr){
    if(empty($keys)){
        return false;
    }
    if(empty($arr)){
        return false;
    }
    foreach($arr as $k=>$v){
        if($keys == $k){
            return true;
            break;
        }
    }
    return false;
}



/**
 * 上传文件
 */
function TXUploadFile(){
    $fileKey = key($_FILES);
    $dir = 'truckpictrue';
    $dr = Input('post.dir');
    if(!empty($dr)){
        $dir = $dr;
    }
    if($dir=='')return json_encode(['msg'=>'没有指定文件目录！','status'=>-1]);
    /*$dirs = WSTConf("CONF.wstUploads");
    if(!in_array($dir, $dirs)){
        return json_encode(['msg'=>'非法文件目录！','status'=>-1]);
    }*/
    //上传文件
    $file = request()->file($fileKey);
    if($file===null){
        return json_encode(['msg'=>'上传文件不存在或超过服务器限制','status'=>-1]);
    }
    $validate = new \think\Validate([
        ['fileExt','fileMime:image/png,image/gif,image/jpeg,image/x-ms-bmp','只允许上传jpg,gif,png,bmp类型的文件'],
        ['fileExt','fileExt:jpg,jpeg,gif,png,bmp','只允许上传后缀为jpg,gif,png,bmp的文件'],
        ['fileSize','fileSize:2097152','文件大小超出限制'],
    ]);
    $data = ['fileMime'  => $file,
             'fileSize' => $file,
             'fileExt'=> $file
            ];
    if (!$validate->check($data)) {
        return json_encode(['msg'=>$validate->getError(),'status'=>-1]);
    }
    $info = $file->rule('uniqid')->move(ROOT_PATH.'/upload/'.$dir."/".date('Y-m'));
    //保存路径
    $filePath = $info->getPathname();
    $filePath = str_replace(ROOT_PATH,'',$filePath);
    $filePath = str_replace('\\','/',$filePath);
    $name = $info->getFilename();
    $filePath = str_replace($name,'',$filePath);
    if($info){
        return json_encode(['status'=>1,'name'=>$info->getFilename(),'route'=>$filePath]);
    }else{
        //上传失败获取错误信息
        return $file->getError();
    }
}


/*  base64格式编码转换为图片并保存对应文件夹 */  
function base64_image_content($base64_image_content,$path){  
    //匹配出图片的格式  
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){  
        $type = $result[2];  
        $new_file = $path."/".date('Ymd',time())."/";  
        if(!file_exists($new_file)){  
            //检查是否有该文件夹，如果没有就创建，并给予最高权限  
            mkdir($new_file, 0700);  
        }  
        $new_file = $new_file.time().".{$type}";  
        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){  
            return '/'.$new_file;  
        }else{  
            return false;  
        }  
    }else{  
        return false;  
    }  
}

/***
*
*制作EXCEL表格
*
***/
function makeRport($arr=array(),$titleDesc=array(),$title=array(),$info){
    Loader::import('phpexcel.PHPExcel.IOFactory');
    if(empty($title)){
        print_r('请输入表格标题');
        exit;
    }
    $excel = new \PHPExcel();
    $mj = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
    $letter = array();
    $letterDesc = array();
    for($s=0;$s<count($titleDesc);$s++){
        $letter[] = $mj[$s];
        $letterDesc[] = $title[$s];
    }
    for($i=0;$i< count($letter);$i++){
        $excel->getActiveSheet()->setCellValue($letter[$i] . '1',$titleDesc[$i]);
    }
    $row = 2;
    for($m=2;$m <= count($arr) + 1;$m++){
        $n = 0;
        foreach($arr[$m - 2] as $k => $v){
            $excel->getActiveSheet()->setCellValue($letter[$n] . $m,$arr[$m - 2][$letterDesc[$n]]);
            $n++;
        }
        
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
    exit();
}


function getReportInfo($sid){
    if(empty($sid)){
        return "";
    }
    $where['classId'] = $sid;
    $rs = Db::name("classes")->alias('c')->join("__PROGRAM__ p","c.proId=p.proId","left")->where($where)->field("p.proName,c.className")->find();
    if(empty($rs)){
        $rs = "";
    }else{
        $rs = implode('',$rs);
    }
    return $rs;
}


function setLog($str){
    file_put_contents("d:\log\log1.jpg",$str);
}

?>