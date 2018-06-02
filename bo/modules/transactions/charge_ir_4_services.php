<?php
////charge IR eWallet for specific services from tickets with predefined ECs. e.g. Vacation confirmation, 

///list of predefined services
$predefined_services=array();
$predefined_services[]=array("service" => "Ownership Change Fees.", "val_ecs"=>100);
$predefined_services[]=array("service" => "Vacation Reservation Fees.", "val_ecs"=>50);
$predefined_services[]=array("service" => "Vacation Guest Certificate Fees.", "val_ecs"=>50);
$predefined_services[]=array("service" => "Product Exchange Fees.", "val_ecs"=>50);
$predefined_services[]=array("service" => "Administration Fees.", "val_ecs"=>50);

/////get the eWallet balance of the IR, this is accessible through ajax with get call_user_func
if(isset($_POST['selectedIR']) && $_POST['selectedIR'] !=""){
	$selectedIR=$_POST['selectedIR'];
	$IR_data=array();
	$sql = "select id, ir_id, ewallet,CONCAT( f_name,  ' ', l_name ) AS name, a_name from ir where ir_id LIKE '".$selectedIR."'";
	//echo $sql;
	$result = mysql_query($sql);
    if ($result>0) {
      // $errormsg="";
	$row = mysql_fetch_assoc($result);
	//echo $result;
    $IR_data[]=array("id"=>$row["id"],"ir"=> $row["ir_id"],"ewallet"=> $row['ewallet'],"name"=> $row['name'],"aname"=>$row['a_name']);
	echo json_encode($IR_data);
	die();
	} else if ($result==0) {
	$IR_data[]=array("id"=>"null","ir"=> "No IR Found","ewallet"=> $result,"name"=> $sql,"aname"=>$row);
	echo json_encode($IR_data);
	die();
	} else {
	$IR_data[]=array("id"=>"err","ir"=> "Error Loading IR","ewallet"=> $result,"name"=> $sql,"aname"=>$row);
	echo json_encode($IR_data);
	die();
    }
	die();
}
/////proceed with the payment
////{'sel_irid': sel_irid,'service_details': service_details,'service_fees': servicefees,'secret':"4unf9unufru49fnr9kcg"},
           
if(isset($_POST) && isset($_POST['sel_irid']) && isset($_POST['secret']) && isset($_POST['service_details']) && isset($_POST['service_fees']) && $_POST['service_details'] !="" && $_POST['sel_irid'] !="" && $_POST['service_fees'] !=0 && $_POST['secret'] == "4unf9unufru49fnr9kcg"){
	$selectedIR=$_POST['sel_irid'];
	$service_details=$_POST['service_details'];
	$service_fees=$_POST['service_fees'];
	//$date=
	$department="Proshops-Support";
	////update the ir ewallet 
	
	///insert the ir transaction
	
	///insert services_transaction
	$resultback=array();
	///update ir ewallet
	$sql1 = "UPDATE ir SET ewallet = (ewallet - ".$service_fees.") WHERE ir_id = '" . $selectedIR . "'";
	$result1 = mysql_query($sql1);
    if ($result1>0) {
	/////get the current ewallet value of the ir
	
		$sql2= "select ir_id,ewallet from ir where ir_id='".$selectedIR."'";
		$result2 = mysql_query($sql2);
		if($result2>0){	
			$row2 = mysql_fetch_assoc($result2);
			$ir_ewallet=$row2["ewallet"];
		///insert the ir transaction
			$sql3="INSERT INTO transaction (ir_id, type, date, amount, balance, comments)  VALUES ('" . $selectedIR . "', 'Service Payment for ".$service_details."' , NOW(), '".$service_fees."','" . $ir_ewallet . "' , '".$department."')";
			$result3 = mysql_query($sql3); 
			if($result3>0){
		
			////insert transaction into service_fees_trns table
				$sql4="INSERT INTO service_fees_trns (ir_id, type, date, amount, comments)  VALUES ('" . $selectedIR . "', 'Service Payment for ".$service_details."' , NOW(), '".$service_fees."' , '".$department."')";
				$result4 = mysql_query($sql4); 
				if($result4>0){
					$resultback[]=array("errtype"=>"done","msg"=> "Payment has been successfully processed.","sql"=>$sql4);
					echo json_encode($resultback);
					die();
				}else{
					$resultback[]=array("errtype"=>"err","msg"=> "Couldn't insert service fees transaction record.","sql"=>$sql4);
					echo json_encode($resultback);
					die();	
				}
			
				
				
			}else{
				
				$resultback[]=array("errtype"=>"err","msg"=> "Couldn't insert transaction to ir ","sql"=>$sql3);
				echo json_encode($resultback);
				die();
			}
		}else{
			$resultback[]=array("errtype"=>"err","msg"=> "Couldn't get the ewallet info of ir ","sql"=>$sql2);
			echo json_encode($resultback);
			die();
		}
		
	} else {
		$resultback[]=array("errtype"=>"err","msg"=> "Couldn;t update ewallet ","sql"=>$sql1);
		echo json_encode($resultback);
		die();
    }
	die();
}
/////////////////////////////////////
startPage();
$errormsg="";
//////////////////////////////////////////////////

