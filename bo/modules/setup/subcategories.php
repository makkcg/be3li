<?php

$g = new jqgrid();

$table = "subcategories";

$select = "SELECT `id`,`title`,`desc`,`artitle`,`ardesc`,`img`,`is_enabled`,`catid` FROM `subcategories` WHERE 1";

$actions = array("add" => true, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->subcategories);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("SubCategory Title", "title");
$columns[] = $new_column;

$custom_sql = "SELECT id as k, title as v from categories where is_enabled=1";
$data_table="categories";
$new_column = newMandatorySelectColumn("Category", "catid", "catid", $data_table, $g, $custom_sql);
$columns[] = $new_column;

$new_column = newTextAreaColumn("Description", "desc");
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("SubCategory Title Ar", "artitle");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Description", "ardesc");
$columns[] = $new_column;

addMandatoryUploadImageColumns($columns, "SubCategory Img", "img", "img","categories");


$new_column = newMandatoryCheckColumn("Is Active", "is_enabled");
$columns[] = $new_column;

$events1 = array(on_insert => array("insertRowInG", null, true), on_after_insert => array("afterInsertRowInG",
        null, true), on_update => array("updateRowInG", null, true), on_delete => array("deleteRowInG", null, true));

function insertRowInG($data) {
    defaultImageUploadAddEventHandler($data["params"]["img"]);
}

function afterInsertRowInG($data) {
	

}

function updateRowInG($data) {
    defaultImageUploadUpdateEventHandler($data["params"]["img"],+ $data["id"], "subcategories", "img");
}

function afterUpdateRowInG($data) {
   
}

function deleteRowInG($data) {
    defaultImageUploadDeleteEventHandler($data["id"], "subcategories", "img");
}

displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>
