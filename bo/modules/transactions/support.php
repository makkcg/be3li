<?php

$g = new jqgrid();

$table = "support";

$select = "SELECT id, datetime, severity, type, message, status, "
        . " response, IF(attachment = '','',CONCAT('<a target=\'_blank\' href=\'../vo/', attachment, '\'>Download</a>'))  AS attachment_link FROM support ";

$actions = array("add" => false, "edit" => edit, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->complaints);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;

$new_column = newDateTimeColumn("Date / Time", "datetime");
$new_column['editable'] = false;
$columns[] = $new_column;

$new_column = newTextColumn("Severity", "severity");
$new_column['editable'] = false;
$columns[] = $new_column;

$new_column = newTextColumn("Type", "type");
$new_column['editable'] = false;
$columns[] = $new_column;

$new_column = newTextAreaColumn("Message", "message");
$new_column['editable'] = false;
$columns[] = $new_column;

$new_column = newTextColumn("Attachment", "attachment_link");
$new_column['editable'] = false;
$columns[] = $new_column;

$select_values = "New:New;Resolved:Resolved";
$new_column = newCustomSelectColumnWithFormatter("Status", "status", "status", $select_values);
$columns[] = $new_column;

$new_column = newTextAreaColumn("Response", "response");
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