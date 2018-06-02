<?php
$sql = "SELECT irid,cardval from scrachcards where cardnumber = '" . $_GET['id'] . "';"; 
$result = $database_manager->query($sql);
if ($row = mysqli_fetch_assoc($result)){
	$cardowner=" Company ";
	if($row['irid']!=""){
		$cardowner=$row['irid'];
	}
    echo $_SESSION["language"]->fundtransfer_msg_cardowner.$row['irid']." - ".$_SESSION["language"]->balance." is: ".$row['cardval'];
}else {
    echo $_SESSION["language"]->fundtransfer_msg_invalidcardnumber;
}


?>