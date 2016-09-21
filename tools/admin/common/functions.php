<?php
/**
 * 类自动加载函数
 * @param string $class 类名
 */
function classLoader($class){
    if (class_exists($class) || interface_exists($class)) {
        return;
    }
    include strtolower($class).'.class.php';
}

/*
 * 扫描所有子目录
 * */
function scanDirs($dir){
    $dirs = array();
    $dh  = @opendir($dir);
    while (false !== ($file_name = readdir($dh))) {
        if($file_name !='.' && $file_name !='..' && is_dir($dir.$file_name)){
            $dirs[] = $dir.$file_name;
        }
    }
    @closedir($dh);
    return $dirs;
}

function alert($content = false, $url = "")
{
    $javascript = "<script>";

    //链接警告窗口
    $javascript .= $content ? ("alert('" . $content . "');") : ("");

    //链接跳转信息
    $javascript .= ($url == "") ? ("history.back();") : ("document.location.href='" . $url . "'");

    //链接尾部信息
    $javascript .= "</script>";

    echo $javascript;
    exit;
}

function ip_valid($ip) {
    //为开发便利设127.0.0.1为合法
    if(DEBUG&&$ip=='127.0.0.1') return true;
    if (filter_var($ip, FILTER_VALIDATE_IP, 
        //FILTER_FLAG_IPV4 | 
        //FILTER_FLAG_IPV6 |
        FILTER_FLAG_NO_RES_RANGE |
        FILTER_FLAG_NO_PRIV_RANGE) === false)
        return false;
    return true;
}

/*
 * HTTP_X_FORWARDED_FOR为逗号分隔的一系列Ip，或者unknown,或者null
 * 取HTTP_X_FORWARDED_FOR的第一个逗号前的字符串，有可能是unknown
 * @return string
 */
function get_XFF_IP(){
    $xip=getenv ( 'HTTP_X_FORWARDED_FOR' );
    //$xip='120.120.121.1,120.120.121.13';
    if(preg_match('/[\d\.]{7,15}(,[\d\.]{7,15})+/',$xip)){
        $xips=explode(',',getenv ( 'HTTP_X_FORWARDED_FOR' ));
        return $xips[0];
    }else{
        return $xip;
    }
}

//获取客户端IP
function get_client_ip() {
    $cip = getenv ( 'HTTP_CLIENT_IP' );
    $rip = getenv ( 'REMOTE_ADDR' );
    $srip = $_SERVER ['REMOTE_ADDR'];
    //多玩平台的HTTP_X_FORWARDED_FOR得到127.0.0.1，这里做一个临时处理
    if ($cip && strcasecmp ( $cip, 'unknown' )) {
        $onlineip = $cip;
    } else{
        $xip = get_XFF_IP();//getenv ( 'HTTP_X_FORWARDED_FOR' );
        if ($xip && ($xip !="127.0.0.1") && strcasecmp ( $xip, 'unknown' )) {
            $onlineip = $xip;
        } elseif ($rip && strcasecmp ( $rip, 'unknown' )) {
            $onlineip = $rip;
        } elseif ($srip && strcasecmp ( $srip, 'unknown' )) {
            $onlineip = $srip;
        }
        preg_match ( "/[\d\.]{7,15}/", $onlineip, $match );
        return $match [0] ? $match [0] : 'unknown';
    }
}

/**
 * 输出变量的内容，通常用于调试
 *
 * @package Core
 *
 * @param mixed $vars 要输出的变量
 * @param string $label
 * @param boolean $return
 */
function dump($vars, $label = '', $return = false) {
    if (ini_get ( 'html_errors' )) {
        $content = "<pre>\n";
        if ($label != '') {
            $content .= "<strong>{$label} :</strong>\n";
        }
        $content .= htmlspecialchars ( print_r ( $vars, true ) );
        $content .= "\n</pre>\n";
    } else {
        $content = $label . " :\n" . print_r ( $vars, true );
    }
    if ($return) {
        return $content;
    }
    echo $content;
    return null;
}

function dstripslashes($mixed) {
    return is_array ( $vars ) ? array_map ( __FUNCTION__, $vars ) : stripslashes ( $vars );
}

function daddslashes($vars) {
    return is_array ( $vars ) ? array_map ( __FUNCTION__, $vars ) : addslashes ( $vars );
}



function get_api_content($url, $post_arr,$timeout = 60)
{
    global $g_c;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array('api_data' => urlencode(serialize($post_arr))));
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    $content = curl_exec($curl);
    if($content === false)
    {
        $curl_error = curl_error($curl);
        Admin::add_admin_log('get_api_content', 'curl_error', $curl_error, false);
    }

    curl_close($curl);
    $content_arr = json_decode($content, 1);

    if ($content_arr['state']) {
        return $content_arr['result'];
    } else {
        //return;
        $error_array=array(
            'url'=>$url,
            'error_content'=>$content,
        );
        Admin::add_admin_log('get_api_content', 'content_error', json_encode($error_array), false);
        return $g_c['api']['error'];
    }
}

