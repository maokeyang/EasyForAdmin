<?php
/**
* 注册用户审核编辑
* 模版生成时间：2016-04-06 15:02:26
*/

class Act_Admin_User_Check_Edit extends Page {

    const TABLE_NAME = "admin_user";
    const TABLE_KEY_FIELD = "user_id";
    const GO_BACK = "?mod=users&act=admin_user_check";

    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(ADMIN);
        $this->db_master = Db::getMasterInstance(ADMIN);
        $this->sex_arr = array(
            "1" => "男",
            "0" => "女",
        );
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
            $user_group = Admin_User_Right::getAdminGroupList();
            $this->assign('user_group', $user_group);
            $data = array();
            if(isset($this->_input['do']) && 'edit' == $this->_input['do']){
                $data = $this->get_data($this->_input[self::TABLE_KEY_FIELD]);
                $this->assign('title', '审核注册用户');
                $this->assign('do', Form::hidden('do','update'));
                $this->assign('data', $data);
            }else{
                $this->assign('title', '审核注册用户');
            }
                $this->sex_select = isset($data['sex'])?$data['sex']:(isset($this->_input['kw']['sex'])?$this->_input['kw']['sex']:null);
                $this->assign('input_sex_arr', Form::select('data[sex]',$this->sex_arr,$this->sex_select));

                $this->user_type_select = isset($data['user_type'])?$data['user_type']:(isset($this->_input['kw']['user_type'])?$this->_input['kw']['user_type']:null);
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
        return $this->db_master->insert(self::TABLE_NAME,$data);
    }

    /**
     * 编辑数据
     */
    private function update_record($data){
        $where = array();
        if(isset($data[self::TABLE_KEY_FIELD])){
            $data['check_state'] = 1;
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
          
        if (!$items['user_name'])        $emsg['user_name'] = '登录名不能为空';

        if (!$items['true_name'])        $emsg['true_name'] = '真实姓名不能为空';
                
        return $this->errorMessageFormat($emsg);
    }

    /**
     * 检查提交数据的有效性
     */
    private function show_validate(){
        $emsg = $this->validate($this->_input['data']);  
        if($emsg){
            $this->assign('title', $this->_input['title']);

            $this->_input['data']['begin_time'] = strtotime($this->_input['data']['begin_time']);
            $this->_input['data']['end_time'] = strtotime($this->_input['data']['end_time']);
            
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

