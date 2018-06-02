<?php
if(!session_id()){
session_start();
}
///load language session class
if($_COOKIE["lang"]=="ar" && !isset($_SESSION['language'])){
		 $_SESSION['language'] = new Arabic_language;
	}else{	
		  $_SESSION['language'] = new English_language;
}
$html_page->writeHeader();

if (!isset($_GET['new_ir_id'])) {
    header("Location: index.php?page=register_first_step");
}

$sql = "SELECT r.title, r.f_name, r.l_name,r.email, r.mobile, b.referral_bu_id,SUBSTRING(b.referral_bu_id,1,8) as ref_irid,SUBSTRING(b.referral_bu_id,10,12) as ref_bu, r.a_name, r.registration_date, r.ir_id , CONCAT(referral.title, ' ', referral.f_name, ' ', referral.l_name ) AS referral_name, referral.email as referral_email, referral.a_name as referral_aname FROM ir r "
        . " LEFT OUTER JOIN bu b ON (b.code = '001' AND b.ir_id = r.ir_id) "
		//. " INNER JOIN scrachcards crd ON (crd.irid = r.ir_id) "
        . " LEFT OUTER JOIN ir referral ON referral.ir_id = SUBSTRING(b.referral_bu_id,1,8) "
        . " WHERE r.ir_id = '".$_GET['new_ir_id']."'";
		//echo $sql;
$result = $database_manager->query($sql);
//echo $result;
$row = mysqli_fetch_assoc($result);

if(isset($_POST['sendPswToEmai']) && $_POST['sendPswToEmai']!=""){
		///prepare the email msg and details
		$to=$_POST['sendPswToEmai'];
		$subject="Welcome to ".$_SESSION["language"]->companyname . " , You have Created your account";
		$msg="";
		///send the details to the user's email
		$sendemail=$core->email($database_manager, $to, $subject, $msg);
		 
		
}

?>
<script>
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
function print_send_info(){
	var css="body{padding: 30px;}.print_reg_btn{display: none;}.infotxt {font-size: 1em;} .form_label {font-size: 1em;margin: 0px 5px;} .infoTitle {font-size: 1em;font-weight: bold;} .headertxt {font-size: 2em;}";
	var divtoprint="fullpageprint"
	printDiv(divtoprint,css)
}


