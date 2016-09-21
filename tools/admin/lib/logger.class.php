<?php
/*-----------------------------------------------------+
 * 日志操作类
 * 
 * 
 +-----------------------------------------------------*/
class Logger{

    //后台操作日志表
    const ADMIN_LOG_TABLE = 'log_admin';

    /*
     * 将日志插入缓存
     **/
    public static function insertLogCache($type, $data){
        $schema = CConstant::$log_config[$type];
        $log_data = array();
        foreach($schema['fields'] as $field){
            $log_data[$field] = isset($data[$field])?$data[$field]:'';
        }
        $log_data['create_time'] = isset($data['create_time'])?$data['create_time']:time();
        $value = array('table' => $schema['table'], 'data' => $log_data);
        return MyRedis::getMasterInstance(CConstant::REDIS_LOG)->push(CConstant::QUEUE_LOGGER, $value);
    }

    /*
     * 按照既定格式插入日志
     **/
    public static function insertLog($value, $log_type_arr){
        $table = $value['table'];
        $data = $value['data'];
        // 必须带上create_time字段
        if(!isset($data['create_time'])){
            return false;
        }
        switch($log_type_arr[$table]){
            case CConstant::LOG_TYPE_DATE:
                $date_time = date('Y_m_d', $data['create_time']);
                $table_name = "{$table}_{$date_time}";
                break;
            case CConstant::LOG_TYPE_MONTH:
                $date_time = date('Y_m', $data['create_time']);
                $table_name = "{$table}_{$date_time}";
                break;
            case CConstant::LOG_TYPE_QUARTER:
                $year = date('Y', $data['create_time']);
                $quarter = get_quarter($data['create_time']);
                $table_name = "{$table}_{$year}_q{$quarter}";
                break;
            default:
                return false;
        }
        return Db::getMasterInstance(CConstant::DB_LOG, array(PDO::ATTR_PERSISTENT => true))->insert($table_name, $data);
    }

    /*
     * 获取日志分表类型配置
     **/
    public static function getLogTypeConfig(){
        return CConstant::$need_create_arr[CConstant::DB_LOG];
    }


    //--------------------------- 后台日志函数 -----------------------------------------------------

    /*
     * 获取日志分表类型配置
     **/
    public static function addAdminLog($log_type, $log_user, $task_name, $data = array()){
        if(is_array($data)){
            $data = json_encode($data);
        }
        $insert_arr = array(
            'create_time' => time(),
            'log_type' => $log_type,
            'log_user' => $log_user,
            'task_name' => $task_name,
            'json_arr' => $data,
        );
        return Db::getMasterInstance(ADMIN)->insert(self::ADMIN_LOG_TABLE, $insert_arr);
    }
}
