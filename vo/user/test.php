<?php

/////calculate the downline at level 1 
////////SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'
session_start();


$html_page->writeHeader();
///refferal ir in array
$ref_irid="BE000010";
$ir_ids_arr=array($ref_irid);
$level_counter=1;
$result=$core->getLevelX_downlineArr(3,$database_manager, $ir_ids_arr, $level_counter, $return_levels_ir_Obj);
print_r($result);
?>
<!----additional html goes here--->

		
<?php $html_page->writeFooter(); ?>