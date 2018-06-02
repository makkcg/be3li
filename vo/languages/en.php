<?php
///////////////front office language
class English_language{
	/////////general 
	public $companyname="Be3li Company";
	public $langdirection="ltr";
	public $ecurrency="L.E";
	public $days="DAY/S";
	public $months="MONTH/S";
	public $years="YEAR/S";
	public $hours="HOUR/S";
	public $minutes="MINUTE/S";
	public $seconds="SECOND/S";
	public $date="Date";
	public $announcement="Announcement";
	public $irid="IR-ID";
	public $Refirid="Referrer Member";
	public $name="Name";
	public $iridname="IR-ID Name";
	public $placement="Placement";
	public $registrationdate="Registration Date";
	public $qualificationdate="Qualification Date";
	public $shoptype="Shop Type";
	public $bu="B.U";
	public $amount="Amount";
	public $confirmamount="Confirm Amount";
	public $virtualcard="Rechargable Card";
	public $cardbalanceinfo="Rechargable Card Balance & Info";
	public $password="Password";
	public $balance="Balance ";
	public $errorinquery="Error In query";
	public $returnsuccess="success";
	public $Next="Next";
	public $IRisnotQualified ="IR is Not Qualified";
	public $payusing="Pay using ";
	public $confirmPayment="Are you sure you want to proceed with the payment?";
	
	
	////////////////REgistration page
	public $reg_pagehsetp1="Registration: Step 1";
	public $reg_pagehsetp2="Registration: Step 2";
	public $reg_pagehsetp3="Registration: Step 3";
	public $reg_cardtransactiontype="Withdraw for IR Registration";
	public $reg_emailsisnotvalid="Email format is not valid it should be like eamil@domain.com";
	public $IRbusinessUnitsLessthanCorrect="This IR is corrupted, please contact the support or select another IR";
	//public $payusing="Pay using ";
	public $payandregister="Pay & Register";
	public $Title="Title";
	public $firstname="Full Name (English)";
	public $lastname="Last Name";
	public $arabicname="Arabic Name";
	public $mobile="Mobile";
	public $phone="Phone";
	public $email="Email";
	public $address="Address";
	public $country="Country";
	public $Governorate="Governorate";
	public $valididtype="Valid ID Type ";
	public $validID="Valid ID";
	public $Nationality="Nationality";
	public $birthdate="Birth Date";
	public $year="Year";
	public $month="Month";
	public $day="Day";
	public $loginpassword="Login Password";
	public $confirmloginpassword="Confirm Login Password";
	public $ewalletpassword="E-Wallet Password";
	public $confirmewalletpassword="Confirm E-Wallet Password";
	public $entercardPIN="Enter Your Card PIN";
	public $confirmcardPIN="Confirm Your Card PIN";
	public $selectpaymentmethod="Select a payment method";
	public $payusingIRID="Enter IRID to pay using his e-wallet";
	public $irname="IR Name";
	public $selectwithdrawmethod="Select method to withdraw your money";
	
	
	//////////////cart
	public $cart_msg_moneynotenough="Insufficient Funds.";
	public $cart_msg_ordercompletedusccess="Congratulations! Your order is confirmed";
	public $cart_msg_andshopisqualified="and your shop is now qualified";
	
