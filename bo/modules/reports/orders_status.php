<?php
/*debug show error function in grids phpgrid_error("is all products fully paid : ".$isAllProductsFullyPaid); */
$g1 = new jqgrid();

$table1 = "shop_orders";
/*
$select1 = "SELECT mt.id, mt.ir_id, mt.datetime, mt.shop_title, IF(mt.customer_id=0,'IR Self Order',mt.customer_id) as customer_id, mt.name, mt.status , IF(mt.customer_id = 0, CONCAT(ir.address, ' , ', ir.area ,' , ',ir.city) , CONCAT(cus.address, ' , ', cus.area ,' , ',cus.city))  as address , ir.mobile
FROM shop_order mt inner join ir
on ir.ir_id=mt.ir_id
left join customer cus on cus.id = mt.customer_id";
*/
$select1 ="SELECT mt.id, mt.ir_id, mt.datetime,IF(mt.status ='Delivered',(select 'Delivered'),DATEDIFF(now(),mt.datetime) ) as dayssncord ,  mt.shop_title, IF(mt.customer_id=0,'IR Self Order',mt.customer_id) as customer_id, mt.name, mt.status , IF(mt.customer_id = 0, CONCAT(ir.address, ' , ', ir.area ,' , ',ir.city) , CONCAT(cus.address, ' , ', cus.area ,' , ',cus.city))  as address , ir.mobile, mt.delivery_datetiem, mt.shipping_company , mt.shipping_tracking_number
FROM shop_order mt inner join ir
on ir.ir_id=mt.ir_id
left join customer cus on cus.id = mt.customer_id
WHERE mt.datetime != '' ";

