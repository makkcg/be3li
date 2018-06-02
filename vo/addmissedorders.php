<?php
////file created to manually add any previously missed or deleted orders , through a form where you select the ir and products by ids

include "includes/database_manager.php";
//include "includes/core.php";

$database_manager = new DatabaseManager();
//$core = new Core($database_manager);

///get the products data
	$listofproducts=array();
	$sql = "SELECT * FROM `product` WHERE 1";
	$result = $database_manager->query($sql);
	while ($row = mysqli_fetch_assoc($result)) {
    $listofproducts[]=array("id"=> $row['id'],"name"=> $row['name'], "price"=> $row['price'], "handling"=> $row['handling'], "dcpts"=> $row['dcpts'], "rpts"=> $row['rpts'], "is_vacation"=> $row['is_vacation'], "category"=> $row['category'], "top_category"=> $row['top_category'], "img"=> $row['img']);
	//echo json_encode($listofproducts);
	}

/////get the ir data sent by ajax
if(isset($_POST['selectedIR']) && $_POST['selectedIR'] !=""){
	$selectedIR=$_POST['selectedIR'];
	$IR_data=array();
	$sql = "SELECT CONCAT(f_name, ' ', l_name) as irname ,email,phone,mobile,address,area,city,country FROM `ir` WHERE ir_id = '".$selectedIR."'";
	$result = $database_manager->query($sql);
    if ($result) {
       $row = mysqli_fetch_assoc($result);
    $IR_data[]=array("irid"=> $selectedIR,"irname"=> $row['irname'], "email"=> $row['email'], "phone"=> $row['phone'], "mobile"=> $row['mobile'], "address"=> $row['address'], "area"=> $row['area'], "city"=> $row['city'], "country"=> $row['country']);
	echo json_encode($IR_data);
	die();
	} else {
		 $errormsg="error no ir or no data ".$result;
        error_log($sql);
	die();
    }
	die();
}

//////////if save btn is pressed  ///////////