	//////fund transfer , charge virtual visa scratch card
	public $fundtransfer_pagetitle ="Fund Transfer/Card Recharge";
	public $fundtransfer_availablefundinEwallet ="Available fund in ewallet ";
			//used in genral check as well
	public $fundtransfer_msg_mandatoryfieldsblank ="Mandatory fields cannot be left blank.";
	public $fundtransfer_msg_invalidIRID ="Invalid IR ID.";
	public $fundtransfer_msg_invalidAmount ="Invalid Amount.";
	public $fundtransfer_msg_amountmismatch ="Amount Mismatch.";
	public $fundtransfer_msg_confirmtransfermoney ="Are you sure you want to transfer the money?";
	public $fundtransfer_msg_transferedsuccess ="Funds were transferred successfully to the IR eWallet.";
	public $fundtransfer_transfer ="Transfer";
	public $fundtransfer_irtransferH="Transfer Money from your ewallet to another IR ewallet OR to Card";
	public $fundtransfer_rechargeewalletH="Transfer/Recharge Money from Card to your ewallet";
	public $fundtransfer_recharge="Recharge";
	public $fundtransfer_msg_donthaveenoughmoneyewallet="You don't have enough Money in your E-Wallet.";
	public $fundtransfer_msg_canttransfertoself="Cannot transfer to yourself.";
	public $fundtransfer_msg_invalidcardnumber="Invalid Card";
	public $fundtransfer_msg_cardowner="Card Owner ";
	public $fundtransfer_cmnt_ewalletrecharge="Ewallet Recharge by Card";
	public $fundtransfer_msg_invalidcardpsw="Invalid Card Password";
	public $fundtransfer_msg_cardbalancezero="Can't Transfer/Recharge - Card Balance is ZERO";
	public $fundtransfer_msg_cantupdateewallet="Error Updating eWallet Balance";
	public $fundtransfer_msg_cantgetewalletbalance="Error Get eWallet Balance";
	public $fundtransfer_msg_cantudateewallettrans="Error Insert eWallet Transaction";
	public $fundtransfer_msg_cantudatecardbalance="Error Updating Card Balance";
	public $fundtransfer_msg_cantgetcardbalance="Error Get Card Balance";
	public $fundtransfer_msg_cantinsertcardtrans="Error Insert Card Transaction";
	public $fundtransfer_msg_cardrechargsuccess="Card balance was transfered successfully to your eWallet.";
	/////////////////////////////////////////////////
	///html
	public $htmlhead="be3li Office | Cloud System ";
	public $Welcome="Welcome";
	public $footer_copyright="Copyright @ 2018 Be3li";
	////menu
	public $menu_qualifyshop="Qualify Shop";
	public $menu_ewallet="E-Wallet";
	public $menu_fundtransfer="Transfer/Recharge Fund";
	public $menu_myshop="My Shop";
	public $menu_orderhistory="Orders History";
	public $menu_retailnetwork="Retail Network";
	public $menu_referredlist="My Customers List";
	public $menu_binarynetwork="My Customers Network";
	public $menu_totalcommission="Total Commission";
	public $menu_dailycounter="Daily Counter";
	public $menu_budinessvoluem="Business Volume";
	public $menu_renewal="Renewal";
	public $menu_logout="Log Out";
	public $menu_changepassword="Password";
	public $menu_myaccount="My Account";
	public $menu_dashboard="Dashboard ";
	public $menu_businesstools="My Tools";
	public $menu_registernewir="Add New IR";
	/////////login screen
	public $loginIRlabel = "Independent Representative (IR) ID:";
	
	/////retail network screen
	public $retailnewtork_header = "Retail Network";
	public $retailnewtork_level1 = "Level 1";
	public $retailnewtork_level2 = "Level 2";
	public $retailnewtork_countactive="Count (Active) ";
	////////////////dashboard 
	public $dashboard_accRenewReminderH="Account Renewal Reminder";
	public $dashboard_show="Show";
	public $dashboard_todayscounterh="Today's Counter";
	public $dashboard_todayscounter = "Today's Counter shows you the Business Volumes (BVs) achieved on the right and left arm for each business unit.";
	public $dashboard_systemannouncements="System Announcements";
	public $timetonextrenwal="Time to my the next Renewal!";
	public $latestRefferalsH="Latest Referrals";
	public $business_unit="Business Unit";
	public $dashboard_recomended_products="Recommended Products";
	public $dashboard_best_seller="Best Seller";
	public $product_cat="Main Categories";
	public $nosubcatmsg="Browse All products under the main category";
    ////////////////////
	

    public function convert_number_to_words($number) {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Fourty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
            100 => 'Hundred',
            1000 => 'Thousand',
            1000000 => 'Million',
            1000000000 => 'Billion',
            1000000000000 => 'Trillion',
            1000000000000000 => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );
        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            trigger_error(
                    'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }
        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        return $string;
    }

}

?>