<?php
$core->checkEwalletPassword("vacation_certificates");

$html_page->writeHeader();
$html_page->writeBody("Vacation Certificates",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "SELECT * FROM vacation_certificate "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
        . " ORDER BY datetime DESC";
$result = $database_manager->query($sql);
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>id</th>
            <th>Date</th>
            <th>Order</th>
            <th>Vacation</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td ><?php echo $row['id']; ?></td>
                <td ><?php echo $row['datetime']; ?></td>
                <td>#<?php echo $row['shop_order_id']; ?></td>
                <td ><?php echo $row['product_name']; ?></td>
                <td ><a target="_blank" href="index.php?page=certificate&id=<?php echo $row['id']; ?>">Print</a></td>
            </tr>
            <?php }
        ?>
    </tbody>
</table>



<?php $html_page->writeFooter(); ?>