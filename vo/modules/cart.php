<?php
;$core->verifyiraccount("cart");
$html_page->writeHeader();
$html_page->writeBody("Cart",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

/****************ORDER , PRODUCT INSERT ****This is to insert product into orderline and create the order id, then store the orderid into session*********/
////if user resets the cart get[id] is the product id
if (isset($_GET['id']) && $_GET['id'] == "reset") {
    $_SESSION['order_id'] = "";
	/*if user buys a product and purchase and id is not confirm  */
} elseif (isset($_GET['id']) && $_GET['id'] != "confirm" && $_GET['id'] != "reset") {
    if ($_SESSION['order_id'] == "" || !isset($_SESSION['order_id'])) {
        $sql = "INSERT INTO shop_order (ir_id, is_paid) "
                . " VALUES('" . $_SESSION['ir_id'] . "', '0') ";
        $database_manager->query($sql);
        $_SESSION['order_id'] = $database_manager->getLastGeneratedId();
		die( $_SESSION['order_id'] );
    }
	
    /*$sql = "SELECT name, (price + handling) AS total_price, rpts, dcpts,p_cost,p_profit,p_l0_com,p_l1_com,p_l2_com, is_vacation FROM product WHERE id = '" . $_GET['id'] . "' ";*/	$sql = "SELECT name, price, handling , p_cost , p_profit, rpts, dcpts, direct_com,bonus ,p_l0_com,p_l1_com,p_l2_com, p_l3_com, p_l4_com, p_l5_com, p_l6_com, p_l7_com, p_l8_com, p_l9_com, p_l10_com, p_l11_com, p_l12_com, p_l13_com, p_l14_com, p_l15_com, p_l16_com, p_l17_com, p_l18_com, p_l19_com, p_l20_com, p_l21_com,p_Z0_com, p_Z1_com,  is_vacation FROM product WHERE id = '" . $_GET['id'] . "' ";	
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
	
	/*handling cost , $commisionpaiddatetime , $total_product_price , $qntity*/
	$commisionpaiddatetime="";
	$qntity=1;
	$total_product_price=$qntity*$row['price'];
	/*added product_id	$qntity=1; ////should set this variable from the session variable	$total_product_price= (int)$row['price']*$qntity;	*/
   /* $sql = "INSERT INTO shop_order_line (shop_order_id, product_name, price, rpts, dcpts, is_vacation,product_id ,p_cost ,p_profit ,p_l0_com ,p_l1_com ,p_l2_com ) VALUES ("*/	$sql2 = "INSERT INTO shop_order_line (`shop_order_id` ,`product_name` ,`price` ,`rpts` ,`dcpts` ,`is_vacation` ,`product_id` ,`p_cost` ,`p_profit` ,`handling_cost` ,`p_l0_com` ,`p_l1_com` ,`p_l2_com` ,`coms_paid` ,`com_paid_datetime` ,`p_l3_com` ,`p_l4_com` ,`p_l5_com` ,`p_l6_com` ,`p_l7_com` ,`p_l8_com` ,`p_l9_com` ,`p_l10_com` ,`p_l11_com` ,`p_l12_com` ,`p_l13_com` ,`p_l14_com` ,`p_l15_com` ,`p_l16_com` ,`p_l17_com` ,`p_l18_com` ,`p_l19_com` ,`p_l20_com` ,`p_l21_com` ,`direct_com` ,`bonus` ,`p_Z0_com` ,`p_Z1_com` ,`qnty` ,`qnty_price` ,`product_status`) VALUES (";
   
   $sql2.= " '" . $_SESSION['order_id'] . "', '" . $row['name'] . "', '" . $row['price'] . "', '" . $row['rpts'] . "', '" . $row['dcpts'] . "', '" . $row['is_vacation'] . "', '" . $_GET['id'] . "', '" . $row['p_cost'] . "', '" . $row['p_profit'] . "','" . $row['handling'] . "', '" . $row['p_l0_com']*$qntity . "', '" . $row['p_l1_com']*$qntity . "', '" . $row['p_l2_com']*$qntity . "','0','".$commisionpaiddatetime."', '" . $row['p_l3_com']*$qntity . "', '" . $row['p_l4_com']*$qntity . "', '" . $row['p_l5_com']*$qntity . "', '" . $row['p_l6_com']*$qntity . "', '" . $row['p_l7_com']*$qntity . "', '" . $row['p_l8_com']*$qntity . "', '" . $row['p_l9_com']*$qntity . "', '" . $row['p_l10_com']*$qntity . "', '" . $row['p_l11_com']*$qntity . "', '" . $row['p_l12_com']*$qntity . "', '" . $row['p_l13_com']*$qntity . "', '" . $row['p_l14_com']*$qntity . "', '" . $row['p_l15_com']*$qntity . "', '" . $row['p_l16_com']*$qntity . "', '" . $row['p_l17_com']*$qntity . "', '" . $row['p_l18_com']*$qntity . "', '" . $row['p_l19_com']*$qntity . "', '" . $row['p_l20_com']*$qntity . "', '" . $row['p_l21_com']*$qntity . "','" . $row['direct_com']*$qntity . "','" . $row['bonus']*$qntity . "','" . $row['p_Z0_com']*$qntity . "','" . $row['p_Z1_com']*$qntity . "', '" . $qntity . "', '" . $total_product_price . "', 'new')";
  /* echo $sql2;
   //var_dump($sql2);
	die();*/
   $database_manager->query($sql2);
}

/*****************************************************************************************************************************************/

////if the url dosent have title get variable or if is not set make it my shop (retail)
if (!isset($_GET['title']) || $_GET['title'] == '') {
    $_GET['title'] = "My Shop";
}
//no conditions on load 1
/********************Qualification for BUs************************************/
///get qualification fees for the bu from config table
/*
$sql = "Select `binary_qualify_fees".$_SESSION['bu_to_qualify']."` FROM configuration ";
$result = $database_manager->query($sql);
error_log("sql error " . $sql);
$row = mysqli_fetch_assoc($result);
$qualify_fees = $row['binary_qualify_fees' . $_SESSION['bu_to_qualify']];
*/
/********************ORDER , Products (after order created get the values)************************************************************/
////get the sum of the prices, dcpts, rpts  of all items in the current order id 
$sql = "Select SUM(qnty_price) AS total_ewallet, "
        . " SUM(dcpts) AS total_dcpts, SUM(rpts) AS total_rpts "
        . " FROM shop_order_line "
        . " WHERE shop_order_id = '" . $_SESSION['order_id'] . "' ";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);
