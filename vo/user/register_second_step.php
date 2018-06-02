<?php

if(!session_id()){
	session_start();
}

/*load language session class*/

if($_COOKIE["lang"]=="ar" && !isset($_SESSION['language'])){

		 $_SESSION['language'] = new Arabic_language;
	}else{	
		  $_SESSION['language'] = new English_language;
}

///load header*/

$html_page->writeHeader();

//////////////////////////*/

////lock the register lock at configuration, used to allow one signup at a time to not to have queries running for two signups causing errors in DB*/

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

////For DEBUGGING ,REMOVE when moving to server*/

$sql = "UPDATE configuration SET register_lock = 0";

$database_manager->query($sql);

////////////////////////////////////

///initiate variables*/

$isAffiliate=0;/////if signup was accessed through affiliate link, this flag is 1, if manual reg flag is 0, default 0*/

$baseNumberOfBUs=7; //// business units base is the number of bu used in the system for each ir , original was 3 , new is 7*/



///////this is new feature to be added to configuration table and back office, value should be dependant on configuration table values*/

$isFreeRegistration=1;///registration is free*/

$isQualifiedByDefault=2;////created account is qualified by default flag 1*/

$AllowCardPinAtReg=1; ////allow user to enter his card pin at registration, if not allowed , the default PIN is 1234*/



///////select from configuration table*/

$sqla = "SELECT registration_fees,is_qualified_free,AllowCardPinAtReg,generategiftcard FROM configuration ";

$resulta = $database_manager->query($sqla);

$rowa = mysqli_fetch_assoc($resulta);



///check if registration fees is zero to set the flag to free registration, else set it to 0*/

$register_fees = (int)$rowa['registration_fees'];

$registration_fees=$register_fees;///used in code below */



if ($register_fees == 0) {

	$isFreeRegistration=1;///registration is free 1;*/

}else{

	$isFreeRegistration=0;///registration is NOT free 0;*/

}

///check if Qualification fees is zero to set the flag to free qualification, else set it to 0*/

$is_qualified_free = $rowa['is_qualified_free'];

if ($is_qualified_free == "1") {

	$isQualifiedByDefault=1;///registration is NOT free 1;*/

}else if($is_qualified_free == "2"){

	$isQualifiedByDefault=2;///registration is NOT free 1;*/

}else{

	$isQualifiedByDefault=0;///registration is free 0;*/

}

///check if Allow user to enter card pin at registration is zero to set the flag , else set it to 0*/

$AllowCardPinAtReg = (int)$rowa['AllowCardPinAtReg'];

////Generate gift card Flag for the new IR , 0 no ,1 yes*/

$generategiftcard = (int)$rowa['generategiftcard'];



////source of cities and governorates of egypt https://www.ask-aladdin.com/Egypt_cities/*/

/////initialize List of countries array, for Country Dropdown(not yet implemented)*/

$countries = array("Egypt","Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador",  "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");



/////initialization of Governorate array*/

$Egypt_Governorate=array("Cairo","Alexandria","Giza","Qalyubia","Port Said","Suez","Gharbia","Dakahlia","Asyut","Fayoum","Sharqia","Ismailia","Aswan","Beheira","Minya","Damietta","Luxor","Qena","Beni Suef","Sohag","Monufia","Red Sea","Kafr el-Sheikh","North Sinai","Matruh","New Valley","South Sinai");



/////initialization of Cities array (not yet implemented)*/

$Egypt_cities=array("Cairo","Alexandria","Giza","Shubra el-Khema","Port Said","Suez","El Mahalla el Kubra","El Mansoura","Tanta","Asyut","Fayoum","Zagazig","Ismailia","Khusus","Aswan","Damanhur","El-Minya","Damietta","Luxor","Qena","Beni Suef","Sohag","Shibin el-Kom","Hurghada","Banha","Kafr al-Sheikh","Mallawi","El Arish","Belbeis","10th of Ramadan City","Marsa Matruh","Mit Ghamr","Kafr el-Dawwar","Qalyub","Desouk","Abu Kabir","Girga","Akhmim","El-Matareya","Edko","Bilqas","Zifta","Samalut","Menouf","Senbellawein","Tahta","Bush","Ashmoun","Manfalut","Senuris","Beni Mazar","Faqous","Talkha","Armant","Maghagha","Manzala","Dairut","Kom Ombo","Kafr al-Zayat","Abu Tig","Qis","Edfu","Rosetta","Esna","Dikirnis","Abnub","Tima","Beila","El-Kanateral-Khiria","Al-Fashn","Al-Mansha","Al-Kareen","El-Gamalia","Fuwa","Minya al-Qamh","Kharga","Qus","Khanka","Abu Qirqas","Biba","Samannoud","Minyet al-Nasr","Shibin al-Qanater","Ibshawai","Sherbin","Drib Nigm","Basyoun","Sers el-Lyan","Dishna","Al-Hamool","Farshut","Tala","Ash-Shuhada","Tamiya","Mashtul el-Sook","Sadat City","El-Ghanayem","Itsa","Al-Baliyana","Hosh Issa","Matai","Juhayna","Sidi Salem","Naj Hammadi","Quesna","Hehya","Abul Matamir","El Ubour","El-Badari","Al-Kanayat","At-Tall al-Kabir","El-Delengat","Al-Hammam","Tukh","Bagour","Etay el-Barud","Deir Mawas","Baltim","Abu Hammad","Abu Hummus","Nabaroh","Sharm el-Sheikh","Daraw","Al-Maragha","Sumusta al-Waqf","Al-Wasta","Ihnasiya","Kom Hamadah","Al-Quseir","Qallin","Birkat al-Sab","Safaga","Ezbet el-Borg","Faraskur","Al-Ibrahimiya","El-Santa","Ras Gharib","Sahel Selim","Dar as-Salam","Rafah","Mit Salsil","Al-Husseinieh","Kafr el-Batikh","Kafr Saqr","Bani Ubayd","El-Qantara","Metoubes","El-Rahmaniyah","Shubrakhit","El-Mahmoudiyah","Al-Waqf","New Damietta City","Qaha","Kotoor","Abu Suweir-el-Mahatta","Kafr Shukr","Kafr Saad","Qift","Fayed","Saqultah","Wadi al-Natrun","Naqadah","As-Sarw","Awlad Saqr","Sidi Barrani","Al-Basaliyah Bahri","Badr","Sedfa","El-Qantara ash-Sharqiya","Ar-Ruda","Mut","Al-Tur","New Salhia","Ash-Shaykh Zawid","Riyadh","New Beni Suef","Aga","Ad-Dabah","Al-Zarqa","As-Sibaiyah Gharb","Siwa","El-Idwa","Yusuf as-Siddiq","Al-Bayadiyah");



///set the error var to nothing (used to complete the payment and registeration )*/

$error == "";

///////////////////////////////////////////////////////////////////////////////*/



///if either step is not 1 or session refid is not set go to step1 form*/

if($_GET['step'] != "1" && (!isset($_SESSION["RefIRID"]) || !isset($_GET['refir'])) && sizeof($_SESSION["RefIRID"])!=8 ){

	//header("Location: index.php?page=register_first_step");

}else{



}



////loading the page first time, set the referrer ID variable and isaffiliate var */
////if ref id is from session variable*/

if( (isset($_SESSION["RefIRID"]) && $_SESSION["RefIRID"]!="") || (isset($_GET['refir']) && $_GET['refir']!="" )  ){

	if(isset($_GET['refir']) && $_GET['refir']!="" ){
		$referrer_ir_id = strtoupper($_GET['refir']);
	}else{
		$referrer_ir_id = strtoupper($_SESSION["RefIRID"]);
	}
	

	$isAffiliate=1;

}else{

	$isAffiliate=0;

}
////check if the url includes referral variable
if (isset($_GET['refir']) && $_GET['refir'] != "") {
	$referrer_ir_id=strtoupper($_GET['refir']);
	////if ther is no GET refir , check the POST refir wher it comes from step 1
}else if (isset($_POST['referrer_ir_id']) && $_POST['referrer_ir_id'] != ""){
	$referrer_ir_id = strtoupper($_POST['referrer_ir_id']);
	///else use the company (RS) code as the referral ir
}else{
	$referrer_ir_id ="BE000010";
}


////loading the page first time, set the referrer ID variable and isaffiliate var */

///if ref id is from post */

if( (isset($_POST['referrer_ir_id']) && $_POST['referrer_ir_id']!="") || (isset($_GET['refir']) && $_GET['refir']!="" ) ){
	if(isset($_GET['refir']) && $_GET['refir']!="" ){
		$referrer_ir_id = strtoupper($database_manager->realEscapeString($_GET['refir']));
	}else{
		$referrer_ir_id = strtoupper($database_manager->realEscapeString($_POST['referrer_ir_id']));
	}
	$isAffiliate=0;

}



