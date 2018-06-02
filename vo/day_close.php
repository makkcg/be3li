<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log("------------------------------ Starting Day Close Operation ------------------------------");

include "includes/database_manager.php";
include "includes/core.php";

$database_manager = new DatabaseManager();
$core = new Core($database_manager);
if ($_GET['secret'] == "e0d39uN3hb38f4uhR84rhf84rfe9e9dfekcg55") {
	echo "Main secret true<br>";
	
/////Retail Commission calculator and payer
$_GET['secret']="e0d39uN3hb38f4uhR84rhf84rfe9e9dfekcg33";
include "pay_retail_commissions.php";
echo "retail com included<br>";
/////Binary Commission,DCpts,loyality program incentives calculator and payer
//$_GET['secret']="as339uN3hb38f4uhR84rhf84rfe9e9dfekcg31";
//include "Pay_binary_commissions.php";
//echo "binary com included<br>";
}else{
	echo '<div style="color:red;font-size:5em;margin:50px auto;text-align:center;">NOT ALLOWED</div>';
}
error_log("------------------------------ Ending Day Close Operation ------------------------------");
?>
