<?php
/***test for using github***/
if (isset($_GET) && isset($_GET['p']) && $_GET['p'] == "admin") {
	header("Location: bo/index.php");
}elseif(isset($_GET) && isset($_GET['p']) && $_GET['p'] == "login"){
	header("Location: vo/index.php");
}elseif(isset($_GET) && isset($_GET['p']) && $_GET['p'] == "register"){	

	header("Location: vo/index.php?page=register_first_step");

}else{
	///should return to the main website
	header("Location: landing/index.html");
}
?>
