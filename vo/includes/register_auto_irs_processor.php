<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
////securing access to file with secret get
if($_GET["secret"]!="proshopskcgmodification"){
	die("Access Not Allowed");
}else{
	
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

///get next IR ID number
function getlastirid($core,$database_manager){
	// Calculate New IR ID
        $sql = " SELECT ir_id AS last_ir_id FROM ir Where id=( SELECT MAX(id)  FROM ir ); ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $last_ir_id = $core->generateNextIRID($row['last_ir_id']);
		return $last_ir_id;
}

////////////////////register ir function//////
function registerNewIR($ref_IR,$ref_business_unit,$ref_position,$core,$database_manager,$last_ir_id,$counter,$newIR_login_pass,$newIR_ewallet_pass,$FormatedDateTime,$FormatedDate){
	
	$error="";
	$referrer_ir_id = strtoupper($ref_IR);
	//$referrer_name = $_POST['referrer_name'];
	$referrer_type = "";
	$referrer_BUs = array();
	//$ewallet_ir_id = strtoupper($_POST['ewallet_ir_id']); /// the ewallet to pay registration
	$sql_BUs = "SELECT b.is_qualified, CONCAT(r.title, ' ', r.f_name, ' ', r.l_name) AS name, b.code FROM bu b "
			. " LEFT OUTER JOIN ir r ON b.ir_id = r.ir_id "
			. " WHERE b.ir_id = '" . $referrer_ir_id . "' ORDER BY b.code ASC";
	$result_BUs = $database_manager->query($sql_BUs);
	if (mysqli_num_rows($result_BUs) == 0) {
	   echo "referrar ir is not correct";
	   die();
	}
	$row_BUs = mysqli_fetch_assoc($result_BUs);
	$referrer_type = $row_BUs['is_qualified'];
	$referrer_name = $row_BUs['name'];
	$referrer_BUs = array("001", "002", "003");

	//////ir registration data//////
		$title="Mr.";
		$f_name="Test IR ".$counter;////add counter to name
		$l_name="IR Family ".$counter; /// add counter to the name
		$a_name=" موزع اختبار ".$counter; ///add counter to the name
		$mobile="01114442161";
		$phone="0233837794";
		$nationality="Egyptian";
		$valid_id="id0123456789".$counter;
		$valid_id_type="National ID";
		$beneficiary="TestBen";
		$relationship ="Son";
		$address="123 Super Tower ,";
		$city="Cairo";
		$area="Maadi";
		$country="Egypt";
		$email="test".$counter."@email.com";///add counter to email
		$birth_date="1979-10-11";
	//////
	///// Calculate New IR ID
		$last_ir_id;
		////////////
		
		/// Calculate upline
		$upline_bu_id = "";
		$upline_bu_id = $core->calculateUpline($database_manager, $ref_position, $referrer_ir_id, $ref_business_unit);
		////encrypt passwords
		$salt = $core->generateSalt();
		$login_pass_crypt = crypt($newIR_login_pass, $salt);
		$ewallet_pass_crypt = crypt($newIR_ewallet_pass, $salt);
		
		$sql = "INSERT INTO ir (ir_id, title, f_name, l_name, a_name, "
					. "email, mobile, phone, address, area, "
					. "city, country, valid_id, valid_id_type, "
					. "nationality, birth_date, beneficiary, "
					. "relationship, login_pass, ewallet_pass, "
					. "ewallet, total_ewallet, dcpts, "
					. "total_dcpts, rpts, total_rpts, "
					. "registration_date, last_renewal_date)"
					. " VALUES("
					. "'" . $last_ir_id . "', '" . $title . "', '" . $f_name . "', '" . $l_name . "', '" . $a_name . "', "
					. "'" . $email . "', '" . $mobile . "', '" . $phone . "', '" . $address . "', '" . $area . "', '" . $city . "', "
					. "'" . $country . "', '" . $valid_id . "', '" . $valid_id_type . "', '" . $nationality . "', "
					. "'" . $birth_date . "', '" . $beneficiary . "', '" . $relationship . "', '" . $login_pass_crypt . "', "
					. "'" . $ewallet_pass_crypt . "', '0', '0', "
					. "'0', '0', '0', '0', '" . $FormatedDateTime . "', '" . $FormatedDate . "'"
					. " ) ";
		if ($database_manager->query($sql)) {
			// INSERT INTO bu

            $string_to_add_to_children = "\'" . $last_ir_id . "-001\', \'" . $last_ir_id . "-002\', \'" . $last_ir_id . "-003\', ";

            $sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "
                    . "left_abv, right_abv, left_children, right_children, "
                    . "parent_bu_id, position, is_qualified, referral_bu_id, "
                    . "position_to_referral) VALUES ( "
                    . " '" . $last_ir_id . "', '001', 0, 0, 0, 0, '\'" . $last_ir_id . "-002\', ', '\'" . $last_ir_id . "-003\', ', '" . $upline_bu_id . "', '" . $ref_position . "', '0', '" . $referrer_ir_id . "-" . $ref_business_unit . "', '" . $ref_position . "' "
                    . " )";
            $database_manager->query($sql);

            $sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "
                    . "left_abv, right_abv, left_children, right_children, "
                    . "parent_bu_id, position, is_qualified, referral_bu_id, "
                    . "position_to_referral) VALUES ( "
                    . " '" . $last_ir_id . "', '002', 0, 0, 0, 0, '', '', '\'" . $last_ir_id . "-001\', " . "', 'left', '0', '" . $referrer_ir_id . "-" . $ref_business_unit . "', '" . $ref_position . "' "
                    . " )";
            $database_manager->query($sql);

            $sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "
                    . "left_abv, right_abv, left_children, right_children, "
                    . "parent_bu_id, position, is_qualified, referral_bu_id, "
                    . "position_to_referral) VALUES ( "
                    . " '" . $last_ir_id . "', '003', 0, 0, 0, 0, '', '', '\'" . $last_ir_id . "-001\', " . "', 'right', '0', '" . $referrer_ir_id . "-" . $ref_business_unit . "', '" . $ref_position . "' "
                    . " )";
            $database_manager->query($sql);

            // INSERT INTO dc

            $sql = "INSERT INTO dc (date, bu_id, left_dc, right_dc) VALUES ( "
                    . " '" . $FormatedDate . "', '" . $last_ir_id . "-001" . "', '0', '0' "
                    . " )";
            $database_manager->query($sql);

            $sql = "INSERT INTO dc (date, bu_id, left_dc, right_dc) VALUES ( "
                    . " '" . $FormatedDate . "', '" . $last_ir_id . "-002" . "', '0', '0' "
                    . " )";
            $database_manager->query($sql);

            $sql = "INSERT INTO dc (date, bu_id, left_dc, right_dc) VALUES ( "
                    . " '" . $FormatedDate . "', '" . $last_ir_id . "-003" . "', '0', '0' "
                    . " )";
            $database_manager->query($sql);

            // Update Parents children and Counter
            $children_column = $ref_position . "_children";

            $sql = "UPDATE bu SET " . $children_column . " = CONCAT(" . $children_column . ", '" . $string_to_add_to_children . "') "
                    . " WHERE ir_id = '" . $core->getIRID($upline_bu_id) . "' "
                    . " AND code = '" . $core->getBUCode($upline_bu_id) . "' ";
            $database_manager->query($sql);

            $sql = "UPDATE bu SET left_children = CONCAT(left_children, '" . $string_to_add_to_children . "') "
                    . " WHERE left_children LIKE '%" . $upline_bu_id . "%' ";
            $database_manager->query($sql);

            $sql = "UPDATE bu SET right_children = CONCAT(right_children, '" . $string_to_add_to_children . "') "
                    . " WHERE right_children LIKE '%" . $upline_bu_id . "%' ";
            $database_manager->query($sql);

            // New IR was registered correctly
				$error="";
		}else {
				$error = "Incorrect IR Information.";
		}///end insert ir query execution 

		///return success or fail with error
		
		if($error==""){
			return true;
		}else{
			return false;
		}
////////////////
}///end register new IR function
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
	//SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'
	//return the downlines level 1 of refir
	$sql = "SELECT ir_id  FROM `bu` WHERE `ir_id` NOT LIKE '$ref_IR' AND `parent_bu_id` LIKE '%$ref_IR%'";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $gen_irs[] = $row['ir_id'];
	$row = mysqli_fetch_assoc($result);
    $gen_irs[] = $row['ir_id'];
	return $gen_irs;
}///end generate R L new irs function

