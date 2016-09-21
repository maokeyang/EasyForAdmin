<?php
/**
* 操作日志表管理管理

* @author wanghan
* 模版生成时间：2016-09-20 04:23:42
*/

class Act_Hqklog extends Page {

    const TABLE_NAME = "hqklog";
    const TABLE_KEY_FIELD = "id";

    public $order_by = "id";
    public $sorting = "ASC";


    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(ADMIN);

                                

    }

    /**
     * 执行入口
     */
    public function process (){
        $this->assign('title', '操作日志表管理');
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
                                    if(isset($this->_input['kw']['user']) && $this->_input['kw']['user']){
            $where[] = "user = '{$this->_input['kw']['user']}'";
        }                            if(isset($this->_input['kw']['action']) && $this->_input['kw']['action']){
            $where[] = "action = '{$this->_input['kw']['action']}'";
        }                
                
        if(isset($this->_input['kw']['cardNo']) && $this->_input['kw']['cardNo']){
            $where[] = "cardNo like '{$this->_input['kw']['cardNo']}%'";
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

