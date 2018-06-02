<?php
error_reporting(E_ALL); ini_set('display_errors', 1);  
$cookie_name="lang";
if(isset($_GET["lang"]) && $_GET["langchange"]==1){
	if( $_GET["lang"]== "ar"){
		$cookie_value = "ar";
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
		//$lang = new Arabic_language;
		include_once "languages/ar.php";
	}else{
		$cookie_value = "en";
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
		//$lang = new English_language;
		include_once "languages/en.php";
	}
}
if(!isset($_COOKIE["lang"])) {
///language is not set in cookies
				///setup selected language
	if( $_GET["lang"]== "ar"){
		$cookie_value = "ar";
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
		//$lang = new Arabic_language;
		include_once "languages/ar.php";
	}else{
		$cookie_value = "en";
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
		//$lang = new English_language;
		include_once "languages/en.php";
	}
} else{
	$_GET['lang']=$_COOKIE["lang"];
	if($_COOKIE["lang"]=="ar"){
		//$lang = new Arabic_language;
		include_once "languages/ar.php";
	}else{
		//$lang = new English_language;
		include_once "languages/en.php";
	}
}
session_start();


header('Content-Type: text/html; charset=utf-8');
include "includes/database_manager.php";
include "includes/core.php";
include "includes/html.php";


$database_manager = new DatabaseManager();
$html_page = new HTMLPage();
$core = new Core($database_manager);
//$curlang=$_GET["lang"];

			
/*******************************************************************/
if (date("H") == 23 && $_GET['error'] != "freeze"  && false) {
    header("Location: index.php?page=login&error=freeze");
} else {

    if (isset($_POST) && isset($_POST['page']) && $_POST['page'] == "check") {
        include "user/check.php";
			
    } elseif (isset($_GET) && isset($_GET['page']) && $_GET['page'] == "get_ir_name") {
        include "modules/" . $_GET['page'] . ".php";
	} elseif (isset($_GET) && isset($_GET['page']) && $_GET['page'] == "get_rechargecardinfo") {
        include "modules/" . $_GET['page'] . ".php";
    
    } elseif (isset($_GET) && ( $core->stringContains($_GET['page'], "forgot_") || $core->stringContains($_GET['page'], "register_") || $core->stringContains($_GET['page'], "test_") )) {
        include "user/" . $_GET['page'] . ".php";
    } elseif ((!isset($_SESSION['secret']) || $_SESSION['secret'] != "e0d39uN3hb38f4uhR84rhf84rfe9e9dfedfc33" ) ||
            ( isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > ( 60 * 60 ) ) ) ||
            isset($_GET) && $_GET['page'] == "login") {
		
        include "user/login.php";

    } else {
		
        $_SESSION['last_activity'] = time();
        if ($_GET['page'] == "my_account" || $_GET['page'] == "change_password") {
			//$_SESSION['lang']=$lang;
			//$_GET['lang']=$langprefix;
            include "user/" . $_GET['page'] . ".php";
        } else {
            if (file_exists("modules/" . $_GET['page'] . ".php")) {
			//	$_SESSION['lang']=$lang;
				//$_GET['lang']=$langprefix;
                include "modules/" . $_GET['page'] . ".php";
            } else {
			//	$_SESSION['lang']=$lang;
				//$_GET['lang']=$langprefix;
                include "modules/dashboard.php";
            }
        }
    }
}
?>