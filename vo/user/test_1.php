<?php

/////calculate the downline at level 1 
////////SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'
session_start();


$html_page->writeHeader();

?>
<!----additional html goes here--->
<div class="row" style="background:white;font-size:2em;">
	<div class="col-xs-12 col-md-12 col-lg-12" >
	<h1>Testing New Core function to get downlines at levelX, or to the maximum depth where there is no more irs in the levelx</h1>
	<br>
	<?php
	///refferal ir in array
$ref_irid="BE000010";
$bu_arr=array("004","005","006","007");
$ir_ids_arr=array($ref_irid);
$level_counter=1;
$result=$core->getLevelX_downlineArr(50,$database_manager, $ir_ids_arr, $level_counter, $return_levels_ir_Obj);
var_dump($result);
$maxlevel=sizeof($result);
//echo "all downline levels ".$maxlevel;

for($level=0;$level<$maxlevel;$level++){
	
	for($ii=0;$ii<sizeof($result[$level]);$ii++){
		echo "<br> level".($level+1)." ".$result[$level][$ii];
	}
}
echo "<br>-------------------NEW core function to find Empty Slot---------------<br>";
$uplineObj=$core->findFreeSlotinDownlinelevels($database_manager,$ref_irid,$bu_arr,$level);
//var_dump($uplineObj);
$upline_id=$uplineObj["upline"]["irid"];
$upline_bu=$uplineObj["upline"]["bu"];
$upline_position=$uplineObj["upline"]["position"];
$upline_irbu_level=$uplineObj["level"];
$upline_bu_id=$upline_id . "-" . $upline_bu;
echo "<br> auto upline ir ".$upline_id;
echo "<br> auto upline bu ".$upline_bu;
echo "<br> auto upline position ".$upline_position;
echo "<br> auto upline  Level ".$upline_irbu_level;
echo "<br> auto upline ir - bu ".$upline_bu_id;

				
			
echo "<br>-----------------------------TESTIN last outer leg upline -----------------------------------<br>";
$ref_irid="BE000010";
$business_unit_ref="007";
$position="left";
$upline_bu_id = "";
$upline_bu_id = $core->calculateUpline($database_manager, $position, $ref_irid, $business_unit_ref);
echo  $upline_bu_id;
	?>
	</div>
</div>
		
<?php $html_page->writeFooter(); ?>