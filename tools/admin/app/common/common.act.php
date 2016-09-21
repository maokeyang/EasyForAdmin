<?php
/**
 * 公用类
 *
 * 模版生成时间：2016-05-03 14:12:41
 */

class Act_Common extends Page {
    public
        $_AuthLevel = ACT_OPEN;
    public function __construct() {
        parent::__construct();
    }

    /**
     * 执行入口
     */
    public function process(){
        if(isset($this->_input['node'])){
            $node = $this->_input['node'];
            die($this->{$node}());
        }
    }


    //------------------------ 公用部分 ----------------------

    public function createHtml($input = array(), $msg=''){
        $html = "<option value=''>".$msg."</option>";
        if(!empty($input)){
            foreach($input as $key=>$val){
                $html .= "<option value=".$key.">".$val."</option>";
            }

        }
        return $html;
    } 
}

