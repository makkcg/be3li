<style>
.ir_box_div{
	padding: 5px;
    white-space: nowrap;
    /* width: 38px; */
    text-align: center;
    box-sizing: border-box;
    height: auto;
    float: left;
    border-radius: 2px;
    font-size: 90%;
    background-color: #555;
    color: #ffffff;
    transform: translateY(-1px);
    margin: 3px;
}
.ir_box_div_qual2{
	background-color: #78539c !important;

}
.ir_box_div_qual1{
	background-color: #d79928 !important;

}
</style>

<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

/////calculate the downline at level 1 
////////SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'



include "database_manager.php";
include "core.php";
include "html.php";

$database_manager = new DatabaseManager();
$core = new Core($database_manager);



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
			$res_html=$res_html.'<div class="ir_box_div ir_box_div_qual2" style="background-color: #78539c;">'.$consolidatedArr[$o].'</div>';
		}else if($row['is_qualified']==1){
			$res_html=$res_html.'<div class="ir_box_div ir_box_div_qual1" style="background-color: #d79928;>'.$consolidatedArr[$o].'</div>';
		}else{
			$res_html=$res_html.'<div class="ir_box_div">'.$consolidatedArr[$o].'</div>';
		}
	
	
	}
	return $res_html;
}

$top_IR=$_GET["topir"];//"PA0100";
$lvlDepth=50;
$considatednetworkArr=createAcumArrDownline($top_IR,$lvlDepth,$core,$database_manager);
print "<pre>";
print_r($considatednetworkArr);
print "</pre>";
echo drawIRdivfromArr($considatednetworkArr,$core,$database_manager);
if($_GET["qualifyir"]!=""){
	$done=qualifydownnetworkBU001($considatednetworkArr,$core,$database_manager);
	
	echo "Number of Qualified IRs : ".$done;
	
};
?>