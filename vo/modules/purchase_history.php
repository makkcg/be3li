<?php
$core->checkEwalletPassword("purchase_history");

$html_page->writeHeader();
$html_page->writeBody("Purchase History",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "SELECT datetime, id, ewallet_amount, rpts_amount, shop_title FROM shop_order "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
        . " AND shop_title != 'Redeem Shop' "		. " AND name != '' "		. " AND email != '' "		. " AND shop_title != '' "
        . " ORDER BY datetime DESC ";
$result = $core->paginationBeforeTable($database_manager, "purchase_history", $sql);
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Order</th>
            <th>Amount</th>

        </tr>
    </thead>
    <tbody>
        
<?php
while ($row = mysqli_fetch_assoc($result)) {
    ?>
            <tr>
                <td><?php echo $row['datetime']; ?></td>
                <td><a href="index.php?page=order&id=<?php echo $row['id']; ?>">#<?php echo $row['id']; ?></a></td>
                <td><?php echo $row['ewallet_amount']; ?></td>
            </tr>
    <?php
}
?>
    </tbody>
</table>


<?php $html_page->writeFooter(); ?>