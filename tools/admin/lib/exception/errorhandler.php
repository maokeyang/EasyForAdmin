<?php
/**----------------------------------------------------+
 * 自定义错误处理(加强错误显示和记录日志)
 * @author wanghan
 +-----------------------------------------------------*/
set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');

// 自定错误处理
function errorHandler($errno, $msg, $file, $line) {
    $errRpt = error_reporting();
    if (($errno & $errRpt) != $errno) return;
    throw new ErrorException("PHP Error:[$errno] $msg", $errno, 0, $file, $line);
}

// 自定义异常处理
function exceptionHandler($e) {
    $msg = "Exception Message:\n[".$e->getCode().'] "'.$e->getMessage().'" in file '.$e->getFile()." (line:".$e->getLine().").\nDebug Trace:\n".$e->getTraceAsString()."\n\n";
    // 如果这个异常已定义输出方式，则使用它来输出信息
    if(method_exists($e, 'raiseMsg')){
        $e->raiseMsg($msg);
    }
    else{
        // DEBUG模式下显示完整错误信息，并且不记录到日志文件
        if(DEBUG){
            header('Content-Type: text/plain; charset=utf-8');
            exit($msg);
        }
        // 出于安全考虑，非DEBUG模式下只显示简单错误信息，并将详细信息记录到日志文件
        else{
            writeFile(VAR_DIR.'/error_log.txt', '['.date('Y-m-d H:i:s')."]\n".$msg, 'ab');
            $code = $e->getCode() ? $e->getCode() : 500;
            header("HTTP/1.1 $code error");
            exit($e->getMessage());
        }
    }
}