$export_enabled=true;
$actions1 = array("add" => false, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
//$select1 .= getDefaultSelectLines2();

//$actions1 = getDefaultActions();
$options1 = getDefaultOptions("Orders Report ".getFormatedDateTime());///export file name

$conditions1 = getDefaultConditions();
$columns1 = array();

$new_column = newIdColumn();
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn("IR_ID", "mt.ir_id");
$new_column["width"] = 100;
$new_column["editable"] = "false";
$columns1[] = $new_column;

$new_column = newTextColumn("CustomerID", "customer_id");
$new_column["width"] = 100;
$new_column["editable"] = "false";
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn("OrderType", "mt.shop_title");
$new_column["width"] = 100;
$new_column["editable"] = "false";
$columns1[] = $new_column;

$new_column = newTextColumn("OrderDate", "mt.datetime");
$new_column["editable"] = "false";
$new_column["width"] = 200;
$columns1[] = $new_column;


$new_column = newTextColumn("Days Since Ordered", "dayssncord");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$select_values = "New:New;InPreparation:InPreparation;Shipping:Shipping;Delivered:Delivered;Postponed:Postponed;Returned:Returned;Fully-Paid:Fully-Paid";
//$select_values = "New:New;Resolved:Resolved";
//$new_column = newCustomSelectColumnWithFormatter("Status", "status", "mt.status", $select_values);

$new_column =newMandatorySelectColumnX("Status", "status", "status", "SELECT `status` FROM `shippingstatus`");
$new_column["width"] = 80;
$new_column['editable'] = true;
$columns1[] = $new_column;



$new_column = newTextColumn("Delivery date", "mt.delivery_datetiem");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("IR/Customer Name", "mt.name");
$new_column["editable"] = "false";
$new_column["width"] = 250;
$columns1[] = $new_column;

$new_column = newTextColumn("Address", "address");
$new_column["editable"] = "false";
$new_column["width"] = 250;
$columns1[] = $new_column;

$new_column = newTextColumn("Referral IRID", "ref_irid");
$new_column["editable"] = "false";
$new_column["width"] = 150;
$columns1[] = $new_column;


$new_column = newTextColumn("Shipping Company", "mt.shipping_company");
$new_column['editable'] = true;
$new_column["width"] = 250;
$columns1[] = $new_column;

$new_column = newTextColumn("Shipping Tracking Number", "mt.shipping_tracking_number");
$new_column['editable'] = true;
$new_column["width"] =250;
$columns1[] = $new_column;
//displayGrid($g1, $select1, $columns1);
//$events1 = getDefaultEvents();

function updateRowInG1($data) {
	if($data["params"]["status"]=="Delivered" || $data["params"]["status"]=="Fully-Paid"){
		$sql="UPDATE shop_order SET  `status` = '".$data["params"]["status"]."' WHERE  shop_order.id =".$data["id"];
		if (!mysql_query($sql)) { 
			error_log($sql);
			phpgrid_error("Error updating order status to delivered : ".$sql);
		}
		
		$sql2="UPDATE shop_order SET  `delivery_datetiem` = NOW() WHERE  shop_order.id =".$data["id"];
		if (!mysql_query($sql2)) { 
			error_log($sql2);
			phpgrid_error("Error updating order delivery datetime : ".$sql);
		}
			
	}else{
		
		$sql="UPDATE shop_order SET  `status` = '".$data["params"]["status"]."' WHERE  shop_order.id =".$data["id"];
		if (!mysql_query($sql)) { 
			error_log($sql); 
			phpgrid_error("Error updating order status to delivered : ".$sql);
		}
		
		$sql2="UPDATE shop_order SET  `delivery_datetiem` = NULL WHERE  shop_order.id =".$data["id"];
		if (!mysql_query($sql2)) { 
			error_log($sql2);
			phpgrid_error("Error updating order delivery datetime : ".$sql);
		}
	}
	
	/*update all items status of this order in "order_line" table with the order status in case of fully-paid status*/
	if($data["params"]["status"]=="Fully-Paid"){
		$sql="UPDATE shop_order_line SET  `product_status` = '".$data["params"]["status"]."' WHERE `shop_order_id`=".$data["id"];
		if (!mysql_query($sql)) { 
			error_log($sql);
			phpgrid_error("Error updating products status : ".$sql);
		}else{
			
		}
	}else{
		$sql="UPDATE shop_order_line SET  `product_status` = '".$data["params"]["status"]."' WHERE `shop_order_id`=".$data["id"];
		if (!mysql_query($sql)) { 
			error_log($sql);
			phpgrid_error("Error updating products status : ".$sql);
		}else{
			
		}
	}
	
}

function insertRowInG1($data) {
}

function afterInsertRowInG1($data) {
}

function deleteRowInG1($data) {
}


$events1 = array(on_insert => array("insertRowInG1", null, false), on_after_insert => array("afterInsertRowInG1", null, true), on_update => array("updateRowInG1", null, false), on_delete => array("deleteRowInG1", null, false));


////////////////table 2////////
$g2 = new jqgrid();

$table2 = "shop_order_line";

$id = intval($_GET["rowid"]);

$select2 = "SELECT id AS id, shop_order_id , product_name, price, qnty, qnty_price, handling_cost, product_status, coms_paid, com_paid_datetime, p_cost, p_profit, p_l0_com, p_l1_com, p_l2_com, p_l3_com, p_l4_com, p_l5_com, p_l6_com, p_l7_com, p_l8_com, p_l9_com, p_l10_com, p_l11_com, p_l12_com, p_l13_com, p_l14_com, p_l15_com, p_l16_com, p_l17_com, p_l18_com, p_l19_com, p_l20_com, p_l21_com, direct_com, bonus, p_Z0_com, p_Z1_com, product_id from ".$table2." where shop_order_id = ".$id;

$actions2 = getDefaultActions();
$options2 = getDefaultOptions("Order Items Report ");
$conditions2 = getDefaultConditions();
$columns2 = array();

$new_column = newIdColumn();
$columns2[] = $new_column;

$new_column = newTextColumn("OrderID", "shop_order_id");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("ProductID", "product_id");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("ProductName", "product_name");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Product Cost", "p_cost");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Product Profit", "p_profit");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Handling Cost", "handling_cost");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$select_values1 = "New:New;InPreparation:InPreparation;Shipping:Shipping;Delivered:Delivered;Postponed:Postponed;Returned:Returned;Fully-Paid:Fully-Paid";
//$new_column = newCustomSelectColumnWithFormatter("Status", "product_status", "product_status", $select_values1);
//$new_column = newCustomSelectColumnWithFormatter("Status", "product_status", "product_status", "SELECT `status` FROM `shippingstatus`");
$new_column =newMandatorySelectColumnX("Status", "product_status", "status", "SELECT `status` FROM `shippingstatus`");
$new_column["editable"] =true;
$columns2[] = $new_column;


