<?php
/*-----------------------------------------------------+
 * mysql merge类
 * @since 2016.4.25
 +-----------------------------------------------------*/

class Merge{

    /*
     * 更新merge表
     * 
     * */
    public static function updateMergeTableAll(){
        $rt = true;
        foreach(CConstant::$need_create_arr as $db_type => $tables){
            echo "正在合并{$db_type}相关表\n";
            $failed_arr = array();
            foreach($tables as $table_name => $log_type){
                if(self::isTableExists($db_type, $table_name)){
                    // 存在则alter
                    if(!self::alterMergeTable($db_type, $table_name)){
                        $failed_arr[] = $table_name;
                    }
                }else{
                    // 不存在则create
                    if(!self::createMergeTable($db_type, $table_name)){
                        $failed_arr[] = $table_name;
                    }
                }
            }
            if(!empty($failed_arr)){
                echo "{$db_type}.".implode(",", $failed_arr)."合并失败\n";
                $rt = false;
            }
        }
        return $rt;
    }

    /*
     * 更新merge表
     * 
     * */
    public static function updateMergeTable($db_type, $table_name){
        if(self::isTableExists($db_type, $table_name)){
            // 存在则alter
            return self::alterMergeTable($db_type, $table_name);
        }else{
            // 不存在则create
            return self::createMergeTable($db_type, $table_name);
        }
    }

    /*
     * 检查表是否存在
     * 
     * */
    public static function isTableExists($db_type, $table_name){
        $sql = "SHOW TABLES LIKE '$table_name'";
        $table = Db::getMasterInstance($db_type) -> get_row($sql);
        if(empty($table)){
            return false;
        }
        return true;
    }

    /*
     * 创建merge表
     * 
     * */
    public static function createMergeTable($db_type, $table_name){
        $sub_arr = self::getSubTableList($db_type, $table_name);
        if(empty($sub_arr)){
            return false;
        }
        //以模板表为模板
        $sql = "SHOW CREATE TABLE {$table_name}_template";
        try{
            $db_struct = Db::getMasterInstance($db_type) -> get_row($sql);
            $create_sql = $db_struct['Create Table'];

            // 以ENGINE=作为分割
            $separate = 'ENGINE=';
            $create_sql = substr($create_sql, 0, strpos($create_sql, $separate));

            //替换表名
            $table_name_tpl = $table_name.'_template';
            $create_sql = str_replace($table_name_tpl, $table_name, $create_sql);

            //添加表存在检查
            $create_sql = str_replace('CREATE TABLE ', 'CREATE TABLE IF NOT EXISTS ', $create_sql);
            $create_sql .= "ENGINE=MRG_MyISAM DEFAULT CHARSET=utf8 UNION=(`".implode('`,`', $sub_arr)."`) INSERT_METHOD=NO";
            //echo $create_sql;
            return $db = Db::getMasterInstance($db_type) -> force_exec($create_sql);
        }catch(Exception $e){
            return false;
        }
    }

    /*
     * 修改merge表的子表列表
     * 
     * */
    public static function alterMergeTable($db_type, $table_name){
        $sub_arr = self::getSubTableList($db_type, $table_name);
        if(empty($sub_arr)){
            return false;
        }
        $sql = "ALTER TABLE `$table_name` ENGINE=MRG_MyISAM UNION=(`".implode('`,`', $sub_arr)."`) INSERT_METHOD=NO";
        return Db::getMasterInstance($db_type) -> force_exec($sql);
    }

    /*
     * 获取所有子表
     * 
     * */
    public static function getSubTableList($db_type, $table_name){
        // 根据分表的配置方法 得到匹配所有分表的规则
        $type_config_arr = CConstant::$need_create_arr[$db_type];
        $type = $type_config_arr[$table_name];
        $type = CConstant::$merge_type_arr[$type];
        $sql = "SHOW TABLES LIKE '{$table_name}_{$type}'";
        $db = Db::getMasterInstance($db_type);
        $rt = $db -> get_all($sql);
        $sub_arr = array();
        foreach($rt as $row){
            $table_name = end($row);
            // 过滤模板表
            if(strpos($table_name, '_template') === false){
                $sub_arr[] = $table_name;
            }
        }
        return $sub_arr;
    }

    /*
     * 获取日志分表类型配置
     **/
    public static function getLogTypeConfig($db_type = Db::DB_LOG){
        return CConstant::$need_create_arr[Db::DB_LOG];
    }

    /*
     * 创建分表
     **/
    public static function createTable($time){
        $date_time = date('Y_m_d', $time);
        $date_time_arr = explode('_', $date_time);
        $year = $date_time_arr[0];
        $month = $date_time_arr[1];
        $date = $date_time_arr[2];
        echo "正在创建{$date_time}相关表\n";
        foreach(CConstant::$need_create_arr as $db_name => $tables){
            $db = Db::getMasterInstance($db_name);
            echo "正在创建{$db_name}的".implode(',',array_keys($tables))."表\n";
            // 根据分表模板生成分表
            foreach($tables as $table => $log_type){
                switch($log_type){
                case CConstant::LOG_TYPE_DATE:
                    $table_name = "{$table}_{$date_time}";
                    break;
                case CConstant::LOG_TYPE_MONTH:
                    $table_name = "{$table}_{$year}_{$month}";
                    break;
                case CConstant::LOG_TYPE_QUARTER:
                    $quarter = get_quarter($time);
                    $table_name = "{$table}_{$year}_q{$quarter}";
                    break;
                default:
                    echo "{$table}未定义相关日志类型!\n";
                    continue;
                } 
                echo "正在创建{$table}表\n";
                $sql = "CREATE TABLE IF NOT EXISTS {$table_name} LIKE {$table}_template";
                $db->force_exec($sql);
            }
        }
        echo "分表创建完成\n";
    }

    /*
     * 检查分表是否创建成功
     **/
    public static function isCreateTableSuss($time){
        $date_time = date('Y_m_d', $time);
        $date_time_arr = explode('_', $date_time);
        $year = $date_time_arr[0];
        $month = $date_time_arr[1];
        $date = $date_time_arr[2];
        $quarter = get_quarter($time); //季度
        $all_failed_tables = array();

        foreach(CConstant::$need_create_arr as $db_name => $tables){
            $db = Db::getMasterInstance($db_name);
            $config =  Config::get($db_name, 'db');
            // 获取所有已建立的表
            $all_tables = $failed_tables =  array();
            $show_tables = $db -> get_all("SHOW TABLES");
            foreach($show_tables as $row){
                $all_tables[] = $row["Tables_in_{$config['dbname']}"];
            }

            foreach($tables as $table => $log_type){
                switch($log_type){
                case CConstant::LOG_TYPE_DATE:
                    $table_name = "{$table}_{$date_time}";
                    break;
                case CConstant::LOG_TYPE_MONTH:
                    $table_name = "{$table}_{$year}_{$month}";
                    break;
                case CConstant::LOG_TYPE_QUARTER:
                    $table_name = "{$table}_{$year}_q{$quarter}";
                    break;
                default:
                    echo "{$table}未定义相关日志类型!\n";
                    $failed_tables[] = $table;
                    continue;
                } 
                // 建立失败的表
                if(!in_array($table_name, $all_tables)){
                    $failed_tables[] = $table_name;
                }
            }
            if(!empty($failed_tables)){
                echo "{$db_name}.".implode(",", $failed_tables)."建立失败\n";
                return false;
            }
        }
        echo "检查分表创建完成\n";
        return true;
    }
}
