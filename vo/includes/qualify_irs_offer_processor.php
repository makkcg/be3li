<?php
session_start();
////processor file for bo/modules/transactions/qualifybu1_offer.php 
//////////////////////////////////////////////////////

include "database_manager.php";
include "core.php";
include "html.php";

$database_manager = new DatabaseManager();
$core = new Core($database_manager);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

////Retrieve IR details posts processors and conditions
////Qualify IR -BU001 down network with only counting Accumulative Business volum, no order no dc,no LRcount, no DBV - 8-11-2016

///start session


////SELF post conditions
//// ajax for confirming the Referral IR / called by ('click','#confirm_refIRID_btn',function() 
if(isset($_POST['confirmed_refid']) && $_POST['confirmed_refid'] !=""){
	
	//$confirmed_IR[]=array("ir" => $_SESSION["Ref_IR"]);
	//echo json_encode($confirmed_IR);
	if(sizeof($_SESSION["Ref_IR"])>0){
	$confirmed_IR[]=array("ir" => $_SESSION["Ref_IR"]);
	echo json_encode($confirmed_IR);
	die();
	}else{
	$confirmed_IR[]=array("ir"=> "err","name"=> "error","aname"=>"error");
	echo json_encode($confirmed_IR);
	die();
    }
	die();
};///end post vars check

/////get the information of the IR, this is accessible through ajax with get call_user_func
if(isset($_POST['selectedIR']) && $_POST['selectedIR'] !=""){
	$_SESSION["Ref_IR"]="";
	$selectedIR=$_POST['selectedIR'];
	$IR_data=array();
	$sql = "select id, ir_id, CONCAT( f_name,  ' ', l_name ) AS name, a_name, mobile, email from ir where ir_id LIKE '".$selectedIR."'";
	//echo $sql;
	//$result = mysql_query($sql);
	$result = $database_manager->query($sql);
    if ($result>0) {
      // $errormsg="";
	$row= mysqli_fetch_assoc($result);
	//$row = mysql_fetch_assoc($result);
	//echo $result;
    $_SESSION["Ref_IR"]=$row["ir_id"];
	$IR_data[]=array("id"=>$row["id"],"ir"=> $_SESSION["Ref_IR"],"name"=> $row['name'],"aname"=>$row['a_name'],"mobile"=>$row['mobile'],"email"=>$row['email']);
	
	echo json_encode($IR_data);
	die();
	} else if ($result==0) {
	$IR_data[]=array("id"=>"null","ir"=> "No IR Found","ewallet"=> $result,"name"=> $sql,"aname"=>$row);
	$_SESSION["Ref_IR"]="";
	echo json_encode($IR_data);
	die();
	} else {
	$IR_data[]=array("id"=>"err","ir"=> "Error Loading IR","ewallet"=> $result,"name"=> $sql,"aname"=>$row);
	$_SESSION["Ref_IR"]="";
	echo json_encode($IR_data);
	die();
    }
	die();
}
///////end if ajax call (searching the referrar IR)




