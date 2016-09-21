<?php
/**----------------------------------------------------+
 * 添加后台管理员
 +-----------------------------------------------------*/

class Act_Add extends Page{

	private	$user_type = array(
		0 => '公司用户',
		1 => '联运-渠道商用户',
		2 => '联运-开发商用户',
		3 => '市场-渠道商用户',
		4 => '市场-开发商用户',
	);
	private $sex_type = array(
		1 => '男',
		0 => '女',
	);
	
    public function __construct(){
        parent::__construct();
        $this->assign('goback', App::url('list', '', '', true));
    }

	/**
     * 执行入口
     */
    public function process(){
		$user_group_cnf = array();
		$user_group_list = Admin_User_Right::getAdminGroupList();
		foreach($user_group_list as $val){
			$user_group_cnf[$val['group_id']] = $val['group_name'];
		}
        if (!isset($this->_input['submit'])){
            $items['gid'] = Form::select('items[gid]', $user_group_cnf);
            $items['type'] = Form::select('items[type]', $this->user_type);
			$items['sex'] = Form::select('items[sex]', $this->sex_type);
            $this->assign('items', $items);
            $this->showPage();
            return;
        }

        $items = $this->_input['items'];
        $emsg = $this->validate($items);
        if ($emsg){
            $items['gid'] = Form::select('items[gid]', $user_group_cnf, $items['gid']);
            $items['type'] = Form::select('items[type]', $this->user_type, $items['type']);
			$items['sex'] = Form::select('items[sex]', $this->sex_type, $items['sex']);
            $this->assign('emsg', $emsg);
            $this->assign('items', $items);
            $this->showPage();
            return;
        }  		
        unset($items['passwd1']);   		
        $items['passwd'] = md5($items['passwd']);

		$param = array(
			'user_name' => $items['username'],
			'true_name' => $items['name'],
			'group_id' => $items['gid'],
            'channel_code' => $items['channel_code'],
			'user_type' => $items['type'],
			'user_pass' => $items['passwd'],
			'status' => 0,
			'job_no' => $items['job_no'],
			'nick_name' => $items['nick_name'],
			'sex' => $items['sex'],
			'id_no' => $items['id_no'],
			'phone' => $items['phone'],
			'remark' => $items['remark'],
			'qq' => $items['qq'],
			'rtx' => $items['rtx'],
			'msn' => $items['msn'],
			'email' => $items['email'],
			'department' => $items['department'],
			'check_state' => 1,
			'create_time' => time(),
			'update_time' => time(),
			'user_identity' => '',
		);
		
		$res = Admin_User::addAdminUser($param);
        App::alert('操作成功','href',App::url('list', '', '', true));		
    }

    /**
     * 检查提交数据的有效性
     * @param array $items
     * @return array
     */
    private function validate(array $items){
        $emsg = array();    	
        if (!$items['username']) 	$emsg['username'] = '用户名不能为空';
        if (!$items['name']) 		$emsg['name'] = '真实名字不能为空';
        if (!$items['gid']) 		$emsg['gid'] = '请选择用户组';
        if (!trim($items['passwd']) || $items['passwd'] != $items['passwd1']) $emsg['passwd']= '密码不能为空且两次输入的密码必需一致';
        if (true === $this->checkIsExists($items['username'])) $emsg['username'] = '用户名已存在，请另外选择一个用户名';
        return $this->errorMessageFormat($emsg);
    }

    /**
     * 检查用户名是否存在
     * @param string $username
     * @return bool
     */
    private function checkIsExists($username){
        if (!trim($username)) return;    	
        $res = Admin_User::getAdminUserByName($username);
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
