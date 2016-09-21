%%?php
/**
* <?php echo $describe;?>删除

* @author <?php echo $author;?>

* 模版生成时间：<?php echo date('Y-m-d H:i:s');?>

*/

class Act_<?php echo $class_name;?>_DELETE extends Page {

    const GO_BACK = "?mod=<?php echo $mod;?>&act=<?php echo $file_name;?>";

    public function __construct() {
        parent::__construct();
        $this->db_master =  Db::getMasterInstance(ADMIN);;
    }

    /**
     * 执行入口
     */
    public function process (){
        if(!isset($this->_input['<?php echo $key_field;?>']) || !is_numeric($this->_input['<?php echo $key_field;?>'])){
            throw new Exception('参数错误!');
            exit();
        }

        $rt = $this->db_master->query("DELETE FROM `<?php echo $table;?>` WHERE <?php echo $key_field;?>='{$this->_input['<?php echo $key_field;?>']}'");
        if($rt){
            $msg = '操作成功';
        }else{
            $msg = '操作失败';
        }
        $this->alert($msg,'href',self::GO_BACK);
    }

}

