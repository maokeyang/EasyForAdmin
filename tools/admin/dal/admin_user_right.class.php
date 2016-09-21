<?php
/**
 * 后台用户组权限管理类
 */
 
class Admin_User_Right {
	
	const ADMIN_GROUP_TABLE = 'admin_group'; 
	/**
	 * 获取用户组列表信息
	 * @param array $where_arr 查询条件数
组	 * @param string $fields 查询字段
	 * @return array $res
	 */
	public static function getAdminGroupList($where_arr = array(), $fields = '*') {
		$res = array();
		$db_slave = Db::getMasterInstance( ADMIN); 
		$res = $db_slave->select_all(self::ADMIN_GROUP_TABLE, $where_arr, $fields);
		
		return $res;
	}
	
	/**
	 * 根据用户组id获取用户组信息
	 * @param int $group_id 用户组id
	 * @param string $fields 查询字段
	 * @return array $res
	 */
	public static function getAdminGroupById($group_id, $fields = '*') {
		$res = array();
		$db_slave = Db::getMasterInstance( ADMIN); 
		$where_arr = array('group_id' => $group_id);
		$res = $db_slave->select_row(self::ADMIN_GROUP_TABLE, $where_arr, $fields);
		
		return $res;
	}
	
	/**
	 * 根据用户组名获取用户组信息
	 * @param string $group_name 用户组名
	 * @param string $fields 查询字段
	 * @return array $res
	 */
	public static function getAdminGroupByName($group_name, $fields = '*') {
		$res = array();
		$db_slave = Db::getMasterInstance( ADMIN); 
		$where_arr = array('group_name' => $group_name);
		$res = $db_slave->select_row(self::ADMIN_GROUP_TABLE, $where_arr, $fields);
		
		return $res;
	}
	
	/**
	 * 编辑用户组
	 * @param array $param 数据
	 * @param array $where_arr 条件 eg:array('group_id' => $group_id)
	 * @return bool true or fasle
	 */
	public static function editAdminGroup($param, $where_arr) {
		$db_master = Db::getMasterInstance( ADMIN); 
		return $db_master->update(self::ADMIN_GROUP_TABLE, $param, $where_arr);
	}
	
	/**
	 * 添加用户组
	 * @param array $param 数据
	 * @return bool true or fasle
	 */
	public static function addAdminGroup($param){
		$db_master = Db::getMasterInstance( ADMIN); 
		return $db_master->insert(self::ADMIN_GROUP_TABLE, $param);
	}
	
	/**
	 * 删除用户组
	 * @param int $group_id 用户组id
	 * @return bool true or fasle
	 */
	public static function delAdminGroup($group_id){
		$db_master = Db::getMasterInstance( ADMIN); 
		$where_arr = array('group_id' => $group_id);
		$res = $db_master->delete(self::ADMIN_GROUP_TABLE, $where_arr);
		
		return $res;
	}
	
}
