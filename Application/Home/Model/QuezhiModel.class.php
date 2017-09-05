<?php
namespace Home\Model;
use Think\Model;
class QuezhiModel extends Model{
    protected $tablePrefix = 'act_';

    //获取拉霸抽奖阙值
    public function get_hold_value_data($type =''){
        return $this->where(array('type' => $type))->find();
    }

    public function get_size_hold_value_data($type = ''){
        return $this->where(array('type' => $type))->find();
    }
}
?>