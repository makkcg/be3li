<?php

/////calculate the downline at level 1 
////////SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'
session_start();
////set initialize variables and session vars

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
};
///$_SESSION["Ref_IR"]="";
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



$html_page->writeHeader();
?>
<script src="../bo/includes/lib/js/jquery.min.js" type="text/javascript"></script> 
<style>
.col1{
    width: 500px;
    float: left;
}

.col2{
    width: 500px;
    float: right;
}
</style>
<div id="right-container">
    <div id="top-bar">
    </div>
    <div id="header">
        <div id="page-title1">
            <h1>Auto Registration: Ideal Network Registration Program</h1>
        </div>
        <div id="header-menu">
        </div>
    </div>
    <div id="page">

		<div  class="col1" style="">
			<h2> Search Referrer IR </h2>
			<label >Referrer IR ID <span class = "astrisk" > * </span></label >
			<input name = "selectedIR_inp" id="selectedIR_inp" type = "text" value = ""  autocomplete = "off" />
			<button type = "button" id="search_ir_btn" > Search </button >
		</div>
		<div  class="col2" style="color:red;">
			<h2> Referrar IR search result: </h2>
			<div id="ir_search_res_div" style=""></div>
			<button type = "button" id="confirm_refIRID_btn" >Confirm Referrar</button>
		</div>
		<br>
		<div  class="col1">
			<?php echo $errormsg; ?>   
		</div>
		<div id="error" class="col2" style="color:red"></div>
		<br>
		<div class="sep dotted"></div>
		<br>
	<div id="step2div">
		<div  class="col1" style="">
			<button type = "button" id="placement_ideal_manu_btn"> Select Placement to Referrar </button >
		</div>
		<div  class="col2" style="">
			<button type = "button" id="placement_ideal_auto_btn"> Auto Placement of Downlines Ideal Network </button >
		</div>
				<br>
		<div class="sep dotted"></div>
		<br>
		
		<div id="auto_placement_div" style="display:none;">
			auto placement form
			<div  class="col1" style="">
			<h2> Auto Generate IRs at right and left for Bu 002/003 as Ideal binary network </h2>
			<label >Select Number of Levels  <span class = "astrisk" > * </span></label >
			<input type="radio" name = "ir_numbers" class="ir_numbers" value="1" > 1 level (2 IRs L-R)<br>
			<input type="radio" name = "ir_numbers" class="ir_numbers" value="2" checked> 2 levels (6 IRs L-R Ideal)<br>
			<button type = "button" id="auto_gen_network_btn" > Start Generating Regestered Network </button >
		</div>
		</div>
		
		<div id="manual_placement_div" style="display:none;">
			manual placement to referrar
		</div>
	</div><!--end of main step2 div-->
		<script>
            function validateForm() {
                var y = document.forms["myform"]["referrer_ir_id"].value;
                if (y == '') {
                    document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                    return false;
                }
                if (y.split('').length !== 6) {
                    document.getElementById("error").innerHTML = "Invalid Referrer IR ID.";
                    return false;
                }
                return true;
            }