//获取加密串
function get_sign($seccode, $param_arr)
{
    $sign = $seccode;
    if (is_array($param_arr)) {
        ksort($param_arr);
        foreach ($param_arr as $key => $val) {
            if ($key !='' && $val !='') {
                $sign .= $key.$val;
            }
        }
    }
    $sign = strtoupper(md5($sign));
    return $sign;
}

function get_api_data()
{
    $post_arr = empty($_REQUEST['api_data']) ? array(): unserialize(urldecode(dstripslashes($_REQUEST['api_data'])));
    return $post_arr;
}

//秒数转化为时间
function secondToString($diff)
{
    if ($diff < 60) {
        return intval($diff % 60) . "秒";
    } elseif ($diff < 60 * 60) {
        return intval($diff / 60) . "分钟";
    } elseif ($diff < 60 * 60 * 24) {
        return intval($diff / 60 / 60) . "小时";
    } elseif ($diff < 60 * 60 * 24 * 7) {
        return intval($diff / 60 / 60/ 24) . "天";
    } elseif ($diff < 60 * 60 * 24 * 30) {
        return intval($diff / 60 / 60/ 24/7) . "星期";
    } elseif ($diff < 60 * 60 * 24 * 365) {
        return intval($diff / 60 / 60/ 24 / 30) . "月(".intval($diff / 60 / 60/ 24)."天)";
    } else {
        $year = intval($diff / 60 / 60/ 24 / 365);
        return $year . "年(".intval($diff / 60 / 60/ 24)."天)";
    }
}


//获取字符串中的时间戳，并以数组形式返回，找不到返回 false
function get_timestamp_array($str)
{
    if(!preg_match_all('#\d{10}#', $str, $matches)){
        return false;
    }
    return $matches[0];
}

function jsonToArr($json, $type = 0, $is_all = 0)
{
    $return = "";

    if(!$type) {
        $return .= "\$array = array(\n";
        $arr = json_decode($json, 1);
        foreach($arr as $key => $val)
        {
            if(is_numeric($val))
            {
                $return .= "\t".( (is_numeric($key)) ? "{$key}" : "'{$key}'" ) ." => {$val},\n";
            }
            elseif(is_array($val))
            {
                $return .= "\t{$key} => ".jsonToArr($val, $type+1, $is_all)."\n";
            }
            else
            {
                $return .= "\t".( (is_numeric($key)) ? "{$key}" : "'{$key}'" ) ." => '{$val}',\n";
            }
        }
        $return .= ");";

        $f = fopen("jsonToArrOnLine.php", "w+");
        fwrite($f, $return);
        fclose($f);
    } else {
        $is_enter = $json ? 1 : 0;
        $t = str_repeat("\t", $type+1);

        $n = "\n";

        if($type > 1)
        {
            $n = "";
            $is_enter = $t = "";
        }

        $return .= "array(";
        $is_enter && $return .= "\n";

        if($is_enter)
        {
            foreach($json as $key => $val)
            {
                if(is_numeric($val))
                {
                    $return .= $t.( (is_numeric($key)) ? "{$key}" : "'{$key}'" ) ." => {$val},".$n;
                }
                elseif(is_array($val))
                {
                    $return .= $t.( (is_numeric($key)) ? "{$key}" : "'{$key}'" )." => ".jsonToArr($val, $type+1, $is_all).$n;
                }
                else
                {
                    $return .= $t.( (is_numeric($key)) ? "{$key}" : "'{$key}'" ) ." => '{$val}',".$n;
                }
            }
        }
        else
        {
            foreach($json as $key => $val)
            {
                if(is_numeric($val))
                {
                    $return .= "{$val}, ";
                }
                elseif(is_array($val))
                {
                    $return .= jsonToArr($val, $type+1, $is_all);
                }
                else
                {
                    $return .= "'{$val}', ";
                }
            }
        }

        $return .= $is_enter ? $t.")," : "), ";

        return $return;
    }
}


// 打印json
function exit_json($var){
    die(json_encode($var));
}

function urlDump($game_api_url, $model, $ac, $post_arr)
{
    $sign = '';//get_api_sign( $post_arr );
    die($game_api_url."{$model}.php?ac={$ac}&sign={$sign}&api_data=".urlencode(serialize($post_arr)));
}

/*
 * 获得一个随机字符串
 * */
function rand_string($len = 32) {
    $char_set = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return do_rand_string($char_set, $len);
}

/*
 * 扩展随机字符串 包含特殊字符
 * */
function randStringx($len = 32) {
    $char_set = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#$%^&*()_+";
    return do_rand_string($char_set, $len);
}
/*
 * 随机字符串
 * */
function do_rand_string($char_set, $len = 32) {
    $strLen = strlen($char_set);
    $string = '';
    for($i = 0; $i < $len; $i++) {
        $string .= substr($char_set, mt_rand(0, $strLen), 1);
    }
    return $string;
}

/**
 * 日志函数
 * @param $vars 需要写log的变量
 * @param string $file_name 文件名
 * 为了debug方便，建议用类名和文件名命名log文件:debug_log($vars,__CLASS__.'-'.__FUNCTION__);
 * logs目录与www目录同级，在boot.php中定义
 */
