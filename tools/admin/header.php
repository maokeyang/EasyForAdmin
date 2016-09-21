<?php
/**
 * 启动文件
 * @author ruanhaobiao
 * @since 2015.08.31
 */

// 打开调试
if(isset($_GET['DEBUG']) && $_GET['DEBUG'] == 'open') {
    define('DEBUG', true);
}else{
    $dev_ip_arr = array(
        '127.0.0.1',
        '192.168.8.33',
    );
    define('DEBUG', !isset($_SERVER['SERVER_ADDR']) || in_array($_SERVER['SERVER_ADDR'], $dev_ip_arr) ? true:false);
}

header('Content-Type: text/html; charset=utf-8');//字符集

if(DEBUG){
    // 开发的时候严格些
    error_reporting(E_ALL | E_STRICT);
}else{
    error_reporting(0);
}

// 根路径
define('ROOT_DIR', dirname(dirname(__FILE__))."/"); 

// global路径
define("GLOBAL_DIR", ROOT_DIR);

// web路径
define("WEB_DIR", ROOT_DIR);

// crontab
define('CRONTAB_DIR', ROOT_DIR."crontab/"); 

// var路径
define("VAR_DIR", ROOT_DIR.'var/');

// 底层库目录
define('LIB_DIR', GLOBAL_DIR.'admin/lib/');

// 数据访问层目录
define('DAL_DIR', GLOBAL_DIR.'admin/dal/');

// sdk类目录
define('SDK_DIR', GLOBAL_DIR.'sdk/');

// API目录
define('API_DIR', WEB_DIR.'api/');

// logs路径
define("LOG_DIR", VAR_DIR.'logs/');

//缓存目录
define('CACHE_DIR', VAR_DIR.'cache/');

//配置目录
define('CONFIG_DIR', GLOBAL_DIR.'admin/config/');

// 文本目录
define("TEXT_DIR", ROOT_DIR.'text/');

// php 路径
define('PHP_BIN_DIR', '/usr/local/php/bin/php');

// 定义数据库
define('ADMIN', 'admin');

require LIB_DIR . '/phpexcel/PHPExcel.php';

include 'common/functions.php';

//如果PHP没有自动转义Request数据则在这里进行转义处理
if (!get_magic_quotes_gpc()) {
    $_GET = daddslashes($_GET);
    $_POST = daddslashes($_POST);
    $_FILES= daddslashes($_FILES);
    $_COOKIE= daddslashes($_COOKIE);
}

set_include_path(
    LIB_DIR.'db'
    .PATH_SEPARATOR. LIB_DIR
    .PATH_SEPARATOR. DAL_DIR
    .PATH_SEPARATOR. SDK_DIR
    .PATH_SEPARATOR. LIB_DIR.'exception'
    .PATH_SEPARATOR. LIB_DIR.'mail'
    .PATH_SEPARATOR. get_include_path()
);

spl_autoload_register('classLoader');