</script>
<div id="fullpageprint" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12" >
		<div class="header col-xs-12 col-md-12 col-lg-12" >
			<div class="col-xs-0 col-md-2 col-lg-2" >
			</div>
			<div class="col-xs-12 col-md-8 col-lg-8" >	
				<div class="logo col-xs-12 col-md-3 col-lg-3" >
					<a href="index.php?page=dashboard"><img style="" class="img-responsive" src="images/testlogo.png"></a>
				</div>
				<div class=" headertxt col-xs-12 col-md-9 col-lg-9 " >
				<div class="align-middle"><strong></strong> <?php echo "Registration: Success"//$_SESSION["language"]->reg_pagehsetp3;?></div>
				</div>
			</div>
			<div class="col-xs-0 col-md-2 col-lg-2" >
			</div>
		</div><!---end header container div-->
	</div>
	<!---Section1 form------------------>
		<div class=" section1 col-xs-12 col-md-12 col-lg-12" >
			<div class="col-xs-0 col-md-2 col-lg-2" ></div>
			<div id="print_area" class="middlecontent col-xs-12 col-md-8 col-lg-8" >
			<h3 class="col-xs-12 col-md-12 col-lg-12">Congratulations…</h3>
			<?php echo $sendemail;?>
			<h4 class="col-xs-12 col-md-12 col-lg-12" style="text-align:left;border-radius:5px;">Dear <?php echo $row['title'] . " " . $row['f_name'] . " " . $row['l_name']; ?>,</h4>
			
			<p class="form_label" >Welcome to <?php echo $_SESSION["language"]->companyname; ?>. You have been registered with our company you can use the created username/IRID and password to access your account and store. Your account details are:</p>
			<div class="col-xs-12 col-md-12 col-lg-12 print_reg_btn" >
			<a class="button" href="index.php?page=login">Login Now</a>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-6 infobox" >
				<div class="form-group">
					<h3 class="col-xs-12 col-md-12 col-lg-12">Your Account Details</h3>
					<div class="form_label infoTitle" >Name : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['f_name'] . " " . $row['l_name']; ?></div>
					
					<div class="form_label infoTitle" >Arabic Name : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['a_name'] ; ?></div>
					
					<div class="form_label infoTitle" >IRID (Username) : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"><?php echo $row['ir_id']; ?></div>
					
					<div class="form_label infoTitle" >Password : <span class = "astrisk" > <?php echo $_SESSION["ir_loginpsw"];?> </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt">
					<!--<button type="button" id="SendPSW" onclick="sendpswtouseremail;" class=""><i class="fa fa-envelope-square fa-fw"></i>Send Password to My Email</button>
					--></div>

					<div class="form_label infoTitle" >Email : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['email'] ; ?></div>
					
					<div class="form_label infoTitle" >Mobile : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['mobile']; ?></div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-6 infobox" >
				<div class="form-group">
				<h3 class="col-xs-12 col-md-12 col-lg-12">Your Referrer Details</h3>
					<div class="form_label infoTitle" >Referrer Name : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['referral_name']; ?></div>
					
					<div class="form_label infoTitle" >Referrer Arabic Name : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['referral_aname']; ?></div>
					
					<div class="form_label infoTitle" >Referrer IRID (Username) : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> <?php echo $row['ref_irid'] ; ?></div>
					
					<div class="form_label infoTitle" >Referrer Email : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"><?php echo $row['referral_email']; ?></div>
					
					
					
					<div class="form_label infoTitle" >Referrer Business Unit : <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"><?php echo $row['ref_bu']; ?></div>

					<div class="form_label infoTitle" > <span class = "astrisk" >  </span></div >
					<div class="col-xs-12 col-md-12 col-lg-12 infotxt"> </div>
					
				</div>
			</div>
			<div class=" col-xs-12 col-md-12 col-lg-12 form_label txtblue">We are pleased to welcome you as a new IR of Company. We feel honored that you have chosen us to satisfy your needs and to achieve your dreams through Company opportunity, our slogan is Changing People Lives for better. Everything your Virtual Office may need is carried to manage your Shop, we have a great variety of products & services to choose from and to deal with, all at competitive prices. It is our privilege to serve you and to provide you with our best possible care. So, we are committed to optimizing our VO to provide the best service that allows our IRs improve their business. We appreciate the trust placed in us and we hope to establish a strong long term business relationship with you.</div>
			<div class="col-xs-12 col-md-12 col-lg-12 print_reg_btn" >
			<script>
			function printDiv(divtoprint,css) {

				var divToPrint=document.getElementById(divtoprint);

				var newWin=window.open('','Print-Window');

				newWin.document.open();
				newWin.document.write('<html>');
				newWin.document.write('<head><link href="css/bs/bootstrap.min.css" rel="stylesheet"><link href="http://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet" type="text/css"><meta charset="UTF-8"><link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet" type="text/css"><link href="css/style.css" rel="stylesheet" type="text/css"><link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></head>')
				newWin.document.write('</head>')
				newWin.document.write('<style>'+css+'</style><body onload="window.print()">'+divToPrint.innerHTML+'');
				  // newWin.document.write('<script src="js/jquery-3.2.1.min.js"><//script><script src="js/bootstrap.min.js"><//script>');						
				  
				newWin.document.write('</body></html>');
				
					var s=document.createElement('script');
					s.type = "text/javascript";
					s.src = "js/jquery-3.2.1.min.js";
					document.body.appendChild(s);
					
					var s=document.createElement('script');
					s.type = "text/javascript";
					s.src = "js/bootstrap.min.js";
					document.body.appendChild(s);
				 newWin.document.close();

				 setTimeout(function(){newWin.print();},1000);

			}
			</script>
			<form method = "post" name = "myform"  onsubmit = "return print_send_info();">
			<input class="form-control" name = "sendPswToEmail" type="hidden" value = "<?php echo $row['email'] ; ?>"  autocomplete = "off" /> 
			<button type="submit" id="print_sendmyinfo" onclick="print_send_info();" class="btn btn-warning payusing_reg_btn"><i class="fa fa-check-square fa-fw"></i>Print & Send 
			</form>
			to my Email</button>
			
			</div>
			
        
		
			
			</div>
			<div class="col-xs-0 col-md-2 col-lg-2" ></div>
		</div><!---end section1 container div-->
</div>
        
        
<?php $html_page->writeFooter(); ?>