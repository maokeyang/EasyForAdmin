<?php
/*-----------------------------------------------------+
 * 首页公告 
 +-----------------------------------------------------*/
class Act_Index extends Page{
    public function __construct(){
        parent::__construct();
		$this->_AuthLevel =  ACT_NEED_LOGIN;
    }

    public function process(){
        $this->assign('title','首页');
        $this->display();
    }
}
