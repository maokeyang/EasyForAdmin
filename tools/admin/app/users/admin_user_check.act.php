<?php
/**
* 注册用户审核管理
* 模版生成时间：2016-04-06 15:02:26
*/

class Act_Admin_User_Check extends Page {

    const TABLE_NAME = "admin_user";
    const TABLE_KEY_FIELD = "user_id";

    public $order_by = "create_time";
    public $sorting = "DESC";


    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(ADMIN);
        if (isset($this->_input['page']) && is_numeric($this->_input['page'])) {
            $this->page = $this->_input['page'];
        }
        if (isset($this->_input['limit']) && is_numeric($this->_input['limit'])) {
            $this->limit = $this->_input['limit'];
        }
                                
        if(!isset($this->_input['kw']['start_time'])){
            $this->_input['kw']['start_time'] = date('Y-m-d', strtotime('-1 month'));
        }

        if(!isset($this->_input['kw']['end_time'])){
            $this->_input['kw']['end_time'] = date('Y-m-d');
        }
        
        $this->sex_arr = array(
            "-999" => "--&nbsp;&nbsp;全部&nbsp;&nbsp;--",
            "1" => "男",
            "0" => "女",
        );
        $this->user_type_arr = array(
            "-999" => "--&nbsp;&nbsp;全部&nbsp;&nbsp;--",
            "0" => "公司用户",
            "1" => "联运-渠道商用户",
            "2" => "联运-开发商用户",
            "3" => "市场-渠道商用户",
            "4" => "市场-开发商用户",
        );

    }

    /**
     * 执行入口
     */
    public function process (){
        $this->assign('title', '注册用户审核');
        if(isset($this->_input['is_ajax'])){
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
        $where[] = "check_state=0";
        /*
        if(isset($this->_input['kw']['channel_code']) && $this->_input['kw']['channel_code']){
            $where[] = "channel_code = '{$this->_input['kw']['channel_code']}'";
        }                    
        if(isset($this->_input['kw']['start_time']) && $this->_input['kw']['start_time']){
            $start_time = strtotime($this->_input['kw']['start_time']);
        }else{
            $start_time = strtotime('-1 month');
            $this->_input['kw']['start_time'] = date('Y-m-d', $start_time);
        }

        if(isset($this->_input['kw']['end_time']) && $this->_input['kw']['end_time']){
            $end_time = strtotime($this->_input['kw']['end_time'].' 23:59:59');
        }else{
            $end_time = strtotime(date('Y-m-d 23:59:59'));
            $this->_input['kw']['end_time'] = date('Y-m-d');
        }    
        $where[] = "create_time >= '".$start_time."'";
        $where[] = "create_time <= '".$end_time."'";
         */


        if(isset($this->_input['kw']['user_name']) && $this->_input['kw']['user_name']){
            $where[] = "user_name like '{$this->_input['kw']['user_name']}%'";
        }

        if(isset($this->_input['kw']['true_name']) && $this->_input['kw']['true_name']){
            $where[] = "true_name like '{$this->_input['kw']['true_name']}%'";
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
}

