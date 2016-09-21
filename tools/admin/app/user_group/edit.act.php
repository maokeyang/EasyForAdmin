<?php
/**
 * 编辑后台用户组
 * 
 */

class Act_Edit extends Page{

    public function __construct(){
        parent::__construct();
		$menu = require CONFIG_DIR.'menu.php';
		$this->assign('title', '编辑用户组');
		$this->assign('menu', $menu);
        $this->assign('goback', App::url('list', '', '', true));
    }

	/**
     * 执行入口
     */
    public function process(){
		if(!isset($this->_input['gid']) || !is_numeric($this->_input['gid'])){
            throw new Exception('参数错误!');
        }
		//用户组id
		$gid = $this->_input['gid'];
		//获取用户组信息
		$data = Admin_User_Right::getAdminGroupById($gid);
		
        if (!isset($this->_input['submit'])){
			if($data['rights'] != 'all'){
				$data['rights'] = json_decode($data['rights'],true);
				$data['ids'] = implode(',',$data['rights']);
			}else{
				$data['rights'] = 'all';
				$data['ids'] = 'all';
			}
			
			$data['group_name_default'] = $data['group_name'];
			$this->showPage($data);
            return;
        }

        $items = $this->_input['items'];
        $emsg = $this->validate($items);
        if ($emsg){
			if($data['rights'] != 'all'){
				$items['rights'] = explode(',',$items['ids']);
			}else{
				$items['rights'] = 'all';
			}
			
            $this->showPage($items,$emsg);
            return;
        }  		

		if($data['rights'] != 'all'){
			if(!empty($items['ids'])){
				$rights = json_encode(explode(',',$items['ids']));
			}else{
				$rights = json_encode(array());
			}
		}else{
			$rights = 'all';
		}
		
		$param = array(
			'group_name' => $items['group_name'],
			'rights' => $rights,
		);
		
		$where_arr = array('group_id' => $gid);
		$res = Admin_User_Right::editAdminGroup($param,$where_arr);
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
        if ($items['group_name'] != $items['group_name_default'] && true === $this->checkIsExists($items['group_name'])) $emsg['user_group_name'] = '用户组名已存在';
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

    private function showPage($data, $emsg = array()){
        $this->assign('emsg', $emsg);
        $this->assign('items', $data);
        $this->display();	
    }
}
