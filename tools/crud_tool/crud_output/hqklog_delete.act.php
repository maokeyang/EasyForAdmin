<?php
/**
* 操作日志表管理删除

* @author wanghan
* 模版生成时间：2016-09-20 04:23:42
*/

class Act_Hqklog_DELETE extends Page {

    const GO_BACK = "?mod=hqklog&act=hqklog";

    public function __construct() {
        parent::__construct();
        $this->db_master =  Db::getMasterInstance(ADMIN);;
    }

    /**
     * 执行入口
     */
    public function process (){
        if(!isset($this->_input['id']) || !is_numeric($this->_input['id'])){
            throw new Exception('参数错误!');
            exit();
        }

        $rt = $this->db_master->query("DELETE FROM `hqklog` WHERE id='{$this->_input['id']}'");
        if($rt){
            $msg = '操作成功';
        }else{
            $msg = '操作失败';
        }
        $this->alert($msg,'href',self::GO_BACK);
    }

}

