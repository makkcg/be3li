<?php

$g = new jqgrid();

$table = "vendors";

$select = "SELECT `id`,`vendor_name`,`company`,`address`,`phone_1`,`phone_2`,`phone_3`,`email`,`website` FROM `vendors` WHERE 1";


$actions = array("add" => true, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->vendors);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;


$new_column = newMandatoryTextColumn("Name", "vendor_name");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Vendor Company", "company");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Address", "address");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Phone 1", "phone_1");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Phone 2", "phone_2");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Phone 3", "phone_3");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Email", "email");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Website", "website");
$columns[] = $new_column;




$events1 = array(on_insert => array("insertRowInG", null, true), on_after_insert => array("afterInsertRowInG",
        null, true), on_update => array("updateRowInG", null, true), on_delete => array("deleteRowInG", null, true));

function insertRowInG($data) {
 //   defaultImageUploadAddEventHandler($data["params"]["img"]);
//	$sumofall=$data["params"]["p_cost"]+$data["params"]["p_profit"]+$data["params"]["p_l0_com"]+$data["params"]["p_l1_com"]+$data["params"]["p_l2_com"];
//    if($data["params"]["price"]!=$sumofall){
//		$data["params"]["price"]=$sumofall;
//	}
}

function afterInsertRowInG($data) {
	

}

function updateRowInG($data) {
// defaultImageUploadUpdateEventHandler($data["params"]["img"],+ $data["id"], "product", "img");
	
	//include 'products/rescrop.php?filename='.$data["params"]["img"].'&secret=12345678';///image resize, crop, watermarking, class
}

function afterUpdateRowInG($data) {
   // include 'products/rescrop.php?filename='.$data["params"]["img"].'&secret=12345678';///image resize, crop, watermarking, class
	//exec('php ../products/rescrop.php?filename='.$data["params"]["img"].'&secret=123456');
}

function deleteRowInG($data) {
 //   defaultImageUploadDeleteEventHandler($data["id"], "product", "img");
}

displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>
