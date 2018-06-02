<?php
/////get the last balance of the IR with proshops accountant, this is accessible through ajax with get call_user_func
if(isset($_POST['selectedIR']) && $_POST['selectedIR'] !=""){
	$selectedIR=$_POST['selectedIR'];
	$IR_data=array();
	$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$selectedIR."' order by id desc limit 1";
    if ($result = mysql_query($sql)) {
      // $errormsg="";
	$row = mysql_fetch_assoc($result);
	//$curr_acc_bal_ec=$row['bal_ec'];
	//$curr_acc_bal_le=$row['bal_le'];
	//$curr_acc_comment=$row['comment'];
    $IR_data[]=array("ir"=> $selectedIR,"bal_ec"=> $row['bal_ec'], "bal_le"=> $row['bal_le'], "comment"=> $row['comment']);
	echo json_encode($IR_data);
	die();
	} else {
		 $errormsg="error reading current balance 1";
        error_log($sql);
	die();
    }
	die();
}
/////////////////////////////////////
startPage();
$errormsg="";
$accid="acc01";


//////////////////////////////////////////////////
///get the accountant last transaction and balance
$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$accid."' order by id desc limit 1";
    if ($result = mysql_query($sql)) {
       $errormsg="";
	$row = mysql_fetch_assoc($result);
	$curr_acc_bal_ec=$row['bal_ec'];
	$curr_acc_bal_le=$row['bal_le'];
	$curr_acc_comment=$row['comment'];
    } else {
		 $errormsg="error reading current balance 1";
        error_log($sql);
    }

///get the Managers IRs data
	$managers_irs=array("PA0102","PA0103","PA1431","PA0514");
	$managers_names=array();
	for($ii=0;$ii< sizeof($managers_irs);$ii++){
		$sql2= "select ir_id, f_name, l_name, a_name,ewallet from ir where ir_id='".$managers_irs[$ii]."'";
		if ($result2 = mysql_query($sql2)) {
			$row2 = mysql_fetch_assoc($result2);
			$errormsg="";
			$fullname_e=$row2["f_name"]." ".$row2["l_name"];
			$managers_names[]=array("ir_id"=> $row2["ir_id"], "fullname_e" => $fullname_e,"fullname_a" => $row2["a_name"],"ewallet" => $row2["ewallet"] );
		} else {
			$errormsg="error receiving ir data";
			error_log($sql2);
		}
	
	}//end for


	
