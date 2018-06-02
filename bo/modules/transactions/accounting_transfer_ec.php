<?php
startPage();
$errormsg="";
$accid="acc01";
			 
///get the accountant last transaction and balance
$sql = "select bal_ec,bal_le,comment from k8_acc_wallet_trns  where code='".$accid."' order by id desc limit 1";
    if ($result = mysql_query($sql)) {
       $errormsg="";
    } else {
		 $errormsg="error reading current balance 1";
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
	$curr_acc_bal_ec=$row['bal_ec'];
	$curr_acc_bal_le=$row['bal_le'];
	$curr_acc_comment=$row['comment'];
///get the Managers IRs data
	$managers_irs=array("PA0102","PA0103","PA1431","PA0514");
	$managers_names=array();
	for($ii=0;$ii< sizeof($managers_irs);$ii++){
		$sql2= "select ir_id, f_name, l_name, a_name from ir where ir_id='".$managers_irs[$ii]."'";
		if ($result2 = mysql_query($sql2)) {
			$row2 = mysql_fetch_assoc($result2);
			$errormsg="";
			$fullname_e=$row2["f_name"]." ".$row2["l_name"];
			$managers_names[]=array("ir_id"=> $row2["ir_id"], "fullname_e" => $fullname_e,"fullname_a" => $row2["a_name"] );
		} else {
			$errormsg="error receiving ir data";
			error_log($sql2);
		}
	
	}//end for
	
///////check if submission vars are set then proceed with transfer
	if(isset($_POST) && isset($_POST['ir_id']) && isset($_POST['transfer_val']) && $_POST['secret'] == "4unf9unufru49fnr9" && $_POST['transfer_val']!="" && $_POST['ir_id']!=""){
		//set variables based on post
		$irid=$_POST['ir_id'];
		$transferVal=$_POST['transfer_val'];
		$ir_name="";
		//$accid=""acc01;
		
		
////1///add transaction to k8_acc_wallet_trns for acc01, substract value from balance
	$new_acc_bal_ec= $curr_acc_bal_ec-$transferVal;
	$new_acc_bal_le= ($new_acc_bal_ec*7);
	///get the difference between current balance and new required balance
	$diff_acc_ec=$new_acc_bal_ec-$curr_acc_bal_ec;
	$diff_acc_le=($diff_acc_ec*7);
	
	if($diff_acc_ec>0){
		$trns_type=1;///adding money to balance
		$comment="Adding ".$diff_acc_ec." EC to the Accountant balane - Note: IR: ".$irid;
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
	$new_ir_bal_ec = $curr_ir_bal_ec - $transferVal;
	$new_ir_bal_le = ($new_ir_bal_ec*7);
	///get the difference between current balance and new required balance
	$diff_ir_ec=$new_ir_bal_ec-$curr_ir_bal_ec;
	$diff_ir_le=($diff_ir_ec*7);
	
	if($diff_ir_ec>=0){
		$trns_type=1;///adding money to balance
		$comment="Paying ".$diff_ir_ec." EC to the Accountant balane From IR: ".$irid;
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
////3/////add update target ir ewallet
		 $sql2 = "UPDATE ir SET ewallet = (ewallet + ".$transferVal.") WHERE ir_id = '" . $irid . "'";
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
				$sql4="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('" . $irid . "', 'Reciving money transfer from Proshops accountant' , DATE(NOW()), '".$transferVal."','" . $ewallet . "' , 'Proshops Accountant')";
				$result4 = mysql_query($sql4); 
				$errormsg="Ecs Transfer succeeded, Please print the receipt.";
			?>
			<!------------------------------------Shiping Form----------------------------->
	<div id="printtable_recpt" class="printtable"><!--- print area-->
	<page>
	<div class="sepbold"></div>
		<div class=" printheader">
			<div class="col0">
				
				<div class="sep"></div>
				<div class="col1_mak border_r" style="text-align:center; width:100%;">
					<img style="height:50px;margin:10px 0;" src="&#10;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAABGCAYAAAAD4YAyAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAAAsTAAALEwEAmpwYAAAB1WlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyI+CiAgICAgICAgIDx0aWZmOkNvbXByZXNzaW9uPjE8L3RpZmY6Q29tcHJlc3Npb24+CiAgICAgICAgIDx0aWZmOk9yaWVudGF0aW9uPjE8L3RpZmY6T3JpZW50YXRpb24+CiAgICAgICAgIDx0aWZmOlBob3RvbWV0cmljSW50ZXJwcmV0YXRpb24+MjwvdGlmZjpQaG90b21ldHJpY0ludGVycHJldGF0aW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KAtiABQAAHq1JREFUeAHtXQmYVMW1rqp7b3fPxjAiYojEaIwi8xkBQUFFRnDJYhaDPRrRvLhBBFFw35K5Y97L4gIGIgaMoPJEnVY/IyaIC7QiboCoyfASJbIKyA4zPd13rfefun1n7RlwSehWa+Z27eupc+rUqY2zAlWyLq7x6oS3uq7q4AOMhkt17n3f9tiRkjEtovE1vs8X7E4bM/uMfuN9KRlPVNeJ6kS1d9tlc4dzZvyaCW0HY74tJLclkxbjzJK+tLjGkQR8PM92pb9HSLmJSbGOaWyNed9574XNVRev0yi90J7vup7vBcxVvhDI25884fSY2HVfRJeHpm2feZ7PJKDqMXFMLCKOKWPemD1PDr6W86X3SbkS4GZsa2zb6wdZvRsjWvSsjN3EuBDIAh4ALxecEZgJ0prQAFsySwK65ExsqbnkkdW60J/yDPlg9fTqzfDjtSbjpsn9XOXMJzfUsLBUCORticEjDc1+WvpecdqSDoCjcSjCXgBAAnyuoXOjKKqxtB256MDqpQ/U15mRymrTNi+dUyWl8bzr2QKwIrhmVdvmIPADwuQoAHiuawY6gM4cN7NGF+KXt95XPYciFgJ2t61Ztrr5qpkmE/j8FbOruh9SvON1wbyjGjPSASIaChxZbFSVUlACsDUGmIjtQis/qWf1kn/WxePayspK6a89crGuRU60nIyHoIS8nSjqBwrP0YcUgktdN3RNAzH0vad8j19uPlC92awydTNpup0kst+diW4VjBrOqlR5vxJpHKVzedSeJun4PjNcTzIXTexixPTwkU52mPW0zZyIxnpYduNlVNHKykrNNE0f6P+s4EBoJEDkvvOP0BpoLxkCqw6hu67tZ6wmTwjjR0yTL5mXzDmCgEyYna+NWVCArmJJNRbarjXScnxmO75wXQlSyvCR3vFzXV80ZjzgpD902YzvFyfQBwJg8GWuSzwYiLOi94SvwRfQfxoDWuyhXxCWCQGgp9MNNgjGkVxGHjMn/G83Ys7QifKyTfOyUJ1iRQ1wCipjy96W4zEP2OiAAXO7/CTPgFFzXL8XYxvLCZspDdd2NhJnzSWhNcBHKWe/ALsDuJNba7/QTmEwfEfSmQYHrMBA1qjfTukyVhNoefZbWIDONh4A6xImY/6jAA0gEiA7fugA5E7kXBnTVjN37ErHQXJIgQDZnnTncusYxlc9gOupTIOUnn+JedFDI4gDj+chCS8sQNcqlgvEVqzyASGMw5LGYofG6FwfwIjxGkElqIC/YV3vo7eF46iuiwrf94vxwdtX5Bs6IExfFo27tGfDKtLvYbzWdOnpl1NfrKzEVC7PVGEBul9cMdRNGX2+BXwE5SZMhc6ht/3crB0IjUaHNMTRnq2GgKX04MVKdqD50WMMEaHIPkMa1B0CPTTvzU7h8KGDoZ9otpUG5fDOvGn0rH40PJgmzfDyR+VVYfbaLNUJalo2+Kq3/5xxtL8Wx5iOMdpygZXtx2kSnsDPjhnMaLL4iuKS8pkUt3HzMMWMgfqfDUlYFnlJ/+QfInMPAz46TllUi32b8qmvT6hOSeZ8UAUFaDU1xlyaGi7tl0xIZcQ/uxWJqO9hiPRAyT3p0UdmCLP80qiI2A7f2WDHxva9ZEnD1AkTosQZ33r+rNN8zx+RsVIuMBozLNhyf/DMdgBF4TvvDKAISMdjjmNXUfnyjXznVa+jBtoXJQFsbjL/pbsG94nyhskA86goRCZqSEUCJNW0idUSWtJzYpNOuv7tt2eMGWOMnTnTMX82++tuhr9cZJT1sd0M42C6WxTINf6DHwwFEoDzwMBJTvKxvbSV9DVhCIT8V3GRN/Tm2RdvRbegiCrFljz2j0mNV/sn60+eKwFZAfuapeuRSvzl2wcM97l1RkSThwNQmuOJ9bbPFy5sOGc+jZd1WACprp4Bkg3q7YsjdC4eTVt7MpqmRUHCITjjEeZD0s05ugvXgZsGdQDu+183uHGKK8H1ZVnsjqUmOKo+wDGAMEjbDkpZ7DA4bq2OJwRLQPSeB6ogAU3tFgJbma9f8RJ0+lSLQ89i0d8hh2YAcgKNTV6Smw/xF2Cgb6/KZKawq/tMAuDvxBCu5KBIJcwjG78FYTHO+1L4Zb7jHkyelVtXcgho8kIVLKCp9QjYpNfVBbLqeHVgTwC4LM5YNezVbTBKkVGOKZai1yEQ2oynJok8amQtq+Wk8zp+1y3n3H84yPK4jNOE/HgOMacCNnUAD8ueAizDV6hc+aQKGtBhQwKgSvSYiPdrASCgGI8HjFsYLtRDAIf2+vp+zViKOKC24Ji3Mj72yJkc1N4BIWiisDTlbg4YRm6jE7MGuiG9A9s454Gl4AFNsmXTrJGf1Zpwm06QZOwX58w+z3HccbZvE7jQkVpIdS74EUPImRYjv349+3UdOFcC/ya3ggY0iRpNk3Z5mOz6s+85WhPR44XPDkdT69mJUNcI2EmjQv6NfQbS9l32Dcd2f4Kpl4btBzRMYMMJfsNUQ3NrXU3D/BzkvZPM/kPOBQtoYpRMzImv+/GMgbqv3eI57rexDagY20QUJBQNV9gXQqVti4awCV3b2mGDZA3bGCBRA9XmGPuxTElhlGo2wBaaszoJXiBdV+ifDZ0XWkECOiDXpn/z2bNPB4DrQCq7e54FKYlLkyCsO6qhMjeEP06zIwWAm1a3siS7fZIEXXILoc0xI4Nw3ZerKJuV4LpJzwdVcIBWmIy58Y2jph/upa05aOjulteADX7CwMoENpsEKtQ/VSMr+IVApJRam8OUs27Aesy8Nd9zt2Af2hvKtwrInQzD7V+94ABdHweHDI7Jy7ALIMzolbZTDgQdESKYucDwaZq3fWcJ0w/dW+y0+CX9iB4VrrSW3PHM+PdDqvNp8v8s4wZD2WeZ4r85rcpEsAQIofZgF2QSWIaNIGhy+s/qgZm8cn8qWKuwYThyCs1hGkFYSjvwa2+ncBgvyFc4GJpBU6bDjEWNlikb2fe3KjRAQ0ZCS4Cm4B4voTVKtegAhpj2ftFSo9oDpsxwU/6BX+gehENYtbzY1o8Y69bhcpnbhMmmDzcnKopIzj3zzr+Me4HKl8izPd+FBmiFGDWYNxMWKUTKgWnN2IfQobk1pipszfq1x9DO7O3jh2mgHNh8GItg8eNv0k/9QhXQVL959VNwY3RL6/nZvXsE6Y6qtWtrc8eQLS7tw7W2tzZTDNgx25ZOVCuKYDq1SkT1UXc8c90WNbfPM2ym8hYkRhMzpOZREEMRVv+HP2QHUQpyjWjFEU+6b+kx9p07nvn5+wTkfCPZBGRSBQloRUYVlw0gq78swLO20DWnTh3jE/ypoYL5aqJuaDFdcE24zPm9X+qO/O3T41fRXrR8BTIBuiBJN0lEJg4HtCDvVMwV1QRW+t+LoiFYhUMS2dDQSAQW2LJurVOBE1akSBmgz67vgsezX4CQ9Y4pz1/1HIUkTM73A3cFCWhqXOzoA3gIk/GRwz4oEmqCM4aCSZmVhUwEbOUa+Kkg2XDEoGPjgfQ3+dx9juneI3e/cO0CSoU2ANaY6AYJTvO8vFaFCWhA1h/uAzAQkwDQe1PUEQBGiZNyNlYrLADWAmjpmEYGXhZQ1kI6FlKzQSGwv0g0CS4bsecvhbANmuArBMu8POXFWz8M8pI8jt0jWDHzzMAh738LE9BoVtq1h11de8VoGpLpJKTQtI0ez/zMdbV1uoFD0AAs9nbbtpO2o0XM7pFmNqtiNubAnfYcEr+SZC4BDE602dCQ93AuzDEamMj8kzwMnGoDHyF2VwpcsqLNGU9vXHpf8ne7Ow2cJB9sNzJrOTNrWH28ZcsuSeQAaJ/Er4WoChejgdO0S3NvpJs2DyEkNvphN5dbVAYg7aYjrlhsUJirtgspyAXjNYg8NjFQ1zFpq0mzamVsdiskQ2ECGmCQJ6mzNgAiprSE4p0pCosPmwmkr/Z8ISCArLATRuidxfxcuRckoGtra2ndF3wS1gbBjKktf52CBZSdRNvNC5idBvxcexQkoAkiRLZpSqTIclcYnQUfRvMu0P5zDWNVucIEtIlx93gAGnw35tOhsKNTaIFyIwLEgIUpB+y0Xh/HozABreAGyGE9mg7DN7PdBNH2eEtuBGD0h4xQu3Zh+eKpwuzjJuCGdQWSWPnS4UTG1Yd5dbM5dMvqWGH64kG3VY0LEtA1NTVqakWAxqFJ/BHAW76gA4R24s4p3JeAbgX3AjGCPAfAJCAGc2nivkMzTkpkzdBxN03g/iWgCwS6LcWsheQKgCUxJkm7mwHZGtiBmfyyH7oGY1+O0S2tmN8mEkNiMcH0sZ1+J0AYAJn0zv6ynQFSkz2CR9NUPaShGPH8rupnW7qCG6Pr4/WKr+ZCLqGmIKwmiYjC7na6kpSANVeCFe7Vz33l3p0AM9X5S0B/tv3os08tkagDZBkrqRBzsD68Gjs96JwVHYHpADyIPnFoQsLfd0AB7qd4cRZ0FDJ/kVTBYTRJreO4z3POc3/cohniKhKO6SKK64WUor1c4LrAlNMNgVh8jkWKOc5w/PeTb8xcRPFwJPYLyZW1Fy8UTCdXYzWWDUcNuew03xG/xIaBIZxrOJbTooDJGWzt+vWTS//4q6wreXfA/JYYn19T63YpuFqGwKYN8+/O33C89MRxuNjtUFREw4VQ63lEW/jE4nvfzVbsCwtkqj/tjea1tR0Eh9m2aavV1EC63CxvbOu3H2xqR19tVa3WetmxfTmqsPZcVYVlSWzwgp/CZozpqoPT9qL24b+0Z1uAOgZdjr4fG4TuINmf+e/Hqn/yrPm7D59c0dMo0z3dhqQQDGoXyt65zfvG2OVqKw5d/4QJKaFGl3G6SO5je4WkmiLGK83IV3uWF/NoGRjKPV2mZRdH+JbN/2pKvDZFzaMv/95vKjxhaDPmXbP9i4LVfEei//uYghShpbCSJXOuZtF8hthzkO1G3M211Ha02Qedu+yv1LqE4e3JefvhwDQhzWin0DvUIJBr2Mg9RFCfIo67Tjt4y8aLsTUQV064RyHzaJc9DfJQALNU0/WXN/dcfNZB24cfIl1nPtwqUONz71k48eXWW4vCYgZbjBSF7zp59HW6wSiMF+rZ+F3GRdhg+MkR38SNSMo7TLCjTgcOO+TbMRgDPuJ2pe11lXSihdROXeN7sJqHy+kBv1YAVGYsGGEPe0VZES9rSJOncVO3Hy//bZtwdA0UrnxC7l1WUCIcx01CuQoVuimKAQtqQwvOKBL2clXdU7pNerNwGTpufxXMkdY2zJSbqDRhvA66kqjw0kjEeOXuReN/NH7YlIHo2G/i8RTN9tNn37v42qc6xGnl0Nkxm8A9jg7c+ThP5aZL5XKd4Ogs3VZZA0BKuEN6G0TZl7it0yGzjodH3KghdMvTr05/1PiIXhatwGHuNkAoVbHKcAWMUZqy7JtxXuFSTcjfbHp00DucL5uvxuyVCdQqAN7/PTiyR7kuSy2xA1fydfOWaL020qVu1CkoKYAF6ZtiU92LPTIppyQaaXU9iND9SFP3XfySpxsoLHUKxCWjt43b10dYSTztNjaiq9ysRe0nUhnPofJZGs7Ct1O0E5ASEbYUTaW7Gpu9pZ9xPacEHPoBk86cfAA2n3S3cWlr6O/hPSWDa6lpJ1y5PaEuwwmYtxCorRua4uMlAMqqWeklBm66MRr4X/hObDBUV2OF24iznZYR8IlRPLZb9x7WHlmk4XYySsDzHbz4UuKm9qR3m0tuUG2g5v+JYP6v4mO7MVGhbUaPg1zHjYRxmwvQyuALR+Nuty183YNHe8UxTaQ945w+5694InyFplVYZQwxd9mM08q/Vr5lcbcieczujDal17lvXx1i6IaHjzs1ormXO643DOErUCjcAsCLPBn5ee/z35oRprHxsUGjNOmMcVx3INy6taMAOGojPkTfe67B5ZOPuPCtVXTFI8m0kn/YsDgmSodaXmrOPYsn/bR9GbuyZxtYXn7yncdh++8rIFkxICOeycliJHVBNDW1NtEHlCuFB1KWY0/h7OkvTXqE0qY0SCfqMmHk5GF4tmUiDuMPhc8BIQ1TyYAiIsx2XdeTTHhTpy28+k2KhvgqLqUxfviUCzGgXYwDPv0xLBYhP6WCYuD0Nxfrhc6fcaLyrpkLJm2iKSRds0XJXDXy7rNcm92A6y2/hYKiHkEdsoULEqJfWvHh6uD2QgG6COoHRz94u4keDWsJ2WJC5VVResVSpbjsPIYz4BRH3b5DmLz10YFjNWYtLIv5cezDK4YAY63ny/UYS9fAvotSogbcPHfAb4q49XhZ1D9Dgx2LyWuQ/zrIKtWHCm/AwyhfLy92L+9muMkNc08cStQA97whMJ49QgC0YglZ90VRzycMpBWvMDwBiiwY37egUqvxrce3Do7rAIl1yGONxjXP4NHTdRaZe9WIP/yW4lazBKrC5cTT/nAxnkZ70eBFP0a4EjTzWoqn4pKO9PAwS3edRUd7Dn/5ipHTLoCbuo2QYA0gz9Bl5CGdx6pwtCADGDTHR7OuRTttxqn6oyKs6Bq9Sb4y7vQp/QOKwOUVI+++AA9EzNN55GR0BpwqYaspvzBvVQ+qi/oklWUtRrkP+epZR3tRID9I90RDiz6B3ZXdsRLUBtgO7j3Em0J450325MK+pVtMnplxOC6/j1UdesGyJR/cf/yx0Uj61VjEL26ytccb3eLrYiy2ebtdJrZYrvfdK+fj2AtjHz446IeRqPUUvYfhS2OKqx/4O255u/USi6/fwFifQyC3TEVxTW56EPdS03uV82N2pfiK9/acfMqp46c3ThgxZaLmF02xnJSP4zVTcVv3HOkaaZJ/qh5HmUBphsd1vci2urHN0xPjFckmgJt4yUaN0b5cjNMbxb7mx6cnJz0+DmN/1MoOVyhD+dYKuTmyrZtm6Tfg/OTVaBfmcKv63peuSfz8lLsGoDle1ZgRc1jmiaKofn25233jbmtnc0cqP6pCbl2/42ugwpMjLPo9nLrchl3lQ6cnr141YcTUn6ELzc64TZJr8n9iRWJKuVXRtLtnEJ/yZmW22JZqGuI5clpMlPRzZfrFHiN2nVG/oFu0lyGW6Dw6wJKpJyPd9XG4xHqPtTWKA7xgVTpRdz53XUrHyCCaUEdN+JM96dxFYZtLnI2Ik94KnYuj6L4xoGeKpXU9dsVXf7JMrSBhceGEiCGLG9Jsl6uV3HT0Ra+tkdIUh/GAiZCLqnR+atLN+M4ppZjBNabFP9xevW/65nfnW0DQNlw7MWFgwF5Z+8CAO3c32Q/C+/BvlC89GkVZOuyU3dMXv6Qdhns5r4yIookA+ER0TA+HqbIlDTTX0piTSfs4ObV6QtXUuqJu6dvNp4PxriUgcBMVJvvWnj3TbRkmQni+ZcJ3pt7MGtlIPG12LK4APxlBE2DiBmNMB0Vzdsgo/+WUFyZ9QGR1Ci2dZlX8EByhffGi9yacNvUXdjpzBsp7IOhQf3ivwrL4CLo5GLOXV7f02vWrRMJEH1X5NQNK8QDzxi4cV3X3vSDP08Dc9N+QPKB3hf33j6Tet4RCg2Js+P3TEz8K8+xKpzFep+UdtBMHOV4LbTty05GxypSaj0YV+iXlebwhbfOlllc056gL33xn2YzjjEFjl9NdmVGKgpRSYJPUXDWRqFdSNxUxkVQJ4GkiJYvGuLZ7ReN8N8tZKw5fhaOs1HMKYL6EtxMkHf0Q44PjGORfbVKjsKsmjJj8l7TXcB68+8IeQ+JBASkQFZg2hzJegsboq7HIrQ27nNMurfpd/E/JGzbggDOGRFrvUFuFYWas4oMXSG8GFCVGRdm8a7XoIfrslOAQUD20C7WBg5NbEVSZNwi8jkZu9fVqRYyitFF2Q6YRz6ulEd3AcVu18AJaplNCGNp2VHywEadCzOBEZiv8ypYHF8w7u7A6xxBX03hj0YzlM51xp0x+ErFvRFuOGzPs9r4o6AYUDtdOh+M0NTtK5/M0Fn3elLKhbnrCbNSxi1IWRzUOAN7Yd0x93bIZY4xBY+hu6xwqmxj5IDWRrC1TDYyjbrjCHm9CYjuAhvuvyT9OXHh2XAezphQgqsKBbqPulRo3620Am/CxGVB12XMwrscNyXF1F2oCGbYCQnDvdsIHc/McEqQP5UA2lEJWqV4Du2ku0jYsej2OW/Z/X2yUDUGj3Ywg4/DUCnN8F61CzaEQmr1X1pvyby4DOkFzgpjC6biZEF0DV5lB4U5QCR4Cz+s4mhAu6oGaJColVsWa44dneTxu6TQppTCewJtMUHRzEbAZU0Nbp0wAaN8M5sPN8bPlQS/PGDgDina11R2VFH+rs/62CuNgA9ujroiwYvA5uO8OCTUXGGEoIdyiyDzXHeuw2GjMDOK6YsbIh2oO1bBpJnACXycq4ICREE94i0BmKRjSwNYepTO/1VSpfRIgQZpaP0SfqK6utxeZVTpuRg37AYInWCU6AHAEPIKfoY5BBfF4cLdmz5VbcVwVVzCvXKlVxDbKnct3+rW8Fn3FbM4KdtgkmzdvLF++fOYjlwy77UTLT18BKj1ozJgZRvqdTTZKi/ICt+iGii5USXSndK3ueC6PAJ1RbYlGpwUTAAEvp/mWTdE3HrdRix+OqX2oVlIdEp4VsTIC79lS53MRj7xdnMpFAdBhwEeV2RrIajMlIX+8tCe3v7FdSyKo4zkZvNqFjmmhS2WDvbbBnsmmXHvRMHOWzfxvYvxRF8xSXFKqkwphIXJf3Ko4KaqXjtidbvwVkW70GPqCXlw1vArBk/jadBKyk5LBJeeBJfwl1o3SUdy7ImahT6AnV1ahEZLAe7HTtulQOe+96k9Djzji0uSqtiHJRkAG5tjsW3j4RMOQ4npaFDtDGDvVTAIySTKqMGQAJpHWTpmMLQ9IccZN9dERHI8mpGfNHOucP/g6kAeO3Qg67gRr08bt0gisEKoo/HbpGDWUK9LbpUunON2euMbuCDitnQmSivxaqXDm4g0ABSgjVgCDm6qDI9FAyNb1Lefh5AMdWiuB87hQKjOPWQODR3zcFIv4yi1cT5+92FyJcPR1qn469JajLLfxv9CEQ0C6mec4UuD5IFXr5EvN8TrF6jBEFbCRzBA1SBvvvaKzeGqADgNk9arsU4OCResaM+7lpRG/z570zrnvTjvmblfTtxhSCBQGYjU0Pk5V4bKfk6W0JxZFBEs36gsqL3tDVWj0sAn9kNdhYGYgmCcBXEeFw9C4pxFbSnw3KjztPNvO/NDT0LmE9yiF9piNtyzBwWH0UPS7YxItLmsAkAMzyAiNRFwMlMYbnm/0xD/A+fZ1M9Y9o46/8m48WL0KFFSoh7iQP1AQu5BZ33Q6dSPuHyNq8EaxZyym+I4HQoWmBkb3PPfECWeQG5UZz+wE7Y0B3EN3Fi4fiWePr4roxaBPXuKR5J0baDyvf27XtzAA9wY3BkKHXNsp4mlAt4Hs/mFpp3G4IWLM5fbf9KIIN0pigm1tkNF2cfZqTYKUBoFktLyEs/QuWY73JzpkDi4a71owrV/1sr/X/3HghZ6fnlFRygajdg9n8LBky4hIRA3cVQTPFoFSbmsQTxuix5WUB71ws/gN+SAYoUE2OjcxUzkVTd3gQUwMLoMFmWzCc9JaTeL16fdS+IyfprlwqYYGsKWFnDpXKTfFXT9aFtENdOI0rQew2YunbT13yPjRGceapYvIsToT93r0CFeokD8pyNaA9ZiA+Q2v6hq7dNaSKUrKBUzmGPeJlA+JsNgCFTgbJzAr8os20VCPDF4vb7i/oXvDreRXP29jma/pT2EOfaiL3VGdtgFiBvXH6RR/z4pYmajRXRmdm7JkTNO5WqAPsU9lupefMKymeUu27NEW4GD6eyzTqEgUq0FHNFsSoFvyaXyvrE48+/rUE05yZWqUYH5/Hzfz0v4+BTZ0EbBePh4fWY8HyRYOuOKdeWC06LyyuNKcZo06Yew820vvgFsj6D+Nea2pDiWRtSOSlGlItd7Xo9q8xKszloYliRRHP3Ct9IMuS5drTNaTe1WS+ckwQKCrdCo3JKx3Dhlb1+Tt2gNx6bPkRWLLx5LmWz/4wcXD/M3OuSARx6PQkC/AU9FEIhS0lyn9kS/kqxGtIfHYa4k0xUtiHu96uEEDgdEJNoEsLaVxLIyL51vA4lILYHCAEAXM/fNPvna/ylcVK7Mz7RWXP46nvvohVgaVzN3bQSBQ/xRuCnjT6O7Pmbvg/h0qfviDAuaOGAb4DHQSl36cZAjIHyd8rrBE8uDeWd06c8+VlHKjeWmnnjk8suFVPt8dcOFD3x94kfx2/9F/RtB9yRthOgFojrxyOYFZBWuO/hV+uQLti1sYn/R9Cg/gEccNdplWM5rzD9MhMq/8OzSEqjDlsQ8fXSgTJ662PVDax91Lkdvk2Sqs5FVVVbpqxBzlIXfyR4Rsm6jOxr7Tf3TiR8ddKs889ieLKivj4dDRvkydlR3JtSlPh3jZ/OBO5TOR/6frJEjvS/XxWiAA9FlDfnru9wZdOP8Hgy+8LBu/K2rz8bLoIvT/A3H9DG4GjEvTAAAAAElFTkSuQmCC">
				</div>
				<h3 style="text-align: center;" dir="rtl">ايصال استلام عهدة دائمة نقدية/ECs</h3>
				
			
				<div style="text-align:right;margin:25px;">
				<div dir="rtl" style="    line-height: 28px;    text-align: justify;" class="inforow"><strong>أقر أنا</strong> <span> <?php echo $ir_aname." - ".$ir_ename ; ?> </span> <strong> الموقع أدناه ، واحمل بطاقة رقم قومي </strong><span> <?php echo " ".$nationalid." " ; ?> </span<strong> ورقمي التعريفي في شركة بروشوبس هو   </strong><span> <?php echo $irid." ," ; ?> </span><strong> أنني استلمت عهدة نقدية / ECs في تاريخ : </strong><span> <?php echo $currentDate ; ?> </span> <strong> بقيمة  </strong><span> <?php echo $transferVal ; ?> </span><strong> عملة الكترونية (ECs), تعادل ما قيمته  </strong><span> <?php echo ($transferVal*7) ; ?> </span><strong> جنيه مصري ،وأتعهد بردها أو برد قيمتها بالجنيه المصري لشركة بروشوبس (Proshops) خلال .........يوم من تاريخ استلامها </strong> </div>
				<br>
				<br>
				<div class="inforow endofpage" style="text-align:right" dir="rtl"><strong>التوقيع : </strong>...............................................................</div>
				<br>
				<br>
				<div class="sepbold"></div>
		
				</div>
			</div>
		</div>
		<div class="sep"></div>
		<div class="col0">
	<button type="button" id="printtable_recpt_btn" class="print_recpt ok">Print</button>
	<div class="sepbold newpage"></div>
	</div>
	</page>
	</div>
	<script>
$(document).ready(function() {
	
	
	$(document).on('click','#printtable_recpt_btn',function(){	
		var htmltoprint;
		//htmltoprint ="<div style='width:100%'><div></div></div>"
		html2=$('#printtable_recpt').html()
	htmltoprint =  html2
		
		//PrintElem(htmltoprint)
		Popup(htmltoprint)
	});
});//end doc ready
function PrintElem(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'Print', 'height=700,width=900mm');
       // mywindow.document.write('<html><head><title>فاتورة</title>');
        /*optional stylesheet*/ //
		mywindow.document.write(' <link rel="stylesheet" type="text/css" href="../../vo/css/style.css" />');
		//mywindow.document.write(' <link rel="stylesheet" type="text/css" href="./././vo/css/grid.css" />');
		//mywindow.document.write(' <link rel="stylesheet" type="text/css" href="css/invprint.css" />');
       // mywindow.document.styleSheets="css/invprint.css"
		mywindow.document.write('<style>body{background:none !important;padding: 0mm;margin:0;}#printable_all_ship{margin:0 auto;padding:0;}#printtable_recpt_btn{display:none;}.printtable{width:210mm; height:297mm;margin:0;padding:0;}@page {size: A4;margin: 27mm 16mm 27mm 16mm;} div.printtable {page-break-before:always;} div.chapter, div.newpage {page-break-after:always;} @media print { @page {size: A4;margin: 27mm 16mm 27mm 16mm;} div.printtable {page-break-before:always;} div.chapter, div.newpage {page-break-after: always;} }</style>');
		mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        //mywindow.print().delay( 5800 );
		//mywindow.close();
        return true;
    }
	

</script>
			
			<?php
			}///end of adding transaction table for IR

		} else {
			$errormsg="error UPDATE ir SET ewallet = (ewallet + ".$transferVal.") WHERE ir_id = '" . $irid . "' ";
			error_log($sql);
		}
		
		
		////echo the receipt for printing
		
		///reset post vars
		
		
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
<div style="float:left;width:49%">current accountant LE balance : </div><div style="float:left;width:49%"><?php echo $curr_acc_bal_le." LE"; ?></div>
</div>
<div class="col2">
<div style="float:left;width:49%">Last Transaction Comment : </div><div style="float:left;width:49%"><?php echo $curr_acc_comment; ?></div>

</div>
<br>
        <div class="sep"></div>
<br>


 <?php


    ?>
	
    <form name = "myform" method="post" onsubmit=" return validateForm();" >
	<div class="col1">
        <label >Ammount to Transfer (EC LIMIT)
            <span class="mandatory">*</span></label>
        <input type="text" name="transfer_val" value="0" >
        <input type="hidden" name="secret" value="4unf9unufru49fnr9">
		<input type="hidden" name="max_ec" id="max_ec" value="<?php echo $curr_acc_bal_ec; ?>">
        <button type="submit"  class="ok"><?php echo $_SESSION['main_language']->transfer_ec; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
		</div>
		<div class="col2">
		<label>Transfer ECs to IR <span class="astrisk"> *</span></label> 
            <select name="ir_id">
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
            ?> ><?php echo $managers_names[$dd]["ir_id"]." - ".$managers_names[$dd]["fullname_e"]." - ".$managers_names[$dd]["fullname_a"]; ?></option>
			<?php };//end for ?>
			</select><br class="clear"/>
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
                var y = document.forms["myform"]["transfer_val"].value;
				var y1=parseInt(y);
				var yy= document.forms["myform"]["max_ec"].value;
				var yy1=parseInt(yy);
                if (y == '') {
                    document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                    window.scrollTo(0, 0);
                    return false;
                }
				if (y == 0) {
                    document.getElementById("error").innerHTML = "Can't transfer ZERO ECs.";
                    window.scrollTo(0, 0);
                    return false;
                }
				if (y1 > yy1) {
                    document.getElementById("error").innerHTML = "Can't transfer more than the available ECs.";
                    window.scrollTo(0, 0);
                    return false;
                }

                //return true;
				return confirm('Are you sure you want to proceed with the ECs Transfer?');
            }
</script>
<!------------------------------------------------------------------------------------------------->
<?php
endPage();
?>