if(isset($_POST["ir_id"]) && isset($_POST["products"]) && isset($_POST["datetime"]) && $_POST["products"] != ""  && $_POST["irname"] != "" && $_POST["iraddress"] != ""){
//$orderid=$_POST["orderid"];
$ir_id=$_POST["ir_id"];
$irname=$_POST["irname"];
$customer_id=$_POST["customer_id"];
$iremail=$_POST["iremail"];
$irphone=$_POST["irphone"];
$irmobile=$_POST["irmobile"];
$iraddress=$_POST["iraddress"];
$irarea=$_POST["irarea"];
$ircity=$_POST["ircity"];
$ircountry=$_POST["ircountry"];
$products= explode("~", $_POST["products"]);
$shoptitle="Qualify";
//for($ad=0;$ad<sizeof($products);$ad++){
//echo $listofproducts[($products[$ad])]["id"]." - ".$listofproducts[($products[$ad])]["name"]."<br>";
//}
$datetime=$_POST["datetime"]; //date("Y-m-d H:i:s");	
$ewalletamount=0;
$totdcpts=0;
for($ad=0;$ad<sizeof($products);$ad++){
	///array("id"=> $row['id'],"name"=> $row['name'], "price"=> $row['price'], "handling"=> $row['handling'], "dcpts"=> $row['dcpts'], "rpts"=> $row['rpts'], "is_vacation"=> $row['is_vacation'], "category"=> $row['category'], "top_category"=> $row['top_category'], "img"=> $row['img'])
$ewalletamount=$ewalletamount + (float)$listofproducts[($products[$ad])]["price"] + (float)$listofproducts[($products[$ad])]["handling"];
//$totdcpts=$totdcpts + (float)$listofproducts[($products[$ad])]["dcpts"];
$rpts_tot=$rpts_tot + $listofproducts[($products[$ad])]["rpts"];
}

///substract from the ewallet the total order value
//insert transaction in ir transactions table

//////insert order in shop_order table
///shop_order fields : `id``ir_id``datetime``customer_id``name``email``phone``mobile``address``area``city``country``ewallet_amount``rpts_amount``shop_title``is_paid`
	$orderid="";
	$sql = "INSERT INTO  `shop_order` (`id` ,`ir_id` ,`datetime` ,`customer_id` ,`name` ,`email` ,`phone` ,`mobile` ,`address` ,`area` ,`city` ,`country` ,`ewallet_amount` ,`rpts_amount` ,`shop_title` ,`is_paid`) VALUES (
NULL ,  '".$ir_id."',  '".$datetime."',  '".$customer_id."',  '".$irname."',  '".$iremail."',  '".$irphone."',  '".$irmobile."',  '".$iraddress."',  '".$irarea."',  '".$ircity."',  '".$ircountry."',  '".$ewalletamount."',  '".$rpts_tot."',  '".$shoptitle."',  '0');";
	$result = $database_manager->query($sql);
    if ($result) {
		///// get the order id to be used in next step
	$orderid=$database_manager->getLastGeneratedId();
	$sql2="SELECT LAST_INSERT_ID()  as lastid";
	$result2 = $database_manager->query($sql2);
	if($result2){
		$row = mysqli_fetch_assoc($result2);
		$orderid=$row["lastid"];
		
		//echo $listofproducts[($products[0])]["name"];
		///insert products in shop_order_line table

		for($aad=0;$aad < sizeof($products);$aad++){
			$totprice= (int)((float)$listofproducts[($products[$aad])]["price"] + (float)$listofproducts[($products[$aad])]["handling"]);
			$sql1 = "INSERT INTO shop_order_line (id, shop_order_id, product_name, price, rpts, dcpts, is_vacation, product_id) VALUES (NULL, '".$orderid."', '".$listofproducts[($products[$aad])]["name"]."', '".$totprice."', '".(int)$listofproducts[($products[$aad])]["rpts"]."', '".(int)$listofproducts[($products[$aad])]["dcpts"]."', '".(int)$listofproducts[($products[$aad])]["is_vacation"]."', '".(int)$listofproducts[($products[$aad])]["id"]."');";
			$result1 = $database_manager->query($sql1);
			//echo $result1;
		};//end looping order products
		die("Order Added successfully");
	}else{
	die($result2);
	}
	}




}

	
?>
<html>
<head>
<title>Manualy insert order to IR</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
</head>
<body>
 <form method="post" name="myform" onsubmit="return confirm('Do you want to proceed adding the order?');" action="" onsubmit="return validateForm();">
 
	
	
	<label style="min-width:150px;float: left;">order date-time</label>
	<input name="datetime_ord" id="datetime_ord"  value="<?php echo date("Y-m-d H:i:s"); ?>" type="datetime" autocomplete="off">
	<p></p>
	<label style="min-width:150px;float: left;">IR ID</label>
	<input name="irid" id="irid"  value="" type="text" autocomplete="on">
	<button type="button" id="loadirdata_btn" class="ok">load shipto data</button>
	<p></p>
	<label style="min-width:150px;float: left;">Customer Id</label>
	<input name="irid"  value="" type="text" autocomplete="on">
	<button type="button" id="loadcustomerdata_btn" class="ok">load customer data</button>
	<p></p>
	<p></p>
	<hr>
	<label>Ship to <span class="astrisk">*</span></label>
	<br class="clear">
	<div id="new-customer-data" style="display: block;">
	<label style="min-width:150px;float: left;">Name <span class="astrisk">*</span></label>
	<input name="name" id="ir_name" type="text" value="">
	<br class="clear">
	<label style="min-width:150px;float: left;">Email <span class="astrisk">*</span></label>
	<input name="email" id="ir_email" type="text" value="">
	<br class="clear">
	<label style="min-width:150px;float: left;">Mobile <span class="astrisk">*</span></label>
	<input name="mobile" id="ir_mobile" type="text" value="">
	<br class="clear">
	<label style="min-width:150px;float: left;">Phone <span class="astrisk">*</span></label>
	<input name="phone" id="ir_phone" type="text" value=""><br class="clear">
	<label style="min-width:150px;float: left;">Address <span class="astrisk">*</span></label>
	<textarea name="address" id="ir_address"></textarea>
	<br class="clear">
	<label style="min-width:150px;float: left;">District <span class="astrisk">*</span></label>
	<input name="area" id="ir_area" type="text" value="">
	<br class="clear">
	<label style="min-width:150px;float: left;">City <span class="astrisk">*</span></label>
	<input name="city" id="ir_city" type="text" value="">
	<br class="clear">
	<label style="min-width:150px;float: left;">Country <span class="astrisk">*</span></label>
	<input name="country" id="ir_country" type="text" value="">
	<br class="clear"></div>
<hr>
<p></p>
	<p></p>