///////check if submission vars are set then proceed with transfer
if(isset($_POST) && isset($_POST['ir_id']) && isset($_POST['payment_val']) && isset($_POST['payment_method']) && $_POST['secret'] == "4unf9unufru49fnr9" && $_POST['payment_val']!="" && $_POST['ir_id']!="" && $_POST['payment_method']!=""){
		//set variables based on post
		$errormsg="";
		$irid=$_POST['ir_id'];
		$payment_method=$_POST['payment_method'];
		$paymentVal=$_POST['payment_val'];
		$ir_name="";
		//$accid=""acc01;
		
		if($payment_method=="cash" || $payment_method=="bank"){
		
			///reduce the credit value on the k8 transaction tble for the selected IR
			
			/////2///add transaction to k8_acc_wallet_trns for the target ir, add value to his balance

			///2-1/// get the current balance of the selected ir
			$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$irid."' order by id desc limit 1";
			if ($result = mysql_query($sql)) {
			   //$errormsg="";
			} else {
				 $errormsg="error reading ir balance - select bal_ec,bal_le,comment from k8_acc_wallet_trns from ir ";
				error_log($sql);
			}
			$row = mysql_fetch_assoc($result);
			$curr_ir_bal_ec=$row['bal_ec'];
			$curr_ir_bal_le=$row['bal_le'];
			$curr_ir_comment=$row['comment'];
			
			//2-2/// add the transaction,of transferring ecs to the ir in k8_acc_wallet_trns table
			$new_ir_bal_le = $curr_ir_bal_le + $paymentVal;
			$new_ir_bal_ec = ($new_ir_bal_le/7);
			///get the difference between current balance and new required balance
			$diff_ir_ec=$new_ir_bal_ec-$curr_ir_bal_ec;
			$diff_ir_le=($diff_ir_ec*7);
			
			if($diff_ir_ec>=0){
				$trns_type=1;///adding money to balance
				if($payment_method=="cash"){
				$comment="Paying Cash (".$diff_ir_le." LE) ".$diff_ir_ec." EC  to the Accountant balane From IR: ".$irid;
				}else if($payment_method=="bank"){
				$comment="Paying By Bank transfer/deposit (".$diff_ir_le." LE) ".$diff_ir_ec." EC  to the Accountant balane From IR: ".$irid;
				}
			}else{
				$trns_type=2;///substracting money from balance
				$comment="Receiving Credit of ".($diff_ir_ec)." EC from the Accountant balane to IR: ".$irid;
			}
			$sql = "INSERT INTO `k8_acc_wallet_trns` (`id`, `code`, `datetime`, `val_ec`, `val_le`, `trns_type`, `tofrom`, `bal_ec`, `bal_le`, `comment`)
			VALUES (NULL, '".$irid."', NOW(), ".$diff_ir_ec.", ".$diff_ir_le.",".$trns_type.", '".$accid."', ".$new_ir_bal_ec.", ".$new_ir_bal_le.", 'IR New Balance due to - ".$comment."');";
			if ($result = mysql_query($sql)) {
				//if query is executed correctly, reload the current balance value
				$sql01 = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$irid."' order by id desc limit 1";
				if ($result01 = mysql_query($sql01)) {
					//	$errormsg="";
					$row01 = mysql_fetch_assoc($result01);
					$curr_ir_bal_ec=$row01['bal_ec'];
					$curr_ir_bal_le=$row01['bal_le'];
				} else {
					$errormsg="error reading current balance of IR after transfer from accounting balance";
					error_log($sql);
				}
				
				//$errormsg="Transfer succeeded";
			} else {
				$errormsg="error in - INSERT INTO `k8_acc_wallet_trns` of IR";
				error_log($sql);
			}
			
		}else if($payment_method=="ECs"){
			///get the latest ewallet balance of the selected IR
			$sql2= "select ir_id,ewallet from ir where ir_id='".$irid."'";
			$result2 = mysql_query($sql2);
			$row2 = mysql_fetch_assoc($result2);
			$ir_ewallet=$row2["ewallet"];
			
		//$irid=$_POST['ir_id'];
		//$payment_method=$_POST['payment_method'];
		//$paymentVal=$_POST['payment_val'];

			
			///check if the IR have enough ECs in his wallet
			if($paymentVal < $ir_ewallet){
				
			////1///add transaction to k8_acc_wallet_trns for acc01, add value from IR ewallet balance
			$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='acc01' order by id desc limit 1";
			if ($result = mysql_query($sql)) {
			   //$errormsg="";
			} else {
				 $errormsg="error reading acc balance - select bal_ec,bal_le,comment from k8_acc_wallet_trns from acc01 ";
				error_log($sql);
			}
			$row = mysql_fetch_assoc($result);
			$curr_acc_bal_ec=$row['bal_ec'];
			$curr_acc_bal_le=$row['bal_le'];
			$curr_acc_comment=$row['comment'];
			
			$new_acc_bal_ec= $curr_acc_bal_ec+$paymentVal;
			$new_acc_bal_le= ($new_acc_bal_ec*7);
			///get the difference between current balance and new required balance
			$diff_acc_ec=$new_acc_bal_ec-$curr_acc_bal_ec;
			$diff_acc_le=($diff_acc_ec*7);
			
			if($diff_acc_ec>0){
				$trns_type=1;///adding money to balance
				$comment="IR ".$irid." Payback, Adding ".$diff_acc_ec." EC to the Accountant balane - Note: IR: ".$irid;
			}else{
				$trns_type=2;///substracting money from balance
				$comment="Transferring ".(-1*$diff_acc_ec)." EC from the current Acc balane to IR: ".$irid;
			}
			$sql = "INSERT INTO `k8_acc_wallet_trns` (`id`, `code`, `datetime`, `val_ec`, `val_le`, `trns_type`, `tofrom`, `bal_ec`, `bal_le`, `comment`) 
			VALUES (NULL, 'acc01', NOW(), ".$diff_acc_ec.", ".$diff_acc_le.",".$trns_type.", '".$irid."', ".$new_acc_bal_ec.", ".$new_acc_bal_le.", 'ACC01 New Balance due to - ".$comment."');";
			if ($result = mysql_query($sql)) {
				//if query is executed correctly, reload the current balance value
				$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='acc01' order by id desc limit 1";
				if ($result = mysql_query($sql)) {
					$errormsg="";
				} else {
					$errormsg="error reading current balance after transfer from accounting balance";
					error_log($sql);
				}
				$row = mysql_fetch_assoc($result);
				$curr_acc_bal_ec=$row['bal_ec'];
				$curr_acc_bal_le=$row['bal_le'];
				$curr_acc_comment=$row['comment'];
				//$errormsg="Transfer succeeded";
			} else {
				$errormsg="error in transfer - INSERT INTO `k8_acc_wallet_trns` acc01";
				error_log($sql);
			}
			
			/////update IR K8 acc wallet transactions , 
			$sql100 = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$irid."' order by id desc limit 1";
			if ($result100 = mysql_query($sql100)) {
			   //$errormsg="";
			} else {
				 $errormsg="error reading ir balance - select bal_ec,bal_le,comment from k8_acc_wallet_trns from irid ";
				error_log($sql100);
			}
			$row100 = mysql_fetch_assoc($result100);
			$curr_ir_bal_ec=$row100['bal_ec'];
			$curr_ir_bal_le=$row100['bal_le'];
			$curr_ir_comment=$row100['comment'];
			
			$new_ir_bal_ec= $curr_ir_bal_ec+$paymentVal;
			$new_ir_bal_le= ($new_ir_bal_ec*7);
			///get the difference between current balance and new required balance
			$diff_ir_ec=$new_ir_bal_ec-$curr_ir_bal_ec;
			$diff_ir_le=($diff_ir_ec*7);
			
			if($diff_ir_ec>0){
				$trns_type=1;///adding money to balance
				$comment="IR ".$irid." Payback, Adding ".$diff_ir_ec." EC to the Accountant balane - Note: IR: ".$irid;
			}else{
				$trns_type=2;///substracting money from balance
				$comment="Transferring ".(-1*$diff_acc_ec)." EC from the current Acc balane to IR: ".$irid;
			}
			$sql = "INSERT INTO `k8_acc_wallet_trns` (`id`, `code`, `datetime`, `val_ec`, `val_le`, `trns_type`, `tofrom`, `bal_ec`, `bal_le`, `comment`) 
			VALUES (NULL, '".$irid."', NOW(), ".$diff_ir_ec.", ".$diff_ir_le.",".$trns_type.", 'acc01', ".$new_ir_bal_ec.", ".$new_ir_bal_le.", 'IR New Balance due to - ".$comment."');";
			if ($result = mysql_query($sql)) {
				//if query is executed correctly, reload the current balance value
				$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$irid."' order by id desc limit 1";
				if ($result = mysql_query($sql)) {
					$errormsg="";
				} else {
					$errormsg="error reading current balance after transfer to accounting balance";
					error_log($sql);
				}
				$row = mysql_fetch_assoc($result);
				$curr_ir_bal_ec=$row['bal_ec'];
				$curr_ir_bal_le=$row['bal_le'];
				$curr_ir_comment=$row['comment'];
				//$errormsg="Transfer succeeded";
			} else {
				$errormsg="error in transfer - INSERT INTO `k8_acc_wallet_trns` IR";
				error_log($sql);
			}			
			
			////3/////add update target ir ewallet
		 $sql2 = "UPDATE ir SET ewallet = (ewallet - ".$paymentVal.") WHERE ir_id = '" . $irid . "'";
		 $result2 = mysql_query($sql2);
		 if ($result2>0) {
			  ///get the ir ewallet value after update
			$sql3 = "SELECT * FROM `ir` WHERE ir_id='".$irid."'";
			$result3 = mysql_query($sql3);
			$row3 = mysql_fetch_assoc($result3);
			$ewallet = $row3['ewallet'];
			$ir_aname=$row3['a_name'];
			$ir_ename= $row3['f_name']." ".$row3['l_name'];
			$nationalid=$row3['valid_id'];
			$ir_nationality=$row3['nationality'];
			$ir_dob=$row3['birth_date'];
			$currentDate=date("Y-m-d");
			 
			 
			// echo "ewallet value of the ir after update  ".$ewallet;
			if($result3>0){
				/// add transaction to transactions of ewallet for the target ir
				$sql4="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('" . $irid . "', 'Payback ECs to Proshops accountant' , DATE(NOW()), '".$paymentVal."','" . $ewallet . "' , 'Proshops Accountant')";
				$result4 = mysql_query($sql4); 
				$errormsg="Ecs Transfered to ProShops accountant succeeded.";
			}///end of adding transaction table for IR

		} else {
			$errormsg="error UPDATE ir SET ewallet = (ewallet - ".$paymentVal.") WHERE ir_id = '" . $irid . "' ";
			error_log($sql);
		}
		
		}else{//if ir ewallet havnt enough ec
			$errormsg="IR (".$irid.") don't have enough ECs in his E-Wallet.";
			//error_log($sql);
		}
		/////////
		}else if($payment_method=="mix"){
			
		}
		
}///end if submited transfer

