<?php

class Core {
	public $curlang;
    function generateNextIRID($previous_ir) {
        ////changes made by kcg 
        $number = substr($previous_ir, -6);
        ///$prefix = str_replace($number, "", $previous_ir);///original code
		////get the last prefix from db
		//
		$database_manager1 = new DatabaseManager();
		$sql = "SELECT prefix FROM prefix ORDER BY id DESC LIMIT 1;";
        $result = $database_manager1->query($sql);
        $row = mysqli_fetch_assoc($result);
		$prefix=$row["prefix"];
        $number = (int) $number;
        if ($number == 999999) {
            return $this->getNextIrCodePrefix($prefix) . "000000";
        } else {
            return $prefix . $this->getNextIrCodeNumber($number);
        }
    }

    private function getNextIrCodePrefix($prefix) {
        return ++$prefix;
    }

    private function getNextIrCodeNumber($number) {
        $number = (string) ++$number;
        $number_of_additional_zeros = 6 - strlen($number);
        for ($i = 0; $i < $number_of_additional_zeros; $i++) {
            $number = "0" . $number;
        }
        return $number;
    }

    function generateSalt() {
        return '$2a$14$' . $this->randString(22);
    }

    private function randString($length) {
        $str = '';
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }
        return $str;
    }

    function stringContains($string, $search) {
        return strpos($string, $search) !== false;
    }

    function getIdFromEmail($database_manager, $email) {
        $sql = "Select ir_id FROM ir WHERE email = '" . $email . "'";
        $result = $database_manager->query($sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['ir_id'];
        } else {
            return false;
        }
    }

    function checkEmailDuplicate($database_manager, $email, $ir_id = "") {
        $sql = "Select ir_id FROM ir "
                . " WHERE email = '" . $email . "' "
                . " AND ir_id != '" . $ir_id . "' ";
        $result = $database_manager->query($sql);
        return mysqli_num_rows($result) > 0;
    }

    function email($database_manager, $to, $subject, $msg) {
        $sql = "SELECT email_signature FROM configuration ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);

        $subject = "Proshops - " . $subject;
        $msg .= "\n" . $row['email_signature'];
        $from = "noreply@cashmlm.com";
        $headers = "From:" . $from . "\r\n";
        $headers .= "Content-Type:html; charset=UTF-8\r\n";
        return mail($to, $subject, $msg, $headers);
    }

    function getPasswordResetCode($database_manager, $ir_id) {
        $code = $ir_id . $this->randString(30);
        $sql = "UPDATE ir "
                . " SET reset_code = '" . $code . "' "
                . " , reset_datetime = '" . $this->getFormatedDateTime() . "' "
                . " WHERE ir_id = '" . $ir_id . "'";
        if ($database_manager->query($sql)) {
            return $code;
        } else {
            return false;
        }
    }

    function getFormatedDate() {
        return date("Y-m-d");
    }

    function getFormatedDateTime() {
        return date("Y-m-d H:i:s");
    }

    function getIdFromResetCode($database_manager, $reset_code) {
        $sql = "Select ir_id FROM ir "
                . " WHERE reset_code = '" . $reset_code . "' "
                . " AND DATE_ADD(reset_datetime, INTERVAL 2 HOUR) > '" . $this->getFormatedDateTime() . "' ";
        $result = $database_manager->query($sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['ir_id'];
        } else {
            return false;
        }
    }
	///function to check if the IR exist and bu001 is qualified to regester new ir 
	function checkqualifiedIR($irid,$database_manager,$bu_code="001"){
		$sql="Select r.id,r.ir_id,is_qualified FROM bu b  LEFT OUTER JOIN ir r ON r.ir_id = b.ir_id WHERE b.ir_id = '".$irid."' and code='".$bu_code."'";
		$result = $database_manager->query($sql);
	   $row = mysqli_fetch_assoc($result);  
	   $is_qualified=$row["is_qualified"];
	   if (mysqli_num_rows($result) > 0) {
		   if($is_qualified>0 ){
			   return "true";
		   }else{
			   return "IR exist, but bu001 is not qualified";
		   }
	   }else{
		   return false;
	   }
	}
	
	/////function to validate that the IR has 7 business uinits in bu table
	function validateIR_Bu($irid,$database_manager){
		$sql="Select r.id,r.ir_id FROM bu b  LEFT OUTER JOIN ir r ON r.ir_id = b.ir_id WHERE b.ir_id = '".$irid."'";
		$result = $database_manager->query($sql);
	   if (mysqli_num_rows($result) == 7) {  
			return true;
	   }else{
		   return false;
	   }
	}
	
    function getMyLastRenewalDate($database_manager) {
        $sql = "Select last_renewal_date FROM ir "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['last_renewal_date'];
    }

    function addToDate($date, $days) {
        $date = strtotime("+" . $days . " days", strtotime($date));
        return date("Y-m-d", $date);
    }

    function drawBUsForTodayCouter($database_manager, $html_page, $ir_id, $additional_class = "") {
        if ($ir_id != "") {
			////get the daily counter of each bu ////not used in our new system
            $result = $this->getDailyCounter($database_manager, $this->getFormatedDate(), $ir_id);

            $is_qualified = array();
            $sql_qualified = "SELECT is_qualified FROM bu WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
                    . " ORDER BY code ASC ";
            $result_qualified = $database_manager->query($sql_qualified);
        }

        if ($ir_id != "" && mysqli_num_rows($result) == 3) {
            $row = mysqli_fetch_assoc($result);
            $row_qualified = mysqli_fetch_assoc($result_qualified);
            echo '<div class="bu-div ' . $additional_class . '">';
            $html_page->drowBU($ir_id . "-001", $row_qualified['is_qualified'], $row['left_dc'], $row['right_dc']);
            echo '<div class="clear"></div>';
            $row = mysqli_fetch_assoc($result);
            $row_qualified = mysqli_fetch_assoc($result_qualified);
            $html_page->drowBU($ir_id . "-002", $row_qualified['is_qualified'], $row['left_dc'], $row['right_dc']);
            $row = mysqli_fetch_assoc($result);
            $row_qualified = mysqli_fetch_assoc($result_qualified);
            $html_page->drowBU($ir_id . "-003", $row_qualified['is_qualified'], $row['left_dc'], $row['right_dc']);
            echo '</div>';
        } else {
            echo '<div class="bu-div">';
            $html_page->drowBU(" ", 0, " ", " ");
            echo '<div class="clear"></div>';
            $html_page->drowBU(" ", 0, " ", " ");
            $html_page->drowBU(" ", 0, " ", " ");
            echo '</a></div>';
        }
    }

    function drawBUs($database_manager, $html_page, $ir_id, $additional_class = "") {
		
        $sql = "Select CONCAT(title, ' ', f_name, ' ', l_name) AS name, code, left_dbv, right_dbv, left_abv, right_abv, is_qualified FROM bu b "
                . " LEFT OUTER JOIN ir r ON r.ir_id = b.ir_id "
                . " WHERE b.ir_id = '" . $ir_id . "' "
                . " ORDER BY code ASC ";

        if ($ir_id != "") {
            $result = $database_manager->query($sql);
        }

        if ($ir_id != "" && mysqli_num_rows($result) == 7) {
            $row = mysqli_fetch_assoc($result);
            echo '<div class="bu-div ' . $additional_class . '"><a href="' . $this->getUrl() . '?page=genealogy_tree&ir_id=' . $ir_id . '">';
            echo "<p class='ir_name'>" . $row['name'] . "</p>";
            $html_page->drowBU($ir_id . "-" . $row['code'], $row['is_qualified'], $row['left_dbv'], $row['right_dbv'], $row['left_abv'], $row['right_abv']);
            echo '<div class="clear"></div>';
            $row = mysqli_fetch_assoc($result);
            $html_page->drowBU($ir_id . "-" . $row['code'], $row['is_qualified'], $row['left_dbv'], $row['right_dbv'], $row['left_abv'], $row['right_abv']);
            $row = mysqli_fetch_assoc($result);
            $html_page->drowBU($ir_id . "-" . $row['code'], $row['is_qualified'], $row['left_dbv'], $row['right_dbv'], $row['left_abv'], $row['right_abv']);
            echo '</a></div>';
        } else {
            echo '<div class="bu-div">';
            $html_page->drowBU(" ", 0, " ", " ", " ", " ");
            echo '<div class="clear"></div>';
            $html_page->drowBU(" ", 0, " ", " ", " ", " ");
            $html_page->drowBU(" ", 0, " ", " ", " ", " ");
            echo '</a></div>';
        }
    }//end function
	
	function drawBUs_new($database_manager, $html_page, $ir_id, $additional_class = "") {

		$sql="Select CONCAT(title, ' ', f_name, ' ', l_name) AS name, code,  left_abv, right_abv, is_qualified FROM bu b  LEFT OUTER JOIN ir r ON r.ir_id = b.ir_id WHERE b.ir_id = '".$ir_id."' ORDER BY code ASC";

        if ($ir_id != "") {
            $result = $database_manager->query($sql);
        }

        if ($ir_id != "" && mysqli_num_rows($result) == 7) {
            
			////geneology container
			
			echo '<div class="col-xs-12 col-md-12 col-lg-12 geneologyTree">';
			///<!-----Row that contains Main IR box for BU 001--->
			////draw row1 - bu001
			$isBU1orIR0=1;///draw bu not IR
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			
			///draw row2
			echo '<div  class="col-xs-12 col-md-12 col-lg-12 row2">';
			///bu002
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			
			////bu003
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			echo '</div>';
			//////////////
			//////////draw row3
			echo '<div  class="col-xs-12 col-md-12 col-lg-12 row3">';
			///draw bu004
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			///draw bu005
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			///draw bu006
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);		
			///draw bu007
			$row = mysqli_fetch_assoc($result);
			$IRID=$ir_id;
			$IRname=$row["name"];
			$bu_id=$row["code"];
			$qualified=$row["is_qualified"];
			$leftcounter=$row["left_abv"];
			$rightcounter=$row["right_abv"];
			$bucode=$row["code"];
			$html_page->drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
						
			echo '</div><!--end row3-->';
			/////////////
			
			/////////draw row4 (IR boxes)
			echo '<div  class="col-xs-12 col-md-12 col-lg-12 row4">';
			////draw IR row
			$isBU1orIR0=0;
			
			///draw bu004 left IR
			$top_ir_bu_code="004";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			
			$bu_id=$this->getLeftChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details,draw empty box
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '<div class="col-xs-1 col-md-1 col-lg-1"></div>';
			///draw bu004 right IR
			$top_ir_bu_code="004";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			//die($top_ir_id ."-". $top_ir_bu_code);
			$bu_id=$this->getRightChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '';
			///draw bu005 left IR
			$top_ir_bu_code="005";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			$bu_id=$this->getLeftChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}	
			echo '<div class="col-xs-1 col-md-1 col-lg-1"></div>';
			///draw bu005 right IR
			$top_ir_bu_code="005";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			$bu_id=$this->getRightChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			/*echo $IRDetails;*/
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '';
			///draw bu006 left IR
			$top_ir_bu_code="006";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			$bu_id=$this->getLeftChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '<div class="col-xs-1 col-md-1 col-lg-1"></div>';
			///draw bu006 right IR
			$top_ir_bu_code="006";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			$bu_id=$this->getRightChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '';
			///draw bu007 left IR
			$top_ir_bu_code="007";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			$bu_id=$this->getLeftChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '<div class="col-xs-1 col-md-1 col-lg-1"></div>';
			///draw bu007 right IR
			$top_ir_bu_code="007";//to find the left or right bu
			$top_ir_id=$IRID;///the top IR that it's bu are shown
			$bu_id=$this->getRightChildBUID($database_manager, $top_ir_id ."-". $top_ir_bu_code);
			$IRID1=$this->getIRID($bu_id);///get the IR at left of bu004
			$IRDetails=$this->getIRAndBu001Details($database_manager,$IRID1); ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
			//echo "fdfdf".$IRDetails;
			if($IRDetails!=false){
				$IRname=$IRDetails["name"];
				$qualified=$IRDetails["is_qualified"];
				$leftcounter=$IRDetails["left_abv"];
				$rightcounter=$IRDetails["right_abv"];
				$bucode= $IRDetails["code"];
				$html_page->drawBU_andIR_new($IRID1,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}else{///if there is IR details
				$html_page->drawBU_andIR_new(0,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode);
			}
			echo '';
			echo '</div><!----end row4--->';
			
			echo "</div><!---end tree container-->";	
			
        } else {///if no IR or empty box

        }
    }///end function
	
	///function to get the details of IR and bu001 of the IR
	function getIRAndBu001Details($database_manager,$IRID) {
       ///returned info is IR: id, name , ArName, email,mobile,phone,address,reg_date,code,left_abv,right_abv, is_qualified
	   $sql="Select r.id as id,CONCAT(title, ' ', f_name, ' ', l_name) AS name, a_name AS ArName,email, mobile,phone,address,registration_date AS reg_date, code,  left_abv, right_abv, is_qualified FROM bu b  LEFT OUTER JOIN ir r ON r.ir_id = b.ir_id WHERE b.ir_id = '".$IRID."' limit 1";
	   $result = $database_manager->query($sql);
	   $row = mysqli_fetch_assoc($result);  
	   if (mysqli_num_rows($result) > 0) {
			 
			return $row;
	   }else{
		   return false;
	   }
    }
	
    function getUrl() {
       // return "http://proshopsllc.com/vo/index.php";
	   //$lang=$_COOKIE['lang'];
	   
	   return "http://khalifacomputergroup.com/be3li/vo/index.php";
	  // return "http://localhost/be3ly/vo/index.php";
    }
	
    function getLeftChildBUID($database_manager, $bu_id) {
        $bu_code = $this->getBUCode($bu_id);
        $ir_id = $this->getIRID($bu_id);

        if ($bu_code == "001") {
            return $this->getBUID($ir_id, "002");
        }
		

        $sql = "SELECT CONCAT(ir_id, '-', code) AS bu_id FROM bu "
                . " WHERE parent_bu_id = '" . $bu_id . "' "
                . " AND position = 'left' ";
        $result = $database_manager->query($sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['bu_id'];
        } else {
            return false;
        }
    }

    function getRightChildBUID($database_manager, $bu_id) {
        $bu_code = $this->getBUCode($bu_id);
        $ir_id = $this->getIRID($bu_id);

        if ($bu_code == "001") {
            return $this->getBUID($ir_id, "003");
        }

        $sql = "SELECT CONCAT(ir_id, '-', code) AS bu_id FROM bu "
                . " WHERE parent_bu_id = '" . $bu_id . "' "
                . " AND position = 'right' ";
        $result = $database_manager->query($sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['bu_id'];
        } else {
            return false;
        }
    }

    function getBinaryParentBUID($database_manager, $bu_id) {
        $bu_code = $this->getBUCode($bu_id);
        $ir_id = $this->getIRID($bu_id);

        if ($bu_code == "002" || $bu_code == "003") {
            return $this->getBUID($ir_id, "001");
        }

        $sql = "SELECT parent_bu_id AS bu_id FROM bu "
                . " WHERE ir_id = '" . $ir_id . "' "
                . " AND code = '" . $bu_code . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);

        if ($row['parent_bu_id'] != "0") {
            return $row['parent_bu_id'];
        } else {
            return false;
        }
    }
	////check if search for IR is in the downline network not upline
    function isBinaryIRChild($database_manager, $parent_ir, $child_ir) {
        $sql = "SELECT id FROM bu "
                . "WHERE "
                . " (left_children LIKE '%" . $child_ir . "%' "
                . " OR right_children LIKE '%" . $child_ir . "%' )"
                . " AND ir_id = '" . $parent_ir . "' ";
        $result = $database_manager->query($sql);
        return mysqli_num_rows($result) > 0;
    }

    private function ewalletPasswordNotSet() {
        return !isset($_SESSION['ewallet_secret']) || $_SESSION['ewallet_secret'] != "hd48fun44949j49vn4r9vjn49j4f9v4jfjfFF";
    }

    function checkEwalletPassword($redirect) {
        if ($this->ewalletPasswordNotSet()) {
            header("Location: " . $this->getURL() . "?page=ewallet_password&redirect=" . $redirect);
        }
    }

    function getBUCode($bu_id) {
        return substr($bu_id, -3);
    }

    function getIRID($bu_id) {
        return substr($bu_id, 0, 8);
    }

    function getBUID($ir_id, $bu_code) {
        return $ir_id . "-" . $bu_code;
    }

    function getLeftChildrenBUIDsString($database_manager, $bu_id) {
        $sql = "SELECT left_children FROM bu "
                . " WHERE ir_id = '" . $this->getIRID($bu_id) . "' "
                . " AND code = '" . $this->getBUCode($bu_id) . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['left_children'];
    }

    function getRightChildrenBUIDsString($database_manager, $bu_id) {
        $sql = "SELECT right_children FROM bu "
                . " WHERE ir_id = '" . $this->getIRID($bu_id) . "' "
                . " AND code = '" . $this->getBUCode($bu_id) . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['right_children'];
    }

    function getLeftReferralsBUIDsString($database_manager, $bu_id) {
        $sql = "SELECT left_referrals FROM bu "
                . " WHERE ir_id = '" . $this->getIRID($bu_id) . "' "
                . " AND code = '" . $this->getBUCode($bu_id) . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['left_referrals'];
    }

    function getRightReferralsBUIDsString($database_manager, $bu_id) {
        $sql = "SELECT right_referrals FROM bu "
                . " WHERE ir_id = '" . $this->getIRID($bu_id) . "' "
                . " AND code = '" . $this->getBUCode($bu_id) . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['right_referrals'];
    }

    function paginationBeforeTable($database_manager, $page_name, $sql) {

        if (isset($_GET['start']) && $_GET['start'] > 0) {
            $start = $_GET['start'];
        } else {
            $start = 0;
        }
        $result = $database_manager->query($sql);
        $num_rows = mysqli_num_rows($result);
        if (!$num_rows > 0) {
            $num_rows = 0;
        }

        $sql .= " LIMIT " . $start . ", 10";
        $result = $database_manager->query($sql);

        $page = (int) (($start + 10) / 10);
        $num_pages = (int) (($num_rows + 10) / 10);

        if ($num_rows > $start + 10) {
            $end = $start + 10;
        } else {
            $end = $num_rows;
        }
        ?>
        <div class="page-info">
            Page <?php echo $page . " / " . $num_pages; ?>
        </div>

        <div class="total-records">
            Total: <?php echo $num_rows; ?> rows
        </div>

        <div class="showing">
            Showing records <?php echo $start . " to " . $end; ?>
        </div>

        <div class="pagination">
            <?php
            if ($start != 0) {
                echo '<a href="' . $this->getURL() . '?page=' . $page_name . '&start=' . (string) ($start - 10) . '"><i class="fa fa-arrow-circle-left fa-fw"></i></a>';
            } else {
                echo '<i style="color: #aaa;" class="fa fa-arrow-circle-left fa-fw"></i>';
            }

            if ($end < $num_rows) {
                echo '<a href="' . $this->getURL() . '?page=' . $page_name . '&start=' . (string) ($start + 10) . '"><i class="fa fa-arrow-circle-right fa-fw"></i></a>';
            } else {
                echo '<i style="color: #aaa;" class="fa fa-arrow-circle-right fa-fw"></i>';
            }
            ?>
        </div>
        <?php
        return $result;
    }

    function fundTransfer($database_manager, $to_ir_id, $amount) {
        if ($to_ir_id == $_SESSION['ir_id']) {
            return $_SESSION["language"]->fundtransfer_msg_canttransfertoself;
        }

        $sql = "SELECT ewallet FROM ir "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $ewallet = $row['ewallet'];
        if ($ewallet < $amount) {
            return $_SESSION["language"]->fundtransfer_msg_donthaveenoughmoneyewallet;
        }
        $from_balance = $row['ewallet'];

        $sql = "SELECT ewallet FROM ir "
                . " WHERE ir_id = '" . $to_ir_id . "'";
        $result = $database_manager->query($sql);
        if (mysqli_num_rows($result) == 0) {
            return $_SESSION["language"]->fundtransfer_msg_invalidIRID;
        }
        $row = mysqli_fetch_assoc($result);
        $to_balance = $row['ewallet'];

        $sql = "UPDATE ir SET ewallet = ewallet - " . $amount . " "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);

        $sql = "UPDATE ir SET ewallet = ewallet + " . $amount . " "
                . " WHERE ir_id = '" . $to_ir_id . "'";
        $database_manager->query($sql);

        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Fund Transferred Out', '";
        $sql .= $this->getFormatedDateTime() . "', '" . (string) (0 - $amount) . "', '" . (string) ($from_balance - $amount) . "', '" . $to_ir_id . "')";
        $database_manager->query($sql);

        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $to_ir_id . "', 'Fund Transferred In', '";
        $sql .= $this->getFormatedDateTime() . "', '" . (string) ($amount) . "', '" . (string) ($to_balance + $amount) . "', '" . $_SESSION['ir_id'] . "')";
        $database_manager->query($sql);

        return $_SESSION["language"]->fundtransfer_msg_transferedsuccess;
    }

    function getDailyCounter($database_manager, $date, $ir_id) {
        $sql = "Select bu_id, left_dc, right_dc FROM dc "
                . " WHERE bu_id LIKE '" . $ir_id . "%' "
                . " AND date = '" . $date . "'"
                . " ORDER BY bu_id ASC ";
        $result = $database_manager->query($sql);
        if (mysqli_num_rows($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    function getShopType($is_qualified) {
        if ($is_qualified == 0) {
            return "Not Qualified";
        }
        if ($is_qualified == 1) {
            return "Retail Shop";
        }
        if ($is_qualified == 2) {
            return "Binary Shop";
        }
    }

    function hasEnoughMoney($database_manager, $ir_id, $total_ewallet) {
        $sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $ir_id . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return (int) $row['ewallet'] >= (int) $total_ewallet;
    }

    function hasEnoughRedeemPoints($database_manager, $ir_id, $total_redeem) {
        $sql = "SELECT rpts FROM ir WHERE ir_id = '" . $ir_id . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        return (int) $row['rpts'] >= (int) $total_redeem;
    }

    function getIRIDsStringFromBUIDsString($bu_ids_string) {
        $ir_ids_string = "";
        $bu_ids = split(", ", $bu_ids_string);

        foreach ($bu_ids AS $bu_id) {
            $ir_ids_string .= $this->getIRID($bu_id) . "', ";
        }
        return substr($ir_ids_string, 0, -3);
    }
	///returns the array of downline IRs in levelX 
	////caling function for first time getLevelX_downlineArr(target level depth,$database_manager,$ir_ids_arr = referral irid as array,$level_counter=1,$return_levels_ir_Obj=array() dont add this to the call)
	function getLevelX_downlineArr($levelX,$database_manager,$ir_ids_arr,$level_counter,$return_levels_ir_Obj=array()){
		$buarray=array("004","005","006","007");
		$iridsarr_length=sizeof($ir_ids_arr);
		//$level_counter=1;
		//echo "<br>"."level counter ".$level_counter."<br>";
		//echo "number of iirid array ".$iridsarr_length."<br>";
		$resulted_levels_irarr=array();
		///loop for irs in single level and store list of irs in array
		for($i=0;$i<$iridsarr_length;$i++){
			$sql = "SELECT ir_id,code FROM `bu` WHERE `parent_bu_id`LIKE '".$ir_ids_arr[$i]."%' and ir_id != '".$ir_ids_arr[$i]."' order by parent_bu_id ASC , position ASC";
			$result = $database_manager->query($sql);
			///if there is downlines, loop through and add to the reslut ir array
			//echo "Query  ".$sql."<br>";
			//echo "Query number of rows ".mysqli_num_rows($result)."<br>";
			if (mysqli_num_rows($result) > 0) {
				while ($row = mysqli_fetch_assoc($result)) {
						$resulted_levels_irarr[]= $row['ir_id']; ///("ir"=>$row['ir_id'],"name"=>$row['irname'],"is_qualified"=>$row['is_qualified']);
						//echo "resluted query irid  ".$row['ir_id']."<br>";
				}
				
			}
			
		}///end looping through number of received IRs
		////if the resulted downline ir array >0 add it to the returned obj
		if( sizeof($resulted_levels_irarr) > 0){
			$return_levels_ir_Obj[]=$resulted_levels_irarr;
		}
		//echo " rprint resulted levels irarr <br>";
		//print_r($resulted_levels_irarr);
		//echo "<br>"."size of resulted levels irarr ".sizeof($resulted_levels_irarr)."<br>";
		///check if theier is irs in this level , if yes call the function again to get the next levels ir else return the resulted object
		if( sizeof($resulted_levels_irarr) > 0 && $level_counter<$levelX ){
			$level_counter++;
			return $this->getLevelX_downlineArr($levelX,$database_manager, $resulted_levels_irarr, $level_counter, $return_levels_ir_Obj);
		}else{
			return $return_levels_ir_Obj;
		}
		
	}
	////function to get the empty slot in downline levels for automatic registration of new IR, according to criteria level by level from top down , left to right,
	////Returns upline IR, and bu number and position left or right
	function findFreeSlotinDownlinelevels($database_manager,$ref_ir,$bu_arr,$level=1){
		///bu_arr in this case 004,005,006,007
		////initialize vars
		//; ///start finding slots in level 1
		
		$ir_ids_arr=array($ref_ir);
		$level_counter=1;
		$searchdepth=7;///max search in 7 levels depth
			////get the object of IRs in levels 1 to max 7 or the first empty level in downline to ref_ir 
		$result=$this->getLevelX_downlineArr($searchdepth,$database_manager, $ir_ids_arr, $level_counter, $return_levels_ir_Obj);
		/*var_dump($result);*/
		$MaxNoLevels=sizeof($result);
		//echo $MaxNoLevels; 
		$found=false;
		$retuned_level=0;
		$found=$this->searchEmpgySlotBelowIR($database_manager,$ref_ir,$bu_arr);
		if(!$found){
			for($level=0;$level<$MaxNoLevels;$level++){////loop through levels
				
				$actualLevel=$level+2;
				$retuned_level=$level+2;
				//echo $actualLevel; 
				$maxNumOfIRsinLevel=pow(8,$actualLevel); ///8,64,512,4096,32768,262144,2097152
				//echo $maxNumOfIRsinLevel; 
				$numberofIRsinLevelX=sizeof($result[$level]);
				//echo $numberofIRsinLevelX; 
				if($numberofIRsinLevelX<$maxNumOfIRsinLevel){////if the number of irs in levelx is less than the max no of irs in that level,then there is a slot, find it according to the criteria and return the upline data
				////check the main ref_ir for free slots if found break the loop and return the object, else loop through irs in next level
					$found=$this->searchEmpgySlotBelowIR($database_manager,$ref_ir,$bu_arr);
					/*echo "first search result <br>";*/
					/*var_dump($found); */
					
						for($ii=0;$ii<sizeof($result[$level]);$ii++){////loop through IRS in each level
							//echo "<br> level".($level+1)." ".$result[$level][$ii];
							$found=$this->searchEmpgySlotBelowIR($database_manager,$result[$level][$ii],$bu_arr);
							if($found){								/*var_dump($found); */
								break;
							}
						}/*loop through IRS in each level*/
					
					/////start finding the empty slots in that level by checking the ref_ir bu_arrays left to right
					
				}
				if($found){
					break;
				}
			}/*end loop through levels*/
		}else{
			$retuned_level=1;
		}
		$returned_Obj=array("level"=>$retuned_level,"upline"=>$found);
		return $returned_Obj;
			///compare number of IRs in level to max number of irs in each level according to the array
	}
	////function to search empty slots below an IR , returns false if no free slots, otherwize return obj (irid,bu,position)
	function searchEmpgySlotBelowIR($database_manager,$ref_ir,$bu_arr){
		/////start finding the empty slots in that level by checking the ref_ir bu_arrays left to right
			$emptySlot_IRID="";
			$emptySlot_BU="";
			$emptySlot_position="";	
			//var_dump($bu_arr);
				for($b=0;$b<sizeof($bu_arr);$b++){
					$position="left";///start with left position to bu
					$children_column = $position . "_children";
					//echo $children_column;
					$sql = "SELECT " . $children_column . " FROM bu "
							. " WHERE ir_id = '" . $ref_ir . "' "
							. " AND code = '" . $bu_arr[$b] . "' ";
					
					$result = $database_manager->query($sql);
					$row = mysqli_fetch_assoc($result);
					$children_string = $row[$children_column];
					//echo $children_string;
					if (strlen($children_string) < 5) {
						$emptySlot_IRID= $ref_ir ;
						$emptySlot_BU= $bu_arr[$b];
						$emptySlot_position=$position;
						break;
					}
					$position="right";
					$children_column = $position . "_children";
					//echo $children_column;
					$sql = "SELECT " . $children_column . " FROM bu "
								. " WHERE ir_id = '" . $ref_ir . "' "
								. " AND code = '" . $bu_arr[$b] . "' ";
					$result = $database_manager->query($sql);
					$row = mysqli_fetch_assoc($result);
					$children_string = $row[$children_column];
					/*echo $children_string;*/
					if (strlen($children_string) < 5) {
						$emptySlot_IRID= $ref_ir ;
						$emptySlot_BU= $bu_arr[$b];
						$emptySlot_position=$position;
						//echo strlen($children_string) < 5;
						break;
						
					}
				}
				 
				if($emptySlot_IRID !="" && $emptySlot_BU !="" && $emptySlot_position!=""){
					$returned_EmptySlot_obj=array("irid"=>$emptySlot_IRID,"bu"=>$emptySlot_BU,"position"=>$emptySlot_position);
					return $returned_EmptySlot_obj;
				}else{
					return false;
				}
				
	}
	/////starting at the referreal irid and selected bu and position , loop down in the tree looking for ir-bu's until you find empty col left_children or right_children then return that ir and bu , and this is the Upline in the netwrok
    function calculateUpline($database_manager, $position, $current_ir_id, $current_business_unit) { 
        ////get the left or right string from the ref ir bu selected by user at registration
		$children_column = $position . "_children";
        $sql = "SELECT " . $children_column . " FROM bu "
                . " WHERE ir_id = '" . $current_ir_id . "' "
                . " AND code = '" . $current_business_unit . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $children_string = $row[$children_column];
		/*echo strlen($children_string)."<br>" ;*/
		///if the left or right ref ir bu is empty , 
        if (strlen($children_string) < 5) {
            return $current_ir_id . "-" . $current_business_unit;
        } else {
            $current_ir_id = $this->getIRID(substr($children_string, 1, 12));
			/*echo $current_ir_id."<br>" ;*/
            $current_business_unit = $this->getBUCode(substr($children_string, 1, 12));
			/*echo $current_business_unit."<br>" ;*/
            return $this->calculateUpline($database_manager, $position, $current_ir_id, $current_business_unit);
        }
    }
	function is_bu_qualified($ir_id,$bucode,$database_manager){
		$sql = "Select is_qualified FROM bu WHERE ir_id = '" . $ir_id . "' and code = '".$bucode."' ";
		$result = $database_manager->query($sql);
		$isqualified=0;
		$row = mysqli_fetch_assoc($result);
		$isqualified=$row["is_qualified"];
		
		return $isqualified;
	}
	//////new functions for retail newtork by KCG, get all IRs in LevelXXX under IRID
	function getLevelXRetailNetwork($database_manager,$ir_id,$level) {
       
		switch ($level) {
			case 0: ///direct customers of the IR(ir_id), purchased products through the IR(ir_id) shop
			
			break;
			case 1: ///All IR referred by and registered through this IR (ir_id)
				//$sql = "SELECT `ir_id` FROM `bu` WHERE SUBSTRING(referral_bu_id,1,CHAR_LENGTH(referral_bu_id) -4 ) ='".$ir_id."' and code ='001'";
				$sql = "SELECT bu.ir_id ,bu.is_qualified ,CONCAT(ir.f_name, '  ', ir.l_name,' (',ir.a_name,')') as irname FROM bu  inner join ir on bu.ir_id = ir.ir_id WHERE SUBSTRING(bu.referral_bu_id , 1 , CHAR_LENGTH(bu.referral_bu_id) -4 ) = '".$ir_id."' and code ='001'";
				$result=$database_manager->query($sql);
				$level1_arr=array(); 
				while ($row = mysqli_fetch_assoc($result)) {
					$level1_arr[]= array("ir"=>$row['ir_id'],"name"=>$row['irname'],"is_qualified"=>$row['is_qualified']);
				}
					return $level1_arr;

			break;
			case 2: ///means customers of the IR
				$level2_arr=array();
				$level1_arr=array(); 
				//get the level one irs
				$level1_arr=$this->getLevelXRetailNetwork($database_manager,$ir_id,1);
				///loop through each level 1 ir and add to the level2 array
				for ($i = 0; $i < count($level1_arr); $i++) {	
					$level2_per_ir=$this->getLevelXRetailNetwork($database_manager,$level1_arr[$i]["ir"],1); 
					$level2_arr=array_merge($level2_arr,$level2_per_ir);
				}
				return $level2_arr;
			break;
		}

    }
	//////new functions for retail newtork by KCG, get the upline referral to levelX
	function getUpReferralLevelX($database_manager,$ir_id,$level) { ///ir_id referral to get the upline ref ir, level 1 or 2 returns array of (level-1,level-2) two uplin IRs

				//$sql = "SELECT SUBSTRING(bu.referral_bu_id , 1 , CHAR_LENGTH(bu.referral_bu_id) -4 ) as upir, CONCAT(ir.f_name, '  ', ir.l_name,' (',ir.a_name,')') as upirname, bu.is_qualified is_qualified FROM bu  inner join ir on SUBSTRING(bu.referral_bu_id , 1 , CHAR_LENGTH(bu.referral_bu_id) -4 ) = ir.ir_id  WHERE bu.ir_id='".$ir_id."' and code='001';";
				
				$sql ="SELECT SUBSTRING(bu.referral_bu_id , 1 , CHAR_LENGTH(bu.referral_bu_id) -4 ) as upir, CONCAT(ir.f_name, '  ', ir.l_name,' (',ir.a_name,')') as upirname, refbu.is_qualified is_qualified FROM bu  left join ir on SUBSTRING(bu.referral_bu_id , 1 , CHAR_LENGTH(bu.referral_bu_id) -4 ) = ir.ir_id left join bu refbu on SUBSTRING(bu.referral_bu_id , 1 , CHAR_LENGTH(bu.referral_bu_id) -4 )= refbu.ir_id WHERE bu.ir_id='".$ir_id."' and bu.code='001' and refbu.code='001';";
				$result=$database_manager->query($sql);
				
				$levelx_arr=array(); 
				
				while ($row = mysqli_fetch_assoc($result)) {
					$levelx_arr[]= array("ir"=>$row['upir'],"name"=>$row['upirname'],"is_qualified"=>$row['is_qualified']);
				}
					if($level>1){
						for($ii=0;$ii<($level-1);$ii++){
							$levelx_arr[]=$this->getUpReferralLevelX($database_manager,$levelx_arr[$ii]["ir"],1);
						}
						return $levelx_arr;
					}else{
						return $levelx_arr[0];	
					}

    }
	//////new functions for retail newtork by KCG, returns list of orders products, and irs, commissions that matches specific criteria
	function getListOrderProductsIRsForRComm($database_manager,$orderStatus,$daySinceOrd,$isCommsPaid) { ///ir_id
		//$orderStatus='Delivered' / '' / '' ;
		$sql = "select ord.id OrderID, ord.ir_id IRID,ordpro.id orderlineid, ordpro.product_id ProductID, ordpro.price productPrice, ordpro.p_l0_com com0, ordpro.p_l1_com com1, ordpro.p_l2_com com2 from shop_order ord left join shop_order_line ordpro on ordpro.shop_order_id=ord.id  where ord.status = '".$orderStatus."' and ord.shop_title='My Shop' and datediff(NOW(), ord.datetime) >= '".$daySinceOrd."' and ordpro.coms_paid ='".$isCommsPaid."' order by OrderID";
		$result=$database_manager->query($sql);
		$IRsProducts_arr=array();
		while ($row = mysqli_fetch_assoc($result)) {
			$IRsProducts_arr[]=$row;
		}
		return $IRsProducts_arr;
	}//efn
/***************************Recharge Card functions****************/
	///function to card balance
	function getcardbalance($cardnumber,$database_manager,$returnmsg){
		$sql = "SELECT cardval from scrachcards where cardnumber = '" . $cardnumber . "';"; 
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);
		if ($row['cardval']>0){
			return $row["cardval"];
		}else{
			if($returnmsg==1){
				return $_SESSION["language"]->fundtransfer_msg_invalidcardnumber;
			}else{
				return 0;
			}
			
		}
	}
	
		///function confirm card psw options 1, return emsg, 2 return zerfo, 3 return nothing if succeeded (default is card id)
	function matchcardpsw($cardnumber,$cardpsw,$database_manager,$returnmsg){
		$sql = "SELECT id from scrachcards where cardnumber = '".$cardnumber."' and cardpsw = '".$cardpsw ."';"; 
		$result = $database_manager->query($sql);
		//$row = mysqli_fetch_assoc($result);
		if($row = mysqli_fetch_assoc($result)){
			if($returnmsg==3){
				return "";
			}else{
				return $row['id'];
			}
			
		}else{
			if($returnmsg==1 || $returnmsg==3){
				return $_SESSION["language"]->fundtransfer_msg_invalidcardpsw;
			}else{
				return 0;
			}
		}
	}
	
	////function to recharge ewallet with card
	function rechargeewalletwithcard($cardnumber,$cardpsw ,$ir_id,$database_manager){
		$success=$_SESSION["language"]->returnsuccess;
		
		////double check the password
		$PSWcheck=$this->matchcardpsw($cardnumber,$cardpsw,$database_manager,0);
		if($PSWcheck<1){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_invalidcardpsw;
		}
		////get card balance
		$cardbalance=$this->getcardbalance($cardnumber,$database_manager,0);
		
		if($cardbalance<1){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cardbalancezero;
		}
		////update ewallet balance +card balance
		$sql = "UPDATE ir SET ewallet = ewallet + " . $cardbalance . " "
                . " WHERE ir_id = '" . $ir_id . "';";
        $result=$database_manager->query($sql);
		if(!$result){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cantupdateewallet;
		}
		
		///get the ewallet new balance
		$sql = "SELECT ewallet FROM ir "
                . " WHERE ir_id = '" . $ir_id . "';";
        $result = $database_manager->query($sql);
		if(!$result){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cantgetewalletbalance;
		}
        $row = mysqli_fetch_assoc($result);
        $ewallet = $row['ewallet'];
		
		///add transaction record to ewallet transactions
        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $ir_id . "', '".$_SESSION["language"]->fundtransfer_cmnt_ewalletrecharge."', '";
        $sql .= $this->getFormatedDateTime() . "', '" . (string) ($cardbalance) . "', '" . (string) ($ewallet + $cardbalance) . "', '" . $cardnumber . "');";
        $result= $database_manager->query($sql);
		if(!$result){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cantudateewallettrans;
		}
		
		////zero card balance
		$sql = "UPDATE scrachcards SET cardval = cardval - " . $cardbalance . " "
                . " WHERE cardnumber = '" . $cardnumber . "';";
        $result=$database_manager->query($sql);
		if(!$result){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cantudatecardbalance;
		}
		
		///get the card new balance
		$sql = "SELECT id,irid,cardval from scrachcards where cardnumber = '".$cardnumber."';";    
        $result = $database_manager->query($sql);
		if(!$result){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cantgetcardbalance;
		}
        $row = mysqli_fetch_assoc($result);
        $cardid = $row['id'];
		$cardnewbal = $row['cardval'];
		$cardcomment="IR used the card for recharging his ewallet";
		////add transaction record to card transactions
		$sql = "INSERT INTO `scrachcards_trans`(`datetime`, `ir_id`, `value`, `cardbalance`, `type`, `comment`, `cardid`) VALUES ('".$this->getFormatedDateTime()."','".$ir_id."','".$cardbalance."','".$cardnewbal."','".$_SESSION["language"]->fundtransfer_cmnt_ewalletrecharge."','".$cardcomment."','".$cardid."');";
       $result= $database_manager->query($sql);
		if(!$result){
			$success=false;
			return $_SESSION["language"]->fundtransfer_msg_cantinsertcardtrans;
		}else{
			return $success;
		}
		////return success
		
	}
}
?>