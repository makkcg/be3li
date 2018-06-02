<?php
$curentdate=getFormatedDate();
$g = new jqgrid();

$table = "Suppliers_Orders";///table name in db

if($_GET['period']=="today"){
	$select = "SELECT product.id, shop_order.datetime, shop_order.ir_id, shop_order_line.shop_order_id,  shop_order_line.product_name, product.top_category, product.category, COUNT( * ) as qnty 
	FROM shop_order, shop_order_line, product
	WHERE shop_order_line.shop_order_id = shop_order.id
	AND shop_order_line.product_id = product.id
	AND shop_order.datetime >0
	AND date(shop_order.datetime)='".$curentdate."'
	GROUP BY shop_order_line.product_name";
}else if($_GET['period']=="all"){
	$select = "SELECT product.id, shop_order.datetime, shop_order.ir_id, shop_order_line.shop_order_id,  shop_order_line.product_name, product.top_category, product.category, COUNT( * ) as qnty 
	FROM shop_order, shop_order_line, product
	WHERE shop_order_line.shop_order_id = shop_order.id
	AND shop_order.datetime >0
	AND shop_order_line.product_id = product.id
	GROUP BY shop_order_line.product_name";
}else if(!isset($_GET['period'])){
	$select = "SELECT product.id, shop_order.datetime, shop_order.ir_id, shop_order_line.shop_order_id,  shop_order_line.product_name, product.top_category, product.category, COUNT( * ) as qnty 
	FROM shop_order, shop_order_line, product
	WHERE shop_order_line.shop_order_id = shop_order.id
	AND shop_order_line.product_id = product.id
	AND shop_order.datetime >0
	AND date(shop_order.datetime)='".$curentdate."'
	GROUP BY shop_order_line.product_name";
}

//$select .="WHERE date(shop_order.datetime)='2016-05-24'";


$actions = array("add" => false, "edit" => false, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => true, "export_csv" => $export_enabled);

$options = getDefaultOptions("Suppliers Orders Report".getFormatedDateTime());///export file name

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn("Product ID", "id");
$columns[] = $new_column;

$new_column = newTextColumn("Order ID", "shop_order_line.shop_order_id");
$columns[] = $new_column;

$new_column = newDateTimeColumn("Order Date", "datetime");
$columns[] = $new_column;

//$new_column = newDateTimeColumn("To Date", "datetime");
//$columns[] = $new_column;

//$new_column = newTextColumn("IR ID", "irid");
//$columns[] = $new_column;

$new_column = newTextColumn("Product Name", "product_name");
$columns[] = $new_column;

$new_column = newTextColumn("Quantity", "qnty");
$columns[] = $new_column;

$new_column = newTextColumn("Category", "top_category");
$columns[] = $new_column;

$new_column = newTextColumn("Sub Category", "category");
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
$('.user_info').after('<div style=\"float:left;width:100%\"><button type=\"button\" class=\"ok\"><a href=\"index.php?page=304&period=today\" >Today<a/></button><button type=\"button\" class=\"ok\"><a href=\"index.php?page=304&period=all\" >All<a/></button></br></br><div class=\"sep\"></div></div>')
	</script>
    ";



displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
echo $script;
//initializejsgridfilterfield_value("gbox_list", "gs_datetime",$curentdate);
?>