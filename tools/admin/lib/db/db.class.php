<?php
/*-----------------------------------------------------+
 * 数据库操作类(继承自PDO)
 * @author wanghan1031@sina.com
 * @since 2016.03.01
 +-----------------------------------------------------*/
class Db extends PDO{

    private static $instance;

    private 
        /**
         * 事务计数器
         * @var int
         */
        $_tcouter = 0,


        /**
         * 从属关系
         * @var string
         */
        $_position = '';

    
    /**
     * 获取主库实例
     * @param string $db_type 数据库名
     */
    public static function getMasterInstance($db_type,$options = array()){
        if(isset(self::$instance)){
            return self::$instance;
        }

        $dsn = self::getMaterConfig($db_type);

        return self::getInstance($dsn,$options);
    }

    /**
     * 获取从库实例
     * @param string $db_type 数据库名
     */
    public static function getSlaveInstance($options = array()){

        return self::getMasterInstance();
    }

    /**
     * 获取单例
     */
    private static function getInstance($dsn, $options){
        try{
            if(!$dsn){
                throw new PDOException("数据库配置错误");
            }
            self::$instance = new self($dsn,$options);
            return self::$instance;
        }catch(PDOException $e){
            self::halt($e,'数据库连接失败');
        }
    }

    /**
     * 获取主库的db配置
     * @param string $db_type 数据库名
     */
    public static function getMaterConfig($db_type){

        $config =  Config::get($db_type,'db');
        
        return $config;
    }

    // /**
    //  * 获取从库的db配置
    //  * @param string $db_name 数据库名
    //  */
    // public static function getSlaveConfig($db_type){
    //     $config =  Config::get('db');
    //     // 随机选取一个从库
    //     $index = rand(0, count($config[CConstant::DB_POS_SLAVE]) -1);
    //     $config['host'] = $config[CConstant::DB_POS_SLAVE][$index]['host'];;
    //     $config['port'] = $config[CConstant::DB_POS_SLAVE][$index]['port'];;
    //     return $config;
    // }

