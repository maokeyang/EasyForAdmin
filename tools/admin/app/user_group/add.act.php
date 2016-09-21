<?php
/**
 * 添加后台用户组
 * 
 */

class Act_Add extends Page{

    public function __construct(){
        parent::__construct();
		$menu = require CONFIG_DIR.'menu.php';
		$this->assign('title', '添加用户组');
		$this->assign('menu', $menu);
        $this->assign('goback', App::url('list', '', '', true));
    }

	/**
     * 执行入口
     */
    public function process(){
        if (!isset($this->_input['submit'])){
            $this->display();
            return;
        }

        $items = $this->_input['items'];
        $emsg = $this->validate($items);
        if ($emsg){
            $this->assign('emsg', $emsg);
            $this->assign('items', $items);
            $this->showPage();
            return;
        }  		

		$param = array(
			'group_name' => $items['group_name'],
			'rights' => json_encode(explode(',',$items['ids'])),
			'create_time' => time(),
		);
		
		$res = Admin_User_Right::addAdminGroup($param);
		App::alert('操作成功','href',App::url('list', '', '', true));
    }

    /**
     * 检查提交数据的有效性
     * @param array $items
     * @return array
     */
    private function validate($items){
        $emsg = array();    	
        if (!$items['group_name']) 	$emsg['group_name'] = '用户组名不能为空';
        if (true === $this->checkIsExists($items['group_name'])) $emsg['group_name'] = '用户组名已存在';
        return $this->errorMessageFormat($emsg);
    }

    /**
     * 检查用户组名是否存在
     * @param string $group_name
     * @return bool
     */
    private function checkIsExists($group_name){
        if (!trim($group_name)) return false;
		$res = Admin_User_Right::getAdminGroupByName($group_name);

        if($res){
			return true;
		}
		return false;
    }

    private function showPage($msg=''){
        $this->assign('message', $msg);
        $this->display();    	
    }
}