<div class="col1">
		<label>Add products to the order <span class="astrisk"> *</span></label> 
            <select name="add_product_dd" id="add_product_dd">
			<?php
			///array("id"=> $row['id'],"name"=> $row['name'], "price"=> $row['price'], "handling"=> $row['handling'], "dcpts"=> $row['dcpts'], "rpts"=> $row['rpts'], "is_vacation"=> $row['is_vacation'], "category"=> $row['category'], "top_category"=> $row['top_category'], "img"=> $row['img'])
			for($dd=0;$dd<sizeof($listofproducts);$dd++){ ?>
                <option value="<?php echo $dd; ?>"
				 data-productid="<?php echo $listofproducts[$dd]["id"]; ?>" data-productname="<?php echo $listofproducts[$dd]["name"]; ?>" data-productprice="<?php echo $listofproducts[$dd]["price"]; ?>" data-producthandling="<?php echo $listofproducts[$dd]["handling"]; ?>" data-productdcpts="<?php echo $listofproducts[$dd]["dcpts"]; ?>" data-productrpts="<?php echo $listofproducts[$dd]["rpts"]; ?>" data-productis_vacation="<?php echo $listofproducts[$dd]["is_vacation"]; ?>"><?php echo $listofproducts[$dd]["name"]." + Shipping & Handling "; ?></option>
			<?php };//end for ?>
			</select>
			<button type="button" id="addproduct_btn" class="ok">Add Selected Product to order</button>
			<br class="clear"/>
	</div>
	<p></p>
	<p></p>
	<label style="min-width:150px;float: left;">added products</label>
	<div id="addedproducts"></div>
	<p></p>
	<p></p>
	
	<button type="button" id="saveorders_btn" class="ok">SAVE Orders</button>
 </form>

 <script>
 $( document ).ready(function() {
	var orderproducts= [];
    console.log( "ready!" );
	
	$("#addproduct_btn").click(function(){
		var productphparr= $("#add_product_dd :Selected").val();
		var productnam=$("#add_product_dd :Selected").text();
		orderproducts.push(productphparr);
		console.log(orderproducts);
		/////show added orders
		//$("#addedproducts").html("")
		//for(ss=0;ss<orderproducts.length;ss++){
		$("#addedproducts").append("<br>"+productnam+"</br>")	
		//}
		
	});//end click add product to order
	
	$("#loadirdata_btn").click(function(){
    var ir=$("#irid").val().toUpperCase();	
	if(ir!=""){
		///do the ajax call to get the IR last balance at Proshops accountant
		$.ajax({
            //url: '', // url is empty because I'm working in the same file
            data: {'selectedIR': ir},
            type: 'post',
            success: function(result) {
				var receivedjson=$.parseJSON(result)
                //array("irid"=> $selectedIR,"irname"=> $row['irname'], "email"=> $row['email'], "phone"=> $row['phone'], "mobile"=> $row['mobile'], "address"=> $row['address'], "area"=> $row['area'], "city"=> $row['city'], "country"=> $row['country']);
                $('#ir_name').val(receivedjson[0].irname);
				$('#ir_email').val(receivedjson[0].email);
				$('#ir_phone').val(receivedjson[0].phone);
				$('#ir_mobile').val(receivedjson[0].mobile);
				$('#ir_address').val(receivedjson[0].address);
				$('#ir_area').val(receivedjson[0].area);
				$('#ir_city').val(receivedjson[0].city);
				$('#ir_country').val(receivedjson[0].country);
				
            }
        });

	};//end if ir !=""
	})////end click ir data
	
	/////click btn to save the order and its products 
	$("#saveorders_btn").click(function(){
		////products data
		var products=""
		
		for(aa=0;aa< orderproducts.length;aa++){
			if(aa< orderproducts.length-1){
			products=products+orderproducts[aa]+"~"
			}else if(aa==orderproducts.length-1){
			products=products+orderproducts[aa]	
			}
		}
		console.log(products)
		
		///order data 
		////`ir_id``datetime``customer_id``name``email``phone``mobile``address``area``city``country``ewallet_amount``rpts_amount``shop_title``is_paid`
		var ir=$("#irid").val().toUpperCase();
		var ord_datetime=$('#datetime_ord').val()
		var customer_id=0;
		var irname=$('#ir_name').val();
		var iremail=$('#ir_email').val();
		var irphone=$('#ir_phone').val();
		var irmobile=		$('#ir_mobile').val();
		var iraddress=		$('#ir_address').val();
		var irarea=$('#ir_area').val();
		var ircity=$('#ir_city').val();
		var ircountry=$('#ir_country').val();
		
		
		/////ajax to save products and data 
		$.ajax({
            //url: '', // url is empty because I'm working in the same file
            data: {'ir_id':ir,'datetime':ord_datetime,'customer_id':customer_id,'irname':irname,'iremail':iremail,'irphone':irphone,'irmobile':irmobile,'iraddress': iraddress, 'irarea':irarea, 'ircity':ircity, 'ircountry':ircountry, 'products':products},
            type: 'post',
            success: function(result) {
				alert(result);
            }
        });///end ajax save 
		
		
	});///end click to save 
	
});//end doc ready

 </script>
 </body></html>