?>
<div id="error" class="col1" style="color:red;">

</div>
<div  class="col2" style="color:red;">
<?php echo $errormsg; ?>
</div>
<br>
        <div class="sep"></div>
<br>
<div class="col1">
<div style="float:left;width:49%">current accountant EC balance : </div><div style="float:left;width:49%" ><?php echo $curr_acc_bal_ec." EC"; ?></div>
<div style="float:left;width:49%">Selected IR EC ewallet balance : </div><div style="float:left;width:15%" id="selected_ir_ewal">0</div><div style="    width: 9%;    display: inline-block;"> EC</div>
</div>
<div class="col2">
<div style="float:left;width:100%">Last IR Balance at Proshops : </div><div id="lastirbalance" style="float:left;width:100%"></div>

</div>
<br>
        <div class="sep"></div>
<br>


 <?php
	///get the Managers IRs data
	$managers_irs=array("PA0102","PA0103","PA1431","PA0514");
	$managers_names=array();
	for($ii=0;$ii< sizeof($managers_irs);$ii++){
		$sql2= "select ir_id, f_name, l_name, a_name,ewallet from ir where ir_id='".$managers_irs[$ii]."'";
		if ($result2 = mysql_query($sql2)) {
			$row2 = mysql_fetch_assoc($result2);
			$errormsg="";
			$fullname_e=$row2["f_name"]." ".$row2["l_name"];
			$managers_names[]=array("ir_id"=> $row2["ir_id"], "fullname_e" => $fullname_e,"fullname_a" => $row2["a_name"],"ewallet" => $row2["ewallet"] );
		} else {
			$errormsg="error receiving ir data";
			error_log($sql2);
		}
	
	}//end for

    ?>
	
    <form name = "myform" method="post" onsubmit=" return validateForm();" >
	<div class="col1">
		<label>Select IR <span class="astrisk"> *</span></label> 
            <select name="ir_id" id="ir_id">
                <option value="" <?php
                if ($_POST['ir_id'] == "") {
                    echo "selected";
                }
            ?> >-- SELECT IR --</option>
			<?php for($dd=0;$dd<sizeof($managers_names);$dd++){ ?>
                <option value="<?php echo $managers_names[$dd]["ir_id"]; ?>"
				<?php
                if ($_POST['ir_id'] == $managers_names[$dd]["ir_id"]) {
                    echo "selected";
                }
            ?> data-ewallet="<?php echo $managers_names[$dd]["ewallet"]; ?>" ><?php echo $managers_names[$dd]["ir_id"]." - ".$managers_names[$dd]["fullname_e"]." - ".$managers_names[$dd]["fullname_a"]; ?></option>
			<?php };//end for ?>
			</select><br class="clear"/>
	</div>
	<div class="col2">
		<label>Payment Method <span class="astrisk"> *</span></label> 
            <select name="payment_method" id="payment_method">
                <option value="" <?php
                if ($_POST['payment_method'] == "") {
                    echo "selected";
                }
            ?> >-- Select Payment Method --</option>
                <option value="cash"
				<?php
                if ($_POST['payment_method'] == "cash") {
                    echo "selected";
                }
            ?> >Pay Cash (LE)</option>
                <option value="ECs"
				<?php
                if ($_POST['payment_method'] == "ECs") {
                    echo "selected";
                }
            ?> >Pay ECs from My eWallet</option>

				 <option value="bank"
				<?php
                if ($_POST['payment_method'] == "bank") {
                    echo "selected";
                }
            ?> >Pay By Bank</option>
			
			</select><br class="clear"/>
	</div>
	<div class="col1">
        <label >Ammount to Pay
            <span class="mandatory">*</span></label>
        <input type="text" name="payment_val" value="0" style="width: 90%;"><div id="currency" style="display: inline-block;margin:0 5px;"></div>
        <input type="hidden" name="secret" value="4unf9unufru49fnr9">
		<input type="hidden" name="max_ec" id="max_ec" value="<?php echo $curr_acc_bal_ec; ?>">
        <button type="submit"  class="ok">Payback</button>
        </br></br>
        <div class="sep"></div>
        </br>
	</div>
		
    </form>
