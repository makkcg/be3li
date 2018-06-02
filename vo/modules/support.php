<?php
$html_page->writeHeader();
$html_page->writeBody("Support",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));


$error = "";
if ($_POST) {
    $target_dir = "attachments/";
    $target_file = $target_dir . basename($_FILES["attachment"]["name"]);
    $uploadOk = 1;

    if (file_exists($target_file)) {
        $target_file = $target_dir . $core->getFormatedDateTime() . basename($_FILES["attachment"]["name"]);
    }

    if ($_FILES["attachment"]["size"] > 2000000) {
        $error = "<p id='error'>Sorry, could not upload your file. Max Upload size 2MG.</p>";
    }

    if ($error == "") {
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO support (datetime, severity, type, status, message, attachment) VALUES( "
                    . " '" . $core->getFormatedDateTime() . "', '" . $_POST['severity'] . "', '" . $_POST['type'] . "', 'New', '" . $_POST['message'] . "', '" . $target_file . "' "
                    . ")";
            $database_manager->query($sql);
            $error = "<p id='error'>Support Ticket was sent Successfully. Your ticket number is #" . $database_manager->getLastGeneratedId() . "</p>";
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}


$sql = "SELECT id, datetime, severity, type, status FROM support "
        . " WHERE ir_id = '" . $_SESSION['ir_id'] . "' "
        . " ORDER BY datetime DESC ";
$result = $core->paginationBeforeTable($database_manager, "purchase_history", $sql);
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Ticket Number</th>
            <th>Date / Time</th>
            <th>Severity</th>
            <th>Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['datetime']; ?></td>
                <td><?php echo $row['severity']; ?></td>
                <td><?php echo $row['type']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><a href="index.php?page=ticket&id=<?php echo $row['id']; ?>">Details</a></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<div class="sep dotted"></div>
<h2>Submit new ticket:</h2>
<script>
    function validateForm1()
    {
        var x = document.forms["myform1"]["message"].value;
        if (x == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        return true;
    }
</script>

<p id="error">
    <?php
    if ($error != "") {
        echo $error;
    }
    ?>
</p>

<form method="post" name="myform1" onsubmit="return validateForm1();"  enctype="multipart/form-data">

    <label>Type: <span class="astrisk"> *</span></label> 
    <select name="type">
        <option value="Personal Info Update">Personal Info Update</option>
        <option value="Product Exchange">Product Exchange</option>
        <option value="Delivery Delay">Delivery Delay</option>
        <option value="Change Ownership">Change Ownership</option>
        <option value="Reserve Vacation">Vacation Reservation</option>
        <option value="Reserve Vacation">E-Wallet Password Retrieval</option>
        <option value="Other">Other</option>
    </select>  <br class="clear"/>

    <label>Severity: <span class="astrisk"> *</span></label> 
    <select name="severity">
        <option value="Low">Low</option>
        <option value="High">High</option>
        <option value="Urgent">Urgent</option>
    </select>  <br class="clear"/>

    <label>Message <span class="astrisk"> *</span></label> 
    <textarea name="message" /></textarea>  <br class="clear"/>
<div class="sep"></div>

<label>Attachment <span class="astrisk"> *</span></label> 
<input name="attachment" type="file" autocomplete="off"/>  <br class="clear"/>

<button type="submit"><i class="fa fa-check-square fa-fw"></i>Send</button>
</form>


<?php $html_page->writeFooter(); ?>