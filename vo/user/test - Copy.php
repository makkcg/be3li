<?php

/////calculate the downline at level 1 
////////SELECT *  FROM `bu` WHERE `ir_id` NOT LIKE 'VA1643' AND `parent_bu_id` LIKE '%VA1643%'
session_start();


$html_page->writeHeader();


?>
<!----additional html goes here--->

		
<?php $html_page->writeFooter(); ?>