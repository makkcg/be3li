<?php 
/*REMOVE later*/
//error_reporting(E_ALL);ini_set('display_errors', 1);
error_log("------------------------------ Starting Day Close Operation ------------------------------");

include "includes/database_manager.php";
include "includes/core.php";

$database_manager = new DatabaseManager();
$core = new Core($database_manager);


/*functions */
/*function to generate the list of Eligible upline IRs for paying commissions according the commission level */
/*IT Returns Array of Array [IR,UpLevelToMainIR ]*/
function geteligibleuplinesLx($database_manager,$mainIR,$uptolevelX,$uplinesLx_arr,$currentlevel){
	//$currentlevel=1;
	if ($currentlevel>$uptolevelX) {
            return $uplinesLx_arr;
        } else {
			$sql = "SELECT bu.ir_id as mainir,bu.parent_bu_id as uplinebu, SUBSTRING(bu.parent_bu_id, 1, LENGTH(bu.parent_bu_id)-4) as uplineir FROM bu bu inner join bu bu2 on SUBSTRING(bu.parent_bu_id, 1, LENGTH(bu.parent_bu_id)-4)=bu2.ir_id ";
			$sql =$sql."WHERE SUBSTRING(bu.parent_bu_id, 1, LENGTH(bu.parent_bu_id)-4) != bu.ir_id ";
			$sql =$sql."and bu.ir_id LIKE '".$mainIR."'";
			$sql =$sql."limit 1";
			$result = $database_manager->query($sql);
			$row = mysqli_fetch_assoc($result);
			/*$row['mainir']
			$row['uplinebu']
			$row['uplineir']*/
			/*check if the IR is eligible for commission then add to the array*/
			if(isIRelibible_conditions_for_com($row['uplineir'])){
				$uplinesLx_arr[]=array("IRID"=>$row['uplineir'],"upLevel"=>$currentlevel);
			}
			
			//var_dump($uplinesLx_arr);	echo "<br><br>";
			$mainIR=$row['uplineir'];
			$currentlevel++;
            return geteligibleuplinesLx($database_manager,$mainIR,$uptolevelX,$uplinesLx_arr,$currentlevel);
        }
}

/*function to check if the IR meets the required conditions for commission or not*/
function isIRelibible_conditions_for_com($IRtoVerify){
	return 1;
}
/*///////////////////////////*/
/*function to insert transaction into IR ewallet*/
function UpdateEwallet_InsertTransactionIR($database_manager,$core,$IRID,$Val,$comment,$fromIRID){
	$sql = "UPDATE ir SET ewallet = (ewallet + ".$Val."), total_ewallet = (total_ewallet + ".$Val.") WHERE ir_id = '" . $IRID . "' ";
	$result=$database_manager->query($sql);
	if(!$result){
		return false;
	}
	$sql2 = "SELECT ewallet FROM ir WHERE ir.ir_id= '".$IRID."';";
	$result2 = $database_manager->query($sql2);
	if(!$result2){
		return false;
	}
	while ($row2 = mysqli_fetch_assoc($result2)) {
		$ewallet2 = $row2['ewallet'];
	}
	$newEwalletBalance=$ewallet2;
	/*update the transactions in ewallet of the ir*/
	$sql3="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('".$IRID."', '".$comment."' , '".$core->getFormatedDateTime()."', ".$Val.",'".(string)($newEwalletBalance)."' , '".$fromIRID."')";
	$result3 =$database_manager->query($sql3);
	
	if(!$result3){
		return false;
	}else{
		return true;
	}	
}

function setProductCommissionToPAID($database_manager,$core,$ProductRowID,$productID){
	/*die($productID.' - '.$ProductRowID.' - '.$core->getFormatedDateTime());*/
	$sql = "UPDATE shop_order_line SET coms_paid = '1', com_paid_datetime = '".$core->getFormatedDateTime()."' WHERE id = '" . $ProductRowID . "' AND product_id =  '" . $productID . "'";
	/*die($sql);*/
	$result=$database_manager->query($sql);
	if(!$result){
		return false;
	}else{
		return true;
	}
}
/********************************/


/************Commission Calc process****************/

