<?php
/*-----------------------------------------------------+
 * PageException定义
 * @author wanghan
 +-----------------------------------------------------*/
class PageException extends Exception{
    public $links;
    public $message = '';
    public $code = 0;

    /**
     * @param string $message 信息字符串
     * @param int $code 信息代号
     * @param array $links 跳转链接
     * 例:$links = array( '到a页'=>'a.html','到b页' => 'b.html');
     */
    public function __construct($message = null, $code = 0, $links = array()){
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
        $this->links = isset($links) && is_array($links)? $links : array();
    }

    // 输出异常内容
    public function raiseMsg($msg = '') {
        $v= array ();
        $v['msg']= $msg ? $msg : $this->message;
        $v['links'] = '';
        if(count($this->links)) {
            foreach ($this->links as $key => $val) {
                $v['links'] .= '<a href="'.$val.'">'.$key.'</a>&nbsp;';
            }
        }

        include APP_DIR.'/_layout/err.tpl.htm';
        exit();
    }
}
