<?php

$my_id = strtoupper($_POST['id']);
$my_password = $_POST['password'];

$my_id = stripslashes($my_id);
$my_password = stripslashes($my_password);
$my_id = $database_manager->realEscapeString($my_id);
$my_password = $database_manager->realEscapeString($my_password);

$sql = "SELECT login_pass, title, f_name, l_name, last_renewal_date FROM ir WHERE ir_id='$my_id'";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);
///get the company name

$sql2 = "SELECT name,name_tr FROM k8_top_organization WHERE 1";
$result2 = $database_manager->query($sql2);
$row2 = mysqli_fetch_assoc($result2);

$my_password = crypt($my_password, $row['login_pass']);
$last_renewal_date = $row['last_renewal_date'];
$next_renewal_date = $core->addToDate($last_renewal_date, 365);

if (strtotime($core->addToDate($next_renewal_date, 30)) < strtotime($core->getFormatedDate())) {
    header("location:index.php?page=login&error=account_deactivated");
    exit;
}

if ($my_password == $row['login_pass']) {
    $_SESSION['last_activity'] = time();
    $_SESSION['secret'] = "e0d39uN3hb38f4uhR84rhf84rfe9e9dfedfc33";
    $_SESSION["ir_id"] = $my_id;
    $_SESSION["full_name"] = $row['title'] . " " . $row['f_name'] . " " . $row['l_name'];
	$_SESSION['company']=$row2['name'].' ('.$row2['name_tr'].')';
	/*****check if the user is active****/	 $sql = "SELECT isactivated FROM ir WHERE ir_id='$my_id'";    $result = $database_manager->query($sql);    $row = mysqli_fetch_assoc($result);    $_SESSION['isactivated'] = $row['isactivated'];		/****************************************/
    $sql = "SELECT is_qualified FROM bu WHERE ir_id='$my_id'";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $_SESSION['access'] = $row['is_qualified'];
	
    if (strtotime($next_renewal_date) < strtotime($core->getFormatedDate())) {
        $_SESSION['access'] = 0;
    }

	//$lang=$_COOKIE['lang']
	//$_GET['lang']=$_COOKIE["lang"];
	if($_COOKIE["lang"]=="ar"){
		
		
		 $_SESSION['language'] = new Arabic_language;
	}else{
		
		  $_SESSION['language'] = new English_language;
	}
	//$_SESSION['language']=serialize($lang);
	
    header("location:index.php?page=dashboard&lang=".$_COOKIE["lang"]);
	
} else {
    header("location:index.php?page=login&error=wrong_pass");
}

?>