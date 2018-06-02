<?php
$core->checkEwalletPassword("fund_transfer");

$html_page->writeHeader();
$html_page->writeBody($_SESSION["language"]->fundtransfer_pagetitle ,$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "Select ewallet FROM ir "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
$result = $database_manager->query($sql);

?>


<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo $_SESSION["language"]->fundtransfer_availablefundinEwallet;?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $row['ewallet']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<br/><br/>


<script>
    function validateForm() {
        var x = document.forms["myform"]["ir_id"].value;
        if (x == '') {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
            return false;
        }
        if (x.split('').length !== 8) {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidIRID;?>";
            return false;
        }
        var y = document.forms["myform"]["amount"].value;
        if (!IsNumeric(y) || !isPositiveInteger(y)) {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidAmount;?>";
            return false;
        }
        if (y == '') {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
            return false;
        }
        var z = document.forms["myform"]["confirm"].value;
        if (z == '') {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
            return false;
        }
        if (y != z) {
            document.getElementById("error").innerHTML =" <?php echo $_SESSION["language"]->fundtransfer_msg_amountmismatch;?>";
            return false;
        }
        //return true;
		return confirm("<?php echo $_SESSION["language"]->fundtransfer_msg_confirmtransfermoney;?>");
    }
	function validateForm1() {
        var x = document.forms["myform1"]["rechargecardnumber"].value;
        if (x == '') {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
            return false;
        }
        if (x.split('').length !== 16) {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidcardnumber;?> ";
            return false;
        }
        var y = document.forms["myform1"]["rechargecardpsw"].value;
        if (!IsNumeric(y) || !isPositiveInteger(y)) {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidcardpsw;?>";
            return false;
        }
        if (y == '') {
            document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
            return false;
        }

        //return true;
		return confirm("<?php echo $_SESSION["language"]->fundtransfer_msg_confirmtransfermoney;?>");
    }
	
    function IsNumeric(input)
    {
        return (input - 0) == input && ('' + input).trim().length > 0;
    }
    function isPositiveInteger(str) {
        var n = ~~Number(str);
        return String(n) === str && n > 0;
    }
    function getIRName() {
        var y = document.forms["myform"]["ir_id"].value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                document.forms["myform"]["ir_name"].value = xhttp.responseText;
            }
        };
        xhttp.open("GET", "index.php?page=get_ir_name&id=" + y, true);
        xhttp.send();
    }
	
	function getrechargecardbalance() {
        var y = document.forms["myform1"]["rechargecardnumber"].value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                document.forms["myform1"]["rechargecard_bal"].value = xhttp.responseText;
            }
        };
        xhttp.open("GET", "index.php?page=get_rechargecardinfo&id=" + y, true);
        xhttp.send();
    }
</script>


<p id="error">
    <?php
	
//************************///if the post is from transfer to IR
    if ($_POST && isset($_POST['ir_id']) && isset($_POST['amount']) && isset($_POST['transfer']) && $_POST['ir_id'] !="" && $_POST['amount'] !="" ) {
		
		$success = false;
		$msg=$core->fundTransfer($database_manager, strtoupper($_POST['ir_id']), $_POST['amount']);
        echo $msg; 
		if($msg==$_SESSION["language"]->fundtransfer_msg_transferedsuccess){
		$success = true;	
		}
		//echo $success;
		if ($success){
			////when transfer is succeeded update the ewallet balance in html
			$sql1 = "Select ewallet FROM ir "
			. " WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
			$result1 = $database_manager->query($sql1);
			$row = mysqli_fetch_assoc($result1);
			//$row['ewallet'];
			?>
			<script>
			document.getElementById("availableecs").innerHTML = "<?php echo $row['ewallet']; ?>";
			</script>
			<?php 
			unset($_POST['transfer']);
		}
    }///end if money transfer
	
