<?php
if($_GET["secret"]=="kcgsetserveron84"){
if ($handle = opendir('../vo')) {
    //while (false !== ($fileName = readdir($handle))) {
		
        $newName = "index-org.php";//str_replace("SKU#","",$fileName);
		$fileName="index.php";
		 if(!readfile("index-org.php")) {
        rename($fileName, $newName);
		///rename maintenance file to index
		$newName1 = "index.php";//str_replace("SKU#","",$fileName);
		$fileName1="index_maintenance.php";
        rename($fileName1, $newName1);
		echo "Server Maintenance Mode is ON NOW";
		}else{
			echo "ERR....Server Maintenance Mode is already ON";
		}
		
  //  }
    closedir($handle);
}
}else{
	echo "no Permission";
}

?>
