<?php
if(!session_id()){
session_destroy();
session_start();
$_SESSION["RefIRID"]="";
$_SESSION["Step"]=0;
}

if($_COOKIE["lang"]=="ar" && !isset($_SESSION['language'])){
		 $_SESSION['language'] = new Arabic_language;
	}else{	
		  $_SESSION['language'] = new English_language;
}

$html_page->writeHeader();
/////if step1 is accessed through affiliate link 
if (isset($_GET['refir']) && $_GET['refir'] != "") {
	$Ref_ir=$_GET['refir'];
	///check if the refir bu001 is qualified and correct,
	$isIRandQualfied=$core->checkqualifiedIR($Ref_ir,$database_manager,"001");
	if($isIRandQualfied=="true" && $core->validateIR_Bu($Ref_ir,$database_manager)){
		////create session var for ref irid, and go to step 2
		$_SESSION["RefIRID"]=$Ref_ir;
		$_SESSION["Step"]=1;
		 header("Location: index.php?page=register_second_step&step=1");
         echo "<meta http-equiv='refresh' content='0;url=index.php?page=register_second_step&step=1'>";
	}elseif(!$core->validateIR_Bu($Ref_ir,$database_manager)){
		$_GET['error']=1;
		$Ref_ir="";
	}else{
		$_GET['error']=1;
		$Ref_ir="";
	}
}else{
	$Ref_ir="";
	//$_SESSION["RefIRID"]="";
}

?>
 <script>
		$(".reg_errormsg").html("");
</script>
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
				<div class="align-middle"><strong>Wellcome To</strong> <?php echo $_SESSION["language"]->reg_pagehsetp1;?></div>
				</div>
			</div>
			<div class="col-xs-0 col-md-2 col-lg-2" >
			</div>
		</div><!---end header container div-->
		
		<div class=" col-xs-12 col-md-12 col-lg-12" >
		
		<div class="col-xs-0 col-md-2 col-lg-2" >
		</div>
		<div class="middlecontent col-xs-12 col-md-8 col-lg-8" >
		<!-----error msg area---->
		<div class="col-xs-12 col-md-12 col-lg-12 reg_errormsg" >
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
		?>
		</div>
		<form method = "post" name = "myform" action="index.php?page=register_second_step&step=1" onsubmit = "return validateForm();" >
		<div class="form-group">
			
				<div class="form_label" for ="referrer_ir_id"><?php echo $_SESSION["language"]->Refirid;?>  <span class = "astrisk" > * </span></div >
			
				<input class="form-control" name = "referrer_ir_id" type = "text" value = "<?php echo $Ref_ir; ?>"  autocomplete = "off" /> 
				<input class="form-control" name = "step" type = "hidden" value = "1"  autocomplete = "off" /> 
			
				<button type = "submit" class="btn btn-primary nextbtn"> <i class = "fa fa-check-square fa-fw" > </i><?php echo $_SESSION["language"]->Next;?></button >
			
			</div>
		</form>
		</div>
		<div class="col-xs-0 col-md-2 col-lg-2" >
		</div>
		</div><!---end middle container div-->
		
	</div><!---end container div-->
</div><!---end container row--->

        <script>
		
            function validateForm() {
                var y = document.forms["myform"]["referrer_ir_id"].value;
                if (y == '') {
                    //document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>";
					$(".reg_errormsg").html("<?php echo $_SESSION["language"]->fundtransfer_msg_mandatoryfieldsblank;?>");
                    return false;
                }
                if (y.split('').length !== 8) {
                    //document.getElementById("error").innerHTML = "<?php echo $_SESSION["language"]->fundtransfer_msg_invalidIRID;?>";
					$(".reg_errormsg").html("<?php echo $_SESSION["language"]->fundtransfer_msg_invalidIRID;?>");
                    return false;
                }
                return true;
            }
        </script>
        
<?php $html_page->writeFooter(); ?>