    /**
     * 构造函数
     * @param string $dsn 数据库配置
     * @param string $position 从属关系
     */
    public function __construct($dsn, $options){
        $user = $dsn['user'];
        $pass = $dsn['pwd'];
        $charset = $dsn['charset'];
        $dsn = "{$dsn['driver']}:host={$dsn['host']};port={$dsn['port']};dbname={$dsn['dbname']}";
        parent::__construct($dsn, $user, $pass, $options);
        $this->query("set names '{$charset}'");
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    /**
     * 执行一个查询
     * @param string $sql SQL语句
     * @return Object 返回一个结果集句柄
     */
    public function query($sql){
        //debug_log($sql,__CLASS__.'-'.__FUNCTION__);
        try{
            $this->halt_invalid_sql($sql);
            $rs = parent::query($sql);
            $rs->setFetchMode(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            self::halt($e,'执行sql失败', $sql);
        }
        return $rs;
    }

    /**
     * 执行一个SQL语句
     * @param string $sql SQL语句
     * @return bool 返回执行结果
     */
    public function exec($sql){
        //debug_log($sql,__CLASS__.'-'.__FUNCTION__);
        try{
            $this->halt_invalid_sql($sql);
            return parent::exec($sql);
        }catch(PDOException $e){
            self::halt($e,'执行sql失败', $sql);
        }
    }

    /**
     * 不加检查的执行一个SQL语句 慎用！！！！
     * @param string $sql SQL语句
     * @return bool 返回执行结果
     */
    public function force_exec($sql){
        //debug_log($sql,__CLASS__.'-'.__FUNCTION__);
        try{
            return parent::exec($sql);
        }catch(PDOException $e){
            self::halt($e,'执行sql失败', $sql);
        }
    }

    /**
     * 返回一个查询结果集中的第一行,第一列
     * @param string $sql SQL语句
     * @return string|int
     */
    public function get_one($sql){
        $rs = $this->query($sql);
        return $rs->fetchColumn();
    }

    /**
     * 返回一个查询结果集中的第一行
     * @param string $sql SQL语句
     * @return array 
     */
    public function get_row($sql){
        $rs = $this->query($sql);
        return $rs->fetch();
    }

    /**
     * 得到一个SQL查询的所有结果集
     * @param string $sql SQL语句
     * @return array
     */
    public function get_all($sql){
        $rs = $this->query($sql);
        return $rs->fetchAll();
    }

    /**
     * 执行一个查询
     * @param array $condition 查询条件
     * condition 子元素
     * table:表名 fields:查询字段 where_arr:查询条件
     * @return Object 返回一个结果集句柄
     */
    public function select_all($table, $where_arr, $fields='*'){
        $rs = $this->get_rs($table, $where_arr, $fields);
        return $rs -> fetchAll();
    }

    /**
     * 执行一个查询
     * @param array $condition 查询条件
     * condition 子元素
     * table:表名 fields:查询字段 where_arr:查询条件
     * @return Object 返回一个结果集句柄
     */
    public function select_row($table, $where_arr, $fields='*'){
        $rs = $this->get_rs($table, $where_arr, $fields);
        return $rs->fetch();
    }

    /**
     * 执行一个查询
     * @param array $condition 查询条件
     * condition 子元素
     * table:表名 fields:查询字段 where_arr:查询条件
     * @return Object 返回一个结果集句柄
     */
    public function select_one($table, $where_arr, $fields){
        $rs = $this->get_rs($table, $where_arr, $fields);
        return $rs->fetchColumn();
    }

    /**
     * 执行一个查询
     * @param array $condition 查询条件
     * condition 子元素
     * table:表名 fields:查询字段 where_arr:查询条件
     * @return Object 返回一个结果集句柄
     */
    private function get_rs($table, $where_arr, $fields){
        if(is_array($fields)){
            $fields = '`'.implode("`,`", $fields).'`';
        }
        $where = self::get_where($where_arr);
        $sql = "SELECT {$fields} FROM `{$table}` $where";
        $rs = parent::query($sql);
        $rs->setFetchMode(PDO::FETCH_ASSOC);
        return $rs;
    }

    /**
     * 执行插入insert
     * @param $table $data
     **/
    public function insert($table, $data, $rt_last_id = true) {
        $sql = $this -> get_insert_sql($table, $data);
        $res = $this -> exec($sql);

        if($rt_last_id){
            return $this -> lastInsertId();
        }
        return $res;
    }
    /**
     * 执行替换replace
     * @param $table $data
     **/
    public function replace($table, $data) {
        $sql = $this -> get_replace_sql($table, $data);
        $res = $this -> exec($sql);
        return $this -> lastInsertId();
    }

    /**
     * 执行一个update操作
     * @param $table 表名, $data 数据
     */
    public function update($table, $data, $where_arr){
        $where = self::get_where($where_arr);
        $sql = $this -> get_update_sql($table, $data, $where);
        $res = $this -> exec($sql);
        // 考虑返回影响行
        return true;
    }

    /**
     * 执行一个delete 操作
     * @param $table 表名, $where 条件
     **/
    public function delete($table, $where_arr) {
        $where = self::get_where($where_arr);
        $sql = $this -> get_delete_sql($table, $where);
        $res = $this -> exec($sql);
        // 考虑返回影响行
        return true;
    }
    /**
     * 无则插入，有则更新
     * @param table, data, field
     */
    public function insert_or_update($table, $data, $field){
        //$data = daddslashes($data);
        $sql = self::get_insert_sql($table, $data);
        $other_filed = ' ON DUPLICATE KEY UPDATE ';
        $other_arr = array();
        foreach($field as $k=>$v){
            $other_arr[] = "`".addslashes($k) ."`='".addslashes($v)."'";
        }
        $sql = $sql. $other_filed. implode(',', $other_arr);
        $res = $this -> exec($sql);
    }

    /**
     * 执行一个Limit查询
     * @param string $sql SQL语句
     * @param int $offset 偏移量
     * @param int $num 要求返回的记录数
     * @return Object 返回一个结果集句柄
     */
    public function get_all_limit($sql, $offset, $num){
        $sql .= " limit $offset, $num";
        $rs = $this->query($sql);
        return $rs->fetchAll();
    }



    /**
     * 跟据数组中的数据返回一条插入语句
     * (目前只考虑Mysql支持)
     * @param string $table 表名
     * @param array $data 数据
     * @return string SQL语句
     */
    public static function get_insert_sql($table, $data){
        return "INSERT ".self::get_col_to_val_sql($table, $data);
    }
    /**
     * 跟据数组中的数据返回一条替换语句
     * (目前只考虑Mysql支持)
     * @param string $table 表名
     * @param array $data 数据
     * @return string SQL语句
     */
    public static function get_replace_sql($table, $data){
        return "REPLACE ".self::get_col_to_val_sql($table, $data);
    }

    /**
     * 跟据数组中的数据返回一条插入语句
     * (目前只考虑Mysql支持)
     * @param string $table 表名
     * @param array $data 数据
     * @return string SQL语句
     */
    private static function get_col_to_val_sql($table, $data){
        //$data = daddslashes($data);
        $col = array();
        $val = array();
        foreach($data as $k=>$v){
            if(null === $v) continue;
            $col[] = addslashes($k);
            $val[] = addslashes($v);
        }
        return "INTO `{$table}`(`".implode('`,`', $col)."`) VALUES('".implode("','", $val)."')";
    }

    /**
     * 跟据数组中的数据返回一条更新语句
     * @param string $table 表名
     * @param array $data 数据
     * @return false|string false或SQL语句
     */
    private function get_update_sql($table, $data, $where){
        if(!$data) return false;
        //$data = daddslashes($data);
        $u = array();
        foreach($data as $k=>$v){
            if(null === $v) continue;
            $u[] = "`".addslashes($k)."`='".addslashes($v)."'";
        }
        return "UPDATE `{$table}` SET ".implode(',', $u)." $where";
    }

    /**
     * 根据数组中的数据返回一条删除语句
     * @param string $table 表名
     * @param $where 查询条件
     * @return sql 语句 
     */
    private function get_delete_sql($table, $where) {
        return " DELETE FROM `{$table}` {$where}";
    }

    public static function get_where($where_arr){
        $where = '';
        $tmp_arr = array();
        if(!empty($where_arr)) {
            foreach($where_arr as $k => $v){
                $tmp_arr[] = "`{$k}` = '{$v}'";
            }
            $where = 'WHERE '. implode(' AND ', $tmp_arr);
        }
        return $where;
    }

    /**
     * 获取指定数据表中的全部字段名
     * @param string $table_name 表名
     * @return array
     */
    public function get_fields($table_name){
        $fields = array();
        $result = $this->get_all("SHOW COLUMNS FROM `{$table_name}`");
        foreach ($result as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }

    /**
     * 获取影响行数
     * @return int
     */
    public function affected_rows(){
        return parent::rowCount();
    }


    /**
     * 事务开始
     */
    public function begin_transaction(){
        if($this->_tcouter<0){
            throw new Exception("beginTransaction 没有严格配对");
        }else if($this->_tcouter == 0){
            parent::beginTransaction(); 
        }
        $this->_tcouter++;
    }

    /**
     * 事务提交
     */
    public function commit(){
        if($this->_tcouter > 1){
            $this->_tcouter--;
        }else if($this->_tcouter == 1){
            $this->_tcouter--;
            parent::commit(); 
        }else{
            throw new PDOException("commit 没有严格配对");
        }
    }

    /**
     * 事务回滚
     */
    public function rollback(){
        if($this->_tcouter > 1){
            $this->_tcouter--;
            throw new PDOException("rollback 内嵌回滚");
        }else if($this->_tcouter == 1){
            $this->_tcouter--;
            parent::rollback(); 
        }else{
            throw new PDOException("rollback 没有严格配对");
        }
    }


    /**
     * 终止错误的主从sql执行
     * 含insert|update|delete的必须在主库执行
     */
    private function halt_invalid_sql($sql){
        if(DEBUG && (!$this->is_sql_valid($sql))){
            throw new PDOException("主从库操作错误");
        }
    }

    /**
     * 检查sql语句是否有效
     * 含insert|update|delete的必须在主库执行
     */
    private function is_sql_valid($sql){
        // $pattern_slave = "/INSERT|UPDATE|DELETE|CREATE/iA";  
        // if(preg_match($pattern_slave, $sql) ){
        //     return false;
        // }
        // $pattern_master = "/SELECT/iA";  
        // if(preg_match($pattern_master, $sql)){
        //     return false;
        // }
        // return true;
        return TRUE;
    }



    /**
     * 终止
     */
    private static function halt($err,$msg='', $sql=''){
        if(@defined('NO_DIE')){
            //异常不终止 重新抛出异常 供上层监测
            throw $err;
        }
        if(!DEBUG)exit('invalid query.');
        $error = $err->getMessage();
        $errno = $err->getCode();
        $debug_info = $err->getTrace();
        $err_html = '';
        if($msg){
            $err_html .= "<b>Database error:</b> $msg <br />";
        }
        if($sql){
            $err_html .= "<b>While quering sql:</b> $sql <br />";
        }
        $err_html .= "<b>MySQL Error:</b><br />errno: {$errno} <br />error: {$error}<br />";
        $err_txt='';
        foreach ($debug_info as $v)
            if (isset($v['file'])) {
                $err_html .= "<b>File:</b> {$v['file']} (Line: {$v['line']})<br />";
                $err_txt .="{$v['file']} (Line: {$v['line']})\r\n";
            }
        echo "<pre>".$err_html."</pre>";
        exit();
    }
}
