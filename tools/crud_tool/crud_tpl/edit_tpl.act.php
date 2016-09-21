%%?php
/**
* <?php echo $describe;?>编辑

* @author <?php echo $author;?>

* 模版生成时间：<?php echo date('Y-m-d H:i:s');?>

*/

class Act_<?php echo $class_name;?>_Edit extends Page {

    const TABLE_NAME = "<?php echo $table;?>";
    const TABLE_KEY_FIELD = "<?php echo $key_field;?>";
    const GO_BACK = "?mod=<?php echo $mod;?>&act=<?php echo $file_name;?>";

    public function __construct() {
        parent::__construct();
        $this->db = Db::getMasterInstance(ADMIN);
<?php if(isset($type_arr_key)){
    foreach($type_arr_key as $k => $v){
?>
        $this-><?php echo $k;?>_arr = array(
<?php foreach($v as $key => $val){ ?>
            "<?php echo $val;?>" => "<?php echo $type_arr_value[$k][$key];?>",
<?php } ?>
        );
<?php }}?>
    }

    /**
     * 执行入口
     */
    public function process (){
        if(isset($this->_input['submit'])){ 
            $this->show_validate();    
            $data = $this->_input['data'];    
<?php if(isset($from_unix_time)){
    foreach($from_unix_time as $k=>$v){
        if($input[$k]=='text'){
?>

            $data['<?php echo $k;?>'] = !empty($data['<?php echo $k;?>'])?strtotime($data['<?php echo $k;?>']):strtotime('now');
<?php }}}?>

            if(isset($this->_input['do']) && 'update' == $this->_input['do']){
                $rt = $this->update_record($data);
            }else{
                $rt = $this->add_record($data);
            }
            if($rt){
                $msg = '操作成功';
            }else{
                $msg = '操作失败';
            }
            $this->alert($msg,'href',self::GO_BACK);

        }else{
            $data = array();
            if(isset($this->_input['do']) && 'edit' == $this->_input['do']){
                $data = $this->get_data($this->_input[self::TABLE_KEY_FIELD]);
                $this->assign('title', '编辑<?php echo $describe;?>');
                $this->assign('do', Form::hidden('do','update'));
                $this->assign('data', $data);
            }else{
                $this->assign('title', '添加<?php echo $describe;?>');
            }
<?php if(isset($type_arr_key)){
    foreach($type_arr_key as $k => $v){
?>
                $this-><?php echo $k?>_select = isset($data['<?php echo $k?>'])?$data['<?php echo $k?>']:(isset($this->_input['kw']['<?php echo $k;?>'])?$this->_input['kw']['<?php echo $k;?>']:null);
                $this->assign('input_<?php echo $k;?>_arr', Form::select('data[<?php echo $k;?>]',$this-><?php echo $k;?>_arr,$this-><?php echo $k?>_select));

<?php }}?>
        }

        $this->display();
    }

    /**
     * 获取数据
     */
    private function get_data($key_value){
        if(!$key_value){
            return array();
        }
        return $this->db->get_row("SELECT * FROM `".self::TABLE_NAME."` where ".self::TABLE_KEY_FIELD." = '{$key_value}' ");    
    }

    /**
     * 添加数据
     */
    private function add_record($data){
        unset($data[self::TABLE_KEY_FIELD]);
        return $this->db->insert(self::TABLE_NAME,$data);
    }

    /**
     * 编辑数据
     */
    private function update_record($data){
        $where = array();
        if(isset($data[self::TABLE_KEY_FIELD])){
            $where[self::TABLE_KEY_FIELD] = $data[self::TABLE_KEY_FIELD];
            unset($data[self::TABLE_KEY_FIELD]);
        }else{
            return false;
        }
        return $this->db->update(self::TABLE_NAME,$data,$where);
    }

    /**
     * 检查提交数据的有效性
     * @param array $items
     * @return array
     */
    private function validate($items){
        $emsg = array();
<?php foreach($input as $k=>$v){
    //非空
    if($v!='noinput'&&isset($nullable[$k])){?>
        <?php if($k==$autoid_field){?>

        if('update' == $this->_input['do']){
            if (!$items['<?php echo $k?>'])        $emsg['<?php echo $k?>'] = '<?php echo $Comment[$k]?>不能为空';
        }
        <?php }else{?>  
        if (!$items['<?php echo $k?>'])        $emsg['<?php echo $k?>'] = '<?php echo $Comment[$k]?>不能为空';
        <?php }}}?>        
        return $this->errorMessageFormat($emsg);
    }

    /**
     * 检查提交数据的有效性
     */
    private function show_validate(){
        $emsg = $this->validate($this->_input['data']);  
        if($emsg){
            $this->assign('title', $this->_input['title']);
<?php if(isset($from_unix_time)){
    foreach($from_unix_time as $k=>$v){
        if(isset($Field[$k]) && $Field[$k]){
?>

            $this->_input['data']['begin_time'] = strtotime($this->_input['data']['begin_time']);
            $this->_input['data']['end_time'] = strtotime($this->_input['data']['end_time']);
            <?php }}}?>

            $this->assign('emsg', $emsg);
            $this->assign('data', $this->_input['data']);
            if(isset($this->_input['do'])){
                $this->assign('do', Form::hidden('do',$this->_input['do']));
            } 
            $this->display();
            exit();
        }
    }

}