function debug_log($vars,$file_name=null){
	// if(!DEBUG) return;
	qlog($vars,$file_name);
}
/**
 * 日志函数
 * @param $vars 需要写log的变量
 * @param string $file_name 文件名
 * 忽略DEBUG变量
 */
function qlog($vars,$file_name=null){
	if(is_array($vars)||is_object($vars)){
		$vars=json_encode($vars);
	}
	$vars=date('h:i:s',time()).': '.$vars;
	$file_name=$file_name?$file_name:'qlog';
	if(!is_dir(LOG_DIR)){
		mkdir(LOG_DIR);
	}
	$log_file=fopen(LOG_DIR.'/'.$file_name.'_'.date('ymd',time()).'.log','a+');
	fputs($log_file,$vars."\r\n");
	fclose($log_file);
}

/**
 * 对变量进行 trim 处理,支持多维数组.(截去字符串首尾的空格)
 * @param mixed $vars
 * @return mixed 
 */
function trimArr($vars) {
    return is_array ( $vars ) ? array_map ( __FUNCTION__, $vars ) : trim ( $vars );
}

/**
 * 对变量进行 nl2br 和 htmlspecialchars 操作,支持多维数组.（可将字符串中的换行符转成HTML的换行符号）
 * @param mixed $vars
 * @return mixed  
 */
function textFormat($vars) {
    return is_array ( $vars ) ? array_map ( __FUNCTION__, $vars ) : nl2br ( htmlspecialchars ( $vars ) );
}

/**
 * 执行一个 HTTP 请求
 * @param string    $Url    执行请求的Url
 * @param mixed     $params 表单参数, 如果是get的话，此参数无效
 * @param string    $method 请求方法 post / get
 * @return array 结果数组
 */
 function request($url, $params = '', $method='get', $content_type='')
{
    $Curl = curl_init();//初始化curl

    if ('get' === $method){//以GET方式发送请求
        curl_setopt($Curl, CURLOPT_URL, $url);
    }else{//以POST方式发送请求
        curl_setopt($Curl, CURLOPT_URL, $url);
        curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($Curl, CURLOPT_POSTFIELDS, $params);//设置传送的参数
    }
    if($content_type){
        curl_setopt($Curl, CURLOPT_HTTPHEADER, array("Content-type: {$content_type}" ));
    }else{
    	curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
	}
    curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
    curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间

    $Res = curl_exec($Curl);//运行curl
    $Err = curl_error($Curl);
     
    if (false === $Res || !empty($Err)){
        $Errno = curl_errno($Curl);
        $Info = curl_getinfo($Curl);
        curl_close($Curl);
        return array(
                'result' => false,
                'errno' => $Errno,
                'msg' => $Err,
                'info' => $Info,
        );
    }
    curl_close($Curl);//关闭curl
    return array(
        'result' => true,
        'msg' => $Res,
    );
}

/*
 * 获取当前时间戳属于一年中第几季度
 * */
function get_quarter($ts){
    $month = date('m', $ts);
    return ceil($month / 3);
}

/*
 * 获取语言
 * */
function get_lang($key){
    echo Lang::getLang($key);
}


/**
 * 通过CURL POST
 * @param data $data 是 json 字符串   
 * 
 */
 function curl_post_json($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json;charset=utf-8'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $Res = curl_exec($ch);//运行curl
    $Err = curl_error($ch);
     
    if (false === $Res || !empty($Err)){
        $Errno = curl_errno($ch);
        $Info = curl_getinfo($ch);
        curl_close($ch);
        return array(
                'result' => false,
                'errno' => $Errno,
                'msg' => $Err,
                'info' => $Info,
        );
    }
    curl_close($ch);//关闭curl
    return array(
        'result' => true,
        'msg' => $Res,
    );
}

/**
 * 保存上传文件
 * @param array $file 上传文件数组
 * @param string $dir 保存目录
 * @param string $ext 保存文件扩展名(默认为空,即原始文件扩展名)
 * @return array $res
 */
function save_upload_file($file, $dir, $ext = ''){
	$res = array();
	foreach($file['data']['tmp_name'] as $key => $val){
		$res[$key] = '';
		if(!empty($val)){
			
			if(!file_exists($dir)){
				mkdir($dir,0777,true);
			}
			
			//文件扩展名
			if(empty($ext)){
				$info = pathinfo($file['data']['name'][$key]);
				$ext = $info['extension'];
			}
			//保存文件
			$new_file_name = $dir.'/'.$key.'_'.date('YmdHis').'.'.$ext;
			if(rename($val,$new_file_name)){
				$res[$key] = ltrim($new_file_name,ADMIN_DIR);
			}
		}
	}
	
	return $res;
	
}

function curl_post_data($url, $header = NULL, $msg = NULL, $is_post = TRUE)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置头信息的地方
        }
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
        }
        $data = array();
        $data[1] = curl_exec($ch);
        $data[0] = curl_getinfo($ch, CURLINFO_HTTP_CODE); //HTTPSTAT
        curl_close($ch);

        return $data;
}
