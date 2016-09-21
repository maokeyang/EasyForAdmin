<?php
/*-----------------------------------------------------+
 * 动作类定义
 * 
 +-----------------------------------------------------*/
abstract class Action{
    const DB_MAIN = 'main';
    const DB_LOG = 'log';
    public
        /**
         * 访问控制方式
         * ACT_OPEN 开放的
         * ACT_NEED_LOGIN 登录后即可访问
         * ACT_NEED_AUTH 需要进行权限验证 
         *
         * @var int
         */
        $_AuthLevel = ACT_NEED_AUTH,

        /**
         * 指定当执行Action发生错误时抛出的Exception类型
         * @var string
         */
        $_exception,

        /**
         * 当无法访问当前action时的跳转地址
         * @var string
         */
        $_redirect = '',
        
        /**
         * 是否有权限进行删除操作
         */ 
        $_can_delete = false;

    public 
        /**
         * 数据库连接
         */ 
        $_db,       // 主连接
        $_db_log;   // 日志连接

    protected
        /**
         * 设定动作的回应类型 
         * 'json' 以JSON格式回应内容
         * 'xml' 以XML格式回应内容
         * 'text' 或其它值则以文本的格式回应内容
         *
         * @var string
         */
        $_responseType = 'json',

        /**
         * 浏览器发送的数据集合(合并了$_GET、$_POST、$_FILE)
         * @var array
         */
        $_input = array();


    public function __construct() {
        $this->_input = trimArr(array_merge($_GET, $_POST));
        $this->_file = trimArr($_FILES);
        $this->_exception = new ActionException();
        $this->_exception->_responseType = $this->_responseType;
        $this->_can_delete = App::isDeleteAllowed();
        //$this->_db = self::getDBInstance(self::DB_MAIN);
        //$this->_db_log = self::getDBInstance(self::DB_LOG);
    }

    private static function getDBInstance($db){
        $dsn = Config::get($db, 'db');
        return Db::getDBInstance($dsn);
    }

    /**
     * 输出回应内容，并中止运行
     * @param mixed $data 回应内容
     */
    public function response($data){
        header('Content-Type: text/plain; charset=utf-8');
        if('json' == $this->_responseType){
            if(!is_array($data)){
                $this->_exception->_message = '传递了错误的参数在 '.__CLASS__.'::'.__FUNCTION__.' 中，必须是一个数组';
                throw $this->_exception;
            }
            exit(json_encode($data));
        }
        else if('xml' == $this->_responseType){
            if(!is_array($data)){
                $this->_exception->_message = '传递了错误的参数在 '.__CLASS__.'::'.__FUNCTION__.' 中，必须是一个数组';
                throw $this->_exception;
            }
            exit(arraytoXml($data));
        }
        exit($data);
    }

    /**
     * 缓存特定的url参数到Session中
     * @param array $param 参数列表
     */
    public function addParamCache($param){
        if(!isset($_SESSION['param_cache'])){
            $_SESSION['param_cache'] = array();
        }
        $_SESSION['param_cache'] = array_merge($_SESSION['param_cache'], $param);
    }

    /**
     * 清除缓存的url参数
     */
    public function clearParamCache(){
        $_SESSION['param_cache'] = array();
    }
	
	/**
	 * 扩展类似JS的alert函数，响应后直接退出php执行脚本
	 * @param $msg 提示信息
	 * @param $act 默认动作返回上一页，其它：href转到链接，close关闭当前窗口
	 * @param $href 网址
	 * @return null
	 */
	public function alert($msg = '操作失败 :-(', $act = 'href', $href = '') {
		$js = '';
		switch ($act) {
			case 'href' :
				if (! $href)
					$href = $_SERVER ['HTTP_REFERER'];
				$js = "location.href='$href';";
				break;
			case 'close' :
				$js = "window.open('','_parent','');window.close();";
				break;
			default :
				$js = "history.go(-1);";
		}
		//避免因字符编码问题
		echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			  <body><script type="text/javascript">
			  alert("' . $msg . '");' . $js . '
			  </script></body></html>';
		exit ();
	}
}
