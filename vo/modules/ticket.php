<?php
$html_page->writeHeader();
$html_page->writeBody("Ticket #" . $_GET['id'],$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "SELECT * FROM support "
        . " WHERE id = '" . $_GET['id'] . "'"
        . " AND ir_id = '" . $_SESSION['ir_id'] . "' ";
$result = $database_manager->query($sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    ?>


    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ticket Number</th>
                <th>Date / Time</th>
                <th>Severity</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['datetime']; ?></td>
                <td><?php echo $row['severity']; ?></td>
                <td><?php echo $row['type']; ?></td>
                <td><?php echo $row['status']; ?></td>
            </tr>
        </tbody>
    </table>

    <div class="sep "></div>

    <form class="support">
        <label>Message: </label> 
        <textarea readonly><?php echo $row['message']; ?></textarea>

        <div class="sep "></div>
        
        <label>Attachment: </label> 
        <a href="<?php echo $row['attachment']; ?>" target="_blank"><i class="fa fa-download fa-fw"></i>Download File</a>

        <div class="sep "></div>

        <label>Response: </label> 
        <textarea readonly><?php echo $row['response']; ?></textarea>

    </form>

<?php } else {
    ?>
    <p id="error">You don not have access to this information.</p>
    <?php
}
?>

<?php $html_page->writeFooter(); ?>