<?php
/**
* 后台用户表编辑
* 模版生成时间：2016-04-08 12:35:36
*/

class Act_Admin_User_Edit extends Page {

    const TABLE_NAME = "admin_user";
    const TABLE_KEY_FIELD = "user_id";
    const GO_BACK = "?mod=users&act=admin_user";

    private $sex_type = array(
        1 => '男',
        0 => '女',
    );

    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(ADMIN);
        $this->db_master = Db::getMasterInstance(ADMIN);
    }

    /**
     * 执行入口
     */
    public function process (){
        $this->assign('sex_type',$this->sex_type);
        $user_group = Admin_User_Right::getAdminGroupList();
        $this->assign('user_group', $user_group);
        
        if(isset($this->_input['submit'])){ 
            $this->show_validate();    
            $data = $this->_input['data'];  
            $data['update_time'] = time();

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
                $this->assign('title', '编辑后台用户表');
                $this->assign('do', Form::hidden('do','update'));
                $this->assign('data', $data);
            }else{
                $this->assign('title', '添加后台用户表');
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
        return $this->db_slave->get_row("SELECT * FROM `".self::TABLE_NAME."` where ".self::TABLE_KEY_FIELD." = '{$key_value}' ");    
    }

    /**
     * 添加数据
     */
    private function add_record($data){
        unset($data[self::TABLE_KEY_FIELD]);
        $data['create_time'] = $data['update_time'] = time();
        $data['user_pass'] = md5($data['user_pass']);
        return $this->db_master->insert(self::TABLE_NAME,$data);
    }

    /**
     * 编辑数据
     */
    private function update_record($data){
        $where = array();
        if(isset($data[self::TABLE_KEY_FIELD])){

            $data['update_time'] = time();
            if(!$data['user_pass']){
                unset($data['user_pass']);
            }else{
                $data['user_pass'] = md5($data['user_pass']);
            }
            $where[self::TABLE_KEY_FIELD] = $data[self::TABLE_KEY_FIELD];
            unset($data[self::TABLE_KEY_FIELD]);
        }else{
            return false;
        }
        return $this->db_master->update(self::TABLE_NAME,$data,$where);
    }

    /**
     * 检查提交数据的有效性
     * @param array $items
     * @return array
     */
    private function validate($items){
        $emsg = array();
        
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