//////////////After step 2 Submit*/



/*****************/

////if is paid through ewallet, set the referrer irid */

if(isset($_POST['ewallet_ir_id']) && $_POST['ewallet_ir_id']!=""){

	$referrer_ir_id = strtoupper($database_manager->realEscapeString($_POST['referrer_ir_id']));

}

/******************/



$referrer_name = $database_manager->realEscapeString($_POST['referrer_name']);

$referrer_type = "";

$referrer_BUs = array();



if(isset($_POST['ewallet_ir_id']) && $_POST['ewallet_ir_id']!=""){

$ewallet_ir_id = strtoupper($_POST['ewallet_ir_id']);

}



if(isset($_POST['rechargecardnumber']) && $_POST['rechargecardnumber']!=""){

	$cardnumber=$_POST['rechargecardnumber'];

}

///////////get the qualified bu of the ref ir to be used later , and to confirm that the ref ir has bu in the bu table*/

$sql_BUs = "SELECT b.is_qualified, CONCAT(r.title, ' ', r.f_name, ' ', r.l_name) AS name, b.code FROM bu b "

        . " LEFT OUTER JOIN ir r ON b.ir_id = r.ir_id "

        . " WHERE b.ir_id = '" . $referrer_ir_id . "' ORDER BY b.code ASC";

$result_BUs = $database_manager->query($sql_BUs);

//////if there is no bu's for the ref ir go back to step 1 with error*/

//die(mysqli_num_rows($result_BUs));*/

if (mysqli_num_rows($result_BUs) == 0) {

    header("Location: index.php?page=register_first_step&error=1");

}



if (mysqli_num_rows($result_BUs) != 7) {

    header("Location: index.php?page=register_first_step&error=1");

}

$row_BUs = mysqli_fetch_assoc($result_BUs);

$referrer_type = $row_BUs['is_qualified'];

$referrer_name = $row_BUs['name'];



/////get new registration commission from config (tor provide commission for ref ir for Registraion ONLY to the referral)*/

$sql = "SELECT new_reg_com FROM configuration ";

$result = $database_manager->query($sql);

$row = mysqli_fetch_assoc($result);

$new_reg_com = $row['new_reg_com'];

////////

/////create array of business units codes for bu dropdown menu*/

//$referrer_BUs = array("001", "002", "003", "004", "005", "006", "007");*/

$referrer_BUs = array("004", "005", "006", "007");



//////////when user submit the step 2 form (THIS FORM) (payment)*/

