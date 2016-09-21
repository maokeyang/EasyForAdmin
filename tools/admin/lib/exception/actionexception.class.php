<?php
/*-----------------------------------------------------+
 * ActionException定义
 * @author wanghan
 +-----------------------------------------------------*/
class ActionException extends Exception{
    public $_responseType;
    public $_message = '';
    public $_code = 0;

    public function __construct($message = null, $code = 0){
        parent::__construct($message, $code);
        $this->_message = $message;
        $this->_code = $code;
    }

    // 输出异常内容
    public function raiseMsg($msg = ''){
        $msg = $msg ? $msg : $this->_message;
        $_code = $this->_code ? $this->_code : 500;
        // header("HTTP/1.1 $_code error");
        if('json' == $this->_responseType){
            exit(json_encode(array('error' => $msg)));
        }
        else if('xml' == $this->_responseType){
            exit(arrayToXml(array('error' => $msg)));
        }
        header('Content-Type: text/plain; charset=utf-8');
        exit($msg);
    }
}
