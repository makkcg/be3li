<?php

if(!session_id()){
session_start();
}

if($_COOKIE["lang"]=="ar" && !isset($_SESSION['language'])){
		
		
		 $_SESSION['language'] = new Arabic_language;
	}else{
		
		  $_SESSION['language'] = new English_language;
}

$html_page->writeHeader();

//////////////////////////
$register_lock = 1;
$sql = "SELECT register_lock FROM configuration ";

while ($register_lock == 1) {
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $register_lock = $row['register_lock'];
    if ($register_lock == 1) {
        usleep(1000000);
    }
}

$sql = "UPDATE configuration SET register_lock = 1";
$database_manager->query($sql);


if ($_POST['step'] != "1" && $_POST['step'] != "2") {
    header("Location: index.php?page=register_first_step");
}
/********************************************VARIABLES asignments************************************/
///set the error var to nothing (used to complete the payment and registeration )
$error == "";
$referrer_ir_id = strtoupper($_POST['referrer_ir_id']);
$referrer_name = $_POST['referrer_name'];
$referrer_type = "";
$referrer_BUs = array();

if(isset($_POST['ewallet_ir_id']) && $_POST['ewallet_ir_id']!=""){
$ewallet_ir_id = strtoupper($_POST['ewallet_ir_id']);
}

if(isset($_POST['rechargecardnumber']) && $_POST['rechargecardnumber']!=""){
	$cardnumber=$_POST['rechargecardnumber'];
}
///////////get the qualified bu of the ref ir to be used later , and to confirm that the ref ir has bu in the bu table
$sql_BUs = "SELECT b.is_qualified, CONCAT(r.title, ' ', r.f_name, ' ', r.l_name) AS name, b.code FROM bu b "
        . " LEFT OUTER JOIN ir r ON b.ir_id = r.ir_id "
        . " WHERE b.ir_id = '" . $referrer_ir_id . "' ORDER BY b.code ASC";
$result_BUs = $database_manager->query($sql_BUs);
//////if there is no bu's for the ref ir go back to step 1 with error
if (mysqli_num_rows($result_BUs) == 0) {
    header("Location: index.php?page=register_first_step&error=1");
}
$row_BUs = mysqli_fetch_assoc($result_BUs);
$referrer_type = $row_BUs['is_qualified'];
$referrer_name = $row_BUs['name'];

/////get new registration commission from config (tor provide commission for ref ir for Registraion ONLY to the referral)
$sql = "SELECT new_reg_com FROM configuration ";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);
$new_reg_com = $row['new_reg_com'];
////////
/////create array of business units codes
$referrer_BUs = array("001", "002", "003");