////function to loop down in IR network
function getdownlineNetwork_arr($accum_arr,$top_IR,$core,$database_manager){
	$downlineNetArr=array();
	if($top_IR=="" && sizeof($accum_arr)>0){
		for($ii=0;$ii<sizeof($accum_arr);$ii++){
			$sql = "SELECT ir_id  FROM `bu` WHERE `ir_id` NOT LIKE '$accum_arr[$ii]' AND `parent_bu_id` LIKE '%$accum_arr[$ii]%'";
			$result = $database_manager->query($sql);
			while ($row = mysqli_fetch_assoc($result)) {
				$downlineNetArr[] = $row['ir_id'];
			};
		}
	}else if($top_IR!=""){
		$sql = "SELECT ir_id  FROM `bu` WHERE `ir_id` NOT LIKE '$top_IR' AND `parent_bu_id` LIKE '%$top_IR%'";
		$result = $database_manager->query($sql);
		
		while ($row = mysqli_fetch_assoc($result)) {
			$downlineNetArr[] = $row['ir_id'];
		};
	}
	
	return $downlineNetArr;
}
////////////////////////////////////////////
////function to create Left and right 002 003 irs under IR
function generateLeftRightIRs($ref_IR,$core,$database_manager){
	$gen_irs=array();
	for ($lr=0;$lr<2;$lr++){
		if($lr==0){
			$last_ir_id= getlastirid($core,$database_manager);
			$counter=0;
			$newIR_login_pass="123456";
			$newIR_ewallet_pass="123456";
			$FormatedDateTime=$core->getFormatedDateTime();
			$FormatedDate=$core->getFormatedDate();
			//$ref_IR=$_POST['refir_genunder'];
			$ref_business_unit='002'; //"003"
			$ref_position='left'; ///"right"
			$generatedsuccess=registerNewIR($ref_IR,$ref_business_unit,$ref_position,$core,$database_manager,$last_ir_id,$counter,$newIR_login_pass,$newIR_ewallet_pass,$FormatedDateTime,$FormatedDate);
			
				if($generatedsuccess){
					//SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'
					
					//$gen_irs[] = $last_ir_id;
				}else{
					///ir was not generated FAIL
				}
		}else{
			$last_ir_id= getlastirid($core,$database_manager);
			$counter=0;
			$newIR_login_pass="123456";
			$newIR_ewallet_pass="123456";
			$FormatedDateTime=$core->getFormatedDateTime();
			$FormatedDate=$core->getFormatedDate();
			//$ref_IR=$_POST['refir_genunder'];
			$ref_business_unit='003'; //"003"
			$ref_position='right'; ///"right"
			$generatedsuccess=registerNewIR($ref_IR,$ref_business_unit,$ref_position,$core,$database_manager,$last_ir_id,$counter,$newIR_login_pass,$newIR_ewallet_pass,$FormatedDateTime,$FormatedDate);
			
				if($generatedsuccess){
					//$gen_irs[] = $last_ir_id;
				}else{
					///ir was not generated FAIL
				}		
		}
	}
	
	//return the downlines level 1 of refir
	$sql = "SELECT ir_id  FROM `bu` WHERE `ir_id` NOT LIKE '$ref_IR' AND `parent_bu_id` LIKE '%$ref_IR%'";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $gen_irs[] = $row['ir_id'];
	$row = mysqli_fetch_assoc($result);
    $gen_irs[] = $row['ir_id'];
	return $gen_irs;
}///end generate R L new irs function

////function to qualify IRs bu001
function qualifyofferbu001($ir,$bunit,$core,$database_manager){
	$bunit="001";
	
	///check if the IR is not previously qualified for bu001
	$sql = "SELECT `ir_id` FROM `bu` WHERE `code`='001' AND `is_qualified` =0 AND `ir_id` = '$ir' ";
	$database_manager->query($sql);
	$result = $database_manager->query($sql);
   // $row = mysqli_fetch_assoc($result);
	if(mysqli_num_rows($result) == 0){
		
	}else{
	
	$sql = "UPDATE bu SET is_qualified = 2 "
			. " WHERE ir_id = '" .$ir. "' "
			. " AND code = '" .$bunit. "' "
			. " AND  `is_qualified` =0 ";
        $database_manager->query($sql);

        $sql = "UPDATE ir SET qualification_date = '" . $core->getFormatedDateTime() . "' WHERE ir_id = '" .$ir . "'";
        $database_manager->query($sql);
		
		// UPDATE Parents dc, dbv, abv
		///in this condition we should should add where the parent bu is qualified 2
        $sql = "UPDATE bu SET left_abv = left_abv + 1 "
                . " WHERE left_children LIKE '%" .$ir. "-" . $bunit . "%' "
				. " AND  `is_qualified` =2 ";
        $database_manager->query($sql);

        $sql = "UPDATE bu SET right_abv = right_abv + 1"
                . " WHERE right_children LIKE '%" . $ir . "-" . $bunit . "%' "
				. " AND  `is_qualified` =2 ";
        $database_manager->query($sql);	
	}//end else
}///end qualify function 

////////////////////////////TESTING//////////////////
/////declear functions 


