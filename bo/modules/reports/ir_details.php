<?php

$g1 = new jqgrid();

$table1 = "ir_details";
/*
$select1 ="SELECT mtt.id, mtt.ir_id, mtt.datetime,IF(mtt.status ='Delivered',(select 'Delivered'),DATEDIFF(now(),mtt.datetime) ) as dayssncord ,  mtt.shop_title, IF(mtt.customer_id=0,'IR Self Order',mtt.customer_id) as customer_id, mtt.name, mtt.status , IF(mtt.customer_id = 0, CONCAT(ir.address, ' , ', ir.area ,' , ',ir.city) , CONCAT(cus.address, ' , ', cus.area ,' , ',cus.city))  as address , ir.mobile, mtt.delivery_datetiem
FROM shop_order mt inner join ir
on ir.ir_id=mtt.ir_id
left join customer cus on cus.id = mtt.customer_id
WHERE mtt.datetime != '' ";
*/

$select1 =" SELECT * FROM (SELECT mtt.id, mtt.ir_id,(select bu.referral_bu_id from bu where bu.ir_id = mtt.ir_id and code='001') refiridbu, (select bu.is_qualified as isq from bu where bu.ir_id = mtt.ir_id and code='001') bu001, (select is_qualified from bu where ir_id = mtt.ir_id and code='002') bu002 , (select bu.is_qualified from bu where bu.ir_id = mtt.ir_id and code='003') bu003, mtt.title,mtt.f_name,mtt.l_name, CONCAT(mtt.title ,' - ',mtt.f_name, ' ', mtt.l_name) as name, mtt.a_name, mtt.email, mtt.mobile, mtt.phone, mtt.address, mtt.area, mtt.city, mtt.country, mtt.valid_id, mtt.valid_id_type, mtt.nationality, mtt.birth_date,CONCAT(mtt.relationship,' : ',mtt.beneficiary ) beneficiary, mtt.login_pass, mtt.ewallet_pass, mtt.ewallet, mtt.registration_date, mtt.qualification_date FROM ir mtt where 1=1) irdet where 1=1 ";
$export_enabled=true;
$actions1 = array("add" => false, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
//$select1 .= getDefaultSelectLines2();

//$actions1 = getDefaultActions();
$options1 = getDefaultOptions("IR Details Report ".getFormatedDateTime());///export file name

$conditions1 = getDefaultConditions();
$columns1 = array();

$new_column = newIdColumn();
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn("IR_ID", "ir_id");
$new_column["width"] = 100;
$new_column["editable"] = "false";
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn("ReferrarIRID-bu", "refiridbu");
$new_column["width"] = 100;
$new_column["editable"] = "false";
$columns1[] = $new_column;

$select_values = "0:No;1:Retail;2:Binary";
$new_column = newCustomSelectColumnWithFormatter("BU001 Qualified", "bu001", "bu001", $select_values);
$new_column["width"] = 100;
//$new_column["dbname"] = "bu.is_qualified";
$new_column["editable"] = "false";
$columns1[] = $new_column;

$select_values = "0:No;1:Retail;2:Binary";
$new_column = newCustomSelectColumnWithFormatter("BU002 Qualified", "bu002", "bu002", $select_values);
$new_column["width"] = 100;
//$new_column["dbname"] = "bu.is_qualified";
$new_column["editable"] = "false";
$columns1[] = $new_column;

$select_values = "0:No;1:Retail;2:Binary";
$new_column = newCustomSelectColumnWithFormatter("BU003 Qualified", "bu003", "bu003", $select_values);
$new_column["width"] = 100;
//$new_column["dbname"] = "bu.is_qualified";
$new_column["editable"] = "false";
$columns1[] = $new_column;

$new_column =newCustomTextColumn("Full Name", "name", "name");
//$new_column = newTextColumn("Full Name", "name");
$new_column["editable"] = "false";
$new_column["width"] = 200;
$columns1[] = $new_column;

$new_column = newTextColumn("Arabic Name", "a_name");
$new_column["editable"] = "false";
$new_column["width"] = 200;
$columns1[] = $new_column;

$new_column = newTextColumn("Email", "email");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Phone", "phone");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Mobile", "mobile");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Address", "address");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Area", "area");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("City", "city");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Country", "country");
$new_column["editable"] = "false";
$new_column["width"] = 100;
$columns1[] = $new_column;

$new_column = newTextColumn("Registration Date", "registration_date");
$new_column["editable"] = "false";
$new_column["width"] = 200;
$columns1[] = $new_column;

$new_column = newTextColumn("Qualification Date", "qualification_date");
$new_column["editable"] = "false";
$new_column["width"] = 200;
$columns1[] = $new_column;


$events1 = array(on_insert => array("insertRowInG1", null, false), on_after_insert => array("afterInsertRowInG1", null, true), on_update => array("updateRowInG1", null, false), on_delete => array("deleteRowInG1", null, false));

function updateRowInG1($data) {
	
}

function insertRowInG1($data) {
}

function afterInsertRowInG1($data) {
}

function deleteRowInG1($data) {
}
////////////////table 2////////
$g2 = new jqgrid();

$table2 = "shop_order_line";

$id = intval($_GET["rowid"]);

$select2 = "SELECT id AS id, shop_order_id , product_name, price, product_id from ".$table2." where shop_order_id = ".$id;

$actions2 = getDefaultActions();
$options2 = getDefaultOptions("Order Items Report ");
$conditions2 = getDefaultConditions();
$columns2 = array();

$new_column = newIdColumn();
$columns2[] = $new_column;

$new_column = newTextColumn("OrderID", "shop_order_id");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("ProductName", "product_name");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("ProductID", "product_id");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$new_column = newTextColumn("ProductPrice", "price (EC)");
$new_column["editable"] = "false";
$columns2[] = $new_column;

$events2 = getDefaultEvents("2");

function insertRowInG2($data) {
}

function afterInsertRowInG2($data) {
}

function updateRowInG2($data) {
}

function afterUpdateRowInG2($data) {
}

function deleteRowInG2($data) {
}

////////////////

//displayTwoGrids($g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, $g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2);

displayGrid($g1,$table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1);
?>