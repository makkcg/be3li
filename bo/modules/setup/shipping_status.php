<?php

$g = new jqgrid();

$table = "shippingstatus";

$select = "SELECT `id`, `status` FROM `shippingstatus` WHERE 1";


$actions = array("add" => true, "edit" => true, "delete" => true, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->shipping_status);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;


$new_column = newMandatoryTextColumn("Status", "status");
$columns[] = $new_column;





$events1 = array(on_insert => array("insertRowInG", null, true), on_after_insert => array("afterInsertRowInG",
        null, true), on_update => array("updateRowInG", null, true), on_delete => array("deleteRowInG", null, true));

function insertRowInG($data) {
 
}

function afterInsertRowInG($data) {
	

}

function updateRowInG($data) {
}

function afterUpdateRowInG($data) {
}

function deleteRowInG($data) {

}

displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>
