<?php
header('Content-Type: text/html; charset=utf-8');
    session_start();
     echo "<h3> Language Session Variables</h3><br/>";
	var_dump($_SESSION["language"]) ;
   echo "<br/><h3> PHP List All Session Variables</h3>";   /*var_dump($_SESSION)*/   echo "order id ".$_SESSION["order_id"]."<br><br>";
   foreach ($_SESSION as $key=>$val){	    echo $key." ".$val."<br/>";   }
?>