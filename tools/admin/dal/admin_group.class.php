<?php 
/**
 *  后台权限组相关操作类
 * @version v2.0
 **/

class Admin_group {

	/**
	 * 获取代理商、财务权限组的手机号码列表
	 * @param type: channel/finance
	 */	
	static public function get_user_mobile_list($type = 'channel'){

		$power = ($type == 'channel') ? 'i_2' : 'i_1'; // 代理商发送至财务,财务发至代理商
		$db = Db::getMasterInstance(ADMIN);

		$sql = "SELECT `group_id` FROM `admin_group` WHERE `rights` like '%{$power}%' OR `rights` = 'all' ";

		$group_list = $db->get_all($sql);

		$list = array();
		foreach ($group_list as $key => $value) {
			$list[] = array_values($value)[0];
		}

		$sql = "SELECT `phone` FROM `admin_user` WHERE `status` = '0' AND `phone` != '' AND `group_id` IN ('" . implode("','", $list) . "')  GROUP BY `phone`";
 
		$mobile_list = $db->get_all($sql);

		return $mobile_list;
	}
}