$total_ewallet = (int) $row['total_ewallet'];
$total_dcpts = (int) $row['total_dcpts'];
$total_rpts = (int) $row['total_rpts'];

/***********************PRODUCT , ORDER (after order created)***********************************************************/
$sql = "Select product_name AS name, price, qnty , qnty_price AS ewallet, rpts "
        . " FROM shop_order_line "
        . " WHERE shop_order_id = '" . $_SESSION['order_id'] . "' ";

$result = $database_manager->query($sql);
/******************************CUSTOMER , related to IR*****************************************************************/
$sql_customers = "SELECT id, name, mobile FROM customer WHERE related_ir_id = '" . $_SESSION['ir_id'] . "'"
        . " ORDER BY name ASC ";
$result_customers = $database_manager->query($sql_customers);

/*****************HTML Show  CART ITEMS***************************/
?>


<table class="table table-striped">
    <thead>
        <tr>
            <th class="product-column">Product</th>
            <th class="price-column">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td class="product-column"><?php echo $row['name']; ?></td>
                <?php if ($_GET['title'] != "Redeem Shop") { ?>
                    <td class="price-column"><?php echo $row['ewallet']; ?> EGP</td>
                <?php } else { ?> 
                    <td class="price-column"><?php echo $row['rpts']; ?> Rpts</td>
                <?php } ?>
            </tr>
            <?php
        }
		////************************Qualification , Add Qualification Fees to the Order Sum***********************************/
        if ($_GET['title'] == "Qualify") {
            $total_ewallet += (int) $qualify_fees;
            ?>
            <tr>
                <td class="product-column">Binary Shop Qualification</td>
                <td class="price-column"><?php echo $qualify_fees;  $_SESSION["language"]->ecurrency?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="total-product-column">Total</td>
            <?php if ($_GET['title'] != "Redeem Shop") { ?>
                <td class="total-price-column"><?php echo $total_ewallet; $_SESSION["language"]->ecurrency?> </td>
            <?php } else { ?> 
                <td class="total-price-column"><?php echo $total_rpts; ?> Rpts</td>
            <?php } ?>
        </tr>
    </tbody>
</table>
<div class="sep"></div>

<?php
/////***************************Chech eWallet , Redeem points Balance*******************/
$has_enough_funds == true;
if ($_GET['title'] != "Redeem Shop") {
    $has_enough_funds = $core->hasEnoughMoney($database_manager, $_SESSION['ir_id'], $_POST['total_ewallet']);
}
if ($_GET['title'] == "Redeem Shop") {
    $has_enough_funds = $core->hasEnoughRedeemPoints($database_manager, $_SESSION['ir_id'], $_POST['total_rpts']);
}
/* commented to allow negative ewallet*/
/*
if ($has_enough_funds == false && $_SESSION['order_id'] != "") {
    echo "<p class='col-xs-12 col-sm-12 col-md-12 col-lg-12' id='error'>".$_SESSION["language"]->cart_msg_moneynotenough."</p>";
}
*/
//***********************************************DO Payment*******(All Conditions are ok)*****************************************/
/*if (isset($_GET['id']) && $_GET['id'] == "confirm" && isset($_POST) && $_SESSION['order_id'] != "" && $has_enough_funds) {*/
/*removed hasenoughmoney to allow -ve ewallet */
if (isset($_GET['id']) && $_GET['id'] == "confirm" && isset($_POST) && $_SESSION['order_id'] != "") {
    $shipping_data = array();
	
	///////////////////*IF order to Existing customer***************/
    if ($_POST['ship_to'] == "Existing Customer") {
        $sql = "SELECT * FROM customer WHERE id = '" . $_POST['existing_customer_id'] . "' ";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $shipping_data['name'] = $row['name'];
        $shipping_data['email'] = $row['email'];
        $shipping_data['phone'] = $row['phone'];
        $shipping_data['mobile'] = $row['mobile'];
        $shipping_data['address'] = $row['address'];
        $shipping_data['area'] = $row['area'];
        $shipping_data['city'] = $row['city'];
        $shipping_data['country'] = $row['country'];
    }
	
	///////////////////*IF order to NEW customer***************/
    if ($_POST['ship_to'] == "New Customer") {
        $sql = "INSERT INTO customer "
                . " (related_ir_id, name, email, phone, mobile, address, area, city, country) "
                . " VALUES ('" . $_SESSION['ir_id'] . "', '" . $_POST['name'] . "', '" . $_POST['email'] . "', "
                . " '" . $_POST['phone'] . "', '" . $_POST['mobile'] . "', '" . $_POST['address'] . "', "
                . " '" . $_POST['area'] . "', '" . $_POST['city'] . "', '" . $_POST['country'] . "')";
        $result = $database_manager->query($sql);
        $_POST['existing_customer_id'] = $database_manager->getLastGeneratedId();/////get the new customer ID
        $_POST['ship_to'] = "Existing Customer";
        $shipping_data['name'] = $_POST['name'];
        $shipping_data['email'] = $_POST['email'];
        $shipping_data['phone'] = $_POST['phone'];
        $shipping_data['mobile'] = $_POST['mobile'];
        $shipping_data['address'] = $_POST['address'];
        $shipping_data['area'] = $_POST['area'];
        $shipping_data['city'] = $_POST['city'];
        $shipping_data['country'] = $_POST['country'];
    }
	
	///////////////////*IF order to SELF IR***************/
    if ($_POST['ship_to'] == "My Address") {
        $_POST['existing_customer_id'] = 0;/*set existing customer to 0*/
        /*$sql = "SELECT * FROM ir WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";*/
		$sql = "SELECT * FROM ir WHERE ir_id LIKE '".$_SESSION['ir_id']."'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $shipping_data['name'] = $row['f_name'] . " " . $row['l_name'];
        $shipping_data['email'] = $row['email'];
        $shipping_data['phone'] = $row['phone'];
        $shipping_data['mobile'] = $row['mobile'];
        $shipping_data['address'] = $row['address'];
        $shipping_data['area'] = $row['area'];
        $shipping_data['city'] = $row['city'];
        $shipping_data['country'] = $row['country'];
    }
	
	////////////////UDATE ORDER TABLE based on Session order id , and shipping date from previous conditions***************/
    $sql = "UPDATE shop_order "
            . " SET datetime = '" . $core->getFormatedDateTime() . "', "
            . " customer_id = '" . $_POST['existing_customer_id'] . "', "
            . " name = '" . $shipping_data['name'] . "', "
            . " email = '" . $shipping_data['email'] . "', "
            . " phone = '" . $shipping_data['phone'] . "', "
            . " mobile = '" . $shipping_data['mobile'] . "', "
            . " address = '" . $shipping_data['address'] . "', "
            . " area = '" . $shipping_data['area'] . "', "
            . " city = '" . $shipping_data['city'] . "', "
            . " country = '" . $shipping_data['country'] . "', "
            . " ewallet_amount = '" . $total_ewallet . "', "
            . " rpts_amount = '" . $total_rpts . "', "
            . " shop_title = '" . $_GET['title'] . "' "
            . " WHERE id = '" . $_SESSION['order_id'] . "' ";
    $result = $database_manager->query($sql);
    $order_number = $_SESSION['order_id'];
    
     echo '<h2>'.$_SESSION['language']->cart_msg_ordercompletedusccess; 
	/// if the order is Qualification******************/
    if ($_GET['title'] == "Qualify") {
        echo " ". $_SESSION['language']->cart_msg_andshopisqualified;
    }
	/****************HTML Show Success and order information***************/
    ?>.</h2>
    <table class="order-info">
        <tbody>
            <tr>
                <th>
                    Order Number: 
                </th>
                <td>
                    <?php echo $order_number; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Name: 
                </th>
                <td>
                    <?php echo $shipping_data['name']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Email: 
                </th>
                <td>
                    <?php echo $shipping_data['email']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Phone: 
                </th>
                <td>
                    <?php echo $shipping_data['phone']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Mobile: 
                </th>
                <td>
                    <?php echo $shipping_data['mobile']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Address: 
                </th>
                <td>
                    <?php echo $shipping_data['address']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Area: 
                </th>
                <td>
                    <?php echo $shipping_data['area']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    City: 
                </th>
                <td>
                    <?php echo $shipping_data['city']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Country: 
                </th>
                <td>
                    <?php echo $shipping_data['country']; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
	
	/*************REDEEMTION POINTS SHOP****************************************************/
    if ($_GET['title'] == "Redeem Shop") {

        $sql = "SELECT rpts FROM ir "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $rpts = $row['rpts'];

        $sql = "UPDATE ir SET rpts = rpts - " . $_POST['total_rpts'] . " "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);

        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) "
                . " VALUES ('" . $_SESSION['ir_id'] . "', 'Redeem', '" .
                $core->getFormatedDateTime() . "', '" . (string) (0 - $_POST['total_rpts']) . "', '" . (string) ($rpts - $_POST['total_rpts']) . "', '".$_SESSION['company']."')";
        $database_manager->query($sql);
    }
	
	///////////////////////////**************************RETAIL SHOP PROCESS PAYMENT***********************************/
    if ($_GET['title'] == "My Shop") {
		
		////get ewallet balance
        $sql = "SELECT ewallet, dcpts FROM ir "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $ewallet = $row['ewallet'];
        $dcpts = $row['dcpts'];
		
		////substract total order from ewallet
        $sql = "UPDATE ir SET ewallet = ewallet - " . $_POST['total_ewallet'] . ", "
                . " dcpts = dcpts + " . $_POST['total_dcpts'] . " , "
                . " total_dcpts = total_dcpts + " . $_POST['total_dcpts'] . " "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);
		
		///create transaction record
		$sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Product Purchase', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $_POST['total_ewallet']) . "', '" . (string) ($ewallet - $_POST['total_ewallet']) . "', '".$_SESSION['company']."')";
        $database_manager->query($sql);
		
		/*******************DC points Retail Commission***************/
		
		///change from Direct Binary Commission to Direct Retail Commission
       /** $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Direct Retail Commission', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) ($_POST['total_dcpts']) . "', '" . (string) ($dcpts + $_POST['total_dcpts']) . "', '".$_SESSION['company']."')";
        $database_manager->query($sql);
		
		/*******************DC points Retail Commission***************/
		
        
    }
	
	///*********************************Qualification payment processing************************************/
    if ($_GET['title'] == "Qualify") {
        
		///get the ewallet, dcpts cur value of the ir
        $sql = "SELECT ewallet, dcpts FROM ir "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $ewallet = $row['ewallet'];
        $dcpts = $row['dcpts'];
		
		////substract the qualification fees , and the product fees from the ewallet , the sum of product and qualification fees are stored in total_ewallet post
		///the total ewallet is submited from the form when click on pay in the cart
        $sql = "UPDATE ir SET ewallet = ewallet - " . $_POST['total_ewallet'] . " "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);
		
		//add the qalification fees && product fees transaction to the transaction table 
        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Qualify Binary Shop', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $_POST['total_ewallet']) . "', '" . (string) ($ewallet - $_POST['total_ewallet']) . "', '".$_SESSION['company']."')";
        $database_manager->query($sql);

        // UPDATE IR & BU

        $sql = "UPDATE bu SET is_qualified = 2 "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
                . " AND code = '" . $_SESSION['bu_to_qualify'] . "' ";
        $database_manager->query($sql);

        $sql = "UPDATE ir SET qualification_date = '" . $core->getFormatedDateTime() . "' WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);

        // UPDATE Parents dc, dbv, abv
		///in this condition we should should add where the parent bu is qualified 2 (Only update qualified binary business units)
        $sql = "UPDATE bu SET left_dbv = left_dbv + 1, left_abv = left_abv + 1, Lcount = (Lcount + 1) "
                . " WHERE left_children LIKE '%" . $_SESSION['ir_id'] . "-" . $_SESSION['bu_to_qualify'] . "%' "
				. " AND  `is_qualified` =2 ";
        $database_manager->query($sql);
		
		/////update daily counter for LEFT child uplines (Only update qualified binary business units)
        $sql = "UPDATE dc SET left_dc = left_dc + 1 "
                . " WHERE date = '" . $core->getFormatedDate() . "' "
                . " AND bu_id IN ( "
                . " SELECT CONCAT(ir_id, '-', code) AS bu_id FROM bu "
                . " WHERE left_children LIKE '%" . $_SESSION['ir_id'] . "-" . $_SESSION['bu_to_qualify'] . "%' "
				. " AND  `is_qualified` =2 "
                . ")";
        $database_manager->query($sql);
		
		/////update Dynamic Counters and Accumulative Counters (Only update qualified binary business units)
        $sql = "UPDATE bu SET right_dbv = right_dbv + 1, right_abv = right_abv + 1 ,Rcount = (Rcount + 1)"
                . " WHERE right_children LIKE '%" . $_SESSION['ir_id'] . "-" . $_SESSION['bu_to_qualify'] . "%' "
				. " AND  `is_qualified` =2 ";
        $database_manager->query($sql);
		
		/////update daily counter for RIGHT child uplines (Only update qualified binary business units)
        $sql = "UPDATE dc SET right_dc = right_dc + 1 "
                . " WHERE date = '" . $core->getFormatedDate() . "' "
                . " AND bu_id IN ( "
                . " SELECT CONCAT(ir_id, '-', code) AS bu_id FROM bu "
                . " WHERE right_children LIKE '%" . $_SESSION['ir_id'] . "-" . $_SESSION['bu_to_qualify'] . "%' "
				. " AND  `is_qualified` =2 "
                . ")";
        $database_manager->query($sql);
		
		///// add dcpts to the sponsor (referrar) IR ,
	
	///get the sponsor IRid  (REFERRAR)
	 //$sql = "SELECT `referral_bu_id` as sponsor_IR_Bu FROM `bu` WHERE `ir_id` ='".$_SESSION['ir_id']."' and `code` = '001'";
	 $sql = "SELECT  REPLACE((SELECT `referral_bu_id` FROM `bu` WHERE `ir_id` ='".$_SESSION['ir_id']."' limit 1), '-001','')  as sponsor_IR";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
		$sponsorIr = $row['sponsor_IR'];
		$sponsorIr= str_replace("-002", "", $sponsorIr);
		$sponsorIr= str_replace("-003", "", $sponsorIr);

        
	////Shafee IR fromthe BU code
	/*******************DC points Retail Commission for Sponser/Referrar IR ***************/
	
	////get the Sponsor IR dcpts
	/** $sql = "SELECT dcpts FROM ir "
                . " WHERE ir_id = '" . $sponsorIr . "'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $dcpts = $row['dcpts'];
	/////get the total order dcpts
	//$totaldcptsoforder=100;
	
	///update sponsor IR dcpts
	 $sql = "UPDATE ir SET dcpts = dcpts + " . $_POST['total_dcpts'] . " , "
                . " total_dcpts = total_dcpts + " . $_POST['total_dcpts'] . " "
                . " WHERE ir_id = '" . $sponsorIr . "'";
        $database_manager->query($sql);
		
	///add transaction to the sponsor ir ewallet with the dcpts update
	 $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $sponsorIr . "', 'Direct Retail Commission Points', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) ($_POST['total_dcpts']) . "', '" . (string) ($dcpts + $_POST['total_dcpts']) . "', '".$_SESSION['company']."')";
        $database_manager->query($sql);
		/////////end dcpts for sponsor
		**/
    }
	
