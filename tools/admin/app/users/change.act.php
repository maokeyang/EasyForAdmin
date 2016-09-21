<?php
/*-----------------------------------------------------+
 * 修改密码
 +-----------------------------------------------------*/
class Act_Change extends Page{
    public function __construct(){
        parent::__construct();
        $this->assign('goback', App::url('index', 'default'));
    }

    /**
     * 执行入口
     */
    public function process(){

        if(!isset($this->_input['submit'])){ //用户是否点击了提交？如果没有则显示页面
            $this->showPage();
            return;
        }

        $data = $this->_input['items'];
        $emsg = $this->validate($data);
        if(count($emsg)){ // 用户提交的数据有错误，显示带有错误提示的页面
            $this->showPage($emsg);
            return;
        }

        $this->update($data);

		App::alert('操作成功','href',App::url('index', 'default', '', true)); //页面跳转
    }

    private function showPage($emsg = array()){
        $this->assign('emsg', $emsg);
        $this->display();
    }

    /**
     * 执行更新
     * @param array $data 帐号数据
     */
    private function update($data){
        unset($data['passwd1']);
        if(strlen($data['passwd'])){
            $data['passwd'] = md5($data['passwd']);
        }else{
            unset($data['passwd']);
        }
		
		if(isset($_SESSION['user_id']) && isset($data['passwd'])){
			$param['user_pass'] = $data['passwd'];
			Admin_User::editAdminUser($param,array('user_id' => $_SESSION['user_id']));
		}

	}

    /**
     * 校验用户提交的数据
     * @param array $data 帐号数据
     * @return array 错误提示信息
     */
    private function validate($data){
        $emsg = array();
        if(!$data['passwd']){
            $emsg['passwd'] = '密码不能为空';
        }else{
            if($data['passwd'] != $data['passwd2']){
                $emsg['passwd'] = '两次输入的密码不一致';
            } 
        }
        
        return $this->errorMessageFormat($emsg);
    }
}