<?php
/**----------------------------------------------------+
 * 退出登录
 +-----------------------------------------------------*/
class Act_Logoff extends Action{
	public function __construct(){
        parent::__construct();
        $this->_AuthLevel =  ACT_OPEN;
    }
    public function process(){
		session_destroy();
        App::redirect(App::url('login', ''));
    }
}