//////////when user submit the step 2 form (THIS FORM) (payment)
if ($_POST['step'] == "2") {
	//////////clean the new ir posted data
    $_POST['f_name'] = $database_manager->realEscapeString($_POST['f_name']);
    $_POST['l_name'] = $database_manager->realEscapeString($_POST['l_name']);
    $_POST['a_name'] = $database_manager->realEscapeString($_POST['a_name']);
    $_POST['nationality'] = $database_manager->realEscapeString($_POST['nationality']);
    $_POST['valid_id'] = $database_manager->realEscapeString($_POST['valid_id']);
    $_POST['beneficiary'] = $database_manager->realEscapeString($_POST['beneficiary']);
    $_POST['address'] = $database_manager->realEscapeString($_POST['address']);
    $_POST['city'] = $database_manager->realEscapeString($_POST['city']);
    $_POST['area'] = $database_manager->realEscapeString($_POST['area']);
	
    // Validate Email

    if ($core->checkEmailDuplicate($database_manager, $_POST['email']) > 0) {
        $error = "This email is associated with another account.";
    }
	
	// validate card pin and sanitize it
	if(strlen($_POST['card_pin'])==4){
		$_POST['card_pin'] = $database_manager->realEscapeString($_POST['card_pin']);
	}else{
		$error="Card Pin should be 4 numbers.";
	}
	
    // Validate Payment
	///////get the registration fees from db
    $sql = "SELECT registration_fees FROM configuration ";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $registration_fees = $row['registration_fees'];
	
	////check if the user use the ewallet OR the card to pay to check the balance
	if ($_POST && isset($_POST['rechargecardnumber']) && isset($_POST['rechargecardpsw']) && $_POST['rechargecardpsw'] !="" && $_POST['rechargecardnumber'] !="") {
		////check if the card password is correct
		$cardpsw=$_POST['rechargecardpsw'];
		$cardnumber=$_POST['rechargecardnumber'];
		
		$error= $core->matchcardpsw($cardnumber,$cardpsw,$database_manager,3);
		
		if($error == ""){///no errors returned from confirming password
			
			/////check the card balance 
			$card_available_fund=$core->getcardbalance($cardnumber,$database_manager,0);
			if ($card_available_fund < $registration_fees) {
				$error = $_SESSION["language"]->cart_msg_moneynotenough; 
			}
		
			
		}
		
	}else{
				////////check the ewallet balance 
		$sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $ewallet_ir_id . "' ";
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);
		$ewallet_ir_available_fund = $row['ewallet'];

		if ($ewallet_ir_available_fund < $registration_fees) {
			$error = $_SESSION["language"]->cart_msg_moneynotenough; 
		}
		/////get the password of the ewallet
		$ewallet_ir_password = $_POST['ewallet_ir_password'];
		////clean the passwords of ewallet and its ir for paying , ir ewallet password 
		$ewallet_ir_password = stripslashes($ewallet_ir_password);
		$ewallet_ir_password = $database_manager->realEscapeString($ewallet_ir_password);
		$ewallet_ir_id = $database_manager->realEscapeString($ewallet_ir_id);
		
		//////check the paying ir ewallet password is correct
		$sql = "SELECT ewallet_pass FROM ir WHERE ir_id='$ewallet_ir_id'";
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);

		$ewallet_ir_password = crypt($ewallet_ir_password, $row['ewallet_pass']);

		if ($ewallet_ir_password != $row['ewallet_pass']) {
			$error = "Wrong E Wallet IR Password";
		}
	
	}
	
	
   
	
	////get the ewallet password of the paying ir
    
	
	////////////if there is no errors do the registraion and payment
    if ($error == "") {

        // Calculate My IR ID

        $sql = " SELECT ir_id AS last_ir_id FROM ir Where id=( SELECT MAX(id)  FROM ir ); ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $my_ir_id = $core->generateNextIRID($row['last_ir_id']);
		
		///get the prefix of the company from db
		//$sql = " SELECT ir_id AS last_ir_id FROM ir Where id=( SELECT MAX(id)  FROM ir ); ";
		$sql="SELECT  prefix FROM `prefix` WHERE 1 ORDER BY id DESC LIMIT 1";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
		
		// generate card for th new IR
		
		$prefix=$row['prefix']."-";
		$cardPIN=$_POST['card_pin'];
		$cardValue=0;
		/////card number is either brought form system from blank irid records , or if there is no blank irids we create a new card
		
		// get one of the blank irid cards from db
		$sql="SELECT id, cardnumber FROM scrachcards WHERE irid='' LIMIT 1";
        $result = $database_manager->query($sql);
       
		
		if( $row = mysqli_fetch_assoc($result)){
			$cardid=$row['id'];
			$cardno=$row['cardnumber'];
			$cardCmnt="New IR Existing Card Assignment";
			$sql="UPDATE scrachcards SET irid='".$my_ir_id."', cardpsw='".$cardPIN."',cardval='".$cardValue."',notes='".$cardCmnt."',status=1 WHERE id = '".$cardid."';";
			$result = $database_manager->query($sql);
			$row = mysqli_fetch_assoc($result);
		}else{
					$cardnumbernew=uniqid($prefix,false); 
			$cardCmnt="New IR Auto Generated Card";
			
			$sql="INSERT INTO `scrachcards`( `datetime`, `irid`, `cardnumber`, `cardpsw`, `cardval`, `notes`, `status`) VALUES ('".$core->getFormatedDateTime()."' ,'".$my_ir_id."' ,'".$cardnumbernew."' , '".$cardPIN."' ,'".$cardValue."','".$cardCmnt."',1)";
			$result = $database_manager->query($sql);
			$row = mysqli_fetch_assoc($result);
		}
		

		
        // Deduct Money
		if ($_POST && isset($_POST['rechargecardnumber']) && isset($_POST['rechargecardpsw']) && $_POST['rechargecardpsw'] !="" && $_POST['rechargecardnumber'] !="") {
			///////get the registration fees from db
			$sql = "SELECT registration_fees FROM configuration ;";
			$result = $database_manager->query($sql);
			$row = mysqli_fetch_assoc($result);
			$registration_fees = $row['registration_fees'];
			
			////FROM Card
			$sql = "UPDATE scrachcards SET cardval = cardval - " . $registration_fees . " "
                . " WHERE cardnumber = '" . $cardnumber . "';";
			$result=$database_manager->query($sql);
			//$database_manager->query($sql);
			
			$sql = "SELECT id,irid,cardval from scrachcards where cardnumber = '".$cardnumber."';";    
			$result = $database_manager->query($sql);
			if(!$result){
				$success=false;
				return $_SESSION["language"]->fundtransfer_msg_cantgetcardbalance;
			}
			
			$row = mysqli_fetch_assoc($result);
			$cardid = $row['id'];
			$cardnewbal = $row['cardval'];
			$cardcomment="Card is used with Referral IR: $referrer_ir_id to register new IR $my_ir_id";
			$cardIRID=$row['irid'];
			////add transaction record to card transactions
			$sql = "INSERT INTO `scrachcards_trans`(`datetime`, `ir_id`, `value`, `cardbalance`, `type`, `comment`, `cardid`) VALUES ('".$core->getFormatedDateTime()."','".$cardIRID."','".$registration_fees."','".$cardnewbal."','".$_SESSION["language"]->reg_cardtransactiontype."','".$cardcomment."','".$cardid."');";
		   $result= $database_manager->query($sql);
			
		}else{
			///////get the registration fees from db
			$sql = "SELECT registration_fees FROM configuration ";
			$result = $database_manager->query($sql);
			$row = mysqli_fetch_assoc($result);
			$registration_fees = $row['registration_fees'];
			
			////FROM eWallet 
			$sql = "UPDATE ir SET ewallet = ewallet - " . $registration_fees . " "
					. " WHERE ir_id = '" . $ewallet_ir_id . "'";
			$database_manager->query($sql);

			$sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
			$sql .= " VALUES ('" . $ewallet_ir_id . "', 'Registration Fees', '";
			$sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $registration_fees) . "', '" . (string) ($ewallet_ir_available_fund - $registration_fees) . "', '" . $my_ir_id . "')";
			$database_manager->query($sql);
		}
        // Calculate upline

        $upline_bu_id = "";

        $upline_bu_id = $core->calculateUpline($database_manager, $_POST['position'], $referrer_ir_id, $_POST['business_unit']);

        // INSERT INTO ir

        if ($_POST['day'] < 10) {
            $birth_date = $_POST['year'] . "-" . $_POST['month'] . "-0" . $_POST['day'];
        } else {
            $birth_date = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
        }

        $salt = $core->generateSalt();
        $login_pass_crypt = crypt($_POST['login_pass'], $salt);
        $ewallet_pass_crypt = crypt($_POST['ewallet_pass'], $salt);

        $sql = "INSERT INTO ir (ir_id, title, f_name, l_name, a_name, "
                . "email, mobile, phone, address, area, "
                . "city, country, valid_id, valid_id_type, "
                . "nationality, birth_date, beneficiary, "
                . "relationship, login_pass, ewallet_pass, "
                . "ewallet, total_ewallet, dcpts, "
                . "total_dcpts, rpts, total_rpts, "
                . "registration_date, last_renewal_date)"
                . " VALUES("
                . "'" . $my_ir_id . "', '" . $_POST['title'] . "', '" . $_POST['f_name'] . "', '" . $_POST['l_name'] . "', '" . $_POST['a_name'] . "', "
                . "'" . $_POST['email'] . "', '" . $_POST['mobile'] . "', '" . $_POST['phone'] . "', '" . $_POST['address'] . "', '" . $_POST['area'] . "', '" . $_POST['city'] . "', "
                . "'" . $_POST['country'] . "', '" . $_POST['valid_id'] . "', '" . $_POST['valid_id_type'] . "', '" . $_POST['nationality'] . "', "
                . "'" . $birth_date . "', '" . $_POST['beneficiary'] . "', '" . $_POST['relationship'] . "', '" . $login_pass_crypt . "', "
                . "'" . $ewallet_pass_crypt . "', '0', '0', "
                . "'0', '0', '0', '0', '" . $core->getFormatedDateTime() . "', '" . $core->getFormatedDate() . "'"
                . " ) ";
        if ($database_manager->query($sql)) {

            // INSERT INTO bu

            $string_to_add_to_children = "\'" . $my_ir_id . "-001\', \'" . $my_ir_id . "-002\', \'" . $my_ir_id . "-003\', ";
			////insert new row for bu001 for the new ir in bu table
            $sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "
                    . "left_abv, right_abv, left_children, right_children, "
                    . "parent_bu_id, position, is_qualified, referral_bu_id, "
                    . "position_to_referral) VALUES ( "
                    . " '" . $my_ir_id . "', '001', 0, 0, 0, 0, '\'" . $my_ir_id . "-002\', ', '\'" . $my_ir_id . "-003\', ', '" . $upline_bu_id . "', '" . $_POST['position'] . "', '0', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "
                    . " )";
            $database_manager->query($sql);
			////insert new row for bu002 for the new ir in bu table
            $sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "
                    . "left_abv, right_abv, left_children, right_children, "
                    . "parent_bu_id, position, is_qualified, referral_bu_id, "
                    . "position_to_referral) VALUES ( "
                    . " '" . $my_ir_id . "', '002', 0, 0, 0, 0, '', '', '\'" . $my_ir_id . "-001\', " . "', 'left', '0', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "
                    . " )";
            $database_manager->query($sql);
			////insert new row for bu003 for the new ir in bu table
            $sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "
                    . "left_abv, right_abv, left_children, right_children, "
                    . "parent_bu_id, position, is_qualified, referral_bu_id, "
                    . "position_to_referral) VALUES ( "
                    . " '" . $my_ir_id . "', '003', 0, 0, 0, 0, '', '', '\'" . $my_ir_id . "-001\', " . "', 'right', '0', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "
                    . " )";
            $database_manager->query($sql);

            // INSERT INTO dc

            $sql = "INSERT INTO dc (date, bu_id, left_dc, right_dc) VALUES ( "
                    . " '" . $core->getFormatedDate() . "', '" . $my_ir_id . "-001" . "', '0', '0' "
                    . " )";
            $database_manager->query($sql);

            $sql = "INSERT INTO dc (date, bu_id, left_dc, right_dc) VALUES ( "
                    . " '" . $core->getFormatedDate() . "', '" . $my_ir_id . "-002" . "', '0', '0' "
                    . " )";
            $database_manager->query($sql);

            $sql = "INSERT INTO dc (date, bu_id, left_dc, right_dc) VALUES ( "
                    . " '" . $core->getFormatedDate() . "', '" . $my_ir_id . "-003" . "', '0', '0' "
                    . " )";
            $database_manager->query($sql);

            // Update Parents children and Counter
            $children_column = $_POST['position'] . "_children";

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
			
		////Give referral the new registration commision to his ewallet as set in config
			
		$sql = "UPDATE ir SET ewallet = (ewallet + " . $new_reg_com . ") WHERE ir_id = '" . $referrer_ir_id . "' ";
        $database_manager->query($sql);
		///Get the Ewallet of Referral IR
		$sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $referrer_ir_id . "' ";
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);
		$Ref_ewallet = $row['ewallet'];
		/////transaction to Referal IR
        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $referrer_ir_id . "', 'New IR Registration Commission', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 + $new_reg_com) . "', '" . (string) ($Ref_ewallet + $new_reg_com) . "', '".$my_ir_id."')";
        $database_manager->query($sql);
		////////////////////////////////////////////////////////////////////////////////////////////////
			
            // Header Location Registration_success

            header("Location: index.php?page=register_success&new_ir_id=" . $my_ir_id);
        } else {
            $error = "Incorrect Information.";
        }
    }
}

