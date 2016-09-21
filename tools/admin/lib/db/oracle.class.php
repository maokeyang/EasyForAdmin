<?php
class Oracle{

    var $conn;
    var $result;
    function __construct($db_type){
        $config =  Config::get($db_type,'db');
        $this->connect($config['uname'],$config['password'],$config['database'],$config['charset']);
    }

    function connect($username,$password,$database,$charset){
        $this->conn = oci_connect($username,$password,$database,$charset);
        if(!$this->conn){
            $error      = oci_error($conn);
            $message = "错误: ".$error['code']."\r\n<br />";
            $message.= "信息: ".$error['message']."\r\n<br />";
            $message.= "日期: ".date("l dS of F Y h:i:s A")."\r\n<br />";
            $message.= "脚本: ".getenv("REQUEST_URI")."\r\n<br />";
            $message.= "SQL:  ".$sql."\r\n<br />";
            $message.= "附注: ".getenv("HTTP_REFERER")."\r\n<br />";
            echo $message;
            exit;
        }
        return $this->conn;
    }

    function query($sql){
        $conn = $this->conn;
        $result = oci_parse($conn,$sql);
        if(!$result){
            $error      = oci_error($conn);
            $message = "错误: ".$error['code']."\r\n<br />";
            $message.= "信息: ".$error['message']."\r\n<br />";
            $message.= "日期: ".date("l dS of F Y h:i:s A")."\r\n<br />";
            $message.= "脚本: ".getenv("REQUEST_URI")."\r\n<br />";
            $message.= "SQL:  ".$sql."\r\n<br />";
            $message.= "附注: ".getenv("HTTP_REFERER")."\r\n<br />";
            echo $message;
            exit;
        }
        oci_execute($result);
        oci_fetch_all($result,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        return $data;
    }
    function execute_default($sql){
        $conn = $this->conn;
        $stmt = oci_parse($conn,$sql);
        $rs      = oci_execute($stmt,OCI_DEFAULT);
        return $rs;
    }
    function fetch_first($sql)
    {
        return $this->FetchFirst($sql);
    }
  
    function FetchFirst($sql)
    {
        $result = array();

        $query = $this->Query($sql);

        if($query)
        {
            $result = $query->GetRow();
        }

        return $result;
    }
    function ResultFirst($sql)
    {
        $result = '';
       
        $query = $this->Query($sql);
       
        if($query)
        {
            $result = $query->result(0);
        }
       
        return $result;
    }
    function AffectedRows()
    {
        return oci_num_rows($this->conn);
    }
    function CloseConnection()
    {
        return oci_close($this->conn);
    }
    function commint(){
        return oci_commit($this->conn);
    }
    function rollback(){
        return oci_rollback($this->conn);
    }
}