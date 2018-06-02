<?php
startPage();
$errormsg="";
$sql = "SELECT * FROM announcement  where 1 order by id desc limit 4";
    if ($result = mysql_query($sql)) {
       $errormsg="";
    } else {
		 $errormsg="error reading announcements";
        error_log($sql);
    }
	$last_annoucements = array();
	while($row = mysql_fetch_assoc($result)){
		$last_annoucements[] = array("date"=>$row["date"], "announce"=> $row["details"]);
	}
	
	///when submitting new ec limit value
if(isset($_POST) && isset($_POST['new_announce']) && $_POST['secret'] == "4unf9unufru49fnr91" && $_POST['new_announce']!=""){
	$new_ann_details=$_POST['new_announce'];
	$sql = "INSERT INTO announcement (`id`, `date`, `details`) VALUES (NULL, date(NOW()), '".$new_ann_details."');";
	if ($result = mysql_query($sql)) {
		$sql2 = "SELECT * FROM announcement  where 1 order by id desc limit 4";
		if ($result2 = mysql_query($sql2)) {
		   $errormsg="";
		} else {
			 $errormsg="error reading announcements";
			error_log($sql2);
		}
		$last_annoucements=array();
		while($row2 = mysql_fetch_assoc($result2)){
			$last_annoucements[]=array("date"=>$row2["date"], "announce"=> $row2["details"]);
		}
		$errormsg="New Announcement added successfully ";
    } else {
		$errormsg="error setting the new limit";
        error_log($sql2);
    }
}
?>

<div class="col1">
<?php
for($io=0;$io < sizeof($last_annoucements);$io++){ 
?>
<div style="float:left;width:29%"><strong>Date : </strong><span> <?php echo $last_annoucements[$io]["date"]; ?> </span></div><div style="float:left;width:59%"><?php echo $last_annoucements[$io]["announce"]; ?></div>
<?php
};//end for
?>
</div>
<div class="col2">
 <?php


    ?>
	
    <form method="post" onsubmit="return confirm('Do you want to add the annoucment?');">
        <label >ADD new Announcement
            <span class="mandatory">*</span></label>
        <input type="text" name="new_announce" value="" >
        <input type="hidden" name="secret" value="4unf9unufru49fnr91">
        <button type="submit"  class="ok"><?php echo $_SESSION['main_language']->add_annoucement; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
</div>
<div class="col1" style="color:red;">
<?php echo $errormsg; ?>
</div>
<!------------------------------------------------------------------------------------------------->
<?php
endPage();
?>