<?php

$g = new jqgrid();

$table = "stocks";

$select = "SELECT `id`, `product_id`, `vendor_id` , `adding_date` , `quantity` FROM `stocks`";


$actions = array("add" => true, "edit" => false, "delete" => true, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->stockcontrol);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;


$new_column = newMandatorySelectColumn("ProductID", "product_id", "id","name", $g, "SELECT id as k, name as v from product");
$columns[] = $new_column;

$new_column = newMandatorySelectColumn("VendorID", "vendor_id", "id","vendors", $g, "SELECT id as k, vendor_name as v from vendors");
$columns[] = $new_column;

$new_column = newTextColumn("Date", "adding_date");
$new_column["editable"] = "false";
$new_column["width"] = 150;
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("Quantity", "quantity");
$columns[] = $new_column;






$events = array(on_insert => array("insertRowInG", null, true), on_after_insert => array("afterInsertRowInG",
        null, true), on_update => array("updateRowInG", null, true), on_delete => array("deleteRowInG", null, true));

function insertRowInG($data) {
 //   defaultImageUploadAddEventHandler($data["params"]["img"]);
//	$sumofall=$data["params"]["p_cost"]+$data["params"]["p_profit"]+$data["params"]["p_l0_com"]+$data["params"]["p_l1_com"]+$data["params"]["p_l2_com"];
//    if($data["params"]["price"]!=$sumofall){
//		$data["params"]["price"]=$sumofall;
//	}

}

function afterInsertRowInG($data) {

UpdateProductStock($data["params"]["product_id"],$data["params"]["quantity"],1);

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
	UpdateProductStock($data["params"]["product_id"],$data["params"]["quantity"],0);
 //   defaultImageUploadDeleteEventHandler($data["id"], "product", "img");
}

displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>