?>

<div  class="col1" style="">
<h2> Search IR to charge for service: </h2>
            <label > IR ID <span class = "astrisk" > * </span></label >
            <input name = "selectedIR_inp" id="selectedIR_inp" type = "text" value = ""  autocomplete = "off" />
			<input type="hidden" name="ewallet" id="ewallet" value="0" />
            <button type = "button" id="search_ir_btn" > Search </button >
</div>
<div  class="col2" style="color:red;">
<h2> IR search result: </h2>
<div id="ir_search_res_div" style="">
</div>
</div>
<br>
<div class="sep"></div>
<br>
<div  class="col1">
         <?php echo $errormsg; ?>   
</div>
<div id="error" class="col2" style="color:red">

</div>
<br>
        <div class="sep"></div>
<br>
	<div class="col1">
		<label>Select Service <span class="astrisk"> *</span></label> 
            <select name="ir_service" id="ir_service">
                <option value="" <?php
                if ($_POST['ir_service'] == "") {
                    echo "selected";
                }
            ?> >-- SELECT SERVICE --</option>
			<?php for($dd=0;$dd < sizeof($predefined_services);$dd++){ ?>
                <option value="<?php echo $dd; ?>"
				<?php
                if ($_POST['ir_service'] == $dd) {
                    echo "selected";
                }///array("service" => "Confirm Vacation Reservation", "val_ecs"=>100);
            ?> data-service_val="<?php echo $predefined_services[$dd]["val_ecs"]; ?>" ><?php echo $predefined_services[$dd]["service"]; ?></option>
			<?php };//end for ?>
			</select>
			<br class="clear"/>
	</div>
	<div class="col2">
            <label > Enter the Service Fees (ECs):<span class = "astrisk" > * </span></label >
            <input name = "service_fees_inp" id="service_fees_inp" type = "number" value = "0" onkeypress='validateNum_input(event)'   autocomplete = "off" />
            <button type = "button" id="proceed_btn" > Proceed the Payment </button >
	</div>
	<div class="col1">

	</div>
		
