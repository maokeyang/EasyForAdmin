<?php
/**
* 登陆方式删除
* 模版生成时间：2016-04-01 17:25:00
*/

class Act_Login_Mode_DELETE extends Page {

    const GO_BACK = "?mod=login_mode&act=login_mode";

    public function __construct() {
        parent::__construct();
        $this->db_master = Db::getMasterInstance(CConstant::DB_USER);
    }

    /**
     * 执行入口
     */
    public function process (){
        if(!isset($this->_input['id']) || !is_numeric($this->_input['id'])){
            throw new Exception('参数错误!');
            exit();
        }

        $rt = $this->db_master->query("DELETE FROM `login_mode` WHERE id='{$this->_input['id']}'");
        if($rt){
            $msg = '操作成功';
        }else{
            $msg = '操作失败';
        }
        $this->alert($msg,'href',self::GO_BACK);
    }

}

