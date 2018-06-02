<?php
/************** query to add page into db************/
/*
INSERT INTO  `k8_top_page` (`id` ,`name` ,`name_tr` ,`variable` ,`url` ,`top_folder_id` ,`comments`)VALUES ('221' ,  'Manage Scretch/Credit Cards',  'Manage Scretch/Credit Cards',  'creatscrachcards',  'modules/transactions/creatscrachcards.php',  '1',  'admin to create and manage scratch/credit cards');

INSERT INTO  `k8_role_access` (`id` ,`role_id` ,`top_page_id` ,`top_role_access_type_id` ,`comments` ,`top_organization_id` ,`created` ,`created_by` ,`modified` ,`modified_by` ,`is_active`)VALUES (NULL ,  '1',  '221',  '25',  'Manage Scratch/Credit Cards',  '1',  '2017-02-01 11:00:00',  '0', NULL , NULL ,  '1');


*/


$g = new jqgrid();
$table = "scrachcards";

$select = "SELECT id, datetime, irid, cardnumber, cardpsw, cardval, status, notes "
        . " FROM scrachcards ";

$actions = array("add" => true, "edit" => edit, "delete" => true, "rowactions" => true,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->complaints);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;

$new_column = newDateTimeColumn("Issue Date / Time", "datetime");
$new_column['editable'] = false;
//$new_column['default'] = getFormatedDateTime();
$columns[] = $new_column;

$new_column = newTextColumn("Owner ID", "irid");
$new_column['editable'] = true;
$columns[] = $new_column;

$new_column = newTextColumn("Card Number", "cardnumber");
$new_column['editable'] = false;
//$new_column['default'] = createcardcode();
$columns[] = $new_column;

$new_column = newTextColumn("Card Password", "cardpsw");
$new_column['editable'] = false;
$new_column['editrules']['required'] = true;
$columns[] = $new_column;

$new_column = newTextColumn("Card Value", "cardval");
$new_column['editable'] = true;
$new_column['editrules']['required'] = true;
$columns[] = $new_column;

$select_values = "1:Active;0:Disabled";
$new_column = newCustomSelectColumnWithFormatter("Card Status", "status", "status", $select_values);
$columns[] = $new_column;

$new_column = newTextColumn("Card Notes", "notes");
$new_column['editable'] = true;
$columns[] = $new_column;

$events = getDefaultEvents();

function insertRowInG($data) {
	$data["params"]['cardnumber'] = createcardcode(strtoupper(randString(2)).'-');
	$data["params"]['cardpsw'] =randNumber(4);
	//defaultInsertEventHandler($data);
	$data["params"]['datetime']=getFormatedDateTime();
}

function afterInsertRowInG($data) {
	
}

function updateRowInG($data) {
}

function afterUpdateRowInG($data) {
}

function deleteRowInG($data) {
}
function createcardcode($prefix){
	$randomcardno=uniqid($prefix,false);
	return $randomcardno;
}
displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>