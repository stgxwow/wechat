<?php
namespace app\model;
use app\model\Base as Base;
use think\Db;
class Logs extends Base{
    /*
    *写入操作日志
    *@$logId:日志Id
    *@$logUser:用户名称
    *@logTime:日志操作时间
    *@logType:日志类型error,edit,insert,delete
    *@logModule:操作所发生在模块名称
    *@logBefore:操作之前的值
    *@logAfter:日志操作之后的值
    */
    public static function saveLog($logAfter='',$logType="sys",$logModule='',$logId=0,$logBefore=''){
        $users = session('SH_USERS');
        if(!isset($users['userName']) || empty($users['userName']) || !isset($users['userId']) || empty($users['userId'])){
            self::setEmptyLog();
            return false;
        }
        $userId = $users['userId'];
        $logTime = time();
        $logUser = $users['nickName'] . '(' . $users['userName'] . ')';
        if(isset($logId) && !empty($logId)){
            $update = true;
        }else{
            $update = false;
        }
        if(!isset($logType) || empty($logType)){
            self::setEmptyLog();
            return false;
        }
        $arr = array();
        if(!isset($logTime) || empty($logTime)){
            $arr['logTime'] = time();
        }else{
            $arr['logTime'] = $logTime;
        }
        $arr['userId'] = $userId;
        $arr['logUser'] = $logUser;
        $arr['logType'] = $logType;
        $arr['logModule'] = $logModule;
        $arr['logBefore'] = $logBefore;
        $arr['logAfter'] = $logAfter;
        $where = array();
        if($update){
            $where['logId'] = $logId;
            if(isset($arr['logUser'])){
                unset($arr['logUser']);
            }
            $rs = Db::name('logs')->where($where)->update($arr);
        }else{
            $rs = Db::name('logs')->insert($arr);
        }
        if(empty($rs) && $update){
            self::setEmptyLog();
        }
    }
    public static function getLog(){

    }
    /*
    *
    *写入错误日志
    * 
    */
    public static function setEmptyLog(){
        self::saveLog(0,'system',time(),'Error',"","","");
    }



    /*
    *
    *分页查询日志
    *@arr条件数组
    *@page分页数组
    *@order排序信息
    * 
    */
    public function getLogList($arr,$page=array(),$order=""){
		if(empty($page)){
			$page['per_page'] = 20;
			$page['current_page'] = 1;
        }
        $where = $arr;
		if(empty($order)){
			$order = "logTime DESC";
        }
		$sum = $this->where($where)->count();
		if($sum == 0){
			$num = 1;
		}else{
			$num = $sum % $page['per_page'] == 0 ? floor($sum / $page['per_page']) : floor($sum / $page['per_page']) + 1;
		}    
		if($page['current_page'] > $num){
			$page['current_page'] = $num;
		}
		$list = $this->where($where)->order($order)->paginate($page['per_page'],false,['page'=>$page['current_page']]);
		return $list;
	}
}
?>