$new_column = newTextColumn("Unit Price", "price");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Product Quantity", "qnty");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Total Price", "qnty_price");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$select_values2 = "1:Yes;0:No";
$new_column = newCustomSelectColumnWithFormatter("Is Product Commission Paid", "coms_paid", "coms_paid", $select_values2);
$new_column["width"] = 100;
$new_column['editable'] = false;
$columns2[] = $new_column;

$new_column = newTextColumn("Commission Paid Date-Time", "com_paid_datetime");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Direct Commission", "direct_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Bonus", "bonus");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Bonus", "bonus");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Cashback", "p_l0_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level1 Comm", "p_l1_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level2 Comm", "p_l2_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level3 Comm", "p_l3_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level4 Comm", "p_l4_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level5 Comm", "p_l5_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level6 Comm", "p_l6_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level7 Comm", "p_l7_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level8 Comm", "p_l8_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level9 Comm", "p_l9_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level10 Comm", "p_l10_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level11 Comm", "p_l11_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level12 Comm", "p_l12_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level13 Comm", "p_l13_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level14 Comm", "p_l14_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level15 Comm", "p_l15_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level16 Comm", "p_l16_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level16 Comm", "p_l16_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level17 Comm", "p_l17_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level18 Comm", "p_l18_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level19 Comm", "p_l19_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level20 Comm", "p_l20_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("Level21 Comm", "p_l21_com");
$new_column["editable"] = "false";
$columns2[] = $new_column;




/*$events2 = getDefaultEvents("2");*/


function insertRowInG2($data) {
}

function afterInsertRowInG2($data) {
}

function updateRowInG2($data) {
	///update the order shopline status
	$sql="UPDATE shop_order_line SET  product_status = '".$data["params"]["product_status"]."' WHERE id =".$data["id"];
		if (!mysql_query($sql)) { 
		error_log($sql); 
		phpgrid_error($sql." : ");
		
		}else{/*if updated successfully check if all products are fully paid then update the order status*/
				/*check if all products of this order has the fully paid status , update the order status to fully update*/
		
				/*get all the products of the order and check fullypaid status of each*/
				$isAllProductsFullyPaid=true;
				
				$sql="SELECT `product_status` FROM `shop_order_line` WHERE `shop_order_id`=".intval($_GET["rowid"]);
				$result_1=mysql_query($sql);
				if (!$result_1) { 
					error_log($sql); 
					phpgrid_error("error in selecting all products in order : ".$sql);
				}else{/*if selectin order products query is ok */
					while($row_1= mysql_fetch_array($result_1)){
						$Product_status=$row_1["product_status"];
						if($Product_status!="Fully-Paid"){
							$isAllProductsFullyPaid=false;
						}
					}
					//phpgrid_error("is all products fully paid : ".$isAllProductsFullyPaid); 
					if($isAllProductsFullyPaid){
						$sql="UPDATE shop_order SET  `status` = 'Fully-Paid' WHERE  shop_order.id =".intval($_GET["rowid"]);
						if (!mysql_query($sql)) { 
							error_log($sql);
							phpgrid_error("error in updating order status : ".$sql);
						}else{
							phpgrid_error("Order Status Updated to full ".$isAllProductsFullyPaid);
						}
				}
				
				
			}
		}
	

}

function afterUpdateRowInG2($data) {
	
}

function deleteRowInG2($data) {
}

////////////////

$events2 = array(on_insert => array("insertRowInG2", null, false), on_after_insert => array("afterInsertRowInG2", null, true), on_update => array("updateRowInG2", null, false), on_delete => array("deleteRowInG2", null, false), on_after_update => array("afterUpdateRowInG2", null, false));

displayTwoGrids($g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, 
        $g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2);

//displayGrid($g1,$table, $select1, $columns1, $options1, $actions1, $events1, $conditions1);
?>