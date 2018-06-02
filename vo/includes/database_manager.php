<?php
class DatabaseManager {

    private $db_conection;
    private $database_name = "be3ly";
    private $database_user = "be3ly";
    private $database_pass = "Be3L!DbCloud@2017";

    function __construct() {
        $this->db_conection = mysqli_connect("localhost", $this->database_user, $this->database_pass, $this->database_name);
        if (mysqli_connect_errno()) {
            error_log("Failed to connect to MySQL: " . mysqli_connect_error());
        }
        $this->setNamesUtf8();
    }

    private function setNamesUtf8() {
        $sql = "SET NAMES 'utf8'";
        $this->query($sql);
    }
    
    function getMaxIrCode(){
        $sql = "SELECT MAX(code) AS max_ir_code FROM ir";
        $result = $this->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['max_ir_code'];
    }
    
    function query($sql) {
        $result = mysqli_query($this->db_conection, $sql);
		/*if(isset($_GET['page']) || $_GET['page']==""){$_GET['page']="nopage";};*/
        if ($result == false) {
            error_log("Failed: " . $_GET['page'] . " => " . $sql);
        }else {
            error_log("Success: " . $_GET['page'] . " => " . $sql);
        }
        return $result;
    }
    
    function realEscapeString($string){
        return mysqli_real_escape_string($this->db_conection, $string);
    }
    
    function getLastGeneratedId(){
        return mysqli_insert_id($this->db_conection);
    }
    
}