////set initialize variables and session vars


//////proces to generate auto ir ajax  numberoflevels2 (binary)
if(isset($_POST['refir_genunder']) && isset($_POST['numberoflevels2']) && $_POST['refir_genunder'] !=""  && $_POST['numberoflevels2'] !="" ){
	$numberofnetworkleveles=$_POST['numberoflevels2'];
	$numberofirstogenereate=2^$numberofnetworkleveles;
	$generatedNEWirs=0;
	$level1IRS=array();
	$level2IRS=array();
	$level3IRS=array();
	////generate IRs Level 1 and get the NEW IRS in array
	$ref_IR=$_POST['refir_genunder'];
	$level1IRS=generateLeftRightIRs($ref_IR,$core,$database_manager);
	$generatedNEWirs=$generatedNEWirs+sizeof($level1IRS);
	$numberofIRs=(2^1);
	///switch if levles is more than 1 level 
	switch($numberofnetworkleveles){
		case 2:
			$numberofIRs=(2^1)+(2^2);
			////generate IRs level 2 and get new IRs in array
			for($lvl2=0;$lvl2<sizeof($level1IRS);$lvl2++){
				//array_push($level2IRS, generateLeftRightIRs($level1IRS[$lvl2],$core,$database_manager));
				$level2IRS=array_merge($level2IRS, generateLeftRightIRs($level1IRS[$lvl2],$core,$database_manager)); 
				//$level2IRS[]=generateLeftRightIRs($level1IRS[$lvl2],$core,$database_manager);	
			}
			$generatedNEWirs=$generatedNEWirs+sizeof($level2IRS);
		break;
		case 3:
			$numberofIRs=(2^1)+(2^2)+(2^3);
			
			////generate IRs level 2 and get new IRs in array
			for($lvl2=0;$lvl2<sizeof($level1IRS);$lvl2++){
				$RLarr=generateLeftRightIRs($level1IRS[$lvl2],$core,$database_manager);
				//array_push($level2IRS, generateLeftRightIRs($level1IRS[$lvl2],$core,$database_manager));
				$level2IRS=array_merge($level2IRS,$RLarr); 
				//$level2IRS[]=generateLeftRightIRs($level1IRS[$lvl2],$core,$database_manager);	
			}
			
			$generatedNEWirs=$generatedNEWirs+sizeof($level2IRS);
			sleep(3);
				////generate IRs level 3 and get new IRs in array
				
			for($lvl3=0;$lvl3 < sizeof($level2IRS);$lvl3++){
				$RLarr2=generateLeftRightIRs($level2IRS[$lvl3],$core,$database_manager);
				//array_push($level3IRS, generateLeftRightIRs($level2IRS[$lvl3],$core,$database_manager));
				$level3IRS=array_merge($level3IRS,$RLarr2 );
				//$level3IRS[]=generateLeftRightIRs($level2IRS[$lvl3],$core,$database_manager);	
			}
			$generatedNEWirs=$generatedNEWirs+sizeof($level3IRS);
			//print_r($level3IRS);
			//die();
		break;
	}///end switch levels
	

	if($generatedNEWirs==$numberofIRs){
		echo "success ".$generatedNEWirs." NEW Irs L1:".sizeof($level1IRS)." - L2: ".sizeof($level2IRS)." - L3: ".sizeof($level3IRS);
	}else{
		echo "fail ".$generatedNEWirs." NEW Irs L1:".sizeof($level1IRS)." - L2: ".sizeof($level2IRS)." - L3: ".sizeof($level3IRS);
	}
};///end auto generate ir ajax





}///end securing access to file functions with get secret
?>