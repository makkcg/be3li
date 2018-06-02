<?php

$g = new jqgrid();

$table = "categories";

$select = "SELECT `id`,`title`,`desc`,`artitle`,`ardesc`,`img`,`is_enabled` FROM `categories` WHERE 1 ";
//$select   = " response, IF(attachment = '','',CONCAT('<a target=\'_blank\' href=\'../vo/', attachment, '\'>Download</a>'))  AS attachment_link FROM Support ";

$actions = array("add" => true, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->categories);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("Category Title", "title");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Description", "desc");
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("Category Title Ar", "artitle");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Description", "ardesc");
$columns[] = $new_column;

addMandatoryUploadImageColumns($columns, "Category Img", "img", "img","categories");


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
    defaultImageUploadUpdateEventHandler($data["params"]["img"],+ $data["id"], "categories", "img");
}

function afterUpdateRowInG($data) {
   
}

function deleteRowInG($data) {
    defaultImageUploadDeleteEventHandler($data["id"], "categories", "img");
}

displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>
