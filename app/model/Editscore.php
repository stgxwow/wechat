<?php
namespace app\model;
use app\model\Base as Base;
class Editscore extends Base{
    public function getListByUser($userId){
        if(!isset($userId) || empty($userId)){
            return array();
        }
        $where['e.userId'] = $userId;
        $where['e.dataFlag'] = 1;
        $where['e.dataFlag'] = 1;
        $list = $this->alias('e')->join('__USERS__ u','e.initiator=u.userId','left')->field('e.scoreId,e.recordId,e.studentId,e.studentName,e.sourceRule,e.sourceMem,e.sourceScore,e.sourceTime,e.targetRule,e.targetMem,e.targetScore,e.targetTime,e.userId,e.isComplete,e.description,e.dataFlag,e.initiator,u.nickName,e.description')->where($where)->select();
        if(empty($list)){
            return array();
        }else{
            foreach($list as $k=>$v){
                $list[$k]['sourceTimeDesc'] = date('Y-m-d',$v['sourceTime']);
                $list[$k]['targetTimeDesc'] = date('Y-m-d',$v['targetTime']);
            }
            return $list;
        }
    }
}
?>