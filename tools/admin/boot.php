<?php
/**
 * 启动文件
 * @author ruanhaobiao
 * @since 2015.08.31
 */

include 'header.php';
set_include_path(
    LIB_DIR.'exception'
    .PATH_SEPARATOR. get_include_path()
);

// APP的权限标识常量定义
define('ACT_NEED_AUTH', 0);     //需要登录并验证
define('ACT_NEED_LOGIN', 1);    //需要登录
define('ACT_OPEN', 2);          //完全开放


// admin目录
define('ADMIN_DIR', WEB_DIR.'admin/');

// app目录
define('APP_DIR', ADMIN_DIR.'app/');

// 语言目录
define('LANG_DIR', GLOBAL_DIR.'lang/');

//上传文件目录
define('ADMIN_UF_DIR', ADMIN_DIR.'uploadfile/');

//sdk资源目录
define('SDK_UF_DIR',ADMIN_DIR.'sdkfile/');

// 时区设置
//date_default_timezone_set($config->get('timezone'));

// 设置session文件的失效时间，默认为1小时
ini_set("session.gc_maxlifetime", 3600);
// session文件清除机率，默认为20%，访问量大的网站可以设小一些
ini_set('session.gc_probability', 20);
// session保存到特定目录
session_save_path(VAR_DIR.'/sess');
header('Cache-control: private, must-revalidate'); // 支持页面回跳
header('P3P: CP=CAO PSA OUR'); // 解决IE中iframe跨域访问cookie/session的问题
@ini_set("session.cookie_httponly", 1); //防止cookie被js获取

// 初始化语言包
