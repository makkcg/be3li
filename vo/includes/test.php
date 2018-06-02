<?php
include 'core.php';
include 'database_manager.php';
$database_manager = new DatabaseManager();
$core = new Core($database_manager);
$var=$_GET["dd"];
print_r(is_null($var) || $var=="" || $var==0);
?>