$sql = "UPDATE configuration SET register_lock = 0";
$database_manager->query($sql);

$html_page->writeHeader();
?>

<div id="left-container">
    <div id="logo">
        <a href="index.php?page=dashboard"><img style="width: 90%;" src="images/testlogo.png"></a>
    </div>
</div>
<div id="right-container">
    <div id="top-bar">
    </div>
    <div id="header">
        <div id="page-title">
            <h1><?php echo $_SESSION["language"]->reg_pagehsetp2;?></h1>
        </div>
        <div id="header-menu">
        </div>
    </div>
    <div id="page">

        <script>

	
	function IsNumeric(input)
    {
        return (input - 0) == input && ('' + input).trim().length > 0;
    }
    function isPositiveInteger(str) {
        var n = ~~Number(str);
        return String(n) === str && n > 0;
    }
	
	/////get card info
	function getrechargecardbalance() {
        var y = document.forms["myform"]["rechargecardnumber"].value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                document.forms["myform"]["rechargecard_bal"].value = xhttp.responseText;
            }
        };
        xhttp.open("GET", "index.php?page=get_rechargecardinfo&id=" + y, true);
        xhttp.send();
    }
	//////////////////////////////////////////////////////////////////////////////////////
			//////pay using ewallet
            function validateForm(btnid)
            {
				console.log(btnid)
                var y = document.forms["myform"]["business_unit"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["position"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["title"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["f_name"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["l_name"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["a_name"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["email"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                if (validateEmail(y) == false) {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->reg_emailsisnotvalid;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["mobile"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["phone"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["address"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["area"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["city"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["country"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["valid_id"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["valid_id_type"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["nationality"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["year"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["month"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["day"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var year = document.forms["myform"]["year"].value;
                var month = document.forms["myform"]["month"].value;
                var day = document.forms["myform"]["day"].value;
                if (day < 10) {
                    day = '0' + day;
                }
                var birth_date = year + '-' + month + '-' + day;
                if (validateAge(birth_date) == false) {
                    document.getElementById("error").innerHTML = "You must me over 16 years old to register.";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["beneficiary"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["relationship"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                var y = document.forms["myform"]["login_pass"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                if (y.split('').length < 8) {
                    document.getElementById("error").innerHTML = "Passwords must be at least 8 characters.";
                    window.scrollTo(0, 0);
                    return false;
                }
                var z = document.forms["myform"]["login_confirm"].value;
                if (z == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                if (y != z) {
                    document.getElementById("error").innerHTML = "Login Password Mismatch.";
                    window.scrollTo(0, 0);
                    return false;
                }
				
                var y = document.forms["myform"]["ewallet_pass"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                if (y.split('').length < 8) {
                    document.getElementById("error").innerHTML = "Passwords must be at least 8 characters.";
                    window.scrollTo(0, 0);
                    return false;
                }
                var z = document.forms["myform"]["ewallet_confirm"].value;
                if (z == '') {
                    document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
                    window.scrollTo(0, 0);
                    return false;
                }
                if (y != z) {
                    document.getElementById("error").innerHTML = "E-Wallet Password Mismatch.";
                    window.scrollTo(0, 0);
                    return false;
                }
				/**********************************if payment from ewallet*******/
                if(btnid==1){
					console.log(btnid)
					var y = document.forms["myform"]["ewallet_ir_id"].value;
					if (y == '') {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
						window.scrollTo(0, 0);
						return false;
					}
					if (y.split('').length !== 8) {
						document.getElementById("error").innerHTML = "Invalid E Wallet IR ID.";
						window.scrollTo(0, 0);
						return false;
					}
					var y = document.forms["myform"]["ewallet_ir_password"].value;
					if (y == '') {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
						window.scrollTo(0, 0);
						return false;
					}
				}else{
				/***********************************************************************/
				console.log(btnid)
					var x = document.forms["myform"]["rechargecardnumber"].value;
					if (x == '') {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
						 window.scrollTo(0, 0);
						return false;
					}
					if (x.split('').length !== 16) {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidcardnumber;?> ";
						 window.scrollTo(0, 0);
						return false;
					}
					var y = document.forms["myform"]["rechargecardpsw"].value;
					if (!IsNumeric(y) || !isPositiveInteger(y)) {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidcardpsw;?>";
						 window.scrollTo(0, 0);
						return false;
					}
					if (y == '') {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
						 window.scrollTo(0, 0);
						return false;
					}
				}
				////verify card pin for new ir
				var y = document.forms["myform"]["card_pin"].value;
					if (!IsNumeric(y) || !isPositiveInteger(y)) {
						document.getElementById("error").innerHTML = "Card Pin Should be Numbers not Characters.";
						 window.scrollTo(0, 0);
						return false;
					}
					if (y == '') {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
						 window.scrollTo(0, 0);
						return false;
					}
					if (y.split('').length !== 4) {
						document.getElementById("error").innerHTML = "Card Pin Should be 4 numbers.";
						window.scrollTo(0, 0);
						return false;
					}
					var z = document.forms["myform"]["card_pin_confirm"].value;
					if (z == '') {
						document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
						window.scrollTo(0, 0);
						return false;
					}
					if (y != z) {
						document.getElementById("error").innerHTML = "Card Pin Mismatch.";
						window.scrollTo(0, 0);
						return false;
					}
                //return true;
				return confirm("<?php echo $_SESSION["language"]->confirmPayment;?>");
            }
            function validateEmail(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }
            function validateAge(birth_date) {
                return calculateAge(birth_date) >= 16;
            }
            function calculateAge(birth_date) {
                the_date = new Date(birth_date);
                var ageDifMs = Date.now() - the_date.getTime();
                var ageDate = new Date(ageDifMs); // miliseconds from epoch
                return Math.abs(ageDate.getUTCFullYear() - 1970);
            }
            function getIRName() {
                var y = document.forms["myform"]["ewallet_ir_id"].value;
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (xhttp.readyState == 4 && xhttp.status == 200) {
                        document.forms["myform"]["ewallet_ir_name"].value = xhttp.responseText;
                    }
                };
                xhttp.open("GET", "index.php?page=get_ir_name&id=" + y, true);
                xhttp.send();
            }
        </script>

        <p id = "error" > <?php
if ($error != "") {
    echo $error;
}
?></p>

        <form method = "post" name = "myform" onsubmit = "" >
            <h2>Placement Details:</h2>

            <label>Referrer IR ID <span class="astrisk"> *</span></label> 
            <input name="referrer_ir_id" type="text" value="<?php echo $referrer_ir_id; ?>"  autocomplete="off" readonly/> <br class="clear"/>

            <label>Referrer Name <span class="astrisk"> *</span></label> 
            <input name="referrer_name" type="text" value="<?php echo $referrer_name; ?>"  autocomplete="off" readonly/> <br class="clear"/>

            <?php if ($referrer_type == "2" || true) { ?>
                <label>Business Unit <span class="astrisk"> *</span></label> 
                <select id="business_unit" name="business_unit">
                    <option value="" <?php
                if ($_POST['business_unit'] == "") {
                    echo "selected";
                }
                ?> >-- SELECT --</option>
                            <?php foreach ($referrer_BUs AS $bu) { ?>
                        <option value="<?php echo $bu; ?>" <?php
                        if ($_POST['business_unit'] == $bu) {
                            echo "selected";
                        }
                                ?>><?php echo $bu; ?></option>
                            <?php } ?>
                </select>  <br class="clear"/>
            <?php } else { ?>
                <input type="hidden" name="business_unit" value="001" />
            <?php } ?>

            <label>Placement <span class="astrisk"> *</span></label> 
            <select name="position">
                <option value="" <?php
            if ($_POST['position'] == "") {
                echo "selected";
            }
            ?> >-- SELECT --</option>
                <option value="left" <?php
                if ($_POST['position'] == "left") {
                    echo "selected";
                }
            ?>>Left</option>
                <option value="right" <?php
                if ($_POST['position'] == "right") {
                    echo "selected";
                }
            ?>>Right</option>
            </select>  <br class="clear"/>

            <div class="sep dotted"></div>

            <h2>Personal Details:</h2>

            <label>Title <span class="astrisk"> *</span></label> 
            <select name="title">
                <option value="" <?php
                if ($_POST['title'] == "") {
                    echo "selected";
                }
            ?> >-- SELECT --</option>
                <option value="Mr." <?php
                if ($_POST['title'] == "Mr.") {
                    echo "selected";
                }
            ?>>Mr.</option>
                <option value="Mrs." <?php
                if ($_POST['title'] == "Mrs.") {
                    echo "selected";
                }
            ?>>Mrs.</option>
                <option value="Miss" <?php
                if ($_POST['title'] == "Miss") {
                    echo "selected";
                }
            ?>>Miss</option>
            </select> <br class="clear"/>

            <label>First Name <span class="astrisk"> *</span></label> 
            <input name="f_name" type="text" value="<?php echo $_POST['f_name']; ?>"  autocomplete="off" /> <br class="clear"/> 

            <label>Last Name <span class="astrisk"> *</span></label> 
            <input name="l_name" type="text" value="<?php echo $_POST['l_name']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Arabic Name <span class="astrisk"> *</span></label> 
            <input name="a_name" type="text" value="<?php echo $_POST['a_name']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Mobile <span class="astrisk"> *</span></label> 
            <input name="mobile" type="text" value="<?php echo $_POST['mobile']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Phone <span class="astrisk"> *</span></label> 
            <input name="phone" type="text" value="<?php echo $_POST['phone']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Email <span class="astrisk"> *</span></label> 
            <input name="email" type="text" value="<?php echo $_POST['email']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Address <span class="astrisk"> *</span></label> 
            <textarea name="address" ><?php echo $_POST['address']; ?></textarea> <br class="clear"/> 

            <label>District <span class="astrisk"> *</span></label> 
            <input name="area" type="text" value="<?php echo $_POST['area']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>City <span class="astrisk"> *</span></label> 
            <input name="city" type="text" value="<?php echo $_POST['city']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Country <span class="astrisk"> *</span></label> 
            <select name="country">
              
                <option value="Egypt" <?php
                if ($_POST['country'] == "Egypt") {
                    echo "selected";
                }
            ?> >Egypt</option>
               
            </select><br class="clear"/> 

            <label>Valid ID <span class="astrisk"> *</span></label> 
            <input name="valid_id" type="text" value="<?php echo $_POST['valid_id']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Valid ID Type <span class="astrisk"> *</span></label> 
            <select name="valid_id_type">
                <option value="National ID" >National ID</option>
                <option value="Passport" >Passport</option>
                <option value="Other" >Other</option>
            </select> <br class="clear"/> 

            <label>Nationality <span class="astrisk"> *</span></label> 
            <input name="nationality" type="text" value="<?php echo $_POST['nationality']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Birth Date <span class="astrisk"> *</span></label> 
            <select name="year" class="year">
                <option value="" <?php
                if ($_POST['year'] == "") {
                    echo "selected";
                }
            ?> >--</option>
                        <?php
                        for ($i = 2016; $i > 1940; $i--) {
                            echo "<option value='$i'";
                            if ($_POST['year'] == $i) {
                                echo "selected";
                            }
                            echo ">$i</option>";
                        }
                        ?>
            </select>
            <select name="month" class="month">
                <option value="" <?php
                        if ($_POST['month'] == "") {
                            echo "selected";
                        }
                        ?> >--</option>
                <option value='01' <?php
                if ($_POST['month'] == "01") {
                    echo "selected";
                }
                        ?>>January</option><option value='02' <?php
                        if ($_POST['month'] == "02") {
                            echo "selected";
                        }
                        ?>>February</option><option value='03' <?php
                        if ($_POST['month'] == "03") {
                            echo "selected";
                        }
                        ?>>March</option><option value='04' <?php
                        if ($_POST['month'] == "04") {
                            echo "selected";
                        }
                        ?> >April</option><option value='05' <?php
                        if ($_POST['month'] == "05") {
                            echo "selected";
                        }
                        ?>>May</option><option value='06' <?php
                        if ($_POST['month'] == "06") {
                            echo "selected";
                        }
                        ?> >June</option>
                <option value='07' <?php
                        if ($_POST['month'] == "07") {
                            echo "selected";
                        }
                        ?>>July</option><option value='08' <?php
                        if ($_POST['month'] == "08") {
                            echo "selected";
                        }
                        ?>>August</option><option value='09' <?php
                        if ($_POST['month'] == "09") {
                            echo "selected";
                        }
                        ?>>September</option><option value='10' <?php
                        if ($_POST['month'] == "10") {
                            echo "selected";
                        }
                        ?>>October</option><option value='11' <?php
                        if ($_POST['month'] == "11") {
                            echo "selected";
                        }
                        ?>>November</option><option value='12' <?php
                        if ($_POST['month'] == "12") {
                            echo "selected";
                        }
                        ?> >December</option>
            </select>  
            <select name="day" class="day">
                <option value="" <?php
                        if ($_POST['day'] == "") {
                            echo "selected";
                        }
                        ?> >--</option>
                        <?php
                        for ($i = 1; $i < 32; $i++) {
                            echo "<option value='$i'";
                            if ($_POST['day'] == $i) {
                                echo "selected";
                            }
                            echo ">$i</option>";
                        }
                        ?>
            </select> 
            <br class="clear"/> 

            <label>Beneficiary Name <span class="astrisk"> *</span></label> 
            <input name="beneficiary" type="text" value="<?php echo $_POST['beneficiary']; ?>"  autocomplete="off" />  <br class="clear"/> 

            <label>Relationship <span class="astrisk"> *</span></label> 
            <select name="relationship">
                <option value="" <?php
                        if ($_POST['relationship'] == "") {
                            echo "selected";
                        }
                        ?> >-- SELECT --</option>
                <option value="Father" <?php
                if ($_POST['relationship'] == "Father") {
                    echo "selected";
                }
                        ?>>Father</option>
                <option value="Mother" <?php
                if ($_POST['relationship'] == "Mother") {
                    echo "selected";
                }
                        ?>>Mother</option>
                <option value="Son" <?php
                if ($_POST['relationship'] == "Son") {
                    echo "selected";
                }
                        ?>>Son</option>
                <option value="Daughter" <?php
                if ($_POST['relationship'] == "Daughter") {
                    echo "selected";
                }
                        ?>>Daughter</option>
                <option value="Brother" <?php
                if ($_POST['relationship'] == "Brother") {
                    echo "selected";
                }
                        ?>>Brother</option>
                <option value="Sister" <?php
                if ($_POST['relationship'] == "Sister") {
                    echo "selected";
                }
                        ?>>Sister</option>
                <option value="Spouse" <?php
                if ($_POST['relationship'] == "Spouse") {
                    echo "selected";
                }
                        ?>>Spouse</option>
                <option value="Other" <?php
                if ($_POST['relationship'] == "Other") {
                    echo "selected";
                }
                        ?>>Other</option>
            </select> <br class="clear"/> 

            <div class="sep dotted"></div>

            <h2>Account Details:</h2>
            <p>Passwords must be at least 8 characters.</p>
            <label>Login Password <span class="astrisk"> *</span></label> 
            <input name="login_pass" type="password" value=""  autocomplete="off" />  <br class="clear"/> 

            <label>Confirm Login Password <span class="astrisk"> *</span></label> 
            <input name="login_confirm" type="password" value=""  autocomplete="off" />  <br class="clear"/> 

            <label>E-Wallet Password <span class="astrisk"> *</span></label> 
            <input name="ewallet_pass" type="password" value=""  autocomplete="off" />  <br class="clear"/> 

            <label>Confirm E-Wallet Password <span class="astrisk"> *</span></label> 
            <input name="ewallet_confirm" type="password" value=""  autocomplete="off" />  <br class="clear"/> 
			
			<label>Enter Your Card PIN <span class="astrisk"> *</span></label> 
            <input name="card_pin" type="password" value=""  autocomplete="off" />  <br class="clear"/> 

            <label>Confirm Your Card PIN <span class="astrisk"> *</span></label> 
            <input name="card_pin_confirm" type="password" value=""  autocomplete="off" />  <br class="clear"/> 

            <div class="sep dotted"></div>

            <h2>Payment Details:</h2>

            <div id="ewallet_payment">

                <div class="payment_icon"><i class="fa fa-money fa-fw"></i></div>
                <h2>Pay using E Wallet:</h2>
                <label>IR ID <span class="astrisk"> *</span></label> 
                <input name="ewallet_ir_id" type="text" value="<?php echo $ewallet_ir_id; ?>"  autocomplete="off" onchange="getIRName();"/> <br class="clear"/>

                <label>IR Name <span class="astrisk"> *</span></label> 
                <input name="ewallet_ir_name" type="text" value=""  autocomplete="off" readonly /> <br class="clear"/>

                <label>E-Wallet Password <span class="astrisk"> *</span></label> 
                <input name="ewallet_ir_password" type="password" value=""  autocomplete="off"/> <br class="clear"/>

                <input name = "step" type = "hidden" value = "2"  autocomplete = "off" /> <br class = "clear" />

                <div class="sep"></div>

				<button id="payewallet_btn" onclick="return validateForm(1);" ><i class="fa fa-check-square fa-fw"></i>confirm Pay & Register</button>
               <!-- <button type="submit"><i class="fa fa-check-square fa-fw"></i>Pay & Register</button>-->
			
            </div>
		
            <div id="creditcard_payment">
                <div class="payment_icon"><i class="fa fa-credit-card fa-fw"></i></div>
                <h2><?php echo $_SESSION["language"]->payusing ." ".$_SESSION["language"]->virtualcard;?></h2>
                <!--<p class="error">Credit Card Payments are not available at the time being.</p>--->
				<!--<form method="post" name="cardform1" onsubmit="return validateForm1();" >--->
					<!--<h3 style="tex-align:center;"><?php //echo $_SESSION["language"]->fundtransfer_rechargeewalletH;?></h3>--->
					<label><?php echo $_SESSION["language"]->virtualcard;?><span class="astrisk"> *</span></label> 
					<input name="rechargecardnumber" type="text" autocomplete="off" value="<?php //echo $cardnumber; ?>" onchange="getrechargecardbalance();"/>  <br class="clear"/>
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
					<button type="submit" id="paycard_btn" onclick="return validateForm(2);" class="button"><i class="fa fa-check-square fa-fw"></i><?php echo $_SESSION["language"]->payandregister;?></button>
				<!--</form>-->
                
            </div>
		</form> 
            <div class="sep dotted"></div>

        
        <?php $html_page->writeFooter(); ?>