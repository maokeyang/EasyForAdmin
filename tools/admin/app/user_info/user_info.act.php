<?php
/**
* 账号日志管理
*
*/

class Act_User_Info extends Page {

    
    const TABLE_KEY_FIELD = "id";
    private $table_name = "";
    public $game_getter;

    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(CConstant::DB_USER);
        $this->game_getter    = NameGetter::getInstance(ADMIN,'game');
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
        

    }

    /**
     * 执行入口
     */
    public function process (){
        $this->assign('title', '账号日志');
        if(isset($this->_input['is_ajax'])){
            if(isset($this->_input['get_game_channel'])){
                die($this->get_channel_from_game($this->_input['kw']['game_code']));
            }elseif(isset($this->_input['get_child_channel'])){
                die($this->get_child_channel($this->_input['kw']['parent_channel_code']));
            }else{
                $this->assign('data', $this->get_data());
                $total_record = $this->get_total_num();
                $this->assign('total_record', $total_record);
                $this->assign('page', $this->create_pager($total_record));
                $this->assign('limit', $this->limit);
                //渠道列表
                $this->assign('channel_data', Channel_Game::getAllChannel());
                //游戏列表
                $this->assign('game_data', Channel_Game::getAllGame());
                //一级游戏列表
                $this->assign('parent_game',Channel_Game::getParentGame());
            } 
        }
        $this->display();
    }

    /**
     * 获取查询条件语句
     */
    private function condition() {
        $where = array();   
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
        $where[] = "ctime >= '".$start_time."'";
        $where[] = "ctime <= '".$end_time."'";

        if(isset($this->_input['kw']['parent_game_id']) && $this->_input['kw']['parent_game_id']){
            if(isset($this->_input['kw']['game_code']) && $this->_input['kw']['game_code']){
                $where[] = "game_code = '{$this->_input['kw']['game_code']}'";
            }else{
                $game_code_arr = Channel_Game::getChildGame($this->_input['kw']['parent_game_id']);
                $game_code_str = implode(array_keys($game_code_arr), '\',\'');
                $where[] = "game_code in ('".$game_code_str."')";
            }
            
        }
        
        if(isset($this->_input['kw']['channel_code']) && $this->_input['kw']['channel_code']){
            $where[] = "channel_code = '{$this->_input['kw']['channel_code']}'";
        }            
        if(isset($this->_input['kw']['uname']) && $this->_input['kw']['uname']){
            $where[] = "uname like '{$this->_input['kw']['uname']}%'";
            $this->table_name = User::getUserTableName($this->_input['kw']['uname']);
        }   
        if(!$where) return '';
        return  " WHERE ".implode(' AND ', $where);
    }

    /**
     * 获取数据列表
     */
    public function get_data(){
        $where = $this->condition();
        $offset = $this->limit*$this->page;
        $limit = $this->limit;
        $now = time();
        $data = array();
        if(empty($this->table_name)){
            for($i=0;$i<200;$i++){
                $this->table_name = 'user_'.$i;
                $this->sql = "SELECT *, ({$now}-`last_login`)/86400 as not_login_days, ({$now}-`last_charge_time`)/86400 as not_charge_days FROM `".$this->table_name."` {$where}  ORDER BY `ctime` DESC LIMIT {$offset},{$limit}";
                $return =  $this->db_slave->get_all($this->sql);
                $sql[] = $this->sql;
                $data = array_merge($data, $return);
            }
            // die(json_encode($sql)); 
        }else{
            $this->sql = "SELECT *,({$now}-`last_login`)/86400 as not_login_days,({$now}-`last_charge_time`)/86400 as not_charge_days FROM `".$this->table_name."` {$where}  ORDER BY `ctime` DESC LIMIT {$offset},{$limit}";
            $data = $this->db_slave->get_all($this->sql);
        }
        //die(json_encode($data));
        return $data;
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

