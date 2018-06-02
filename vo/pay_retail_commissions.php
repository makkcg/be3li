<?php

error_log("------------------------------ Starting Pay Retail Commissions ------------------------------");

/*
  Transaction Types:
  ------------------

 */


////Main Vairables
	$isCommsPaid=0; ///is retail commission paid to irs for this product , usefull to get report of paid and not paid commissions
	$NoDaystoPayRCom=0;///days to pay the retail commission since order date
	$orderStatusToPayRCom='Delivered';/////status of order to pay retail commission : Delivered , New , InPreparation , Shipping , Postponed , Returned
	
// Lock Registration OK
$sql = "UPDATE configuration SET register_lock = 1";
$database_manager->query($sql);
//error_log($sql);

if ($_GET['secret'] == "e0d39uN3hb38f4uhR84rhf84rfe9e9dfekcg33") {
	echo "retail com secret true <br>";
    $sql = "SELECT nodaystopayretailcom, orderstatuspayretailcomm FROM configuration";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $NoDaystoPayRCom = $row['nodaystopayretailcom'];////number of days starting from order date to pay the retail commissions
    $orderStatusToPayRCom=$row['orderstatuspayretailcomm'];///order status to pay the retail commissions for all the levels
	///Query orders and order products that meets condition (com not paid, days since ordered til now >= xxx , product is delivered, shop title my shop "retail not binary" )
		echo "days to pay retail : $NoDaystoPayRCom <br>";
		$IRsProducts_arr=$core->getListOrderProductsIRsForRComm($database_manager,$orderStatusToPayRCom,$NoDaystoPayRCom,$isCommsPaid);
		///resulted array keys =  OrderID,IRID,orderlineid, ProductID, productPrice,com0, com1, com2
		$sizeofirproductsarr=sizeof($IRsProducts_arr);		
		echo "size of ir products ".$sizeofirproductsarr."<br>";
		////loop through orders match the conditions to pay the Retail commissions
		for($ii=0;$ii<sizeof($IRsProducts_arr);$ii++){
			
			$twouplinesIRs=$core->getUpReferralLevelX($database_manager,$IRsProducts_arr[$ii]['IRID'],2);//get the ir refferals to two levels up
			//echo $IRsProducts_arr[$ii]['IRID'].' discount: '.$IRsProducts_arr[$ii]['com0'].'<br>';
			
			$MainIR=$IRsProducts_arr[$ii]['IRID'];
			echo "Main IR com0 : ".$twouplinesIRs['IRID']." <br>";
			/**************************Pay REtail Discount to IR on his purchased*******************************/
			////pay the ir Retail discount to his ewallet
			 $sql = "UPDATE ir SET ewallet = (ewallet + ".$IRsProducts_arr[$ii]['com0']."), total_ewallet = (total_ewallet + ".$IRsProducts_arr[$ii]['com0'].") WHERE ir_id = '" . $MainIR . "';";
			$database_manager->query($sql);
			
			///get IR ewallet info to update the transaction
			$sql2 = "SELECT ewallet FROM ir WHERE ir.ir_id= '".$MainIR."';";
			$result2 = $database_manager->query($sql2);
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$ewallet = $row2['ewallet'];
			}
			$newEwalletBalance=$ewallet;///+$IRsProducts_arr[$ii]["com0"];
			//echo "newbal  ".$newEwalletBalance."<br><br><br><br>";
			////update the transactions in ewallet of the ir
			$companyname=$_SESSION['company'];
			$sql="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('".$MainIR."', 'Retail Product Discount Refund' , '".$core->getFormatedDateTime()."', ".$IRsProducts_arr[$ii]["com0"].",'".(string)($newEwalletBalance)."' , 'Retail Commission- $companyname')";
			$database_manager->query($sql);

			/**************************Pay REtail Commission to Uplines 2 levels up*******************************/
			
			/**************pay retail commission for Level 1 IR up *******/
			if($twouplinesIRs[0]['is_qualified']>0){
				//echo 'Uplevel1_IR: '.$twouplinesIRs[0]['ir'].' com1: '.$IRsProducts_arr[$ii]['com1'].'<br>';
				
				////pay the ir Retail commsion level1 up to his ewallet
				$sql = "UPDATE ir SET ewallet = (ewallet + ".$IRsProducts_arr[$ii]['com1']."), total_ewallet = (total_ewallet + ".$IRsProducts_arr[$ii]['com1'].") WHERE ir_id = '" . $twouplinesIRs[0]['ir'] . "' ";
                $database_manager->query($sql);
			
				///get IR ewallet info to update the transaction
				
				$sql2 = "SELECT ewallet FROM ir WHERE ir.ir_id= '".$twouplinesIRs[0]['ir']."';";
				$result2 = $database_manager->query($sql2);
				while ($row2 = mysqli_fetch_assoc($result2)) {
					$ewallet1 = $row2['ewallet'];
				}
				$newEwalletBalance=$ewallet1;//+$IRsProducts_arr[$ii]["com1"];
				////update the transactions in ewallet of the ir
				//$sql="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('" . $twouplinesIRs[0]['ir'] . "', 'Retail Prodcut Commission from IR: '".$IRsProducts_arr[$ii]['IRID']."' , '".$core->getFormatedDateTime()."', ".$IRsProducts_arr[$ii]['com1'].",'" . (string) ($ewallet1 + $IRsProducts_arr[$ii]['com1']) . "' , 'Retail Commission-'".$_SESSION['company'].")";
				
				$sql="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('".$twouplinesIRs[0]['ir']."', 'Retail Prodcut Commission from IR: $MainIR' , '".$core->getFormatedDateTime()."', ".$IRsProducts_arr[$ii]['com1'].",'".(string)($newEwalletBalance)."' , 'Retail Commission- $companyname')";
				$database_manager->query($sql);		
				
			}else{
				//echo 'Uplevel1_IR: '.$twouplinesIRs[0]['ir'].' com1: No Commission, IR is Not Qualified<br>';
			}
			
			/**************pay retail commission for Level 2 IR up *******/
			if($twouplinesIRs[1]['is_qualified']>0){
				//echo 'Uplevel2_IR: '.$twouplinesIRs[1]['ir'].' com2: '.$IRsProducts_arr[$ii]['com2'].'<br><br>';
				
				////pay the ir Retail commsion level1 up to his ewallet
				$sql = "UPDATE ir SET ewallet = (ewallet + ".$IRsProducts_arr[$ii]['com2']."), total_ewallet = (total_ewallet + ".$IRsProducts_arr[$ii]['com2'].") WHERE ir_id = '" . $twouplinesIRs[1]['ir'] . "' ";
                $database_manager->query($sql);
			
				///get IR ewallet info to update the transaction
				
				$sql2 = "SELECT ewallet FROM ir WHERE ir.ir_id= '".$twouplinesIRs[1]['ir']."';";
				$result2 = $database_manager->query($sql2);
				while ($row2 = mysqli_fetch_assoc($result2)) {
					$ewallet2 = $row2['ewallet'];
				}
				$newEwalletBalance=$ewallet2;///+$IRsProducts_arr[$ii]["com2"];
				////update the transactions in ewallet of the ir
				//$sql="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('" . $twouplinesIRs[1]['ir'] . "', 'Retail Prodcut Commission from IR: '".$IRsProducts_arr[$ii]['IRID']." , '".$core->getFormatedDateTime()."', ".$IRsProducts_arr[$ii]['com2'].",'" . (string) ($ewallet2 + $IRsProducts_arr[$ii]['com2']) . "' , 'Retail Commission-'".$_SESSION['company']."')";
				
				$sql="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('".$twouplinesIRs[1]['ir']."', 'Retail Prodcut Commission from IR: $MainIR' , '".$core->getFormatedDateTime()."', ".$IRsProducts_arr[$ii]['com2'].",'".(string)($newEwalletBalance)."' , 'Retail Commission- $companyname')";
				
				$database_manager->query($sql);	
			}else{
				//echo 'Uplevel2_IR: '.$twouplinesIRs[1]['ir'].' com2: No Commission, IR is Not Qualified<br>';
			}
			
			///update the order product ispaid flag to paid =1 when retail commission is paid to all uplines
			$sql = "UPDATE shop_order_line SET coms_paid= 1 WHERE id = '".$IRsProducts_arr[$ii]['orderlineid']."'";
			$database_manager->query($sql);
			echo "retail com process done<br>";
		}///end for
}

// Release Registration Lock OK

$sql = "UPDATE configuration SET register_lock = 0";
$database_manager->query($sql);
//error_log($sql);

error_log("------------------------------ Ending Pay Retail Commissions  ------------------------------");
?>
