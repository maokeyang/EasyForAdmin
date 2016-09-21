<?php
/**
 * 登陆方式编辑
 * 模版生成时间：2016-04-01 17:17:26
 */

class Act_Login_Mode_Edit extends Page {

    const TABLE_NAME = "login_mode";
    const TABLE_KEY_FIELD = "id";
    const GO_BACK = "?mod=login_mode&act=login_mode";

    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(CConstant::DB_USER);
        $this->db_master = Db::getMasterInstance(CConstant::DB_USER);
        $this->state_arr = array(
            "1" => "使用中",
            "0" => "停用",
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
            $data = array();
            if(isset($this->_input['do']) && 'edit' == $this->_input['do']){
                $data = $this->get_data($this->_input[self::TABLE_KEY_FIELD]);
                $this->assign('input_state_arr', Form::select('data[state]',$this->state_arr, $data['state']));

                $this->assign('title', '编辑登陆方式');
                $this->assign('do', Form::hidden('do','update'));
                $this->assign('data', $data);
            }else{
                $this->assign('input_state_arr', Form::select('data[state]',$this->state_arr, ''));
                $this->assign('title', '添加登陆方式');
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
        return $this->db_master->insert(self::TABLE_NAME,$data);
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
        return $this->db_master->update(self::TABLE_NAME,$data,$where);
    }

    /**
     * 检查提交数据的有效性
     * @param array $items
     * @return array
     */
    private function validate($items){
        $emsg = array();

        if (!$items['mode_code'])        $emsg['mode_code'] = '登录缩写不能为空';

        if (!$items['mode_name'])        $emsg['mode_name'] = '登录名称不能为空';

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