if ($_POST['step'] == "2") {

	//////////sanitaize the new ir posted data*/

	$_POST['title'] = $database_manager->realEscapeString($_POST['title']);

	$_POST['business_unit'] = $database_manager->realEscapeString($_POST['business_unit']);

	$_POST['position'] = $database_manager->realEscapeString($_POST['position']);

    $_POST['title'] = $database_manager->realEscapeString($_POST['title']);

    /*$_POST['f_name'] = $database_manager->realEscapeString($_POST['f_name']);*/

    $_POST['f_name'] = $database_manager->realEscapeString($_POST['f_name']);

    $_POST['l_name'] = $database_manager->realEscapeString($_POST['l_name']);

    $_POST['a_name'] = $database_manager->realEscapeString($_POST['a_name']);

	$_POST['mobile'] = $database_manager->realEscapeString($_POST['mobile']);

	$_POST['phone'] = $database_manager->realEscapeString($_POST['phone']);

	$_POST['email'] = $database_manager->realEscapeString($_POST['email']);

    $_POST['nationality'] = $database_manager->realEscapeString($_POST['nationality']);

    $_POST['valid_id'] = $database_manager->realEscapeString($_POST['valid_id']);

    $_POST['beneficiary'] = $database_manager->realEscapeString($_POST['beneficiary']);

    $_POST['address'] = $database_manager->realEscapeString($_POST['address']);

    $_POST['city'] = $database_manager->realEscapeString($_POST['city']);

    $_POST['area'] = $database_manager->realEscapeString($_POST['area']);

	///bank info

	$_POST['Bank_name'] = $database_manager->realEscapeString($_POST['Bank_name']);

	$_POST['Bank_branch'] = $database_manager->realEscapeString($_POST['Bank_branch']);

	$_POST['Bank_account'] = $database_manager->realEscapeString($_POST['Bank_account']);

	$_POST['Bank_owner'] = $database_manager->realEscapeString($_POST['Bank_owner']);

	$_POST['Bank_owner_phonenumber'] = $database_manager->realEscapeString($_POST['Bank_owner_phonenumber']);

	//vodafone cash info

	$_POST['vf_cash_number'] = $database_manager->realEscapeString($_POST['vf_cash_number']);

	$_POST['vf_cash_name'] = $database_manager->realEscapeString($_POST['vf_cash_name']);

	//$_POST['Bank_owner'] = $database_manager->realEscapeString($_POST['Bank_owner']);*/

	$_POST['selectpaymentmethod']=(int)$database_manager->realEscapeString($_POST['selectpaymentmethod']);

	/*Server side Double validation*/

	/* Validate Email is not doublicated */

    if ($core->checkEmailDuplicate($database_manager, $_POST['email']) > 0) {

        $error = "This email is associated with another account.";

    }

	

	// Validate Payment method if registration is not free*/

	

	////if registration fees =0 (free)*/

	if(!$isFreeRegistration){

		// validate card pin is at correct length and matches the correct pasw and sanitize it, if registration is NOT Free, and then if user selected to pay using gift card*/

		if($_POST['selectpaymentmethod']==2){///user select to pay using giftcard*/

			if(strlen($_POST['card_pin'])==4){

				$_POST['card_pin'] = $database_manager->realEscapeString($_POST['card_pin']);

				/////check if card password is correct*/

				$cardpsw=$_POST['rechargecardpsw'];

				$cardnumber=$_POST['rechargecardnumber'];

				$error= $core->matchcardpsw($cardnumber,$cardpsw,$database_manager,3);

				if($error == ""){

					///check the card balance */

					/////check the card balance */

					$card_available_fund=$core->getcardbalance($cardnumber,$database_manager,0);

					if ($card_available_fund < $registration_fees) {

						$error = $_SESSION["language"]->cart_msg_moneynotenough; 

					}

				}

				

			}else{

				$error="Card Pin should be 4 numbers.";

			}

		}else if($_POST['selectpaymentmethod']==1){///user select to pay using ewallet*/

					////////check the ewallet balance */

			$sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $ewallet_ir_id . "' ";

			$result = $database_manager->query($sql);

			$row = mysqli_fetch_assoc($result);

			$ewallet_ir_available_fund = $row['ewallet'];



			if ($ewallet_ir_available_fund < $registration_fees) {

				$error = $_SESSION["language"]->cart_msg_moneynotenough; 

			}

			/////get the password of the ewallet*/

			$ewallet_ir_password = $_POST['ewallet_ir_password'];

			////clean the passwords of ewallet and its ir for paying , ir ewallet password */

			$ewallet_ir_password = stripslashes($ewallet_ir_password);

			$ewallet_ir_password = $database_manager->realEscapeString($ewallet_ir_password);

			$ewallet_ir_id = $database_manager->realEscapeString($ewallet_ir_id);

			

			//////check the paying ir ewallet password is correct*/

			$sql = "SELECT ewallet_pass FROM ir WHERE ir_id='$ewallet_ir_id'";

			$result = $database_manager->query($sql);

			$row = mysqli_fetch_assoc($result);



			$ewallet_ir_password = crypt($ewallet_ir_password, $row['ewallet_pass']);



			if ($ewallet_ir_password != $row['ewallet_pass']) {

				$error = "Wrong E Wallet IR Password";

			}

		}

	}///end validate payment method balance and passwords, if registration is not free*/

	



	////////////if there is no errors do the registraion and payment*/

    if ($error == "") {



        // Calculate My IR ID*/



        $sql = " SELECT ir_id AS last_ir_id FROM ir Where id=( SELECT MAX(id)  FROM ir ); ";

        $result = $database_manager->query($sql);

        $row = mysqli_fetch_assoc($result);

        $my_ir_id = $core->generateNextIRID($row['last_ir_id']);

		

		///get the prefix of the company from db*/

		//$sql = " SELECT ir_id AS last_ir_id FROM ir Where id=( SELECT MAX(id)  FROM ir ); ";*/

		$sql="SELECT  prefix FROM `prefix` WHERE 1 ORDER BY id DESC LIMIT 1";

        $result = $database_manager->query($sql);

        $row = mysqli_fetch_assoc($result);

		

		////if generate gift card flag in configuration is 1 */

		// generate card for th new IR

		if($generategiftcard){

			$prefix=$row['prefix']."-";

			$cardPIN=$_POST['card_pin'];

			$cardValue=0;

			/////card number is either brought form system from blank irid records , or if there is no blank irids we create a new card*/

		

			// get one of the blank irid cards from db*/

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

		}///end if generate gift card flag is true*/



		

		////if registration is NOT free deduce the money according to user selected payment method*/

		if(!$isFreeRegistration){

			// Deduct Money  */

			///user select to pay using giftcard*/

			if ($_POST['selectpaymentmethod']==2 && $_POST && isset($_POST['rechargecardnumber']) && isset($_POST['rechargecardpsw']) && $_POST['rechargecardpsw'] !="" && $_POST['rechargecardnumber'] !="") {

				///////get the registration fees from db/*/

				$sql = "SELECT registration_fees FROM configuration ;";

				$result = $database_manager->query($sql);

				$row = mysqli_fetch_assoc($result);

				$registration_fees = $row['registration_fees'];

				

				////FROM Card

				///deduce the card balance with registraiton fees*/

				$sql = "UPDATE scrachcards SET cardval = cardval - " . $registration_fees . " "

					. " WHERE cardnumber = '" . $cardnumber . "';";

				$result=$database_manager->query($sql);

				

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

				

				////add transaction record to card transactions*/

				$sql = "INSERT INTO `scrachcards_trans`(`datetime`, `ir_id`, `value`, `cardbalance`, `type`, `comment`, `cardid`) VALUES ('".$core->getFormatedDateTime()."','".$cardIRID."','".$registration_fees."','".$cardnewbal."','".$_SESSION["language"]->reg_cardtransactiontype."','".$cardcomment."','".$cardid."');";

				$result= $database_manager->query($sql);

				

			}else if($_POST['selectpaymentmethod']==1){ ///user selected to pay using ewallet*/

				///////get the registration fees from db*/

				$sql = "SELECT registration_fees FROM configuration ";

				$result = $database_manager->query($sql);

				$row = mysqli_fetch_assoc($result);

				$registration_fees = $row['registration_fees'];

				

				////FROM eWallet */

				$sql = "UPDATE ir SET ewallet = ewallet - " . $registration_fees . " "

						. " WHERE ir_id = '" . $ewallet_ir_id . "'";

				$database_manager->query($sql);



				$sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";

				$sql .= " VALUES ('" . $ewallet_ir_id . "', 'Registration Fees', '";

				$sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $registration_fees) . "', '" . (string) ($ewallet_ir_available_fund - $registration_fees) . "', '" . $my_ir_id . "')";

				$database_manager->query($sql);

			}

		}///end if registration is Not free to reduce money from payment option*/

		

		//////POSITION of NEW IR code to position the new IR , we have two options either automatic or manual///////////////////////////////////////// */

		

		///if automatic , means automatic select the bu and select the position to place the new ir*/

		////automatic criteria : start at position left bu 004 for the referral to find empty slot at level1 , if not found move to the level 2 start at left and find free slot in level 2 to place the ir and so on moving to levels until the deepiest level ever. */

		  // Calculate upline*/

		////loop through level 1 to find a free slot starting at bu004 left of the referral ir, the free slot means there is no data in position_children in bu table*/

		if(isset($_POST['business_unit']) && $_POST['business_unit']=="Auto" && isset($_POST['position']) && $_POST['position']=="Auto"){///if user selected automatic positioning and bu*/

		//////////////////////////////////AUTOMATIC positionaning of new Ir CASE 1 - BU-auto , Position- Auto//////////////////////////////////////////////////////////*/
			//die("pos auto , bu auto");
			$bu_arr=array("004","005","006","007");

			$uplineObj=$core->findFreeSlotinDownlinelevels($database_manager,$referrer_ir_id,$bu_arr,$level);

			$upline_id=$uplineObj["upline"]["irid"];

			$upline_bu=$uplineObj["upline"]["bu"];

			$upline_position=$uplineObj["upline"]["position"];


			$upline_irbu_level=$uplineObj["level"];

			////assign upline buid var*/

			$_POST['position']=$upline_position;

			$_POST['business_unit']=$upline_bu;

			

			$upline_bu_id = "";

			$upline_bu_id=$upline_id . "-" . $upline_bu;

	
			//die($upline_bu_id);
		}else{
			////case 4 - Position- Auto BU not auto*/
			///in this case we will check at empty slot at left first for down level 1 then right if empty slot exist in we set the position where the empty slot available, if level 1 is full find slot in level 2 and so....
			if(isset($_POST['position']) && $_POST['position']=="Auto" && isset($_POST['business_unit']) && ($_POST['business_unit'] == "004" || $_POST['business_unit'] == "005" || $_POST['business_unit'] == "006" || $_POST['business_unit'] == "007")  ){
				///check downline left of selected bu of ref-ir if empty set postion to left if not check right position 
				//die("pos auto , bu not auto");
				////check only the first level down left and right , if not set the position to left
				$bu_arr=array($_POST['business_unit']);
				$uplineObj=$core->searchEmpgySlotBelowIR($database_manager,$referrer_ir_id,$bu_arr);
				if($uplineObj){
					$_POST['position']=$uplineObj['position'];
				}elseif($uplineObj==false){///if not found in first level position the new ir in the left
					$_POST['position']="left";
				}
				
			}
			//////////////////////////////////Manual positionaning of new Ir//////////////////////////////////////////////////////////*/
			///Case 2 - BU-auto Position Left*/
			if(isset($_POST['business_unit']) && $_POST['business_unit']=="Auto" && isset($_POST['position']) && $_POST['position']=="left"){
			//die("pos Left , bu auto");
				$_POST['business_unit']="004";
				//die($_POST['business_unit'])
			}
			///Case 3 - BU-auto Position Right*/
			if(isset($_POST['business_unit']) && $_POST['business_unit']=="Auto" && isset($_POST['position']) &&  $_POST['position']=="right"){
				//die("pos right , bu auto");
				$_POST['business_unit']="007";
				//die($_POST['business_unit']);
			}
			
			/*assign upline buid var*/

			$upline_bu_id = "";
			
			$upline_bu_id = $core->calculateUpline($database_manager, $_POST['position'], $referrer_ir_id, $_POST['business_unit']);
			//die($upline_bu_id);
		}

		//die($referrer_ir_id."-".$_POST['business_unit']."  ref position ".$_POST['position']);

		

		// INSERT INTO ir

		

		////formulate the birthdate var

        if ($_POST['day'] < 10) {

            $birth_date = $_POST['year'] . "-" . $_POST['month'] . "-0" . $_POST['day'];

        } else {

            $birth_date = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];

        }

		

		////encrypt the login password

		$_SESSION["ir_loginpsw"]=$_POST['login_pass'];

        $salt = $core->generateSalt();

        $login_pass_crypt = crypt($_POST['login_pass'], $salt);

        /*******$ewallet_pass_crypt = crypt($_POST['ewallet_pass'], $salt);****/
		$ewallet_pass_crypt="";


        $sql = "INSERT INTO ir (ir_id, title, f_name, l_name, a_name, "

                . "email, mobile, phone, address, area, "

                . "city, country, valid_id, valid_id_type, "

                . "nationality, birth_date, beneficiary, "

                . "relationship, login_pass, ewallet_pass, "

                . "ewallet, total_ewallet, dcpts, "

                . "total_dcpts, rpts, total_rpts, "

                . "registration_date, last_renewal_date, isactivated)"

                . " VALUES("

                . "'" . $my_ir_id . "', '" . $_POST['title'] . "', '" . $_POST['f_name'] . "', '" . $_POST['l_name'] . "', '" . $_POST['a_name'] . "', "

                . "'" . $_POST['email'] . "', '" . $_POST['mobile'] . "', '" . $_POST['phone'] . "', '" . $_POST['address'] . "', '" . $_POST['area'] . "', '" . $_POST['city'] . "', "

                . "'" . $_POST['country'] . "', '" . $_POST['valid_id'] . "', '" . $_POST['valid_id_type'] . "', '" . $_POST['nationality'] . "', "

                . "'" . $birth_date . "', '" . $_POST['beneficiary'] . "', '" . $_POST['relationship'] . "', '" . $login_pass_crypt . "', "

                . "'" . $ewallet_pass_crypt . "', '0', '0', "

                . "'0', '0', '0', '0', '" . $core->getFormatedDateTime() . "', '" . $core->getFormatedDate() . "'"

                . ", 0 ) ";

        if ($database_manager->query($sql)) {



            // INSERT INTO bu for 3 bu 

			

			

			if($baseNumberOfBUs==3){

				//3 bu

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

				

			}elseif($baseNumberOfBUs==7){
				
				
				/////if isQualifiedByDefault

				///7 bu

				$string_to_add_to_children = "\'" . $my_ir_id . "-001\', \'" . $my_ir_id . "-002\', \'" . $my_ir_id . "-003\',  \'" . $my_ir_id . "-004\',  \'" . $my_ir_id . "-005\',  \'" . $my_ir_id . "-006\',  \'" . $my_ir_id . "-007\', ";

				$left_children001="\'" . $my_ir_id . "-002\', \'" . $my_ir_id . "-004\', \'" . $my_ir_id . "-005\', ";

				$right_children001="\'" . $my_ir_id . "-003\', \'" . $my_ir_id . "-006\',\'" . $my_ir_id . "-007\', ";

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

					. "left_abv, right_abv, left_children, right_children, "

					. "parent_bu_id, position, is_qualified, referral_bu_id, "

					. "position_to_referral) VALUES ( "

					. " '" . $my_ir_id . "', '001', 0, 0, 0, 0,'".$left_children001."' ,'".$right_children001."' ,'" . $upline_bu_id . "', '" . $_POST['position'] . "', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

					. " )";

				$database_manager->query($sql);

				

				///////////////////line 1

				////insert new row for bu002 for the new ir in bu table

				$left_children002="\'" . $my_ir_id . "-004\', ";

				$right_children002="\'" . $my_ir_id . "-005\', ";

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

						. "left_abv, right_abv, left_children, right_children, "

						. "parent_bu_id, position, is_qualified, referral_bu_id, "

						. "position_to_referral) VALUES ( "

						. " '" . $my_ir_id . "', '002', 0, 0, 0, 0, '".$left_children002."','".$right_children002."','" . $my_ir_id . "-001', 'left', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

						. " )";

				$database_manager->query($sql);

				////insert new row for bu003 for the new ir in bu table

				$left_children003="\'" . $my_ir_id . "-006\', ";

				$right_children003="\'" . $my_ir_id . "-007\', ";

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

						. "left_abv, right_abv, left_children, right_children, "

						. "parent_bu_id, position, is_qualified, referral_bu_id, "

						. "position_to_referral) VALUES ( "

						. " '" . $my_ir_id . "', '003', 0, 0, 0, 0, '".$left_children003."', '".$right_children003."','" . $my_ir_id . "-001', 'right', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

						. " )";

				$database_manager->query($sql);

				

				////////////////////////////////////////line 2

				////insert new row for bu004 for the new ir in bu table

				$left_children004="";

				$right_children004="";

				

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

						. "left_abv, right_abv, left_children, right_children, "

						. "parent_bu_id, position, is_qualified, referral_bu_id, "

						. "position_to_referral) VALUES ( "

						. " '" . $my_ir_id . "', '004', 0, 0, 0, 0, '".$left_children004."', '".$right_children004."','" . $my_ir_id . "-002', 'left', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

						. " )";

				$database_manager->query($sql);

				////insert new row for bu005 for the new ir in bu table

				$left_children005="";

				$right_children005="";

				

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

						. "left_abv, right_abv, left_children, right_children, "

						. "parent_bu_id, position, is_qualified, referral_bu_id, "

						. "position_to_referral) VALUES ( "

						. " '" . $my_ir_id . "', '005', 0, 0, 0, 0, '".$left_children005."', '".$right_children005."','" . $my_ir_id . "-002', 'right', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

						. " )";

				$database_manager->query($sql);

				

				////insert new row for bu006 for the new ir in bu table

				$left_children006="";

				$right_children006="";

				

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

						. "left_abv, right_abv, left_children, right_children, "

						. "parent_bu_id, position, is_qualified, referral_bu_id, "

						. "position_to_referral) VALUES ( "

						. " '" . $my_ir_id . "', '006', 0, 0, 0, 0, '".$left_children006."', '".$right_children006."','" . $my_ir_id . "-003', 'left', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

						. " )";

				$database_manager->query($sql);

				////insert new row for bu007 for the new ir in bu table

				$left_children007="";

				$right_children007="";

				

				$sql = "INSERT INTO bu (ir_id, code, left_dbv, right_dbv, "

						. "left_abv, right_abv, left_children, right_children, "

						. "parent_bu_id, position, is_qualified, referral_bu_id, "

						. "position_to_referral) VALUES ( "

						. " '" . $my_ir_id . "', '007', 0, 0, 0, 0, '".$left_children007."', '".$right_children007."','" . $my_ir_id . "-003', 'right', '".$isQualifiedByDefault."', '" . $referrer_ir_id . "-" . $_POST['business_unit'] . "', '" . $_POST['position'] . "' "

						. " )";

				$database_manager->query($sql);

				

			}

            ////insert withdraw data according to selected selectwithdrawmethod

			if(isset($_POST["selectwithdrawmethod"]) && $_POST["selectwithdrawmethod"]!=""){

				$withdrawbankmethod=(int)$_POST["selectwithdrawmethod"];

				

				switch($withdrawbankmethod){

					case 1:///pay in cash

						$method_name="Withdraw in cash";

					break;

					case 2:////transfer to bank account

						$method_name="Withdraw to bank account";

					break;

					case 3:///pay using vf cash

						$method_name="Withdraw by Vodafone cash";

					break;

				}

				if(!isset($_POST["Bank_name"]) || $_POST["Bank_name"]==""){

					$bank_name="";

				}else{

					//$bank_name="cib";

					$bank_name=$database_manager->realEscapeString($_POST["Bank_name"]);

				}

				if(!isset($_POST["Bank_branch"]) || $_POST["Bank_branch"]==""){

					$bank_branch="";

				}else{

					//$bank_branch="haram";

					$bank_branch=$database_manager->realEscapeString($_POST["Bank_branch"]);

				}

				if(!isset($_POST["Bank_account"]) || $_POST["Bank_account"]==""){

					$bank_account="";

				}else{

					//$bank_account="5464565465";

					$bank_account=$database_manager->realEscapeString($_POST["Bank_account"]);

				}

				if(!isset($_POST["Bank_owner"]) || $_POST["Bank_owner"]==""){

					$bank_owner="";

				}else{

					//$bank_owner="Mohammed";

					$bank_owner=$database_manager->realEscapeString($_POST["Bank_owner"]);

				}

				if(!isset($_POST["Bank_owner_phonenumber"]) || $_POST["Bank_owner_phonenumber"]==""){

					$bank_owner_phonenumber="";

				}else{

					//$bank_owner_phonenumber="011144415412";

					$bank_owner_phonenumber=$database_manager->realEscapeString($_POST["Bank_owner_phonenumber"]);

				}

				if(!isset($_POST["Bank_SWIFT"]) || $_POST["Bank_SWIFT"]==""){

					$bank_SWIFT="";

				}else{

					//$bank_SWIFT="CIBEG5412";

					$bank_SWIFT=$database_manager->realEscapeString($_POST["Bank_SWIFT"]);

				}

				if(!isset($_POST["vf_cash_number"]) || $_POST["vf_cash_number"]==""){

					$vf_cash_number="";

				}else{

					//$vf_cash_number="0100101012";

					$vf_cash_number=$database_manager->realEscapeString($_POST["vf_cash_number"]);

				}

				if(!isset($_POST["vf_cash_name"]) || $_POST["vf_cash_name"]==""){

					$vf_cash_name="";

				}else{

					//$vf_cash_name="Omar Kalifa";

					$vf_cash_name=$database_manager->realEscapeString($_POST["vf_cash_name"]);

				}

				

				$sql="INSERT INTO `ir_withdraw_methods`(`id`, `ir_id`, `selected_method`, `method_name`, `Bank_name`, `Bank_branch`, `Bank_account`, `Bank_owner`, `Bank_owner_phonenumber`, `Bank_SWIFT`, `vf_cash_number`, `vf_cash_name`) VALUES (NULL,'".$my_ir_id."','".$withdrawbankmethod."','".$method_name."','".$bank_name."','".$bank_branch."','".$bank_account."','".$bank_owner."','".$bank_owner_phonenumber."','".$bank_SWIFT."','".$vf_cash_number."','".$vf_cash_name."')";

				$dd=$database_manager->query($sql);

			}



			



            // INSERT INTO dc

			$updateDC=0;///ignore for now

			if($updateDC){

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

			};

			

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

			

			/////Update accumulative counter for uplines bu 

				////if isQualifiedByDefault =2 (binary)

				//if($isQualifiedByDefault==2 || $isQualifiedByDefault==1){

					        // UPDATE IR & BU

					

					$sql1 = "UPDATE bu SET is_qualified = 2 WHERE ir_id = '" . $my_ir_id . "' ";

							//. " AND code = '" . $_SESSION['bu_to_qualify'] . "' ;";

					$database_manager->query($sql1);

					

					$sql1 = "UPDATE ir SET qualification_date = '" . $core->getFormatedDateTime() . "' WHERE ir_id = '" . $my_ir_id . "' ;";

					$qu=$database_manager->query($sql1);

					

					// UPDATE Parents dc, dbv, abv

					//$bu_to_qualify="001";

					$sql1 = "UPDATE bu SET left_dbv = left_dbv + 1, left_abv = left_abv + 1, Lcount = (Lcount + 1) WHERE left_children LIKE '%". $my_ir_id ."-001%'; ";

					$ss=$database_manager->query($sql1);

						

					$sql1 = "UPDATE bu SET right_dbv = right_dbv + 1, right_abv = right_abv + 1 ,Rcount = (Rcount + 1) WHERE right_children LIKE '%". $my_ir_id ."-001%' ;";

					$database_manager->query($sql1);

			

		//////////////////Give referral the new registration commision to his ewallet as set in config///////////////////////////////////////

		///only if registration is NOT Free

		if(!$isFreeRegistration){

			$sql = "UPDATE ir SET ewallet = (ewallet + " . $new_reg_com . ") WHERE ir_id = '" . $referrer_ir_id . "' ;";

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

		}

		////////////////////////////////////////////////////////////////////////////////////////////////

			

            // Header Location Registration_success



           header("Location: index.php?page=register_success&new_ir_id=" . $my_ir_id);

            echo "<meta http-equiv='refresh' content='0;url=index.php?page=register_success&new_ir_id=".$my_ir_id."'>";

        } else {

            $error = "Incorrect Information.";

        }

    }

}



