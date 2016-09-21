<?php
/*-----------------------------------------------------+
 * CmdException定义
 * @author zengjin.zhan
 +-----------------------------------------------------*/
class CmdException extends Exception{
    public $message = '';
    public $code = 0;

    /**
     * @param string $message 信息字符串
     * @param int $code 信息代号
     */
    public function __construct($message = null, $code = 255){
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
    }

    // 输出异常内容
    public function raiseMsg($msg = '') {
        echo($msg? $msg : $this->message);
    }
}
