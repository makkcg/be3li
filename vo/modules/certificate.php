<?php
$html_page->writeHeader();

$sql = "SELECT datetime, product_name FROM vacation_certificate "
        . " WHERE id = '" . $_GET['id'] . "'"
        . " AND ir_id = '" . $_SESSION['ir_id'] . "' ";
$result = $database_manager->query($sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    ?>
    <div id="certificate-print">
        <img class="certificate-bg" src="images/certificate-bg.jpg">
        <img class="certificate-logo" src="images/testlogo.png">
        <p class="certificate-text-small">Max Health certifies that
            <?php echo $_SESSION['full_name']; ?> 
            has purchased
        </p>
        <p class="certificate-text-big">
            <?php echo $row['product_name']; ?><br/>
        </p>
        <p class="certificate-date">Date: <?php echo date('d/m/Y', strtotime($row['datetime'])); ?></p>
        <img class="certificate-stamp" src="images/stamp.png">
    </div>
<?php } else {
    ?>
    <div id="certificate-print">
        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
        <p id="error">You don not have access to this information.</p>
    </div>
    <?php
}
?>
<style>
    #footer {
        display: none!important;
    }
</style>




<?php $html_page->writeFooter(); ?>