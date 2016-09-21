<?php
/**
 * 后台用户管理类
 *
 */
 
class Admin_User {
	
	const ADMIN_USER_TABLE = 'admin_user'; 
	const ADMIN_GROUP_TABLE = 'admin_group';
	
	/**
	 * 获取用户列表信息
	 * @param array $param 条件数组
	 * @param string $fields 查询字段
	 * @return array $res
	 */
	public static function getAdminUserList($param) {
		$res = array();
		$db_slave = Db::getMasterInstance(ADMIN);
		$sql = 'SELECT u.*,ug.group_name 
			FROM '.self::ADMIN_USER_TABLE.' AS u
			LEFT JOIN '.self::ADMIN_GROUP_TABLE.' AS ug ON u.group_id = ug.group_id
			WHERE `status` = 0';
		if(isset($param['kw_user_name']) && !empty($param['kw_user_name'])){
			$sql .= ' AND u.user_name LIKE "%'.$param['kw_user_name'].'%"';
		}
		if(isset($param['kw_true_name']) && !empty($param['kw_true_name'])){
			$sql .= ' AND u.true_name LIKE "%'.$param['kw_true_name'].'%"';
		}
		$sql .= ' ORDER BY u.user_name LIMIT '.$param['offset'].','.$param['num'];

		$res = $db_slave->get_all($sql);
		
		return $res;
	}
	
	/**
	 * 获取用户列表条数
	 * @param array $param 条件数组
	 * @param string $fields 查询字段
	 * @return int 
	 */
	public static function recordCount($param){
		$db_slave = Db::getMasterInstance(ADMIN);
		$sql = 'SELECT COUNT(u.user_id) AS count FROM '.self::ADMIN_USER_TABLE.' AS u WHERE `status` = 0';
		if(isset($param['kw_username']) && !empty($param['kw_username'])){
			$sql .= ' AND u.user_name LIKE "%'.$param['kw_username'].'%"';
		}
		if(isset($param['kw_true_name']) && !empty($param['kw_true_name'])){
			$sql .= ' AND u.true_name LIKE "%'.$param['kw_true_name'].'%"';
		}
		
		$res = $db_slave->get_row($sql);
		return $res['count'];
	}
	
	/**
	 * 根据用户id获取用户信息
	 * @param int $user_id 用户id
	 * @param string $fields 查询字段
	 * @return array $res
	 */
	public static function getAdminUserById($user_id, $fields = '*') {
		$res = array();
		$db_slave = Db::getMasterInstance(ADMIN);
		$where_arr = array('user_id' => $user_id);
		$res = $db_slave->select_row(self::ADMIN_USER_TABLE, $where_arr, $fields);
		
		return $res;
	}
	
	/**
	 * 根据用户名获取用户信息
	 * @param string $user_name 用户名
	 * @param string $fields 查询字段
	 * @return array $res
	 */
	public static function getAdminUserByName($user_name, $fields = '*') {
		$res = array();
		$db_slave = Db::getMasterInstance(ADMIN);
		$where_arr = array('user_name' => $user_name);
		$res = $db_slave->select_row(self::ADMIN_USER_TABLE, $where_arr, $fields);
		
		return $res;
	}
	
	/**
	 * 编辑用户
	 * @param array $param 数据
	 * @param array $where_arr 条件 eg:array('user_id' => $user_id)
	 * @return bool true or fasle
	 */
	public static function editAdminUser($param, $where_arr) {
		$db_master = Db::getMasterInstance(ADMIN);
		
		return $db_master->update(self::ADMIN_USER_TABLE, $param, $where_arr);
	}
	
	/**
	 * 添加用户
	 * @param array $param 数据
	 * @return bool true or fasle
	 */
	public static function addAdminUser($param) {
		$db_master = Db::getMasterInstance(ADMIN);
		
		return $db_master->insert(self::ADMIN_USER_TABLE, $param);
	}
	
	/**
	 * 获取用户id、登陆账号对应列表
	 * @return array $res
	 */
	public static function getUserListMap() {
		$res = array();
		$db_slave = Db::getMasterInstance(ADMIN);
		$sql = 'SELECT user_id,user_name FROM `admin_user` WHERE `user_type` IN (2,4)';
		
		$data = $db_slave->get_all($sql);
		foreach($data as $val){
			$res[$val['user_id']] = $val['user_name'];
		}
		
		return $res;
	}
}
