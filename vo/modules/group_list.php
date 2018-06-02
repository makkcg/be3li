<?php
$core->checkEwalletPassword("group_list");

$html_page->writeHeader();
$html_page->writeBody("Group List",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$bu_code = "001";
if (isset($_GET['bu_code'])) {
    $bu_code = $_GET['bu_code'];
}

$children_bu_ids = $core->getLeftChildrenBUIDsString($database_manager, $_SESSION['ir_id'] . "-" . $bu_code);
$children_bu_ids .= $core->getRightChildrenBUIDsString($database_manager, $_SESSION['ir_id'] . "-" . $bu_code);

$sql = "SELECT r.ir_id AS ir_id, CONCAT(r.f_name, ' ', r.l_name) AS name, "
        . " b.parent_bu_id AS upline_bu_id, CONCAT(upline.f_name, ' ', upline.l_name) AS upline_name, "
        . " b.position AS position, r.registration_date AS registration_date, "
        . " r.qualification_date AS qualification_date "
        . " FROM ir r"
        . " LEFT OUTER JOIN bu b ON b.code = '001' AND r.ir_id = b.ir_id"
        . " LEFT OUTER JOIN ir upline ON upline.ir_id = SUBSTRING(b.parent_bu_id, 1,6) "
        . " WHERE CONCAT(r.ir_id, '-001') IN (" . $children_bu_ids . "'') "
        . " ORDER BY registration_date DESC ";
$result = $database_manager->query($sql);
$num_rows = mysqli_num_rows($result);
echo "Your Group List is: " . $num_rows;
echo "<div class='sep'></div>";
$result = $core->paginationBeforeTable($database_manager, "group_list", $sql);
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>IR ID</th>
            <th>Name</th>
            <th>Upline IR ID-BU</th>
            <th>Upline Name</th>
            <th>Placement</th>
            <th>Registration Date</th>
            <th>Qualification Date</th>

        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $row['ir_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['upline_bu_id']; ?></td>
                <td><?php echo $row['upline_name']; ?></td>
                <td><?php echo $row['position']; ?></td>
                <td><?php echo $row['registration_date']; ?></td>
                <td><?php echo $row['qualification_date']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>


<?php $html_page->writeFooter(); ?>