/*****pay 7 business units system commissions (revolving) ***/
/****steps & conditions****//*0- get configuration values to set some vars1- get all orders with status Fully-Paid , and commission not paid (IDs , irs)2- get all order items with status Fully-Paid and commission not paid (ids, com_0 to 21 , bonus , z0-1 , direct com) inner join with (1)3- */

/*set vars gathered from config table*/
$sql = "SELECT nodaystopayretailcom, orderstatuspayretailcomm, orderstatuspaybinarycom FROM configuration";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);
$orderStatusToPayRCom=$row["orderstatuspaybinarycom"];
$commission_notpaid=0;

/***select all orders fully paid and commission not paid join products fully paid commission not paid***/
/*$sql = "SELECT ord.id, ord.ir_id, ord.status, ord.ref_irid, orditm.* FROM shop_order ord ";$sql.= "inner join ";$sql.= "shop_order_line orditm on ord.id= orditm.shop_order_id";$sql.="WHERE";$sql.="ord.status='Fully-Paid' and";$sql.="ord.is_paid=0 and";$sql.="orditm.product_status= 'Fully-Paid' and";$sql.="orditm.coms_paid=0;";*/

$sql="SELECT ord.id as ordid, ord.ir_id, ord.status, ord.ref_irid, orditm.*, orditm.id as prodrowid FROM shop_order ord inner join shop_order_line orditm on ord.id= orditm.shop_order_id WHERE ord.is_paid=0 and orditm.product_status= '".$orderStatusToPayRCom."' and orditm.coms_paid=0";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);
while($row = mysqli_fetch_assoc($result)){
	echo "<br>-------------------Start looping Product commissions--------------------------<br>";
	$uptolevelX=21;
	$mainIR=$row['ir_id'];
	/*$mainIR="BE000169";*/
	$uplinesLx_arr="";/*array();*/
	$currentlevel=1;
	$uplineIRs=geteligibleuplinesLx($database_manager,$mainIR,$uptolevelX,$uplinesLx_arr,$currentlevel);
	/*the full array of uplines and thier commissions*/
	$fullupline_com_arr=array();
	for($q=0;$q<sizeof($uplineIRs);$q++){
		/*only add non zero commissions levels , and if the upline ir is not empty (top level)*/
		if($row['p_l'.($q+1).'_com']!=0 && $uplineIRs[$q]["IRID"]!=""){
			$fullupline_com_arr[]=array("OrderID"=>$row['shop_order_id'],"ProductRowID"=>$row['prodrowid'],"ProductID"=>$row['product_id'],"ProductName"=>$row['product_name'],"IRID"=>$uplineIRs[$q]["IRID"],"Level"=>$uplineIRs[$q]["upLevel"],"Commission"=>$row['p_l'.($q+1).'_com']);
		}
	}
	
	/****Start paying the Main IR*******/
		
		/***Pay the direct Commission to the referrar of the main ir **/
		  /*get the referrar irid */
			$sql001 = "SELECT SUBSTRING(`referral_bu_id`, 1, LENGTH(referral_bu_id)-4) as refirid FROM bu WHERE ir_id LIKE '".$mainIR."' LIMIT 1";
			echo $sql001."<br>";
			$result001 = $database_manager->query($sql001);
			if(!$result001){
				/*return false;*/
			}
			$row001 = mysqli_fetch_assoc($result001);
			$RefIRID = $row001['refirid'];
			echo "<br>REfirid   ".$sql001."<br>";
		/*pay the commission for the referrar */
		$IRID=$mainIR;
		$Val=$row['direct_com'];
		$comment='Direct Commission from IR: '.$IRID.' Order#'.$row['shop_order_id'].' Product '.$row['product_name'];
		$fromIRID=$IRID;
		if($Val>0){
			if(!UpdateEwallet_InsertTransactionIR($database_manager,$core,$RefIRID,$Val,$comment,$fromIRID)){
					echo "error updating ewallet and transaction for  $comment";
					break;
			}
		}else{
			echo "MainIR $IRID direct_com is Zero";
		}
		
		/***Pay the Cashback*********/
		$IRID=$mainIR;
		$Val=$row['p_l0_com'];
		$comment='Cashback from IR: '.$IRID.' Order#'.$row['shop_order_id'].' Product '.$row['product_name'];
		$fromIRID=$IRID;
		if($Val>0){
			if(!UpdateEwallet_InsertTransactionIR($database_manager,$core,$IRID,$Val,$comment,$fromIRID)){
					echo "error updating ewallet and transaction for  $comment";
					break;
			}
		}else{
			echo "MainIR $IRID p_l0_com (Cashback) is Zero";
		}
		/***Pay the Bonus*********/
		$IRID=$mainIR;
		$Val=$row['bonus'];
		$comment='Bonus from IR: '.$IRID.' Order#'.$row['shop_order_id'].' Product '.$row['product_name'];
		$fromIRID=$IRID;
		if($Val>0){
			if(!UpdateEwallet_InsertTransactionIR($database_manager,$core,$IRID,$Val,$comment,$fromIRID)){
					echo "error updating ewallet and transaction for  $comment";
					break;
			}
		}else{
			echo "MainIR $IRID bonus is Zero";
		}
		/***Pay the rpts*****/
		/*$IRID=$mainIR;
		$Val=$row['bonus'];
		$comment='Bonus from IR: '.$IRID.' Order#'.$row['shop_order_id'].' Product '.$row['product_name'];
		$fromIRID=$IRID;
		if($Val>0){
			if(!UpdateEwallet_InsertTransactionIR($database_manager,$core,$IRID,$Val,$comment,$fromIRID)){
					echo "error updating ewallet and transaction for  $comment";
					break;
			}
		}else{
			echo "MainIR $IRID bonus is Zero";
		}*/
		
		/****Pay the dcpts****/
		
		/*$IRID=$mainIR;
		$Val=$row['bonus'];
		$comment='Bonus from IR: '.$IRID.' Order#'.$row['shop_order_id'].' Product '.$row['product_name'];
		$fromIRID=$IRID;
		if($Val>0){
			if(!UpdateEwallet_InsertTransactionIR($database_manager,$core,$IRID,$Val,$comment,$fromIRID)){
					echo "error updating ewallet and transaction for  $comment";
					break;
			}
		}else{
			echo "MainIR $IRID bonus is Zero";
		}*/
		
	/*******Start paying All the eligible Upline IRS****/
		for($r=0;$r<sizeof($fullupline_com_arr);$r++){
			
			$IRID=$fullupline_com_arr[$r]["IRID"];
			$Val=$fullupline_com_arr[$r]['Commission'];
			$level=$fullupline_com_arr[$r]["Level"];
			$fromIRID=$mainIR;
			$comment='Commission from IR: '.$mainIR.' To '.$IRID.' for Level'.$level.' and Order#'.$row['shop_order_id'].' and Product '.$row['product_name'];
			if($Val>0){
				if(!UpdateEwallet_InsertTransactionIR($database_manager,$core,$IRID,$Val,$comment,$fromIRID)){
					echo "error updating ewallet and transaction for  $IRID";
					break;
				}
			}else{
				echo "$IRID ,Level  Commission is Zero";
			}
			
			/*Set Commission paid flag to 1 in orders line*/
			$ProductRowID=$fullupline_com_arr[$r]["ProductRowID"];
			$productID=$fullupline_com_arr[$r]["ProductID"];
			if(setProductCommissionToPAID($database_manager,$core,$ProductRowID,$productID)){
				echo "Commissiona PAID : ".$fullupline_com_arr[$r]["IRID"]." Order#".$fullupline_com_arr[$r]["OrderID"]."<br> Product name id :".$fullupline_com_arr[$r]["ProductID"]."-".$fullupline_com_arr[$r]["ProductName"]." - Level ".$fullupline_com_arr[$r]["Level"]." - Commission: ".$fullupline_com_arr[$r]['Commission']."<br><br>";
			
			}else{
				echo "error set com paid to 1";
			}
		}
			
	
}/*end while looping all products*/


/*
while($row = mysqli_fetch_assoc($result)){
	var_dump($row);	echo "<br><br>";
	/*$upline_bu=$core->calculateUpline($database_manager, $position, $current_ir_id, $current_business_unit);*/
	/*var_dump($upline_bu);
	echo "<br><br>";
	}
	*/
?>