<div id="footer">
    <p class="left"><?php echo $_SESSION['main_language']->all_rights_reserved; ?> - 
        <a target="_blank" href="http://<?php echo $_SESSION["top_organization_website"]; ?>">
            <?php echo $_SESSION["top_organization_name"]; ?></a>
        – <?php echo $_SESSION['main_language']->c_r; ?> <?php echo $_SESSION["top_organization_cr"]; ?>
        – <?php echo $_SESSION["main_branch_country"]; ?></p>
    <p class="right">
        <?php echo $_SESSION["top_organization_name"]; ?> – <?php echo $_SESSION['main_language']->erp_system; ?>
        – <?php echo $_SESSION['main_language']->powered_by; ?> 
        <a target="_blank" href="http://khalifacomputergroup.com/"><?php echo $_SESSION['main_language']->kcg_software; ?></a>
    </p>
</div>
<script>
$(document).ready(function(){
	$('document').on('change','#p_cost , #p_profit, #p_l0_com, #p_l1_com, #p_l2_com', function(){
		var sumall=parseInt($('#p_cost').val())+parseInt($('#p_profit').val())+parseInt($('#p_l0_com').val())+parseInt($('#p_l1_com').val())+parseInt($('#p_l2_com').val())
		$('#price').text(sumall);
	})
});
</script>
</body>
</html>