//***********************///if the post is from recharge ewallet with card
    if ($_POST && isset($_POST['rechargecardnumber']) && isset($_POST['rechargecardpsw']) && isset($_POST['recharge']) && $_POST['rechargecardpsw'] !="" && $_POST['rechargecardnumber'] !="") {
		$msg="";
		$success = false;
		$cardpsw=$_POST['rechargecardpsw'];
		$cardnumber=$_POST['rechargecardnumber'];
		$ir_id=$_SESSION['ir_id'];
		////check the card password
		$msg=$core->matchcardpsw($cardnumber,$cardpsw,$database_manager,0);
		if($msg<1){
			$success = false;
			$msg=$_SESSION["language"]->fundtransfer_msg_invalidcardpsw;
		}else{
			
			$msg=$core->rechargeewalletwithcard($cardnumber,$cardpsw,$ir_id,$database_manager);
			echo $msg; 
			if($msg == $_SESSION["language"]->returnsuccess){
				//$msg=$_SESSION["language"]->errorinquery;
				$success = true;
				$msg=$_SESSION["language"]->fundtransfer_msg_cardrechargsuccess; 
			}else{
				//echo $msg; 
				$success = false;
				
			}
		}
		
        
		//echo $success;
		if ($success){
			////when transfer is succeeded update the ewallet balance in html
			$sql1 = "Select ewallet FROM ir "
			. " WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
			$result1 = $database_manager->query($sql1);
			$row = mysqli_fetch_assoc($result1);
			//$row['ewallet'];
			?>
			<script>
			document.getElementById("availableecs").innerHTML = "<?php echo $row['ewallet']; ?>";
			</script>
			<?php 
			
			unset($_POST['recharge']);
		}
		echo $msg; 
    }///end if money transfer
    ?>
</p>

<div class="transfertoir_div">
<form method="post" name="myform" onsubmit="return validateForm();" >
	<h3 style="tex-align:center;"><?php echo $_SESSION["language"]->fundtransfer_irtransferH;?></h3>
    <label><?php echo $_SESSION["language"]->irid;?><span class="astrisk"> *</span></label> 
    <input name="ir_id" type="text" autocomplete="off" onchange="getIRName();"/>  <br class="clear"/>
    <label><?php echo $_SESSION["language"]->iridname;?><span class="astrisk"> *</span></label> 
    <input name="ir_name" type="text" value=""  autocomplete="off" readonly /> <br class="clear"/>
    <label><?php echo $_SESSION["language"]->amount." ".$_SESSION["language"]->ecurrency;?><span class="astrisk"> *</span></label> 
    <input name="amount" type="text" autocomplete="off" />  <br class="clear"/>
    <label><?php echo $_SESSION["language"]->confirmamount." ".$_SESSION["language"]->ecurrency;?><span class="astrisk"> *</span></label> 
    <input name="confirm" type="text" autocomplete="off" />  <br class="clear"/>
	<input name="transfer" type="hidden" value="1" />  <br class="clear"/>

    <div class="sep"></div>

    <button type="submit"><i class="fa fa-check-square fa-fw"></i><?php echo $_SESSION["language"]->fundtransfer_transfer;?></button>
</form>
</div>

<div class="transfertoir_div">
<div class="payment_icon" style="text-align: center;"><i class="fa fa-credit-card fa-fw"></i></div>
<form method="post" name="myform1" onsubmit="return validateForm1();" >
	<h3 style="tex-align:center;"><?php echo $_SESSION["language"]->fundtransfer_rechargeewalletH;?></h3>
    <label><?php echo $_SESSION["language"]->virtualcard;?><span class="astrisk"> *</span></label> 
    <input name="rechargecardnumber" type="text" autocomplete="off" onchange="getrechargecardbalance();"/>  <br class="clear"/>
    <label><?php echo $_SESSION["language"]->cardbalanceinfo;?><span class="astrisk"> *</span></label> 
    <input name="rechargecard_bal" type="text" value=""  autocomplete="off" readonly /> <br class="clear"/>
	<label><?php echo $_SESSION["language"]->virtualcard." ".$_SESSION["language"]->password;?><span class="astrisk"> *</span></label> 
    <input name="rechargecardpsw" type="text" autocomplete="off" />  <br class="clear"/>
    <!--<label><?php// echo $_SESSION["language"]->amount." ".$_SESSION["language"]->ecurrency;?><span class="astrisk"> *</span></label> 
    <input name="amount" type="text" autocomplete="off" />  <br class="clear"/>
    <label><?php //echo $_SESSION["language"]->confirmamount." ".$_SESSION["language"]->ecurrency;?><span class="astrisk"> *</span></label> 
    <input name="confirmcharge" type="text" autocomplete="off" />  <br class="clear"/>-->
	<input name="recharge" type="hidden" value="1" />  <br class="clear"/>
    <div class="sep"></div>
    <button type="submit"><i class="fa fa-check-square fa-fw"></i><?php echo $_SESSION["language"]->fundtransfer_recharge;?></button>
</form>
</div>

<div class="sep dotted"></div>


<?php $html_page->writeFooter(); ?>