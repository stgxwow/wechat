<?php
namespace app\model;
use think\Db;
use app\model\Base as Base;
class Msg extends Base{
    public function saveMsg($arr){
        if(empty($arr)){
            return false;
        }
        $rs = $this->allowField(true)->insert($arr);
        if(isset($arr['receiver']) || !empty($arr['receiver'])){
            $text = '向' . getUsersById($arr['receiver']) . '发送了一条消息';
        }else{
            $text = '获取消息失败';
        }
        \app\model\Logs::saveLog($text,'添加','消息');
        $err = $this->getError();
        if($rs <= 0 && !empty($err)){
            return false;
        }else{
            return true;
        }
    }
    /*
    *
    *@userId:为用户ID
    *@model为消息类型0为所有消息,1为己读消息
    *
    */
    public function getMsgByUser($userId,$model=0){
        if(!isset($userId) || empty($userId)){
            return array();
        }
        $where['m.receiver'] = $userId;
        if($model == 1){
            $where['m.haveRead'] = 1;
        }else if($model == 2){
            $where['m.haveRead'] = 0;
        }
        $list = $this->alias('m')->join('__USERS__ u','m.sender=u.userId','left')->field('m.msgId,m.sender,u.nickName as userName,m.receiver,m.msgText,m.sendTime,m.haveRead,m.msgTarget')->where($where)->order('m.sendTime DESC')->select();
        $err = $this->getError();
        if(empty($list)){
            return array();
        }else{
            return $list;
        }
    }

    /*
    *
    *@userId:为用户ID
    *@model为消息类型0为未读消息,1为己读消息
    *
    */
    public function updateMsg($msgId,$haveRead){
        if($msgId <= 0){
            return false;
        }
        $where['msgId'] = $msgId;
        $arr['haveRead'] = $haveRead;
        $rs = $this->allowField(true)->isUpdate(true)->save($arr,$where);
        return $rs;
    }
}
?>