$(document).ready(function() {
	$('#ir_search_res_div').html("");
	$('#selectedIR').val("");
	$('#ir_psw_typ').val("");
	$("#step2div").hide()
	$("#confirm_refIRID_btn").hide()
	var ir_found=false;
	
	
	 //select
	$("#selectedIR_inp").keypress(function(e) {
		$('#ir_search_res_div').html("");
		$("#confirm_refIRID_btn").hide()
		
		if(e.which == 13) {
			$("#step2div").hide()
				$("#confirm_refIRID_btn").hide()
			var selected_ir = $("#selectedIR_inp").val();
			///do the ajax call to get the IR last balance at Proshops accountant
			if(selected_ir.length >=6){
			$('#ir_search_res_div').html("");
			$.ajax({
				//url: '', // url is empty because I'm working in the same file
				data: {'selectedIR': selected_ir},
				type: 'post',
				success: function(result) {
					var receivedjson=$.parseJSON(result)
			
					if(receivedjson[0].id!="err" && receivedjson[0].id!=null){
					  $('#ir_search_res_div').html('<span style="color: #3F51B5;"><strong>IR :</strong> '+receivedjson[0].ir+'</span><br><span style="color: black;"><strong>Name :  </strong>'+receivedjson[0].name+' </span>, <br><span style="color: black;">'+receivedjson[0].aname+' </span><br> <span style="color:#3F51B5;"><strong>Mobile : </strong>'+receivedjson[0].mobile+' </span><br>  <span style="color:#3F51B5;"><strong>E-Mail : </strong>'+receivedjson[0].email+' </span><br>');
					ir_found=true;
						$("#confirm_refIRID_btn").show()
					}else if(receivedjson[0].id== "err"){
						$('#ir_search_res_div').html('<span style="color: red;">'+receivedjson[0].ir+'</span>');
						$('#ewallet').val(0)
							$("#confirm_refIRID_btn").hide()
						ir_found=false;
					}else if(receivedjson[0].id == null){
						$('#ir_search_res_div').html('<span style="color: red;">IR not Found / Incorrect</span>');
						$('#ewallet').val(0);
							$("#confirm_refIRID_btn").hide()
						ir_found=false;
					}
				}
			});//end ajax
			}//end if
	}//end if press enter key
	});//end key press event
	
	
	
	$(document).on('click','#search_ir_btn',function(){	
	$('#ir_search_res_div').html("");
	$("#step2div").hide()
	$("#confirm_refIRID_btn").hide()
		var selected_ir = $("#selectedIR_inp").val();
		///do the ajax call to get the IR last balance at Proshops accountant
		if(selected_ir.length >=6){
		$('#ir_search_res_div').html("");
		$.ajax({
            //url: '', // url is empty because I'm working in the same file
            data: {'selectedIR': selected_ir},
            type: 'post',
            success: function(result) {
				var receivedjson=$.parseJSON(result)
				if(receivedjson[0].id!="err" && receivedjson[0].id!=null){
                $('#ir_search_res_div').html('<span style="color: #3F51B5;"><strong>IR :</strong> '+receivedjson[0].ir+'</span><br><span style="color: black;"><strong>Name :  </strong>'+receivedjson[0].name+' </span>, <br><span style="color: black;">'+receivedjson[0].aname+' </span><br> <span style="color:#3F51B5;"><strong>Mobile : </strong>'+receivedjson[0].mobile+' </span><br>  <span style="color:#3F51B5;"><strong>E-Mail : </strong>'+receivedjson[0].email+' </span><br>');
				ir_found=true;
					$("#confirm_refIRID_btn").show()
				}else if(receivedjson[0].id == "err"){
					$('#ir_search_res_div').html('<span style="color: red;">'+receivedjson[0].ir+'</span>');
					$('#ewallet').val(0)
						$("#confirm_refIRID_btn").hide()
					ir_found=false;
				}else if(receivedjson[0].id == null){
					$('#ir_search_res_div').html('<span style="color: red;">IR not Found / Incorrect</span>');
					$('#ewallet').val(0)
						$("#confirm_refIRID_btn").hide()
					ir_found=false;
				}
            }
        });
		}//end if
	});//end search ir btn click
	
$(document).on('click','#confirm_refIRID_btn',function(){

	///do the ajax call to get the IR last balance at Proshops accountant
		$.ajax({
            //url: '', // url is empty because I'm working in the same file
            data: {'confirmed_refid':'confirmed'},
            type: 'post',
            success: function(result) {
				var receivedjson=$.parseJSON(result)
				alert(receivedjson[0].ir)
				if(receivedjson[0].ir !="err"){
					alert(receivedjson[0].ir+" Confirmed as Refferal");
					$("#step2div").show()
					localStorage.setItem("refIR",receivedjson[0].ir)
				}else{
					alert("Error has occured, ir not stored in session")
					$("#step2div").hide()
					localStorage.clear()
				}
            }
        });

	});//end search ir btn click
	
	////show manual placement form
	$(document).on('click','#placement_ideal_manu_btn',function(){
		$('#manual_placement_div').show()
		$('#auto_placement_div').hide()
	});
	
	////show Auto placement form
	$(document).on('click','#placement_ideal_auto_btn',function(){
		$('#manual_placement_div').hide()
		$('#auto_placement_div').show()		
	});
	
	$(document).on('click','#proceed_btn',function(){	
	///validate inputs
		$('#error').html("")
		if(ir_found==false){
			$('#error').html("Please Search for correct IR.");
			window.scrollTo(0, 0);
			return false;
		}
		var y = $('#selectedIR_inp').val()// document.forms["myform"]["selectedIR_inp"].value;
		if (y == '') {
			$('#error').html("Please Select IR ,Mandatory fields cannot be left blank.");
			window.scrollTo(0, 0);
			return false;
		}
		//// verify that user has selected type of password
		var y = $('#ir_psw_typ').val()
		if (y == '') {
			$('#error').html("Please Select which password type you want to change for the IR (login / eWallet) ,Mandatory fields cannot be left blank.");
			window.scrollTo(0, 0);
			return false;
		}
        ///verify that the password is not Empty,  verify that the password doesnt have spaces
		var y   = $('#new_pass_inp').val();
		var yy  = hasWhiteSpace($("#new_pass_inp").val())
		var yyy = $("#new_pass_inp").val().length
		
		 /// verify that the password is not Empty
		if (y == '') {
			$('#error').html("Please enter the new password for the IR ,Mandatory fields cannot be left blank.");
			window.scrollTo(0, 0);
			return false;
		}
		 /// verify that the password doesnt have spaces
		if (yy) {
			$('#error').html("Password cannot have white spaces, please enter a new password without spaces.");
			window.scrollTo(0, 0);
			return false;
		}
		
		if (yyy < 6) {
			$('#error').html("Password must be at least 6 characters.");
			window.scrollTo(0, 0);
			return false;
		}
		
		var sel_irid = $("#selectedIR_inp").val();
		var newpsw = $("#new_pass_inp").val();
		var pswtype_val = $("#ir_psw_typ option:selected").val();
		var pswtype_txt = $("#ir_psw_typ option:selected").text();
		var pswtype_data=$("#ir_psw_typ option:selected").data("pswd_type_val");
		///proceed with pswrd change if user confirms
		if(confirm('Are you sure you want to change '+pswtype_txt+' for IR '+sel_irid+' ?')){
			console.log("thank you")
			$.ajax({
            //url: '', // url is empty because I'm working in the same file
			//var sel_ir=
            data: {'sel_irid': sel_irid,'pswtype_val': pswtype_val, 'pswtype_data': pswtype_data,'newpswinp': newpsw,'secret':"4unf9unufru49fnr9kcg"},
            type: 'post',
            success: function(result) {
				console.log(result)
				var receivedjson = $.parseJSON(result)
				////$resultback[]=array("errtype"=>"err","msg"=> "Couldn;t update ewallet ","sql"=>$sql1);
		
				if(receivedjson[0].errtype!="err"){
					$('#error').html('<span style="color:green;font-weight: bold;background: yellow;padding: 5px;">'+receivedjson[0].msg+'</span>');
					///update the ewallet of the user again
					$("#search_ir_btn").trigger("click")
				}else {
					$('#error').html('<span style="color:red">'+receivedjson[0].msg+'</span>');
					
				}
				
            }//end success ajax
        });//end ajax
		}else{
		console.log("Why>>>Not confirmed?")
			
		}
		
		////proceed with ajax payment
            
	});//end proceed payment btn click
	
	$(document).on('click','#auto_gen_network_btn',function(){	
		var refIR=localStorage.getItem("refIR")
		var nubmerofIRs=$(".ir_numbers:checked").val();//$('#ir_numbers').val()
		if(nubmerofIRs=="" || nubmerofIRs<1){
			alert("Enter valid number")
		}else{
		if(confirm('Are you sure you want to generate '+nubmerofIRs+' new IRs under the referral IR Number '+refIR+' ?')){
			console.log("start ajax to auto generate irs")
			$.ajax({
            url: './includes/register_auto_irs_processor.php?secret=proshopskcgmodification', // url is empty because I'm working in the same file
			//var sel_ir=
            data: {'refir_genunder': refIR,'numberoflevels2': nubmerofIRs},
            type: 'post',
            success: function(result) {
				console.log(result)
				alert(result)
				//var receivedjson = $.parseJSON(result)
				////$resultback[]=array("errtype"=>"err","msg"=> "Couldn;t update ewallet ","sql"=>$sql1);
		
				/*if(receivedjson[0].errtype!="err"){
					$('#error').html('<span style="color:green;font-weight: bold;background: yellow;padding: 5px;">'+receivedjson[0].msg+'</span>');
					
					
				}else {
					$('#error').html('<span style="color:red">'+receivedjson[0].msg+'</span>');
					
				}*/
				
            }//end success ajax
        });//end ajax
		}else{
		console.log("Why>>>Not confirmed?")
			
		}//end confirm
		
	};////end inp text of ir numbers verification
	});//end auto generate irs btn
});//end doc ready

function hasWhiteSpace(s) {
  return /\s/g.test(s);
}
        </script>
		
<?php $html_page->writeFooter(); ?>