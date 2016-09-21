<?php
/**
 * 注册用户审核删除
 * 模版生成时间：2016-04-06 15:02:26
 **/

class Act_Admin_User_Check_Delete extends Page {

    const GO_BACK = "?mod=users&act=admin_user_check";

    public function __construct() {
        parent::__construct();
        $this->db_master = Db::getMasterInstance(ADMIN);
    }

    /**
     * 执行入口
     */
    public function process (){
        if(!isset($this->_input['user_id']) || !is_numeric($this->_input['user_id'])){
            throw new Exception('参数错误!');
            exit();
        }

        $rt = $this->db_master->query("DELETE FROM `admin_user` WHERE user_id='{$this->_input['user_id']}'");
        if($rt){
            $msg = '操作成功';
        }else{
            $msg = '操作失败';
        }
        $this->alert($msg,'href',self::GO_BACK);
    }

}