<div class="sep"></div>

<script>	
            function validateForm()
            {
				document.getElementById("error").innerHTML = "";
				var y = document.forms["myform"]["ir_id"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "Please Select IR ,Mandatory fields cannot be left blank.";
                    window.scrollTo(0, 0);
                    return false;
                }
				var y = document.forms["myform"]["payment_method"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "Please Select a payment method ,Mandatory fields cannot be left blank.";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["payment_val"].value;
				var y1=parseInt(y);
				var yy=parseInt($( "#ir_id option:selected").attr("data-ewallet"))
				var payfromewallet=$( "#payment_method option:selected").val()
				//var yy= document.forms["myform"]["max_ec"].value;
				var yy1=parseInt(yy);
                if (y == '') {
                    document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                    window.scrollTo(0, 0);
                    return false;
                }
				if (y == 0) {
                    document.getElementById("error").innerHTML = "Can't Pay ZERO LE/ECs.";
                    window.scrollTo(0, 0);
                    return false;
                }
				if (y1 > yy1) {
					if(payfromewallet=="ECs"){
                    document.getElementById("error").innerHTML = "Can't Pay more than the available ECs.";
                    window.scrollTo(0, 0);
                    return false;
					}
                }

                //return true;
				return confirm('Are you sure you want to proceed with the Payment?');
            }
</script>
<script>
$(document).ready(function() {
	$('#currency').html("")
	$('#selected_ir_ewal').html("")
	$('#ir_id').val("")
	 //select
	
	$(document).on('change','#payment_method',function(){	
		var selected_method=$( "#payment_method option:selected" ).val();
		if(selected_method=="cash" || selected_method=="bank"){
			$('#currency').html("LE")
		}else if(selected_method=="ECs"){
			$('#currency').html("EC")
		}	
	});
	
	$(document).on('change','#ir_id',function(){	
		var selected_ir_ewallet=$( "#ir_id option:selected").attr("data-ewallet");
		var selected_ir=$( "#ir_id option:selected").val();
		$('#selected_ir_ewal').html(selected_ir_ewallet);
///do the ajax call to get the IR last balance at Proshops accountant
		$.ajax({
            //url: '', // url is empty because I'm working in the same file
            data: {'selectedIR': selected_ir},
            type: 'post',
            success: function(result) {
				var receivedjson=$.parseJSON(result)
                //alert("action performed successfully "+receivedjson[0].ir); //this alert is fired
                $('#lastirbalance').html('Last balance of <span style="color: red;">'+receivedjson[0].ir+'</span> at proshops accountant is : <br><span style="color: red;">'+receivedjson[0].bal_ec+' ECs</span>, <br>which is : <br><span style="color: red;">'+receivedjson[0].bal_le+' LE. </span><br> Last transaction comment : <br><span>'+receivedjson[0].comment+'</span>');
            }
        });

	});
});//end doc ready

</script>
<!------------------------------------------------------------------------------------------------->
<?php
endPage();
?>