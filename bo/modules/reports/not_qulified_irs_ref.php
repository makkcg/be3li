<?php
///report to show not qualified IRs (Registered) and their refferals contact for verification by proshops

$curentdate=getFormatedDate();
$g = new jqgrid();
$select = "select * from (SELECT ir.id, ir.ir_id, CONCAT( ir.f_name,  ', ', ir.l_name ) AS notq_ir_name, ir.a_name AS notq_ir_aname, ir.mobile, ir.phone, ir.registration_date, SUBSTRING( bu.referral_bu_id, 1, CHAR_LENGTH( bu.referral_bu_id ) -4 ) AS referral_ir, CONCAT( refir.f_name,  ', ', refir.l_name ) AS ref_ir_name, refir.a_name AS ref_ir_aname, refir.mobile AS ref_ir_mob, refir.phone AS ref_ir_phone 
FROM ir
INNER JOIN bu ON ir.ir_id = bu.ir_id
INNER JOIN ir refir ON refir.ir_id = SUBSTRING( bu.referral_bu_id, 1, CHAR_LENGTH( bu.referral_bu_id ) -4 ) 
WHERE ir.qualification_date IS NULL 
AND bu.code ='002' ) ir_ref_data where 1 ";
//$table = "ir";///table name in db

if($_GET['qulifytype']=="notqualified"){
	$select = "select * from (SELECT ir.id, ir.ir_id, CONCAT( ir.f_name,  ', ', ir.l_name ) AS notq_ir_name, ir.a_name AS notq_ir_aname, ir.mobile, ir.phone, ir.registration_date, SUBSTRING( bu.referral_bu_id, 1, CHAR_LENGTH( bu.referral_bu_id ) -4 ) AS referral_ir, CONCAT( refir.f_name,  ', ', refir.l_name ) AS ref_ir_name, refir.a_name AS ref_ir_aname, refir.mobile AS ref_ir_mob, refir.phone AS ref_ir_phone 
FROM ir
INNER JOIN bu ON ir.ir_id = bu.ir_id
INNER JOIN ir refir ON refir.ir_id = SUBSTRING( bu.referral_bu_id, 1, CHAR_LENGTH( bu.referral_bu_id ) -4 ) 
WHERE ir.qualification_date IS NULL 
AND bu.code ='002' ) ir_ref_data where 1 ";
}else if($_GET['qulifytype']=="qualified"){
	$select = "";
}else if(!isset($_GET['period'])){

}

//$select .="WHERE date(shop_order.datetime)='2016-05-24'";


$actions = array("add" => false, "edit" => false, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => true, "export_csv" => $export_enabled);

$options = getDefaultOptions("Suppliers Orders Report".getFormatedDateTime());///export file name

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn("ID", "ir.id");
$columns[] = $new_column;

$new_column = newTextColumn("IR ID", "ir.ir_id");
$columns[] = $new_column;

$new_column = newTextColumn("IR Name", "ir_ref_data.notq_ir_name");
$columns[] = $new_column;

$new_column = newTextColumn("IR Arabic", "ir_ref_data.notq_ir_aname");
$columns[] = $new_column;

$new_column = newTextColumn("IR Mobile", "ir.mobile");
$columns[] = $new_column;

$new_column = newTextColumn("IR Phone", "ir.phone");
$columns[] = $new_column;

$new_column = newDateTimeColumn("IR Reg. Date", "ir.registration_date");
$columns[] = $new_column;

//$new_column = newDateTimeColumn("To Date", "datetime");
//$columns[] = $new_column;

//$new_column = newTextColumn("IR ID", "irid");
//$columns[] = $new_column;

$new_column = newTextColumn("Ref. IR", "ir_ref_data.referral_ir");
$columns[] = $new_column;

$new_column = newTextColumn("Ref. Name", "ir_ref_data.ref_ir_name");
$columns[] = $new_column;

$new_column = newTextColumn("Ref. Arabic", "ir_ref_data.ref_ir_aname");
$columns[] = $new_column;

$new_column = newTextColumn("Ref. Mobile", "ir_ref_data.ref_ir_mob");
$columns[] = $new_column;

$new_column = newTextColumn("Ref. Phone", "ir_ref_data.ref_ir_phone");
$columns[] = $new_column;

//$new_column = newTextColumn("Balance", "balance");
//$columns[] = $new_column;


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

$script = "
<script>
$('.user_info').after('<div style=\"float:left;width:100%\"><button type=\"button\" class=\"ok\"><a href=\"index.php?page=309&qulifytype=notqualified\" >Not Qualified IRs<a/></button><button style=\"display:none;\" type=\"button\" class=\"ok\"><a href=\"index.php?page=309&qulifytype=qualified\" >Qualified IRs<a/></button></br></br><div class=\"sep\"></div></div>')
	</script>
    ";



displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
echo $script;
//initializejsgridfilterfield_value("gbox_list", "gs_datetime",$curentdate);
?>