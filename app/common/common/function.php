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
use app\model\Infogroup as IM;
use app\model\Info as IOM;
use app\model\Scorerecord as SCM;
use app\model\Msg;
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

function getUserList($proId=0,$md=0){
    $m = new UM();
    if(empty($md)){
        $where = array();
    }else{
        $where['dataFlag'] = 1;
    }
    if(!empty($proId)){
        $where['proId'] = $proId;
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
*获取用户未读消息数量
*
***/
function getNoReadMsgNum($userId=0){
    if(empty($userId)){
        $user = session('SH_USERS');
        $userId = $user['userId'];
        if(empty($userId)){
            return 0;
        }
    }
    $m = new Msg();
    $num = $m->where(array('receiver'=>$userId,'haveRead'=>0))->count();
    return $num;
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
    $user = Db::name('users')->alias("u")->join("__PROGRAM__ p","u.proId=p.proId","left")->join("__ROLE__ r","u.userRole=r.roleId","left")->where($where)->field("u.userId,u.userName,u.nickName,u.userPwd,u.proId,u.mobile,u.gender,u.logTime,u.userRole,u.userEmail,u.classList,u.dataFlag,p.proName,r.roleName,r.rolePower,u.userImg,u.isManager")->find();
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
        $user['msgNum'] = getNoReadMsgNum($user['userId']);
        $rs['data'] = $user;
    }
    return $rs;
}

/***
 * 
 * 根据项目ID获取管理者列表
 *  
 ***/
function getManagerList($proId=0){
    $where['isManager'] = 1;
    if(!empty($proId)){
        $where['proId'] = $proId;
    }
    $m = new UM();
    $list = $m->where($where)->select();
    $err = $m->getError();
    if(empty($list)){
        return array();
    }else{
        return $list;
    }
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
 * 根据用户ID获取班级列表
 * 
 ***/
function getClassesByUserLogin(){
    $users = session('SH_USERS');
    if(empty($users['classList'])){
        return array();
    }
    $where = 'className in (' . $users['classList'] . ') and dataFlag=1';
    $list = Db::name('classes')->where($where)->select();
    return $list;
}

/*
 * 
 * 获取项目负责人
 * 
*/

function getManagerByProId($proId){
    if(!isset($proId) || empty($proId)){
        return false;
    }
    $m = new PM();
    $where['proId'] = $proId;
    $pro = $m->where($where)->find();
    if(isset($pro['manager'])){
        return $pro['manager'];
    }else{
        return false;
    }
}
/*
 * 
 * 根据千分制操作ID,查询信息
 * 
*/
function getRecordScoreById($recordId){
    if(!isset($recordId) || empty($recordId)){
        return false;
    }
    $m = new SCM();
    $where['recordId'] = $recordId;
    $where['dataFlag'] = 1;
    $arr = $m->where($where)->find();
    if(empty($arr)){
        return false;
    }else{
        return $arr;
    }
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
function getCourseList($classId=0,$md=0,$groupId=0){
    $where = array();
    if($classId > 0){
       $where['classId'] = $classId; 
    }
    if($groupId > 0){
        $where['groupId'] = $groupId;
    }
    if($md > 0){
        $where['dataFlag'] = 1;
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
function getStudentListByClass($classId=0,$dataFlag=0,$fields=''){
    $where = array();
    if($dataFlag >0){
        $where['dataFlag'] = 1;
    }
    if($classId > 0){
        $where['classId'] = $classId;
    }
    if(empty($fields)){
        $fields = "studentId,studentName,studentNumber,proId,classId,gender,IDCard,birthDay,address,mobile,img,dataFlag";
    }else{
        $fields = "studentId,studentName";
    }
    $m = new SM();
    $list = $m->where($where)->field($fields)->order("studentId ASC")->select();
    return $list;
}


/***
*
*根据学员姓名获取学员Id;
*
*@studentName 学员姓名
*
***/
function getStudentIdByName($studentName,$classId=0,$studentNumber=''){
    if(empty($studentName)){
        return false;
    }
    $m = new SM();
    $where['studentName'] = $studentName;
    if($classId > 0){
        $where['classId'] = $classId;
    }
    if(!empty($studentNumber)){
        $where['studentNumber'] = $studentNumber;
    }
    $rs = $m->where($where)->find();
    $err = $m->getError();
    if(!empty($err) || empty($rs)){
        return false;
    }else{
        return $rs['studentId'];
    }
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


/*
*
*
*根据项目获取分组
*
*/
function getGroupByProgram($proId=0){
    if($proId <= 0){
        return array();
    }
    $rs = Db::name('group')->alias('g')->join('__CLASSES__ c','g.classId=c.classId','left')->join('__PROGRAM__ p','c.proId=p.proId','left')->where('p.proId=' . $proId)->field('g.groupId,g.groupName,g.dataFlag,g.classId')->order('groupId Desc')->select();
    return $rs;
}



/***

获取分组列表

***/
function getGroupByClass($classId,$m = 0){
    if(empty($classId)){
        return array();
    }
    if($m > 0){
        $where['dataFlag'] = 1;
    }else{
        $where = array();
    }
    $where['classId'] = $classId;
    $list = Db::name("group")->where($where)->order("groupId DESC")->select();
    return $list;
}
/***
*
*根据ID查找用户名和姓名
*
***/
function getUsersById($userId,$model=1){
    if(!isset($userId) || empty($userId)){
        return '';
    }
    $info = Db::name('users')->where(array('userId'=>$userId,'dataFlag'=>1))->find();
    if(empty($info)){
        return '';
    }
    if($model == 1){
        return $info['nickName'] . '(' . $info['userName'] . ')';
    }else{
        return $info['userName'];
    }
}

/***
*
*根据ID查找班级
*
***/
function getClassById($classId){
    if(!isset($classId) || empty($classId)){
        return '';
    }
    $info = Db::name('classes')->where(array('classId'=>$classId,'dataFlag'=>1))->find();
    if(empty($info)){
        return '';
    }
    return $info['className'];
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
*写入消息
*
***/
function inputMsg($arr){
    $m = new Msg();
    $rs = $m->saveMsg($arr);
    return $rs;
}
/*
*
*编辑消息
*
*/
function editMsg($arr){
    $where = array();
    if(isset($arr['msgId']) && !empty($arr['msgId'])){
        $update = true;
        $where['msgId'] = $arr['msgId'];
        unset($arr['msgId']);
        $m = new Msg();
        $rs = $m->allowField(true)->isUpdate(true)->save($arr,$where);
        $err = $m->getError();
        if(!empty($err)){
            return false;
        }else{
            $str = implode('-',$arr);
            \app\model\Logs::saveLog($str,'编辑','消息',1,'');
            return $rs;
        }
    }else{
        return false;
    }

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
    var_dump($fileKey);
    if(!empty($dr)){
        $dir = $dr;
    }
    if($dir=='')return json_encode(['msg'=>'没有指定文件目录！|1','status'=>-1]);
    /*$dirs = WSTConf("CONF.wstUploads");
    if(!in_array($dir, $dirs)){
        return json_encode(['msg'=>'非法文件目录！','status'=>-1]);
    }*/
    //上传文件
    $file = request()->file($fileKey);
    var_dump($file);
    if($file===null){
        return json_encode(['msg'=>'上传文件不存在或超过服务器限制|2','status'=>-1]);
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
        return json_encode(['msg'=>$validate->getError() . '|3','status'=>-1]);
    }
    $info = $file->rule('uniqid')->move(ROOT_PATH.'/upload/'.$dir."/".date('Y-m'));
    //保存路径
    $filePath = $info->getPathname();
    $filePath = str_replace(ROOT_PATH,'',$filePath);
    $filePath = str_replace('\\','/',$filePath);
    $name = $info->getFilename();
    $filePath = str_replace($name,'',$filePath);
    if($info){
        return json_encode(['status'=>1,'name'=>$info->getFilename() . '|4','route'=>$filePath]);
    }else{
        //上传失败获取错误信息
        return json_encode(array('status'=>-1,'msg'=>$file->getError()));
    }
}

/**
 * 上传文件,返回文件路径
 */
function uploadFile(){
    $fileKey = key($_FILES);
    $dir = input('dir');
    $fname = input('fileName');
    if(empty($dir)){
        $dir = 'common';
    }
    if(empty($fname)){
        $fname = "common";
    }
    $file = request()->file($fileKey);
    if($file===null){
        $datas['status'] = -2;
        $datas['msg'] = '上传文件不存在或超过服务器限制';
        $datas['data'] = [];
        $datas['err'] = [];
        return $datas;
    }
    $validate = new \think\Validate([
        ['fileExt','fileMime:xlsx','只允许上传xlsx类型的文件'],
        ['fileExt','fileExt:xls','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:jpg','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:png','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:gif','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:doc','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:docx','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:txt','只允许上传后缀为xlsx的文件'],
        ['fileExt','fileExt:bmp','只允许上传后缀为xlsx的文件'],
        ['fileSize','fileSize:9097152','文件大小超出限制'],
    ]);
    $data = ['fileMime'  => $file,
             'fileSize' => $file,
             'fileExt'=> $file
            ];
    //$if = $file->getInfo();
    $info = $file->validate(['ext'=>'xlsx'])->rule('uniqid')->move(ROOT_PATH.'/public/upload/' . $dir . '/'.date('Y-m-d'),$fname . mt_rand(1000,2000));
    //保存路径
    $filePath = $info->getPathname();
    $filePath = str_replace(ROOT_PATH,'',$filePath);
    $filePath = str_replace('\\','/',$filePath);
    $name = $info->getFilename();
    $filePath = str_replace($name,'',$filePath);
    $fileName = $filePath . $name;
    $datas['status'] = 1;
    $datas['msg'] = 'ok';
    $datas['data'] = iconv('gb2312','utf-8',$fileName);
    return $datas;
}

/**
 * 批量上传文件,返回文件路径
 */
function uploadFiles(){
    /* $fileKey = key($_FILES); */
    $dir = input('dir');
    $fname = input('fileName');
    if(empty($dir)){
        $dir = 'common';
    }
    if(empty($fname)){
        $fname = "common";
    }
    $arrs = array();
    foreach($_FILES as $k=>$v){
        $arrs[] = $k;
    }
    if(empty($arrs)){
        $datas['status'] = -11;
        $datas['msg'] = '没有找到上传的文件';
        $datas['data'] = $_FILES;
        return json_encode($datas);
        exit;
    }
    $file_list = array();
    foreach($arrs as $k=>$v){
        $file = request()->file($v);
        if($file===null){
            $datas['status'] = -2;
            $datas['msg'] = '上传文件不存在或超过服务器限制';
            $datas['data'] = $_FILES;
            $datas['err'] = $fileKey;
            $datas['temp'] = $arrs;
            return $datas;
        }
        $info = $file->move(ROOT_PATH.'/public/upload/' . $dir . '/'.date('Y-m-d'),$fname . mt_rand(1000,2000));
        $filePath = $info->getPathname();
        $filePath = str_replace(ROOT_PATH,'',$filePath);
        $filePath = str_replace('\\','/',$filePath);
        $name = $info->getFilename();
        $filePath = str_replace($name,'',$filePath);
        $fileName = iconv('gb2312','utf-8',$filePath . $name);
        $file_list[] = $fileName;
    }
    $datas['status'] = 1;
    $datas['msg'] = 'ok';
    $datas['data'] = $file_list;
    return $datas;
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
*获取学员分组列表
*
***/
function getInfoList($classId=0){
    $m = new IM();
    $where['dataFlag'] = 1;
    if(!isset($classId) || empty($classId)){
        
    }else{
        $where['classId'] = $classId;
    }
    $list = $m->where($where)->select();
    $err = $m->getError();
    if(!empty($err)){
        return array();
    }else{
        return $list;
    }
}
/***
*
*根据学生Id和分组Id查询学生作品展示信息
*
***/
function getInfoByStudent($studentId,$groupId){
    if(empty($studentId) || $studentId < 0){
        return array();
    }
    if(empty($groupId) || $groupId < 0){
        return array();
    }
    $m = new IOM();
    $where['studentId'] = $studentId;
    $where['groupId'] = $groupId;
    $where['dataFlag'] = 1;
    $list = $m->where($where)->find();
    $err = $m->getError();
    if(!empty($err)){
        return array();
        exit;
    }
    return $list;
}

/*
*
*
*根据班级ID和分组还查询学生作品展示信息
*
*
*/
function getInfoByClass($classId,$groupId){
    if(!isset($classId) || empty($classId) || !isset($groupId) || empty($groupId)){
        return array();
    }
    $where['s.classId'] = $classId;
    $where['i.groupId'] = $groupId;
    $where['i.dataFlag'] = 1;
    $m = new IOM();
    $rs = $m->alias('i')->join('__STUDENT__ s','s.studentId=i.studentId')->join('__INFOGROUP__ in','i.groupId=in.groupId')->where($where)->field('i.infoId,i.studentId,s.studentNumber,s.studentName,i.groupId,in.groupName,i.imgList,i.vedioList,i.dataFlag')->order('infoId Desc')->select();
    if(empty($rs)){
        $rs =array();
    }
    return $rs;
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
            $excel->getActiveSheet()->setCellValueExplicit($letter[$n] . $m,$arr[$m - 2][$letterDesc[$n]],\PHPExcel_Cell_DataType::TYPE_STRING);
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
/***
*
*读取EXCEL表格中的key的列表
*
***/
function getKeys(){

}
/***
*
*读取EXCEL表格中的数据
*
***/

function getExcelData($fileName){
    Loader::import('phpexcel.PHPExcel.IOFactory');
    if(strlen($fileName) > 0){
        $fileName = substr($fileName,1);
    }
    //$fileName = iconv('UTF-8','GB2312',$fileName);
    if(!file_exists($fileName)){
        return array();
        exit;
    }
    $objExcel = PHPExcel_IOFactory::load($fileName);
    //获取sheet表格数目
    $sheetCount = $objExcel->getSheetCount();
    //默认选中sheet0表
    $sheetSelected = 0;
    $objExcel->setActiveSheetIndex($sheetSelected);
    //获取表格行数
    $rowCount = $objExcel->getActiveSheet()->getHighestRow();
    //获取表格列数
    $columnCount = $objExcel->getActiveSheet()->getHighestColumn();
    $col_title = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
    $maxCol = 0;
    foreach($col_title as $k=>$v){
        if($columnCount == $v){
            break;
        }else{
            $maxCol++;
        }
    }
    $keys = array();
    for($i='A';$i<=$columnCount;$i++){
        $str = $objExcel->getActiveSheet()->getCell($i.'4')->getValue();
        if(preg_match('/^[a-zA-Z\s]+$/',$str)){
            $keys[] = $str;
        }else{
            return -1;
        }
    }
    $arr = array();
    for($row=5;$row<=$rowCount;$row++){
        $temp = array();
        $col_id = 0;
        for($col='A';$col<=$columnCount;$col++){
            $val = $objExcel->getActiveSheet()->getCell($col.$row)->getValue();
            if(strlen($val) == 0){
                break 2;
            }
            if(isset($keys[$col_id])){
                $temp[$keys[$col_id]] = $val;
            }else{
                $temp[] = $val;
            }
            $col_id++;
        }
        $arr[] = $temp;
    }
    return $arr;
}

/***
*
*读取EXCEL表格中的数据(纯数据)
*@line从第几行开始,默认为第1行;
*
***/

function getExcelDataToArray($fileName,$line){
    if(!isset($line) || empty($line)){
        $line = 1;
    }
    Loader::import('phpexcel.PHPExcel.IOFactory');
    if(strlen($fileName) > 0){
        $fileName = substr($fileName,1);
    }
    //$fileName = iconv('UTF-8','GB2312',$fileName);
    if(!file_exists($fileName)){
        return array();
        exit;
    }
    $objExcel = PHPExcel_IOFactory::load($fileName);
    //获取sheet表格数目
    $sheetCount = $objExcel->getSheetCount();
    //默认选中sheet0表
    $sheetSelected = 0;
    $objExcel->setActiveSheetIndex($sheetSelected);
    //获取表格行数
    $rowCount = $objExcel->getActiveSheet()->getHighestRow();
    //获取表格列数
    $columnCount = $objExcel->getActiveSheet()->getHighestColumn();
    $col_title = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
    $arr = array();
    for($row=$line;$row<=$rowCount;$row++){
        $temp = array();
        for($col='A';$col<=$columnCount;$col++){
            $val = $objExcel->getActiveSheet()->getCell($col.$row)->getValue();
            if(empty($val) && empty($vak)){
                break 2;
            }
            $temp[] = $val;
        }
        $arr[] = $temp;
    }
    return $arr;
}


function getBirthDay($IDCard){
    if(!isset($IDCard) || strlen($IDCard) < 18){
        return '';
    }
    $year = substr($IDCard,6,4);
    $month = substr($IDCard,10,2);
    $day = substr($IDCard,12,2);
    return $year . '-' . $month . '-' . $day;
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

function arrayToStr($arr,$lev=1,$counts=1){
    if(empty($arr)){
        return '';
    }
    $str = '';
    foreach($arr as $k=>$v){
        
    }
}

function setLog($str){
    file_put_contents("d:\log\log1.jpg",$str);
}

/***
*
*获取远程文件内容
*需要打开php_openssl
*
***/
function fopen_url($url)
{
    if (function_exists('file_get_contents')) {
        $file_content = @file_get_contents($url);
    } elseif (ini_get('allow_url_fopen') && ($file = @fopen($url, 'rb'))){
        $i = 0;
        while (!feof($file) && $i++ < 1000) {
            $file_content .= strtolower(fread($file, 4096));
        }
        fclose($file);
    } elseif (function_exists('curl_init')) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl_handle, CURLOPT_FAILONERROR,1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Trackback Spam Check'); //引用垃圾邮件检查
        $file_content = curl_exec($curl_handle);
        curl_close($curl_handle);
    } else {
        $file_content = '';
    }
    return $file_content;
}

function getVedioInfo($video){
    $vid = '';
    preg_match_all("/(?:\/page\/)(.*)(?:\.html)/i",$video, $vid);
    $vid = $vid[1][0];
    $urlString = 'https://vv.video.qq.com/getinfo?otype=json&appver=3.2.19.333&platform=11&defnpayver=1&vid='.$vid;
    $res = fopen_url($urlString);
    //字符串截取json
    $json = str_replace("QZOutputJson=","",$res);
    $json = str_replace("}}]}};","}}]}}",$json);
    $json = substr($json,0,-1);
    //json转换为数组
    $json = json_decode($json,true);
    $fileName = $json['vl']['vi'][0]['fn'];
    $fvkey = $json['vl']['vi'][0]['fvkey'];
    $host = $json['vl']['vi'][0]['ul']['ui'][2]['url'];
    $url = $host.$fileName.'?vkey='.$fvkey;
    return $url;
}

?>