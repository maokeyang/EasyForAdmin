<?php
/**----------------------------------------------------+
 * 系统用户注册
 +-----------------------------------------------------*/
class Act_Register extends Page{

    public function __construct(){
        parent::__construct();
        $this->_AuthLevel =  ACT_OPEN;
    }

    public function process(){

        // 添加
        if(isset($this->_input['register'])){
            $this -> add();
            $this->alert('注册成功','href', App::url('login'));
        }
        $this->display();
    }

    private function showPage($msg='', $username=''){
        $this->assign('message', $msg);
        $this->display();
    }

    /*
     * 添加用户
     * */
    private function add(){
        if(!$this->_input['user_name']){
            $this->showPage('请输入用户名');
        }

        if(!$this->_input['user_pass']){
            $this->showPage('请输入密码');
        }

        if(!$this->_input['confirm_user_pass']){
            $this->showPage('请再次输入密码');
        }

        if(!$this->_input['true_name']){
            $this->showPage('请输入真实姓名');
        }

        if(!$this->_input['email']){
            $this->showPage('请输入邮箱地址');
        }

        if(!$this->_input['qq']){
            $this->showPage('请输入qq号码');
        }

        if(!$this->_input['company_name']){
            $this->showPage('请输入公司名称');
        }

        if($this->_input['user_pass'] != $this->_input['confirm_user_pass']){
            $this->showPage('两次输入密码不一致');
        }

        $param = array(
            'user_name' => $this->_input['user_name'],
            'true_name' => $this->_input['true_name'],
            //'group_id' => $this->_input['gid'],
            //'channel_code' => $this->_input['channel_code'],
            //'user_type' => $this->_input['type'],
            'user_pass' => md5($this->_input['user_pass']),
            'check_state' => 0,
            'status' => 0,
            //'job_no' => $this->_input['job_no'],
            'nick_name' => $this->_input['true_name'],
            //'sex' => $this->_input['sex'],
            //'id_no' => $this->_input['id_no'],
            'phone' => $this->_input['phone'],
            //'remark' => $this->_input['remark'],
            'qq' => $this->_input['qq'],
            //'rtx' => $this->_input['rtx'],
            //'msn' => $this->_input['msn'],
            'email' => $this->_input['email'],
            'company_name' => $this->_input['company_name'],
            'department' => $this->_input['department'],
            'create_time' => time(),
            'update_time' => time(),
            'user_identity' => '',
            'entry_time' => 0,
        );

        return Admin_User::addAdminUser($param);
    }
}