/*******************END DC points Retail Commission***************/

/****************if the order has vacation products create certificates for the vacation order***************/
    $sql = "SELECT product_name FROM shop_order_line WHERE shop_order_id = '" . $_SESSION['order_id'] . "' "
            . " AND is_vacation = 1 ";
    $result = $database_manager->query($sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $sql = "INSERT INTO vacation_certificate (ir_id, datetime, shop_order_id, product_name) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', '";
        $sql .= $core->getFormatedDateTime() . "', '" . $order_number . "', '" . $row['product_name'] . "')";
        $database_manager->query($sql);
    }
	/////clear the order session after processing the payment 
    $_SESSION['order_id'] = "";
} else {/////end if do payment all parameters ar OK , if not do the following
	
	////Order still in the session and not paid yet SHOW SHIPPING info AND PAyment OPTIONS****************/
    if (isset($_SESSION['order_id']) && $_SESSION['order_id'] != "") {
        ?>
        <script>
            function showNewCustomerData() {
                var y = document.forms["myform"]["ship_to"].value;
                if (y == "New Customer") {
                    document.getElementById("new-customer-data").style.display = "block";
                    document.getElementById("existing-customer-data").style.display = "none";
                }
                if (y == "Existing Customer") {
                    document.getElementById("new-customer-data").style.display = "none";
                    document.getElementById("existing-customer-data").style.display = "block";
                }
                if (y == "My Address") {
                    document.getElementById("new-customer-data").style.display = "none";
                    document.getElementById("existing-customer-data").style.display = "none";
                }
            }
            function validateForm() {
                var y = document.forms["myform"]["ship_to"].value;
                if (y == "New Customer") {
                    var y = document.forms["myform"]["name"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    var y = document.forms["myform"]["email"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    if (validateEmail(y) == false) {
                        document.getElementById("error").innerHTML = "Please type your email correctly.";
                        return false;
                    }
                    var y = document.forms["myform"]["mobile"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    var y = document.forms["myform"]["phone"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    var y = document.forms["myform"]["address"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    var y = document.forms["myform"]["area"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    var y = document.forms["myform"]["city"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                    var y = document.forms["myform"]["country"].value;
                    if (y == '') {
                        document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                        return false;
                    }
                }
                return true;
            }
        </script>
        <p id="error"></p>

        <form method="post" name="myform" onsubmit="return confirm('Do you want to proceed with the payment?');" action="index.php?page=cart&title=<?php echo $_GET['title']; ?>&id=confirm"
              onsubmit="return validateForm();">
            <label>Ship to <span class="astrisk"> *</span></label> 
            <select name="ship_to" onchange="showNewCustomerData();">
                <option value="My Address" selected>My Address</option>
                <?php if (mysqli_num_rows($result_customers) > 0) { ?>
                    <option value="Existing Customer">Existing Customer</option>
                <?php } ?>
                <option value="New Customer">New Customer</option>
            </select> 
            <br class="clear"/>
            <div id="existing-customer-data">
                <label>Search Customers <span class="astrisk"> *</span></label> 
                <select name="existing_customer_id">
                    <?php
                    while ($row_customers = mysqli_fetch_assoc($result_customers)) {
                        ?>
                        <option value="<?php echo $row_customers['id']; ?>" 
                        <?php
                        if (isset($_POST) && $row_customers['id'] == $_POST['existing_customer_id']) {
                            echo "selected";
                        }
                        ?>>
                                <?php echo $row_customers['name'] . " - " . $row_customers['mobile']; ?></option>
                            <?php
                    }
                    ?>
                </select> <br class="clear"/>
            </div>
            <div id="new-customer-data">

                <label>Name <span class="astrisk"> *</span></label> 
                <input name="name" type="text" value="<?php
            if (isset($_POST)) {
                echo $_POST['name'];
            }
                    ?>" />  <br class="clear"/>

                <label>Email <span class="astrisk"> *</span></label> 
                <input name="email" type="text" value="<?php
        if (isset($_POST)) {
            echo $_POST['email'];
        }
                    ?>" />  <br class="clear"/>

                <label>Mobile <span class="astrisk"> *</span></label> 
                <input name="mobile" type="text" value="<?php
        if (isset($_POST)) {
            echo $_POST['mobile'];
        }
                    ?>" />  <br class="clear"/>

                <label>Phone <span class="astrisk"> *</span></label> 
                <input name="phone" type="text" value="<?php
        if (isset($_POST)) {
            echo $_POST['phone'];
        }
                    ?>" />  <br class="clear"/>

                <label>Address <span class="astrisk"> *</span></label> 
                <textarea name="address" ><?php
        if (isset($_POST)) {
            echo $_POST['address'];
        }
                    ?></textarea> <br class="clear"/>

                <label>District <span class="astrisk"> *</span></label> 
                <input name="area" type="text" value="<?php
            if (isset($_POST)) {
                echo $_POST['area'];
            }
                    ?>" />  <br class="clear"/>

                <label>City <span class="astrisk"> *</span></label> 
                <input name="city" type="text" value="<?php
        if (isset($_POST)) {
            echo $_POST['city'];
        }
                    ?>" />  <br class="clear"/>

                <label>Country <span class="astrisk"> *</span></label> 
                <input name="country" type="text" value="<?php
        if (isset($_POST)) {
            echo $_POST['country'];
        }
                    ?>" />  <br class="clear"/>

            </div>
            <div class="sep"></div>
            <input type="hidden" name="total_ewallet" value="<?php echo $total_ewallet; ?>">
            <input type="hidden" name="total_rpts" value="<?php echo $total_rpts; ?>">
            <input type="hidden" name="total_dcpts" value="<?php echo $total_dcpts; ?>">
            <div id="cart-actions">
                <?php if ($_GET['title'] != "Qualify") { ?>
                    <a class="button" href="index.php?page=categories&title=<?php echo $_GET['title']; ?>">Add Products</a>
                    <?php
                }
                if (isset($_SESSION['order_id']) && $_SESSION['order_id'] != "") {
                    ?>
                    <a class="button" href="index.php?page=cart&title=<?php echo $_GET['title']; ?>&id=reset">Reset</a>
                </div>
                <?php if ($_GET['title'] != "Redeem Shop") { ?>
                    <div class="sep dotted"></div>

                    <h2>Payment Details:</h2>

                    <div id="ewallet_payment">
                        <div class="payment_icon"><i class="fa fa-money fa-fw"></i></div>
                        <h2>Pay using E Wallet:</h2>
                        <p class="error">You should have enough credit in your E Wallet.</p>

                        <button type="submit"><i class="fa fa-check-square fa-fw"></i>Pay & Confirm Purchase</button>

                    </div>

                    <div id="creditcard_payment">
                        <div class="payment_icon"><i class="fa fa-credit-card fa-fw"></i></div>
                        <h2>Pay using Recharge Card:</h2>
						<label>Card Number <span class="astrisk"> *</span></label> 
						<input name="cardnumber" type="text" value="<?php
						if (isset($_POST)) {
							echo $_POST['cardnumber'];
						}?>" />  <br class="clear"/>
						<label>Card Security Code <span class="astrisk"> *</span></label> 
						<input name="cardpsw" type="password" value="<?php
						if (isset($_POST)) {
							echo $_POST['cardpsw'];
						}?>" />  <br class="clear"/>						
                        <p class="error">Credit Card Payments are not available at the time being.</p>
                        <a class="button inactive" ><i class="fa fa-check-square fa-fw"></i>Pay & Confirm Purchase</a>
                    </div>

                    <div class="sep dotted"></div>
                <?php } else { ?>
                    <div class="sep dotted"></div>

                    <h2>Payment Details:</h2>

                    <div id="ewallet_payment">

                        <h2>Pay using Redeem Points:</h2>
                        <p class="error">You should have enough Redeem Points.</p>

                        <button type="submit"><i class="fa fa-check-square fa-fw"></i>Pay & Confirm Purchase</button>

                        <div class="sep dotted"></div>

                    </div>
                <?php } ?>

            <?php } else { ?>
            </div>
        <?php } ?>
        </form>
        <?php
    } else {
        ?>

        <div class="sep"></div>
        <div id="cart-actions">
            <?php if ($_GET['title'] != "Qualify") { ?>
                <a class="button" href="index.php?page=categories&title=<?php echo $_GET['title']; ?>">Add Products</a>
            <?php } ?>
        </div>
        <?php
    }
}
?>

<?php $html_page->writeFooter(); ?>