$sql = "UPDATE configuration SET register_lock = 0";

$database_manager->query($sql);



$html_page->writeHeader();

?>

<script src="js/jquery-3.2.1.min.js"></script>

<script language="JavaScript" type="text/javascript">



	$( document ).ready(function() {

		////payment method selection event and initialization

		$("#selectpaymentmethod").change(function(){

			selectedpaymentmethod= parseInt($("#selectpaymentmethod").val())

			if(selectedpaymentmethod==1){

				$(".allpaymentmethods").hide()

				$(".ewallet_payment").show()	

			}else if(selectedpaymentmethod==2){

				$(".allpaymentmethods").hide()

				$(".giftcard_payment").show()

			}else{

				$(".allpaymentmethods").hide()

			}

		});

		selectedpaymentmethod= parseInt($("#selectpaymentmethod").val())

		if(selectedpaymentmethod==1){

			$(".allpaymentmethods").hide()

			$(".ewallet_payment").show()

			

		}else if(selectedpaymentmethod==2){

			$(".allpaymentmethods").hide()

			$(".giftcard_payment").show()

		}else{

			$(".allpaymentmethods").hide()

		}

		/////////////withdraw money selection event and initialization

		$("#selectwithdrawmethod").change(function(){

			selectwithdrawmethod= parseInt($("#selectwithdrawmethod").val())

			if(selectwithdrawmethod==1){

				$(".allpaymentmethods").hide()

				$(".withdrawcashmethod").show()	

			}else if(selectwithdrawmethod==2){

				$(".allpaymentmethods").hide()

				$(".withdrawbankmethod").show()

			}else if(selectwithdrawmethod==3){

				$(".allpaymentmethods").hide()

				$(".withdrawvfmethod").show()

			}else{

				$(".allpaymentmethods").hide()

			}

		});

		selectwithdrawmethod= parseInt($("#selectwithdrawmethod").val())

			if(selectwithdrawmethod==1){

				$(".allpaymentmethods").hide()

				$(".withdrawcashmethod").show()	

			}else if(selectwithdrawmethod==2){

				$(".allpaymentmethods").hide()

				$(".withdrawbankmethod").show()

			}else if(selectwithdrawmethod==3){

				$(".allpaymentmethods").hide()

				$(".withdrawvfmethod").show()

			}else{

				$(".allpaymentmethods").hide()

		}

	});///end doc ready

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

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Business Unit";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["position"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> position";

                    window.scrollTo(0, 0);

                    return false;

                }

				var y = document.forms["myform"]["login_pass"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Login Password";

                    window.scrollTo(0, 0);

                    return false;

                }

                if (y.split('').length < 8) {

                    document.getElementById("error1").innerHTML = "Passwords must be at least 8 characters. Login Password";

                    window.scrollTo(0, 0);

                    return false;

                }

                var z = document.forms["myform"]["login_confirm"].value;

                if (z == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Confirm Login Password";

                    window.scrollTo(0, 0);

                    return false;

                }

                if (y != z) {

                    document.getElementById("error1").innerHTML = "Login Password Mismatch.";

                    window.scrollTo(0, 0);

                    return false;

                }

				

                var y = document.forms["myform"]["ewallet_pass"].value;

               /* if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> E-Wallet Password";

                    window.scrollTo(0, 0);

                    return false;

                }

                if (y.split('').length < 8) {

                    document.getElementById("error1").innerHTML = "Passwords must be at least 8 characters. E-Wallet Password";

                    window.scrollTo(0, 0);

                    return false;

                }

                var z = document.forms["myform"]["ewallet_confirm"].value;

                if (z == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Confirm E-Wallet Password";

                    window.scrollTo(0, 0);

                    return false;

                }

                if (y != z) {

                    document.getElementById("error1").innerHTML = "E-Wallet Password Mismatch.";

                    window.scrollTo(0, 0);

                    return false;

                }*/

               /* var y = document.forms["myform"]["title"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Title";

                    window.scrollTo(0, 0);

                    return false;

                }*/

                var y = document.forms["myform"]["f_name"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> First Name";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["l_name"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Last Name";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["a_name"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Arabic Name";

                    window.scrollTo(0, 0);

                    return false;

                }

				var y = document.forms["myform"]["mobile"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Mobile";

                    window.scrollTo(0, 0);

                    return false;

                }

				var y = document.forms["myform"]["phone"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Phone";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["email"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Email";

                    window.scrollTo(0, 0);

                    return false;

                }

                if (validateEmail(y) == false) {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->reg_emailsisnotvalid;?>";

                    window.scrollTo(0, 0);

                    return false;

                }

                

                

                var y = document.forms["myform"]["address"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Address";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["country"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Country";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["area"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Governorate";

                    window.scrollTo(0, 0);

                    return false;

                }

                 var y = document.forms["myform"]["valid_id_type"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Valid ID Type";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["valid_id"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Valid ID";

                    window.scrollTo(0, 0);

                    return false;

                }

               

               /* var y = document.forms["myform"]["nationality"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Nationality";

                    window.scrollTo(0, 0);

                    return false;

                }*/

                var y = document.forms["myform"]["year"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Birth Date - Year";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["month"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Birth Date - Month";

                    window.scrollTo(0, 0);

                    return false;

                }

                var y = document.forms["myform"]["day"].value;

                if (y == '') {

                    document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Birth Date - Day";

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

                    document.getElementById("error1").innerHTML = "You must me over 16 years old to register.";

                    window.scrollTo(0, 0);

                    return false;

                }

                /*****************withdraw options validate****/

				

					////payment method selection event and initialization

					var ddval=parseInt( document.forms["myform"]["selectwithdrawmethod"].value)

					switch(ddval){

						case 1://cash withdraw

						break;

						case 2:///bank details

							var y = document.forms["myform"]["Bank_name"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Bank Name";

								window.scrollTo(0, 0);

								return false;

							}

							var y = document.forms["myform"]["Bank_branch"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Bank Branch";

								window.scrollTo(0, 0);

								return false;

							}

							var y = document.forms["myform"]["Bank_account"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Bank Account";

								window.scrollTo(0, 0);

								return false;

							}

							var y = document.forms["myform"]["Bank_owner"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Bank Owner";

								window.scrollTo(0, 0);

								return false;

							}

							var y = document.forms["myform"]["Bank_owner_phonenumber"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Bank_owner Phonenumber";

								window.scrollTo(0, 0);

								return false;

							}

							var y = document.forms["myform"]["Bank_SWIFT"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Bank SWIFT Code";

								window.scrollTo(0, 0);

								return false;

							}

						break;

						case 3:////vodafone cash

							var y = document.forms["myform"]["vf_cash_number"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Vodafone Cash Number (Mobile)";

								window.scrollTo(0, 0);

								return false;

							}

							var y = document.forms["myform"]["vf_cash_name"].value;

							if (y == '') {

								document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?> Vodafone Cash Owner's Name";

								window.scrollTo(0, 0);

								return false;

							}

						break;

					}

		

				

				/**********************************if payment from ewallet*******/

                if(btnid==1){///pay using ewallet

					console.log(btnid)

					var y = document.forms["myform"]["ewallet_ir_id"].value;

					if (y == '') {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";

						window.scrollTo(0, 0);

						return false;

					}

					if (y.split('').length !== 8) {

						document.getElementById("error1").innerHTML = "Invalid E Wallet IR ID.";

						window.scrollTo(0, 0);

						return false;

					}

					var y = document.forms["myform"]["ewallet_ir_password"].value;

					if (y == '') {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";

						window.scrollTo(0, 0);

						return false;

					}

				}else if(btnid==2){///pay using gift card 

				/***********************************************************************/

				console.log(btnid)

					var x = document.forms["myform"]["rechargecardnumber"].value;

					if (x == '') {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";

						 window.scrollTo(0, 0);

						return false;

					}

					if (x.split('').length !== 16) {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidcardnumber;?> ";

						 window.scrollTo(0, 0);

						return false;

					}

					var y = document.forms["myform"]["rechargecardpsw"].value;

					if (!IsNumeric(y) || !isPositiveInteger(y)) {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidcardpsw;?>";

						 window.scrollTo(0, 0);

						return false;

					}

					if (y == '') {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";

						 window.scrollTo(0, 0);

						return false;

					}

					

					////verify card pin for new ir

				var y = document.forms["myform"]["card_pin"].value;

					if (!IsNumeric(y) || !isPositiveInteger(y)) {

						document.getElementById("error1").innerHTML = "Card Pin Should be Numbers not Characters.";

						 window.scrollTo(0, 0);

						return false;

					}

					if (y == '') {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";

						 window.scrollTo(0, 0);

						return false;

					}

					if (y.split('').length !== 4) {

						document.getElementById("error1").innerHTML = "Card Pin Should be 4 numbers.";

						window.scrollTo(0, 0);

						return false;

					}

					var z = document.forms["myform"]["card_pin_confirm"].value;

					if (z == '') {

						document.getElementById("error1").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";

						window.scrollTo(0, 0);

						return false;

					}

					if (y != z) {

						document.getElementById("error1").innerHTML = "Card Pin Mismatch.";

						window.scrollTo(0, 0);

						return false;

					}

				}else if(btnid==0){////free registration

					return confirm("Are you sure you want complete the registration?");

				}

				

                //return true;

				return confirm("<?php echo $_SESSION["language"]->confirmPayment;?>");

            }///end validate function

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

<!---------------NEW Registration Form by KCG new design--- for be3ly system-->

<div class="row">

	<div class="col-xs-12 col-md-12 col-lg-12" >

		<div class="header col-xs-12 col-md-12 col-lg-12" >

			<div class="col-xs-0 col-md-2 col-lg-2" >

			</div>

			<div class="col-xs-12 col-md-8 col-lg-8" >	

				<div class="logo col-xs-12 col-md-3 col-lg-3" >

					<a href="index.php?page=dashboard"><img style="" class="img-responsive" src="images/testlogo.png"></a>

				</div>

				<div class=" headertxt col-xs-12 col-md-9 col-lg-9 " >

				<div class="align-middle"><strong>Wellcome To</strong> <?php echo $_SESSION["language"]->reg_pagehsetp2;?></div>

				</div>

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" >

			</div>

		</div><!---end header container div-->

		<form method = "post" name = "myform" onsubmit = "" onsubmit = "return validateForm();">

		

		

		

		<!---Section1 form------------------>

		<div class=" section1 col-xs-12 col-md-12 col-lg-12" >

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

			<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >

			<!-----error msg area---->

				<div id="error1" class="col-xs-12 col-md-12 col-lg-12 reg_errormsg" >

				<?php 

					if (isset($_GET['error']) && $_GET['error'] == "1") {

						echo $_SESSION["language"]->fundtransfer_msg_invalidIRID;

					}

					

					if (isset($_GET['error']) && $_GET['error'] == "2") {

						echo $_SESSION["language"]->IRisnotQualified;

					}

					if (isset($_GET['error']) && $_GET['error'] == "3") {

						echo $_SESSION["language"]->IRbusinessUnitsLessthanCorrect;

					}

					if($error!=""){

						echo $error;

					}

				?>

				</div>

				<h3 class="col-xs-12 col-md-12 col-lg-12">Referral Info</h3>

				<!--<form method = "post" name = "myform" action="index.php?page=register_second_step&step=1" onsubmit = "return validateForm();" >-->

				<div class="form-group">

						<div class="form_label" for ="referrer_name"><?php echo $_SESSION["language"]->Refirid;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="referrer_ir_id" type="text" value="<?php echo $referrer_ir_id; ?>"  autocomplete="off" readonly/> 

						<input class="form-control" style="display:none;" name="referrer_name" type="text" value="<?php echo $referrer_name; ?>"  autocomplete="off" readonly/>

						<!----Referrer BU downline placement--->

						 

						<div class="form_label" <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo 'style="display:none;"';}?> for ="business_unit"><?php echo $_SESSION["language"]->business_unit;?>  <span class = "astrisk" > * </span></div >

						<?php if ($isAffiliate == 0) { ///is manual registration and placement?>

							<select class="form-control" id="business_unit" name="business_unit" <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo "readonly";} ?> <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo 'style="display:none;"';}?>>

								

								<option value="Auto" <?php

									if ($_POST['business_unit'] == "") {

										echo "selected";

									}

								?> >-- Auto Select Referrer Business Unit --</option>

								<?php foreach ($referrer_BUs AS $bu) { ?>

										<option value="<?php echo $bu; ?>" <?php

										if ($_POST['business_unit'] == $bu) {

											echo "selected";

										}

										?>><?php echo $bu; ?></option>

								<?php } ?>

							</select>  

						<?php } else { ////registration is through affiliate?>

							<select class="form-control" id="business_unit" name="business_unit"  readonly <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo 'style="display:none;"';}?>>

								

								<option value="Auto" <?php

									if ($_POST['business_unit'] == "") {

										echo "selected";

									}

								?> >-- Auto Select Referrer Business Unit --</option>

								<?php foreach ($referrer_BUs AS $bu) { ?>

										<option value="<?php echo $bu; ?>" <?php

										if ($_POST['business_unit'] == $bu) {

											echo "selected";

										}

										?>><?php echo $bu; ?></option>

								<?php } ?>

							</select>  

							

							

						<?php } ?>

						

						<!----Referrer BU downline placement left right auto--->

						<div class="form_label" <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo 'style="display:none;"';}?> for ="business_unit"><?php echo $_SESSION["language"]->placement;?>  <span class = "astrisk" > * </span></div >

							<?php if ($isAffiliate == 0) { ///is manual registration and positioning?>

							<select class="form-control" <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo 'style="display:none;"';}?> name="position" id="position" <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo "readonly";} ?>>

								<option value="Auto" <?php

									if ($_POST['position'] == "") {

										echo "selected";

									}

									?> >-- Auto Position --</option>

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

							</select> 

							<?php } else { ////registration is through affiliate?>

							

								<select class="form-control" name="position" id="position"  readonly <?php if(isset($_GET['refir']) && $_GET['refir']!="" ){ echo 'style="display:none;"';}?>>

								<option value="Auto" <?php

									if ($_POST['position'] == "") {

										echo "selected";

									}

									?> >-- Auto Position --</option>

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

							</select> 

								

							<?php } ?>

				</div>

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

		</div><!---end section1 container div-->

		

		<!---Section0 Login Account details form------------------>

		<div class=" section0 col-xs-12 col-md-12 col-lg-12" >

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

			<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >

			<h3 class="col-xs-12 col-md-12 col-lg-12">Account Details</h3>

			<p class="form_label">Your username will be generated after registration in next step, it will be in the form of; two letters and 6 digits, Example: "BE000000"</p>

			<p class="form_label txtblue">All Passwords must be at least 8 characters.</p>

			<div class="form-group">

						<h4 class="col-xs-12 col-md-12 col-lg-12">Login Password</h4>

						

						<div class="form_label" for ="login_pass"><?php echo $_SESSION["language"]->loginpassword;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name = "login_pass" type="password" value = ""  autocomplete = "off" /> 

						

						<div class="form_label" for ="login_confirm"><?php echo $_SESSION["language"]->confirmloginpassword;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name = "login_confirm" type="password" value = ""  autocomplete = "off" /> 

						

						

				</div>

				<div class="form-group" style="display:none;">

						<h4 class="col-xs-12 col-md-12 col-lg-12">E-Wallet Password</h4>

						<p class="form_label txtblue" >Your E-Wallet password is a protection password for your account electronic wallet (E-Wallet). You will receive your commissions into the electronic wallet until you ask for the payout. You also use your ewallet to buy products from the online shop</p>

						

						<div class="form_label" for ="ewallet_pass"><?php echo $_SESSION["language"]->ewalletpassword;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name = "ewallet_pass" type="password" value = "0"  autocomplete = "off" /> 

						

						<div class="form_label" for ="ewallet_confirm"><?php echo $_SESSION["language"]->confirmewalletpassword;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name = "ewallet_confirm" type="password" value = "0"  autocomplete = "off" /> 

						

				</div>

				<?php ////according to the card flag at configuration either show the card section to allow user to enter pin or only generate the card with default pin in hidden fields

				if($AllowCardPinAtReg){///if allowed

				?>

					<div class="form-group" style="display:none;">

						<h4 class="col-xs-12 col-md-12 col-lg-12">Website Depit Card / Gift Card</h4>

						<p class="form_label txtblue" >A Card Number will be generated and attached to your account as an alternative method of payment only on this website, You can use your card for recharging your E-Wallet, purchase products from this website only. You need to enter a 4-digits PIN for your card</p>

						

						<div class="form_label" for ="card_pin"><?php echo $_SESSION["language"]->entercardPIN;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name = "card_pin" type="password" value = ""  autocomplete = "off" /> 

						

						<div class="form_label" for ="card_pin_confirm"><?php echo $_SESSION["language"]->confirmcardPIN;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name = "card_pin_confirm" type="password" value = ""  autocomplete = "off" /> 	

					</div>

				<?php

				}else{ ?>

					<div class="form-group" style="display:none;">

							<input class="form-control" name = "card_pin" type="hidden" value = "1234"  autocomplete = "off" /> 

							<input class="form-control" name = "card_pin_confirm" type="hidden" value = "1234"  autocomplete = "off" /> 	

					</div>

				<?php } ////end else if card section to be shown during registration?>

				

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

		</div><!---end section0 container div-->

		

		<!---Section2 form------------------>

		<div class=" section2 col-xs-12 col-md-12 col-lg-12" >

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

			<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >

			<h3 class="col-xs-12 col-md-12 col-lg-12">Personal Details</h3>

			<div class="form-group">

						

						<h4 class="col-xs-12 col-md-12 col-lg-12" style="display:none;">Name</h4>

						<div class="form_label" for ="title"><?php echo $_SESSION["language"]->Title;?>  <span class = "astrisk" >  </span></div >

						<select  class="form-control" name="title">

							<option value="Mr." <?php

								if ($_POST['title'] == "Mr.") {

									echo "selected";

								}

							?> >Mr.</option>

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

						</select> 

						

						<div class="form_label" for ="f_name"><?php echo $_SESSION["language"]->firstname;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control"  name="f_name" type="text" value="<?php echo $_POST['f_name']; ?>"  autocomplete="off" />  

						

						<div class="form_label" for ="l_name" style="display:none;"><?php echo $_SESSION["language"]->lastname;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="l_name" style="display:none;" type="text" value="<?php echo 0;//$_POST['l_name']; ?>"  autocomplete="off" />  

						

						<div class="form_label" for ="a_name" style="display:none;"><?php echo $_SESSION["language"]->arabicname;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="a_name" style="display:none;" type="text" value="<?php echo 0;//$_POST['a_name']; ?>"  autocomplete="off" />   

						

						<h4 class="col-xs-12 col-md-12 col-lg-12" style="display:none;">Contact Information</h4>

						<div class="form_label" for ="mobile"><?php echo $_SESSION["language"]->mobile;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="mobile" type="text" value="<?php echo $_POST['mobile']; ?>"  autocomplete="off" />  

						

						<div class="form_label" for ="phone" style="display:none;"><?php echo $_SESSION["language"]->phone;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="phone" style="display:none;" type="text" value="<?php echo 0;//$_POST['phone']; ?>"  autocomplete="off" />   

						

						<div class="form_label" for ="email"><?php echo $_SESSION["language"]->email;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="email" type="text" value="<?php echo $_POST['email']; ?>"  autocomplete="off" />  

						

						<h4 class="col-xs-12 col-md-12 col-lg-12" style="display:none;">Full Address (Include Your City For Shipping Products)</h4>

						<div class="form_label" for ="address" style="display:none;"><?php echo $_SESSION["language"]->address;?>  <span class = "astrisk" > * </span></div >

						<textarea class="form-control" name="address" style="display:none;" ><?php echo 0;//$_POST['address']; ?></textarea> 

						

						<div class="form_label" for ="country"><?php echo $_SESSION["language"]->country;?>  <span class = "astrisk" > * </span></div >

						<select class="form-control" name="country">						  

							<option value="<?php echo $countries[0];?>" <?php

								if ($_POST['country'] == $countries[0]) {

									echo "selected";

								}

							?> ><?php echo $countries[0];?></option>		   

						</select>

						<div class="form_label" style="display:none;" for ="area"><?php echo $_SESSION["language"]->Governorate;?>  <span class = "astrisk" > * </span></div >

						<select class="form-control" name="area" style="display:none;">

								<option value="0" <?php

									if ($_POST['area'] == "") {

										echo "selected";

									}

								?> >--Select Governorate --</option>

								<?php foreach ($Egypt_Governorate AS $gov) { ?>

										<option value="<?php echo $gov; ?>" <?php

										if ($_POST['area'] == $gov) {

											echo "selected";

										}

										?>><?php echo $gov; ?></option>

								<?php } ?>

						</select> 

						<input class="form_label" name="city" style="display:none;" type="hidden" value="<?php echo 0;//$_POST['area']; ?>"  autocomplete="off" />

						

						<h4 class="col-xs-12 col-md-12 col-lg-12" style="display:none;">Legal Identification</h4>

						<div class="form_label" for ="valid_id_type" style="display:none;"><?php echo $_SESSION["language"]->valididtype;?>  <span class = "astrisk" > * </span></div >

						<select class="form-control" name="valid_id_type" style="display:none;">

							<option value="National ID" selected>National ID</option>

							<option value="Passport" >Passport</option>

							<option value="Other" >Other</option>

						</select> 

						

						<div class="form_label" for ="valid_id" style="display:none;"><?php echo $_SESSION["language"]->validID;?>  <span class = "astrisk" > * </span></div >

						<input class="form-control" name="valid_id" style="display:none;" type="text" value="<?php echo 0;//$_POST['valid_id']; ?>"  autocomplete="off" />   

						

						<div class="form_label" for ="nationality" style="display:none;"><?php echo $_SESSION["language"]->Nationality;?>  <span class = "astrisk" >  </span></div >

						<input class="form-control" name="nationality" style="display:none;" type="text" value="<?php echo 0;//$_POST['nationality']; ?>"  autocomplete="off" />  

						

						<div class="form_label" ><?php echo $_SESSION["language"]->birthdate;?>  <span class = "astrisk" > * </span></div >

						<div class="form_label" for ="day"><?php echo $_SESSION["language"]->day;?>  <span class = "astrisk" > * </span></div >

						<select class="form-control" name="day" class="day">

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

						<div class="form_label" for ="month"><?php echo $_SESSION["language"]->month;?>  <span class = "astrisk" > * </span></div >

						<select class="form-control" name="month" class="month">

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

						<div class="form_label" for ="year"><?php echo $_SESSION["language"]->year;?>  <span class = "astrisk" > * </span></div >

						<select class="form-control" name="year" class="year">

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

						<!----Inheritance section Hidden for Be3ly---->

						<!--<h4>Inheritance Information</h4>

						<label>Beneficiary Name <span class="astrisk"> *</span></label> 

						<input name="beneficiary" type="text" value="<?php //echo $_POST['beneficiary']; ?>"  autocomplete="off" />--->

						<input name="beneficiary" style="display:none;" type="hidden" value="Sone Name - Default"  autocomplete="off" />

						<input name="relationship" style="display:none;"  type="hidden" value="Son - Default"  autocomplete="off" />

						

						

				</div>

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

		</div><!---end section2 container div-->

		

		<!---Section3 form------------------>

		<div class=" section3 col-xs-12 col-md-12 col-lg-12" style="display:none;">

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

			<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >

			<h3 class="col-xs-12 col-md-12 col-lg-12">Withdraw Money Details (Payout Commissions)</h3>

			<p class="form_label txtblue" >When you receive commissions to your E-Wallet, you can request to withdraw your money using one of the following methods:</p>

			<div class="form-group">

				<div class="form_label" for ="selectwithdrawmethod"><?php echo $_SESSION["language"]->selectwithdrawmethod;?>  <span class = "astrisk" > * </span></div >

				<select class="form-control" name="selectwithdrawmethod" id="selectwithdrawmethod" class="form-control">

					<option value="1" selected>Receive In Cash</option>

					<option value="2" >To Bank Account (Transfer/Direct Deposit)</option>

					<option value="3" >Using Vodafone Cash</option>						

				</select>

			</div>

			<div class="form-group allpaymentmethods withdrawcashmethod">

				<h3 class="col-xs-12 col-md-12 col-lg-12">Withdraw my money in cash</h3>

				<p class="form_label" >When you request your money, contact the company to arrange for receiving your money in cash.</p>

			</div>

			<div class="form-group allpaymentmethods withdrawbankmethod">

				

				<h3 class="col-xs-12 col-md-12 col-lg-12">Withdraw my money to my bank account, Details below</h3>

				

				<div class="form_label" for ="Bank_name">Bank Name  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "Bank_name" type = "text" value=""  autocomplete = "off" /> 

				

				<div class="form_label" for ="Bank_branch">Bank Branch Name  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "Bank_branch" type = "text" value = "" autocomplete="off" /> 

				

				<div class="form_label" for ="Bank_account">Account Number  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "Bank_account" type = "text" value=""  autocomplete = "off" /> 

				

				<div class="form_label" for ="Bank_owner">Bank Account Owner Name <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "Bank_owner" type = "text" value = "" autocomplete="off" />

				

				<div class="form_label" for ="Bank_owner_phonenumber">Bank Account Owner Mobile <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "Bank_owner_phonenumber" type = "text" value = "" autocomplete="off" />

				

				<div class="form_label" for ="Bank_SWIFT">Bank SWIFT Code <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "Bank_SWIFT" type = "text" value = "" autocomplete="off" />

				

			</div>

			<div class="form-group allpaymentmethods withdrawvfmethod">

				<h3 class="col-xs-12 col-md-12 col-lg-12">Withdraw my money to Vodafone Cash</h3>

				<p class="form_label" >Enter the Vodafone Cash number to receive your money. To be able to to receive your money through this option; you should have a Vodafone simcard and have activated Vodafone cash service by contacting Vodafone.</p>

				<div class="form_label" for ="vf_cash_number">Vodafone Cash Number (Mobile)  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "vf_cash_number" type = "text" value=""  autocomplete = "off" /> 

				

				<div class="form_label" for ="vf_cash_name">Vodafone Cash Owner's Name <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "vf_cash_name" type = "text" value = "" autocomplete="off" /> 

				

			</div>

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

		</div><!---end section3 container div-->

		

		

		

		<!---Section0 form account details------------------>

		<?php if($isFreeRegistration){ ?>

			

			<div class=" section0 col-xs-12 col-md-12 col-lg-12" >

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

			<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >

			<h3 class="col-xs-12 col-md-12 col-lg-12">Sign Up Is Free</h3>



			<div class="form-group">

				

				<input class="form-control" name = "ewallet_ir_id" type = "hidden" value="free"  autocomplete = "off" /> 

				<input class="form-control" name = "ewallet_ir_name" type = "hidden" value = "free" autocomplete="off" /> 

				<input class="form-control" name = "ewallet_ir_password" type="hidden" value = "free" autocomplete="off" /> 

				<input name = "step" type = "hidden" value = "2"  autocomplete = "off" /> <br class = "clear" />

				<button id="payewallet_btn" onclick="return validateForm(0);" class="freeRegBtn" ><i class="fa fa-check-square fa-fw"></i><?php echo $_SESSION["language"]->Next;?></button>



			</div>	

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

		</div><!---end section0 container div-->

			

			

		<?php

		}else{

		?>

		<div class=" section0 col-xs-12 col-md-12 col-lg-12" >

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

			<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >

			<h3 class="col-xs-12 col-md-12 col-lg-12">Pay SignUp Fees Using</h3>

			<p class="form_label txtblue" >To be able to complete the registration, Registration fees must be paid, you have the following options to pay the fees:</p>

			<div class="form-group">

				<div class="form_label" for ="selectpaymentmethod"><?php echo $_SESSION["language"]->selectpaymentmethod;?>  <span class = "astrisk" > * </span></div >

				<select class="form-control" name="selectpaymentmethod" id="selectpaymentmethod" class="form-control">

					<option value="1" selected>Using IR E-Wallet</option>

					<option value="2" >Using Gift Card</option>		

				</select>

			</div>

			<div class="form-group allpaymentmethods ewallet_payment">

				<div class="payment_icon"><i class="fa fa-money fa-fw"></i></div>

				<h3 class="col-xs-12 col-md-12 col-lg-12"><?php echo $_SESSION["language"]->payusing ;?> E Wallet:</h3>

				

				<div class="form_label" for ="ewallet_ir_id"><?php echo $_SESSION["language"]->payusingIRID;?>  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "ewallet_ir_id" type = "text" value=""  autocomplete = "off" onchange="getIRName();"/> 

				

				<div class="form_label" for ="ewallet_ir_name"><?php echo $_SESSION["language"]->irname;?>  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "ewallet_ir_name" type = "text" value = "" autocomplete="off" readonly/> 

				

				<div class="form_label" for ="ewallet_ir_password"><?php echo $_SESSION["language"]->ewalletpassword;?>  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "ewallet_ir_password" type="password" value = "" autocomplete="off" /> 

				

				<input name = "step" type = "hidden" value = "2"  autocomplete = "off" /> <br class = "clear" />

					

						<input class="form-control" name = "step" type = "hidden" value = "1"  autocomplete = "off" /> 

						<!--<button type = "submit" class="btn btn-primary nextbtn"> <i class = "fa fa-check-square fa-fw" > </i><?php// echo $_SESSION["language"]->Next;?></button >--->

						<button id="payewallet_btn" onclick="return validateForm(1);" class="payusing_reg_btn"><i class="fa fa-check-square fa-fw"></i>confirm Pay & Register</button>



			</div>

			

			<div class="form-group allpaymentmethods giftcard_payment">

				<div class="payment_icon"><i class="fa fa-credit-card fa-fw"></i></div>

				<h3 class="col-xs-12 col-md-12 col-lg-12"><?php echo $_SESSION["language"]->payusing ." ".$_SESSION["language"]->virtualcard;?></h3>

				

				<div class="form_label" for ="rechargecardnumber"><?php echo $_SESSION["language"]->virtualcard;?>  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "rechargecardnumber" type = "text" value=""  autocomplete = "off" onchange="getrechargecardbalance();"/> 

				

				<div class="form_label" for ="rechargecard_bal"><?php echo $_SESSION["language"]->cardbalanceinfo;?>  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "rechargecard_bal" type = "text" value = "" autocomplete="off" readonly/> 

				

				<div class="form_label" for ="rechargecardpsw"><?php echo $_SESSION["language"]->virtualcard." ".$_SESSION["language"]->password;?>  <span class = "astrisk" > * </span></div >

				<input class="form-control" name = "rechargecardpsw" type="password" value = "" autocomplete="off" /> 

				

				<input name="recharge" type="hidden" value="1" />  <br class="clear"/>

				

				<input name = "step" type = "hidden" value = "2"  autocomplete = "off" /> <br class = "clear" />		

				<input class="form-control" name = "step" type = "hidden" value = "1"  autocomplete = "off" /> 

				

				<!---<button type = "submit" class="btn btn-primary nextbtn"> <i class = "fa fa-check-square fa-fw" > </i><?php// echo $_SESSION["language"]->Next;?></button >-->

				<button type="submit" id="paycard_btn" onclick="return validateForm(2);" class="payusing_reg_btn"><i class="fa fa-check-square fa-fw"></i><?php echo $_SESSION["language"]->payandregister;?></button>

					

			</div>

			

			</div>

			<div class="col-xs-0 col-md-2 col-lg-2" ></div>

		</div><!---end section0 container div-->

		<?php }?>

		

		</form>

	</div><!---end container div-->

</div><!---end container row--->



<!-------------------------------------->







        

        <?php $html_page->writeFooter(); ?>