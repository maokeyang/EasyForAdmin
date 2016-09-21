%%?php
/**
* <?php echo $describe;?>管理

* @author <?php echo $author;?>

* 模版生成时间：<?php echo date('Y-m-d H:i:s');?>

*/
<?php 
$class_name=explode('_',$file_name);
foreach($class_name as &$v){
    $v=ucfirst($v);
}
$class_name=implode('_',$class_name);

?>

class Act_<?php echo $class_name;?> extends Page {

    const TABLE_NAME = "<?php echo $table;?>";
    const TABLE_KEY_FIELD = "<?php echo $key_field;?>";

<?php foreach($name_table as $k=>$v){?>
<?php if($name_table[$k]){?>
	public $<?php echo $name_table[$k]?>_getter;
<?php }}?>

    public function __construct() {
        parent::__construct();
        $this->db_slave = Db::getMasterInstance(ADMIN);
<?php foreach($name_table as $k=>$v){?>
<?php if($name_table[$k]){?>
	    $this-><?php echo $name_table[$k]?>_getter = NameGetter::getInstance(CConstant::DB_MAIN,'<?php echo $name_table[$k]?>');
<?php }}?>
<?php if(isset($type_arr_key)){
    foreach($type_arr_key as $k => $v){
?>
        $this-><?php echo $k;?>_arr = array(
            "-999" => "--&nbsp;&nbsp;全部&nbsp;&nbsp;--",
<?php foreach($v as $key => $val){ ?>
            "<?php echo $val;?>" => "<?php echo $type_arr_value[$k][$key];?>",
<?php } ?>
        );
<?php }
if(isset($Field[$k])){ ?>
        $this-><?php echo $k?>_select = isset($this->_input['kw']['<?php echo $k;?>'])?$this->_input['kw']['<?php echo $k;?>']:null;
        $this->assign('input_<?php echo $k;?>_arr', Form::select('kw[<?php echo $k;?>]',$this-><?php echo $k;?>_arr,$this-><?php echo $k?>_select));

<?php }}?>

    }

    /**
     * 执行入口
     */
    public function process (){
        $this->assign('title', '<?php echo $describe;?>');
        $this->assign('data', $this->get_data());
        $total_record = $this->get_total_num();
        $this->assign('total_record', $total_record);
        $this->assign('page', $this->create_pager($total_record));
        $this->assign('pager_limit', $this->create_pager_limit());
        $this->assign('limit', $this->limit);
        $this->display();
    }

    /**
     * 获取查询条件语句
     */
    private function condition() {
        $where = array();   
        <?php if(isset($Field)){?>
        <?php foreach($Field as $k=>$v){?>
            <?php if(isset($is_time[$k])){?>

        if(isset($this->_input['kw']['start_time']) && $this->_input['kw']['start_time']){
            $start_time = strtotime($this->_input['kw']['start_time']);
        }else{
            $start_time = strtotime('-1 month');
            $this->_input['kw']['start_time'] = date('Y-m-d', $start_time);
        }

        if(isset($this->_input['kw']['end_time']) && $this->_input['kw']['end_time']){
            $end_time = strtotime($this->_input['kw']['end_time'].' 23:59:59');
        }else{
            $end_time = strtotime(date('Y-m-d 23:59:59'));
            $this->_input['kw']['end_time'] = date('Y-m-d');
        }    
        $where[] = "<?php echo $v?> >= '".$start_time."'";
        $where[] = "<?php echo $v?> <= '".$end_time."'";
<?php }else{
    if(isset($type_arr_key[$k])){
?>

        if(isset($this->_input['kw']['<?php echo $v?>']) && $this->_input['kw']['<?php echo $v?>'] !='-999'){
            $where[] = "<?php echo $v?> = '{$this->_input['kw']['<?php echo $v?>']}'";
        }
<?php }else{ 
?>
        if(isset($this->_input['kw']['<?php echo $v?>']) && $this->_input['kw']['<?php echo $v?>']){
            $where[] = "<?php echo $v?> = '{$this->_input['kw']['<?php echo $v?>']}'";
        }<?php }}?>
        <?php }?>
        <?php }?>

        <?php if(isset($Like_Field)){?>
        <?php foreach($Like_Field as $k=>$v){?>

        if(isset($this->_input['kw']['<?php echo $v?>']) && $this->_input['kw']['<?php echo $v?>']){
            $where[] = "<?php echo $v?> like '{$this->_input['kw']['<?php echo $v?>']}%'";
        }
        <?php }?>
        <?php }?>

        if(!$where) return '';
        return  " WHERE ".implode(' AND ', $where);
    }

    /**
     * 获取数据列表
     */
    public function get_data(){
        $where = $this->condition();
        $order_and_limit = $this->get_order_and_limit();
        $this->sql = "SELECT COUNT(*) FROM `".self::TABLE_NAME."` {$where}";
        return $this->db_slave->get_all("SELECT * FROM `".self::TABLE_NAME."` {$where} {$order_and_limit}");
    }

    /**
     * 获取数据总条数
     */
    public function get_total_num(){
        return $this->db_slave->get_one($this->sql);
    }

}
