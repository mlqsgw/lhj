<?php
namespace Home\Model;
use Think\Model ;
class UsersModel extends Model {
    protected $tablePrefix = 'lxtx_';

    public function get_users($u_id = ""){
        return $this->where(array('id' => $u_id))->find();
    }

}