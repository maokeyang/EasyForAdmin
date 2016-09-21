<?php
/**
 * 入口文件
 * @author ruanhaobiao
 * @since 2015.08.31
 */

error_reporting(E_ALL);
// 引入启动文件
include 'boot.php';
// 每个请求最长允许运行5分钟
set_time_limit(300);
// 启用session
session_start();
// 运行应用
App::run();
