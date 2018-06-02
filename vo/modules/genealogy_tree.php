<?php;$core->verifyiraccount("genealogy_tree");
$core->checkEwalletPassword("genealogy_tree");

$html_page->writeHeader();
$html_page->writeBody("My Network",$core->getewalletval($database_manager,$_SESSION['ir_id']));
?>
<script>
$("#error").hide()
////search IR function
    function validateForm()
    {
        var y = document.forms["myform"]["ir_id"].value;
        if (y == '') {
			$("#error").show()
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        if (y.split('').length !== 8) {
			$("#error").show()
            document.getElementById("error").innerHTML = "Invalid IR ID.";
            return false;
        }
		$("#error").hide()
        return true;
    }
</script>

<!---search IR form--->
<form method="get" class="col-xs-12 col-md-12 col-lg-12" id="searchIR_network_form" name="myform" onsubmit="return validateForm();" >

    <label class="col-xs-3 col-md-3 col-lg-3">IR ID: <span class="astrisk"> *</span></label> 
    <input type="hidden" name='page'  value='genealogy_tree'>
    <input class="col-xs-6 col-md-6 col-lg-6" id="searchIR_inp" name="ir_id" type="text" value="<?php
if (isset($_GET['ir_id'])) {
    echo $_GET['ir_id'];
}
?>" />  
   
    
    <div class="col-xs-3 col-md-3 col-lg-3">
		 <button type="submit" ><i class="fa fa-check-square fa-fw"></i>Search</button>
	</div>

   

</form>
<div id='error' class="col-xs-12 col-md-12 col-lg-12"></div>

<!---search IR form--->
<div class="sep"></div>

<?php
////if ir was from search box else use the loggedin ir
if (isset($_GET['ir_id'])) {
    $top_ir_id = strtoupper($_GET['ir_id']);
} else {
    $top_ir_id = $_SESSION['ir_id'];
}
//////////////////////////////////////////new code by KCG////////////////////////////////
////check the IRID to make sure it is in the downline not upline

if ($top_ir_id == $_SESSION['ir_id'] || $core->isBinaryIRChild($database_manager, $_SESSION['ir_id'], $top_ir_id)) {
	
	echo '<div class="center genealogy">';
	$core->drawBUs_new($database_manager, $html_page, $top_ir_id, "");
    //$core->drawBUs($database_manager, $html_page, $top_ir_id, "first_genealogy_ir");
    //echo '<div class="clear"></div>';
    //$core->drawBUs($database_manager, $html_page, $core->getIRID($core->getLeftChildBUID($database_manager, $top_ir_id . "-002")));
    //$core->drawBUs($database_manager, $html_page, $core->getIRID($core->getRightChildBUID($database_manager, $top_ir_id . "-002")));
    //$core->drawBUs($database_manager, $html_page, $core->getIRID($core->getLeftChildBUID($database_manager, $top_ir_id . "-003")));
   // $core->drawBUs($database_manager, $html_page, $core->getIRID($core->getRightChildBUID($database_manager, $top_ir_id . "-003")));
    echo '<div class="clear"></div>';
    ?>
    </div>
    <div class='sep dotted'></div>
   <!-- <div class="halfwidth left">
        <p>
            You can click on any of your referrals to check their Genealogy Tree.
        </p>

        <p>
            <i class="fa fa-circle fa-fw" style="color: #78539c;"></i>Qualified  Business Unit
        </p>
        <p>
            <i class="fa fa-circle fa-fw" style="color: #d79928;"></i>Qualified Retail Shop
        </p>
        <p>
            <i class="fa fa-circle fa-fw" style="color: #555;"></i>Not Qualified
        </p>
    </div>
    <div class="halfwidth right">
        <br/>
        <img src="images/Geneology.png" align="right">
    </div>-->
    <?php
} else {/////if the entered IR is in the upline of the loggedin IR
	echo '<p style="text-align: center;font-size: 2.5em;color: #eb313e;">You do not have access to this information.</p>';
}
?>

    <br class="clear"/>


<?php $html_page->writeFooter(); ?>