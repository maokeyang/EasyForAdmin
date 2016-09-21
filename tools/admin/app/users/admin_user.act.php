<?php
/**
* 后台用户表管理
*
* 模版生成时间：2016-04-01 12:35:36
*/

class Act_Admin_User extends Page {

    private $user_type = array(
        0 => '公司用户',
        1 => '联运-渠道商用户',
        2 => '联运-开发商用户',
        3 => '市场-渠道商用户',
        4 => '市场-开发商用户',
    );

    public $status_arr = array(
        0 => '启用',
        1 => '禁用',
    );

    const TABLE_NAME = "admin_user";
    const TABLE_KEY_FIELD = "user_id";

    public $order_by = "create_time";
    public $sorting = "DESC";


    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(ADMIN);

        

    }

    /**
     * 执行入口
     */
    public function process (){
        $user_group = $this->get_user_group_list();

        $this->assign('title', '后台用户表');
        $this->assign('user_group',$user_group);
        if(isset($this->_input['is_ajax'])){
            $this->assign('user_group',$user_group);

            $this->assign('user_type', $this->user_type);
            $this->assign('data', $this->get_data());
            $total_record = $this->get_total_num();
            $this->assign('total_record', $total_record);
            $this->assign('page', $this->create_pager($total_record));
            $this->assign('pager_limit', $this->create_pager_limit());
            $this->assign('limit', $this->limit);
        }
        $this->display();
    }

    /**
     * 获取查询条件语句
     */
    private function condition() {
        $where = array();
        if(isset($this->_input['kw']['user_name']) && $this->_input['kw']['user_name']){
            $where[] = "user_name = '{$this->_input['kw']['user_name']}'";
        }                            if(isset($this->_input['kw']['true_name']) && $this->_input['kw']['true_name']){
            $where[] = "true_name = '{$this->_input['kw']['true_name']}'";
        }                

        if(!$where) return '';
        return  " WHERE ".implode(' AND ', $where);
    }

    /**
     * 获取数据列表
     */
    public function get_data(){
        $where = $this->condition();
        $order_and_limit = $this->get_order_and_limit();
        $this->sql = "SELECT COUNT(*) FROM `".self::TABLE_NAME."` {$where}";
        return $this->db_slave->get_all("SELECT * FROM `".self::TABLE_NAME."` {$where} {$order_and_limit}");
    }

    /**
     * 获取数据总条数
     */
    public function get_total_num(){
        return $this->db_slave->get_one($this->sql);
    }


    public function fetch() {
        //如果没有添加任何模板则默认使用与当前动作同名的模板
        if(!count($this->_tplFile)){
            if(isset($this->_input['is_ajax'])){
                $this->addTemplate('sub_'.CURRENT_ACTION);
            }else{
                $this->addTemplate(CURRENT_ACTION);
            }
        }
        $this->compile($this->_pagevar);
        return $this->_contents;
    }

    public function get_user_group_list() {
        $db_slave = Db::getMasterInstance(ADMIN);
        $strsql = "SELECT `group_id`,`group_name` FROM `admin_group`";
        $res = $db_slave->get_all($strsql);

        return $res;
    }

    public function get_user_group_name($group_id) {
        if(!$group_id) return '';
        $res = Admin_User_Right::getAdminGroupById($group_id);
        return @$res['group_name'];
    }
}

