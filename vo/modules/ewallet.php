<?php;$core->verifyiraccount("ewallet");
$core->checkEwalletPassword("ewallet");
$html_page->writeHeader();
$html_page->writeBody("E-Wallet",$core->getewalletval($database_manager,$_SESSION['ir_id']));

$sql = "Select ewallet FROM ir "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
$result = $database_manager->query($sql);
?>


<table class="table table-striped">
    <thead>
        <tr>
            <th>Available Fund</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $row['ewallet']; ?> LE</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<h3>Transaction History</h3>

<?php

$sql = "SELECT * FROM transaction "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
        . " AND type != 'Redeem'  "
        . " ORDER BY id DESC ";
$result = $core->paginationBeforeTable($database_manager, "ewallet", $sql);

?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date & Time</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>From/To</th>

        </tr>
    </thead>
    <tbody>
<?php
while (($row = mysqli_fetch_assoc($result))) {
    ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['type']; ?></td>
                <td><?php echo $row['amount']; ?> LE</td>
                <td><?php echo $row['balance']; ?> LE</td>
                <td><?php echo $row['comments']; ?></td>
            </tr>
    <?php
}
?>
    </tbody>
</table>


<?php $html_page->writeFooter(); ?>