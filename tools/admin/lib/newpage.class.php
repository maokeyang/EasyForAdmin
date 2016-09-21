<?php
/*-----------------------------------------------------+
 * 页面形式的动作类
 * @author wanghan
 +-----------------------------------------------------*/
class NewPage extends Action {
    protected
        $_layoutFile,
        $_blocks= array(),
        $_blocksStack= array(),
        $_tplFile= array(),
        $_pagevar= array(),
        $_contents;

    // 分页显示数量
    protected $limit_arr = array(20, 50, 100);

    public $limit = 20, $page = 0;
    public $order_by = '', $sorting = 'ASC';
    
    public static $_css_arr = array(
        '0' => 'info',
        '1' => 'success',
        '2' => 'warning',
        '3' => 'danger',
        '4' => 'default',
        '5' => 'primary',
    );

    public function __construct() {
        parent :: __construct();
        $this->exception = new PageException();
        if (isset($this->_input['page']) && is_numeric($this->_input['page'])) {
            $this->page = $this->_input['page'];
        }
        if (isset($this->_input['limit']) && is_numeric($this->_input['limit'])) {
            $this->limit = $this->_input['limit'];
        }
        if (!empty($this->_input['order_by'])) {
            $this->order_by = $this->_input['order_by'];
        }
        if (!empty($this->_input['sorting'])) {
            $this->sorting = $this->_input['sorting'];
        }
    }

    /**
     * 使用布局页面
     */
    private function setLayout($filename){
        $this->_layoutFile = APP_DIR.'/_layout/'.$filename.'.layout.htm';
    }

    /**
     * 加载块内容
     */
    private function loadBlock($name){
        echo $this->_blocks[$name];
    }

    /**
     * 块定义开始
     */
    private function block($name){
        array_push($this->_blocksStack, $name);
        ob_start();
    }

    /**
     * 块定义结束
     */
    private function endBlock($endName){
        $name = array_pop($this->_blocksStack);
        if($name != $endName){
            throw new Exception('区块定义有误，未配对或有交叉');
        }
        $this->_blocks[$name] = ob_get_clean();
    }

    /**
     * 编译模板
     */
    protected function compile() {
        $eReport = error_reporting(); // 保存原来的设定
        error_reporting(E_ALL ^E_NOTICE);
        extract($this->_pagevar);

        ob_start();
        foreach ($this->_tplFile as $file) {
            if($file['absolute'])
                include $file['filename'];
            else include APP_ROOT.'/_tpl/'.$file['filename'];
        }
        $this->_contents= ob_get_contents();
        ob_end_clean();

        if($this->_layoutFile){
            ob_start();
            include $this->_layoutFile;
            $this->_contents= ob_get_contents();
            ob_end_clean();
        }
        error_reporting($eReport); // 恢复原来的设定
    }

    /**
     * 添加模板
     * @param string $filname 模板文件名
     * @param bool $absPath 模板文件是否使用绝对路径的
     */
    public function addTemplate($filename, $absPath=false) {
        if(!$absPath) $filename .= '.tpl.htm';
        $this->_tplFile[]= array(
            'filename' => $filename
            ,'absolute' => $absPath
        );
    }

    /**
     * 清空模板设置
     */
    public function cleanTemplate(){
        $this->_tplFile = array();
    }

    /**
     * 添加模板变量
     *
     * @param string $key 变量名
     * @param mixed $var 变量值
     */
    public function assign($key, $var) {
        $this->_pagevar[$key] = $var;
    }

    /**
     * 添加模板变量
     *
     * @param string $key 变量名
     * @param mixed $var 变量值
     */
    public function assignArr($var) {
        if(!empty($var)) {
            foreach($var as $k => $v) {
                $this->_pagevar[$k] = $v;
            }
        }
    }

    /**
     * 编译并返回模板内容
     *
     * @return string
     */
    public function fetch() {
        //如果没有添加任何模板则默认使用与当前动作同名的模板
        if(!count($this->_tplFile)){
            $this->addTemplate(CURRENT_ACTION);
        }
        $this->compile($this->_pagevar);
        return $this->_contents;
    }

    /**
     * 显示页面(即输出模板内容)，并中止运行
     */
    public function display() {
        exit($this->fetch());
    }

    /**
     * 错误信息格式化
     * @param array $emsg 错误信息
     * @return array 格式化后的错误信息
     */
    public function errorMessageFormat($emsg){
        $rtn = array();
        foreach($emsg as $k=>$v){
            if(is_array($v)){
                $rtn[$k] = $this->errorMessageFormat($v);
            }else{
                $rtn[$k] = "<span class='err'>$v</span>";
            }
        }

        return $rtn;
    }

    /**
     * 获取多种css
     */
    public static function getMultiCss($value){
        return self::$_css_arr[$value];
    }

    /**
     * 分页
     * @param int $total_record
     * @return string
     */
    protected function create_pager($total_record) {
        if (! $total_record)
            return;
        return NewUtils::pager($total_record, $this->page, $this->limit);
    }

    protected function create_pager_limit() {
        return NewUtils::pagerLimit($this->limit_arr);
    }

    protected function get_order_and_limit() {
        if($this->limit > 0){
            $offset = $this->limit*$this->page;
            $limit = "LIMIT {$offset},{$this->limit}";
        }else{
            $limit = "";
        }
        if($this->order_by){
            if(!$this->sorting){
                $this->sorting = "ASC";
            }
            $order_by = "ORDER BY `{$this->order_by}` {$this->sorting}";
        }else{
            $order_by = "";
        }

        return "$order_by $limit";
    }

    public function get_sorting($field){
        if($field == $this->order_by){
            return 'sorting_' . strtolower($this->sorting);
        }
        return 'sorting';
    }
}
