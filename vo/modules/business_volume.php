<?php
$core->checkEwalletPassword("business_volume");

$html_page->writeHeader();
$html_page->writeBody("Business Volume",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "Select code, left_dbv, right_dbv, left_abv, right_abv FROM bu "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
        . " ORDER BY code ASC ";
$result = $database_manager->query($sql);
?>


<table class="table table-striped">
    <thead>
        <tr>
            <th>BU</th>
            <th>Left Dynamic BV</th>
            <th>Right Dynamic BV</th>
            <th>Left Accumulative BV</th>
            <th>Right Accumulative BV</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $row['code']; ?></td>
                <td><?php echo $row['left_dbv']; ?></td>
                <td><?php echo $row['right_dbv']; ?></td>
                <td><?php echo $row['left_abv']; ?></td>
                <td><?php echo $row['right_abv']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<?php $html_page->writeFooter(); ?>