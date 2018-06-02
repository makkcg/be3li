<?php

$g = new jqgrid();

$table = "product";

$select = "SELECT id, top_category, category, catid, subcatid, name, img,img2, price, handling, dcpts, rpts, is_vacation, is_enabled,`vendor`, `recommended`, `new_product`, `hot_offer`,`old_price`, `sold_out`, in_qualify, p_cost, p_profit, p_l0_com, p_l1_com, p_l2_com , p_l3_com, p_l4_com, p_l5_com , p_l6_com, p_l7_com, p_l8_com, p_l9_com, p_l10_com, p_l11_com, p_l12_com, p_l13_com, p_l14_com, p_l15_com, p_l16_com, p_l17_com, p_l18_com, p_l19_com, p_l20_com, p_l21_com, direct_com,bonus,p_Z0_com,p_Z1_com , stock FROM product where 1 ";


$actions = array("add" => true, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);

$options = getDefaultOptions($_SESSION['main_language']->products);

$conditions = getDefaultConditions();

$columns = array();

$new_column = newIdColumn();
$columns[] = $new_column;

$custom_sql = "SELECT id as k, title as v from categories where is_enabled=1 ";
$data_table="categories";
$new_column = newMandatorySelectColumn("CategoryID", "catid", "catid", $data_table, $g, $custom_sql);
$columns[] = $new_column;

$custom_sql1 = "SELECT subcat.id as k, CONCAT(cat.title,'->' ,subcat.title) as v from subcategories subcat LEFT OUTER JOIN categories cat ON cat.id = subcat.catid where subcat.is_enabled=1";
$data_table1="subcategories";
$new_column = newMandatorySelectColumn("Sub CategoryID", "subcatid", "subcatid", $data_table1, $g, $custom_sql1);
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("Name", "name");
$columns[] = $new_column;

addMandatoryUploadImageColumns($columns, "Product Img", "img", "img","products");

$new_column = newMandatoryTextColumn("Product Cost", "p_cost");
$columns[] = $new_column;

$new_column = newMandatoryTextColumn("Product Profit", "p_profit");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Direct Commission", "direct_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Bonus", "bonus");
$columns[] = $new_column;


$new_column = newMandatoryTextColumn("Shipping", "handling");
$columns[] = $new_column;

$sumofall=$data["params"]["p_cost"]+$data["params"]["p_profit"]+$data["params"]["p_l0_com"]+$data["params"]["p_l1_com"]+$data["params"]["p_l2_com"];
    
$new_column = newMandatoryTextColumn("Price", "price",$sumofall);
$columns[] = $new_column;


$new_column = newMandatoryCheckColumn("Is Active", "is_enabled");
$columns[] = $new_column;

$new_column = newMandatorySelectColumn("VendorID", "vendor", "id","vendors", $g, "SELECT id as k, vendor_name as v from vendors");
$columns[] = $new_column;

$new_column = newMandatoryCheckColumn("Rrecommended", "recommended");
$columns[] = $new_column;

$new_column = newMandatoryCheckColumn("New product", "new_product");
$columns[] = $new_column;

$new_column = newMandatoryCheckColumn("Hot Offer", "hot_offer");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Old Price", "old_price");
$columns[] = $new_column;

$new_column = newMandatoryCheckColumn("Sold Out", "sold_out");
$columns[] = $new_column;

$new_column = newTextColumn("Stock", "stock");
$new_column["editable"] = "false";
$columns[] = $new_column;

$new_column = newMandatoryCheckColumnDefaultNo("Is Binary Product", "in_qualify");
$columns[] = $new_column;

addMandatoryUploadImageColumns($columns, "image2", "img2", "img2" ,"products/img2");

$new_column = newTextAreaColumn("Cash Back", "p_l0_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 1 Commission", "p_l1_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 2 Commission", "p_l2_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 3 Commission", "p_l3_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 4 Commission", "p_l4_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 5 Commission", "p_l5_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 6 Commission", "p_l6_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 7 Commission", "p_l7_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 8 Commission", "p_l8_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 9 Commission", "p_l9_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 10 Commission", "p_l10_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 11 Commission", "p_l11_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 12 Commission", "p_l12_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 13 Commission", "p_l13_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 14 Commission", "p_l14_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Level 15 Commission", "p_l15_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 16 Commission", "p_l16_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 17 Commission", "p_l17_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 18 Commission", "p_l18_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 19 Commission", "p_l19_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 20 Commission", "p_l20_com");
$columns[] = $new_column;
$new_column = newTextAreaColumn("Level 21 Commission", "p_l21_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Z_0 parameter", "p_Z0_com");
$columns[] = $new_column;

$new_column = newTextAreaColumn("Z_1 parameter", "p_Z1_com");
$columns[] = $new_column;



$events1 = array(on_insert => array("insertRowInG", null, true), on_after_insert => array("afterInsertRowInG",
        null, true), on_update => array("updateRowInG", null, true), on_delete => array("deleteRowInG", null, true));

function insertRowInG($data) {
    defaultImageUploadAddEventHandler($data["params"]["img"]);
	$sumofall=$data["params"]["p_cost"]+$data["params"]["p_profit"]+$data["params"]["p_l0_com"]+$data["params"]["p_l1_com"]+$data["params"]["p_l2_com"];
    if($data["params"]["price"]!=$sumofall){
		$data["params"]["price"]=$sumofall;
	}
}

function afterInsertRowInG($data) {
	

}

function updateRowInG($data) {
    defaultImageUploadUpdateEventHandler($data["params"]["img"],+ $data["id"], "product", "img");
	
	//include 'products/rescrop.php?filename='.$data["params"]["img"].'&secret=12345678';///image resize, crop, watermarking, class
}

function afterUpdateRowInG($data) {
   // include 'products/rescrop.php?filename='.$data["params"]["img"].'&secret=12345678';///image resize, crop, watermarking, class
	//exec('php ../products/rescrop.php?filename='.$data["params"]["img"].'&secret=123456');
}

function deleteRowInG($data) {
    defaultImageUploadDeleteEventHandler($data["id"], "product", "img");
}

displayGrid($g, $table, $select, $columns, $options, $actions, $events, $conditions);
?>
