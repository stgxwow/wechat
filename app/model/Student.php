<?php
namespace app\model;
use think\Db;
use app\model\Base as Base;
class Student extends Base{
	public function getStudentList($arr=array(),$page=array(),$order="",$m=0){
		if(empty($page)){
			$page['per_page'] = 20;
			$page['current_page'] = 1;
		}
		$keys = $this->getEModel("student");
		$wh = array();
		foreach($keys as $k=>$v){
			if(isset($arr[$k]) && !empty($arr[$k])){
				$wh[$k] = $arr[$k];
			}
		}
		if($m > 0 && !isset($wh['dataFlag'])){
			$wh['dataFlag'] = 1;
		}
		$where = array();
		if(!empty($wh)){
			foreach($wh as $k=>$v){
				$where['s.' . $k] = $v;
			}
		}
		if(empty($order)){
			$order = "s.studentId ASC";
		}
		$sum = $this->where($wh)->count();
		if($sum == 0){
			$num = 1;
		}else{
			$num = $sum % $page['per_page'] == 0 ? floor($sum / $page['per_page']) : floor($sum / $page['per_page']) + 1;
		}    
		if($page['current_page'] > $num){
			$page['current_page'] = $num;
		}
		$list = Db::name("student")->alias("s")->join("__CLASSES__ c","s.classId=c.classId","left")->join("__PROGRAM__ p","s.proId=p.proId","left")->where($where)->field("s.studentId,s.studentNumber,s.studentName,s.proId,p.proName,s.classId,c.className,s.gender,s.IDCard,s.birthDay,s.address,s.mobile,s.img,s.dataFlag")->order($order)->paginate($page['per_page'],false,['page'=>$page['current_page']]);
		return $list;
	}

	public function saveStudent($arr){
		if(empty($arr)){
			$data['status'] = -1;
			$data['msg'] = "参数错误,请传入正确的数据";
			$data['data'] = $arr;
			return $data;
			exit;
		}
		$sid = 0;
		$update = false;
		$type = "添加";
		$where = array();
		if(isset($arr['studentId'])){
			if(!empty($arr['studentId'])){
				$update = true;
				$sid = $arr['studentId'];
				$where['studentId'] = $sid;
			}
			unset($arr['studentId']);
			$type = '编辑';
		}
		$rs = $this->allowField(true)->isUpdate($update)->save($arr,$where);
		$err = $this->getError();
		if(empty($rs) && !empty($err)){
			$data['status'] = -1;
			$data['msg'] = $err;
			$data['data'] = $arr;
			return $data;
			exit;
		}
		$strs = implode('-',$arr);
		\app\model\Logs::saveLog($strs,$type,'学生');
		$data['status'] = 1;
		$data['msg'] = "ok";
		if($update){
			$arr['studentId'] = $sid;
		}else{
			$arr['studentId'] = $this->studentId;
		}
		$data['data'] = $arr;
		return $data;
	}

	/***
	*
	*根据班姓名获取学员列表
	*
	*@$str 学员姓名
	*@$m 模式,为1时只查dataFlag为1的记录,为0时查询所有
	*
	***/
	public function getStudent($str,$m=0){
	    if(!isset($str) || empty($str)){
	        $where = array();
	    }else{
	        $where = "studentName like '%" . $str . "%'";
	    }
	    if($m > 0){
	        if(!isset($where['dataFlag']) || $where['dataFlag'] <= 0){
	            $where = $where . " and dataFlag=1";
	        }
	    }
	    $rs = Db::name("student")->alias("s")->join("__CLASSES__ c","s.classId=c.classId","left")->field("s.studentId,s.studentName,s.classId,c.className,s.proId,p.proName,s.gender,s.IDCard,s.mobile,s.address,s.dataFlag")->join("__PROGRAM__ p","s.proId=p.proId","left")->where($where)->select();
	    return $rs;
	}
	public function getStudentByWhere($where=array()){
		$list = $this->where($where)->select();
		return $list;
	}
}
?>