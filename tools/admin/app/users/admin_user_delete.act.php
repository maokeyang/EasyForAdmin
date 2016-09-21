<?php
/**
 * 后台用户表删除
 * 模版生成时间：2016-04-07 12:35:36
 */

class Act_Admin_User_DELETE extends Page {

    const GO_BACK = "?mod=users&act=admin_user";

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

        if($this->_input['do']=='delete'){
            $rt = $this->db_master->query("DELETE FROM `admin_user` WHERE user_id='{$this->_input['user_id']}'");
        }elseif($this->_input['do']=='enable'){
            $rt = $this->db_master->query("UPDATE `admin_user` SET `status`='0' WHERE user_id='{$this->_input['user_id']}'");
        }else{
            $rt = $this->db_master->query("UPDATE `admin_user` SET `status`='1' WHERE user_id='{$this->_input['user_id']}'");
        }
        if($rt){
            $msg = '操作成功';
        }else{
            $msg = '操作失败';
        }
        $this->alert($msg,'href',self::GO_BACK);
    }

}

