<?php
/*-----------------------------------------------------+
 * 系统预定义数据操作类
 +-----------------------------------------------------*/
class Config{

    private static $conf = array();

    /**
     * 取出指定键的数据
     * @param string $key 键名
     */
    public static function get($key, $config_type = 'replace') {
        $config = self::load($config_type);

        return $config[$key];
    }

    /**
     * 保存配置
     * TODO: 未实现 
     */
    public static function set($key, $value){
    }

    /**
     * 加载数据
     * @return 内容
     */
    private static function load($config_type){

        if(!isset(self::$conf[$config_type])){
            self::$conf[$config_type] = require CONFIG_DIR."$config_type.php";
        }
        
        return self::$conf[$config_type];
    }
}