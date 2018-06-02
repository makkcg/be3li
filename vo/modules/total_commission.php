<?php;$core->verifyiraccount("total_commission");
$core->checkEwalletPassword("total_commission");

$html_page->writeHeader();
$html_page->writeBody("Total Commission",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "Select total_ewallet, total_rpts, total_dcpts FROM ir "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
$result = $database_manager->query($sql);
?>


<table class="table table-striped">
    <thead>
        <tr>
            <th>Total ECs Earned</th>
            <th>Total DC Points Earned</th>
            <th>Total Redeem Points Earned</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $row['total_ewallet']; ?></td>
                <td><?php echo $row['total_dcpts']; ?></td>
                <td><?php echo $row['total_rpts']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<?php $html_page->writeFooter(); ?>