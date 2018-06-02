<?php
startPage();
$errormsg="";
$sql = "select bal_ec,bal_le from k8_acc_wallet_trns  where code='acc01' order by id desc limit 1";
    if ($result = mysql_query($sql)) {
       $errormsg="";
    } else {
		 $errormsg="error reading current balance 1";
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
	$curr_bal_ec=$row['bal_ec'];
	$curr_bal_le=$row['bal_le'];
	
	///when submitting new ec limit value
if(isset($_POST) && isset($_POST['new_balance']) && $_POST['secret'] == "4unf9unufru49fnr9" && $_POST['new_balance']!=""){
	$new_ec_limit=$_POST['new_balance'];
	$new_le_limit=$new_ec_limit*7;
	///get the difference between current balance and new required balance
	$diff_ec=$new_ec_limit-$curr_bal_ec;
	$diff_le=($diff_ec*7);
	if($diff_ec>0){
		$trns_type=1;///adding money to acc account
		$comment="Adding ".$diff_ec." EC to the current Acc balane";
	}else{
		$trns_type=2;///substracting money from acc account
		$comment="Reducing ".(-1*$diff_ec)." EC from the current Acc balane";
	}
	$sql = "INSERT INTO `k8_acc_wallet_trns` (`id`, `code`, `datetime`, `val_ec`, `val_le`, `trns_type`, `tofrom`, `bal_ec`, `bal_le`, `comment`) VALUES (NULL, 'acc01', NOW(), ".$diff_ec.", ".$diff_le.",".$trns_type.", 'admin', ".$new_ec_limit.", ".$new_le_limit.", 'new EC limit Set by Admin for  accounting 01 - ".$comment."');";
	if ($result = mysql_query($sql)) {
        //if query is executed correctly, reload the current balance value
		$sql = "select bal_ec,bal_le from k8_acc_wallet_trns  where code='acc01' order by id desc limit 1";
		if ($result = mysql_query($sql)) {
			$errormsg="";
		} else {
			$errormsg="error reading current balance after update limit";
			error_log($sql);
		}
		$row = mysql_fetch_assoc($result);
		$curr_bal_ec=$row['bal_ec'];
		$curr_bal_le=$row['bal_le'];
		$errormsg="New EC limit for Accountant 01 is set successfully ";
    } else {
		$errormsg="error setting the new limit";
        error_log($sql);
    }
}
?>

<div class="col1">
<div style="float:left;width:49%">current accountant EC balance : </div><div style="float:left;width:49%"><?php echo $curr_bal_ec." EC"; ?></div>
<div style="float:left;width:49%">current accountant LE balance : </div><div style="float:left;width:49%"><?php echo $curr_bal_le." LE"; ?></div>
</div>
<div class="col2">
 <?php


    ?>
	
    <form method="post" onsubmit="return confirm('Do you want to submit the form?');">
        <label >Set New Balance (EC LIMIT)
            <span class="mandatory">*</span></label>
        <input type="text" name="new_balance" value="<?php echo $curr_bal_ec; ?>" >
        <input type="hidden" name="secret" value="4unf9unufru49fnr9">
        <button type="submit"  class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
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