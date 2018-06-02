<?php

$g = new jqgrid();

$table = "transaction";

$select = "SELECT id, date, type, ir_id, amount, balance FROM transaction";

$actions = array("add" => false, "edit" => false, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions("All Transactions Report");

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;

$new_column = newDateTimeColumn("Date", "date");
$columns[] = $new_column;

$new_column = newTextColumn("IR ID", "ir_id");
$columns[] = $new_column;

$new_column = newTextColumn("Type", "type");
$columns[] = $new_column;

$new_column = newTextColumn("Amount", "amount");
$columns[] = $new_column;

$new_column = newTextColumn("Balance", "balance");
$columns[] = $new_column;


$events = array();

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