<div class="sep"></div>
<script>
function validateNum_input(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
$(document).ready(function() {
	$('#ir_search_res_div').html("");
	$('#selectedIR').val("");
	$('#ir_service').val("");
	
	var ir_found=false;
	 //select
	$("#selectedIR_inp").keypress(function(e) {
		$('#ir_search_res_div').html("");
		if(e.which == 13) {
			var selected_ir = $("#selectedIR_inp").val();
			//$('#selected_ir_ewal').
	///do the ajax call to get the IR last balance at Proshops accountant
			if(selected_ir.length >=6){
			$('#ir_search_res_div').html("");
			$.ajax({
				//url: '', // url is empty because I'm working in the same file
				data: {'selectedIR': selected_ir},
				type: 'post',
				success: function(result) {
					var receivedjson=$.parseJSON(result)
					///"id"=>$row["id"],"ir"=> $row["ir_id"],"ewallet"=> $row['ewallet'],"name"=> $row['name'],"aname"=>$row['aname']
					//alert("action performed successfully "+receivedjson[0].ir); //this alert is fired
					
					if(receivedjson[0].id!="err" && receivedjson[0].id!=null){
						//alert(receivedjson[0].ir)
					  $('#ir_search_res_div').html('<span style="color: #3F51B5;"><strong>IR :</strong> '+receivedjson[0].ir+'</span><br><span style="color: black;"><strong>Name :  </strong>'+receivedjson[0].name+' </span>, <br><span style="color: black;">'+receivedjson[0].aname+' </span><br> <span style="color:#3F51B5;"><strong>eWallet Value : </strong>'+receivedjson[0].ewallet+' ECs.</span><br>');
					ir_found=true;
					$('#ewallet').val(receivedjson[0].ewallet)
					}else if(receivedjson[0].id== "err"){
						$('#ir_search_res_div').html('<span style="color: red;">'+receivedjson[0].ir+'</span>');
						$('#ewallet').val(0)
						ir_found=false;
					}else if(receivedjson[0].ir == null){
						$('#ir_search_res_div').html('<span style="color: red;">IR not Found / Incorrect</span>');
						$('#ewallet').val(0);
						ir_found=false;
					}
				}
			});//end ajax
			}//end if
	}//end if press enter key
	});//end key press event
	
	$(document).on('click','#search_ir_btn',function(){	
	$('#ir_search_res_div').html("");
		var selected_ir = $("#selectedIR_inp").val();
		//$('#selected_ir_ewal').
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
                $('#ir_search_res_div').html('<span style="color: #3F51B5;"><strong>IR :</strong> '+receivedjson[0].ir+'</span><br><span style="color: black;"><strong>Name :  </strong>'+receivedjson[0].name+' </span>, <br><span style="color: black;">'+receivedjson[0].aname+' </span><br> <span style="color:#3F51B5;"><strong>eWallet Value : </strong>'+receivedjson[0].ewallet+' ECs.</span><br>');
				$('#ewallet').val(receivedjson[0].ewallet)
				ir_found=true;
				}else if(receivedjson[0].id== "err"){
					$('#ir_search_res_div').html('<span style="color: red;">'+receivedjson[0].ir+'</span>');
					$('#ewallet').val(0)
					ir_found=false;
				}else if(receivedjson[0].ir == null){
					$('#ir_search_res_div').html('<span style="color: red;">IR not Found / Incorrect</span>');
					$('#ewallet').val(0)
					ir_found=false;
				}
            }
        });
		}//end if
	});//end search ir btn click
	
	$(document).on('click','#proceed_btn',function(){	
	///validate inputs
		$('#error').html("")
		if(ir_found==false){
			$('#error').html("Please Search for correct IR.");
			//document.getElementById("error").innerHTML = "Please Select IR ,Mandatory fields cannot be left blank.";
			window.scrollTo(0, 0);
			return false;
		}
		var y = $('#selectedIR_inp').val()// document.forms["myform"]["selectedIR_inp"].value;
		if (y == '') {
			$('#error').html("Please Select IR ,Mandatory fields cannot be left blank.");
			//document.getElementById("error").innerHTML = "Please Select IR ,Mandatory fields cannot be left blank.";
			window.scrollTo(0, 0);
			return false;
		}
		var y = $('#ir_service').val()//document.forms["myform"]["ir_service"].value;
		if (y == '') {
			$('#error').html("Please Select a service to charge the IR ,Mandatory fields cannot be left blank.");
			//document.getElementById("error").innerHTML = "Please Select a service to charge the IR ,Mandatory fields cannot be left blank.";
			window.scrollTo(0, 0);
			return false;
		}
                
		var y = $('#service_fees_inp').val();
		//document.forms["myform"]["service_fees_inp"].value;
		var y1 = parseInt(y);
		var yy = $('#ewallet').val();
		//document.forms["myform"]["ewallet"].value;
		var yy1=parseInt(yy);
		if (y == '' || y1 == 0) {
			$('#error').html("Please enter correct fees value in ECs ,Mandatory fields cannot be left blank.");
			//document.getElementById("error").innerHTML = "Please enter correct fees value in ECs ,Mandatory fields cannot be left blank.";
			window.scrollTo(0, 0);
			return false;
		}
		if (yy == '' || yy1 == 0) {
			$('#error').html("The selected IR ID doesnt have ECs in his ewallet.");
			
			//document.getElementById("error").innerHTML = "The selected IR ID doesnt have ECs in his ewallet.";
			window.scrollTo(0, 0);
			return false;
		}
		if (y1 > yy1) {
			$('#error').html("The selected IR ID doesnt have enough ECs in his ewallet.");
			
			//document.getElementById("error").innerHTML = "The selected IR ID doesnt have enough ECs in his ewallet.";
			window.scrollTo(0, 0);
			return false;
		}
		var sel_irid = $("#selectedIR_inp").val();
		//document.forms["myform"]["selectedIR_inp"].value
		var servicefees = $("#service_fees_inp").val();
		//document.forms["myform"]["service_fees_inp"].value
		var service_details = $("#ir_service option:selected").html();
		///proceed with payment if user confirms
		if(confirm('Are you sure you want to proceed with the withdrawal of '+servicefees+' ECs from IR '+sel_irid+' eWallet for '+service_details+'?')){
			console.log("thank you")
			
			$.ajax({
            //url: '', // url is empty because I'm working in the same file
			//var sel_ir=
            data: {'sel_irid': sel_irid,'service_details': service_details,'service_fees': servicefees,'secret':"4unf9unufru49fnr9kcg"},
            type: 'post',
            success: function(result) {
				console.log(result)
				var receivedjson=$.parseJSON(result)
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
});//end doc ready

</script>
<!------------------------------------------------------------------------------------------------->
<?php
endPage();
?>