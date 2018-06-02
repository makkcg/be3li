<?php
if($_GET["secret"]=="kcgsetserveroff84"){
if ($handle = opendir('../vo')) {
 
		///rename back index to maintenance file 
		$fileName1= "index.php";//str_replace("SKU#","",$fileName);
		$newName1="index_maintenance.php";
		   if(!readfile("index_maintenance.php")) {
		rename($fileName1, $newName1);
		   
		///rename original file back
		$fileName= "index-org.php";//str_replace("SKU#","",$fileName);
		$newName="index.php";
        rename($fileName, $newName);
		echo "Server Maintenance Mode is OFF NOW";
		}else{
			echo "ERR....Server Maintenance Mode is already off";
		}
        
		
  //  }
    closedir($handle);
}
}else{
	echo "no Permission";
}

?>
