<?php
/**
* 操作日志表管理编辑

* @author wanghan
* 模版生成时间：2016-09-20 04:23:42
*/

class Act_Hqklog_Edit extends Page {

    const TABLE_NAME = "hqklog";
    const TABLE_KEY_FIELD = "id";
    const GO_BACK = "?mod=hqklog&act=hqklog";

    public function __construct() {
        parent::__construct();
        $this->db = Db::getMasterInstance(ADMIN);
    }

    /**
     * 执行入口
     */
    public function process (){
        if(isset($this->_input['submit'])){ 
            $this->show_validate();    
            $data = $this->_input['data'];    

            if(isset($this->_input['do']) && 'update' == $this->_input['do']){
                $rt = $this->update_record($data);
            }else{
                $rt = $this->add_record($data);
            }
            if($rt){
                $msg = '操作成功';
            }else{
                $msg = '操作失败';
            }
            $this->alert($msg,'href',self::GO_BACK);

        }else{
            $data = array();
            if(isset($this->_input['do']) && 'edit' == $this->_input['do']){
                $data = $this->get_data($this->_input[self::TABLE_KEY_FIELD]);
                $this->assign('title', '编辑操作日志表管理');
                $this->assign('do', Form::hidden('do','update'));
                $this->assign('data', $data);
            }else{
                $this->assign('title', '添加操作日志表管理');
            }
        }

        $this->display();
    }

    /**
     * 获取数据
     */
    private function get_data($key_value){
        if(!$key_value){
            return array();
        }
        return $this->db->get_row("SELECT * FROM `".self::TABLE_NAME."` where ".self::TABLE_KEY_FIELD." = '{$key_value}' ");    
    }

    /**
     * 添加数据
     */
    private function add_record($data){
        unset($data[self::TABLE_KEY_FIELD]);
        return $this->db->insert(self::TABLE_NAME,$data);
    }

    /**
     * 编辑数据
     */
    private function update_record($data){
        $where = array();
        if(isset($data[self::TABLE_KEY_FIELD])){
            $where[self::TABLE_KEY_FIELD] = $data[self::TABLE_KEY_FIELD];
            unset($data[self::TABLE_KEY_FIELD]);
        }else{
            return false;
        }
        return $this->db->update(self::TABLE_NAME,$data,$where);
    }

    /**
     * 检查提交数据的有效性
     * @param array $items
     * @return array
     */
    private function validate($items){
        $emsg = array();
          
        if (!$items['cardNo'])        $emsg['cardNo'] = '卡号不能为空';
                
        return $this->errorMessageFormat($emsg);
    }

    /**
     * 检查提交数据的有效性
     */
    private function show_validate(){
        $emsg = $this->validate($this->_input['data']);  
        if($emsg){
            $this->assign('title', $this->_input['title']);

            $this->assign('emsg', $emsg);
            $this->assign('data', $this->_input['data']);
            if(isset($this->_input['do'])){
                $this->assign('do', Form::hidden('do',$this->_input['do']));
            } 
            $this->display();
            exit();
        }
    }

}

