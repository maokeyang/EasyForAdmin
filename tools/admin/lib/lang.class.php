<?php
/*-----------------------------------------------------+
 * 简单的语言包
 * 
 +-----------------------------------------------------*/

class Lang{

    //根据key值获得相应的语言包
    public static function getLang($key){
        return @defined("LANG_{$key}") ? constant("LANG_{$key}") : $key;
    }

    //获取所有语言
    public static function getAllLang(){
        return CConstant::$lang_arr;
    }

    //初始化语言
    public static function initLang(){
        if(!isset($_SESSION['lang'])){
            // 默认中文
            $_SESSION['lang'] = 'zh_cn';
        }
        $all_lang = require(LANG_DIR.$_SESSION['lang'].'.php');
        foreach($all_lang as $k => $v){
            // 将错误码的key定义为宏
            define("LANG_{$k}", $v);
        }
        foreach(CConstant::$lang_arr as $lang => &$name){
            $name = self::getLang($lang);
        }
    }
}