function createAcumArrDownline($top_IR,$lvlDepth,$core,$database_manager){
	$accrr1=array();
	$consolidatedIRsArr=array();
	$accrr1[]=getdownlineNetwork_arr(array(),$top_IR,$core,$database_manager);
	for($Lvlcounter=0;$Lvlcounter<$lvlDepth;$Lvlcounter++){
		$resultarr=getdownlineNetwork_arr($accrr1[$Lvlcounter],"",$core,$database_manager);
		if(sizeof($resultarr)<1){
			break;
		}else{
			$accrr1[]=$resultarr;
		}
	}
	
	$consolidatedIRsArr=array();
	for($ll=0;$ll<sizeof($accrr1);$ll++){
		$consolidatedIRsArr=array_merge($consolidatedIRsArr, $accrr1[$ll]); 
	}
	///to return accumulated array of all downline IRs
	return $consolidatedIRsArr;
	///return array of level arrays of irs
	//return $accrr1;
}///end function consilidate IRs in levles into one array

////function to qualify down network of IR
function qualifydownnetworkBU001($downnetwork,$core,$database_manager){
	for($irset=0;$irset < sizeof($downnetwork);$irset++){
		qualifyofferbu001($downnetwork[$irset],$bunit,$core,$database_manager);
	}
	return sizeof($downnetwork);
}///end qualify down network of IR

////function to create boxed divs of consolidated IR network
function drawIRdivfromArr($consolidatedArr,$core,$database_manager){
	$res_html="";
	for($o=0;$o<sizeof($consolidatedArr);$o++){
		////check if the IR is qualified or not for providing spcific bg color to the box
		$sql = "SELECT is_qualified  FROM `bu` WHERE `ir_id` = '$consolidatedArr[$o]' AND `code`='001' ";
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);
		//echo $row['is_qualified'];
		if($row['is_qualified']==2){
			$res_html=$res_html."<div class='ir_box_div ir_box_div_qual2' style='background-color: #78539c;'>".$consolidatedArr[$o]."</div>";
		}else if($row['is_qualified']==1){
			$res_html=$res_html."<div class='ir_box_div ir_box_div_qual1' style='background-color: #d79928;'>".$consolidatedArr[$o]."</div>";
		}else{
			$res_html=$res_html.'<div class="ir_box_div">'.$consolidatedArr[$o].'</div>';
		}
	
	
	}
	return $res_html;
}
///////////////////////////////////////////////////////////////////////END Functions//////////////////////////////////////
	
////ajax to get all the downlines and returns both the divboxes of the IRs and the array of the irs /based on the posted top IR
if(isset($_POST['TopIR']) && $_POST['TopIR'] !=""){
	$return_data=array();
	$top_IR=$_POST['TopIR'];//
	$lvlDepth=100;
	
	$considatednetworkArr=createAcumArrDownline($top_IR,$lvlDepth,$core,$database_manager);
	$ir_downnetwork_html=drawIRdivfromArr($considatednetworkArr,$core,$database_manager);
	
	$return_data[]=array("ir_downnetwork_arr"=>$considatednetworkArr,"ir_downnetwork_html"=> $ir_downnetwork_html);
	
	echo json_encode($return_data);
	die();
	
};
////ajax to qualify the top ir and all the downlines 
if(isset($_POST['TopIR_qualifyNetwork']) && $_POST['TopIR_qualifyNetwork'] !=""){
	$return_data=array();
	$top_IR=$_POST['TopIR_qualifyNetwork'];//
	$lvlDepth=100;
	///qualify Top IR first 
	qualifyofferbu001($top_IR,$bunit,$core,$database_manager);
	///Qualify All the down network
	$considatednetworkArr=createAcumArrDownline($top_IR,$lvlDepth,$core,$database_manager);
	$done=qualifydownnetworkBU001($considatednetworkArr,$core,$database_manager);
	if($done>0){
		$return_data[]=array("ir_downnetwork_no"=>$done,"msg"=> "Success");
	}else{
		$return_data[]=array("ir_downnetwork_no"=>$done,"msg"=> "Faild");
	}
	echo json_encode($return_data);
	die();
	
};

///end ajax condition to retrieve TopIR down network and its html

?>