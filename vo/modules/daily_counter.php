<?php
$html_page->writeHeader();
$html_page->writeBody("Daily Counter",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));
?>

<script>
    function validateForm()
    {
        var y = document.forms["myform"]["date"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        return true;
    }
</script>

<p id='error'></p>

<form method="post" name="myform" onsubmit="return validateForm();" >

    <label>Date: <span class="astrisk"> *</span></label> 
    <input name="date" type="date" value="<?php if ($_POST) {
    echo $_POST['date'];
} else {
    echo $core->getFormatedDate();
} ?>" />  <br class="clear"/>


    <div class="sep"></div>

    <button type="submit"><i class="fa fa-check-square fa-fw"></i>Show</button>

</form>
    <div class="sep"></div>

<?php
if ($_POST) {
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>BU</th>
                <th>Left</th>
                <th>Right</th>
            </tr>
        </thead>
        <tbody>
    <?php
    $result = $core->getDailyCounter($database_manager, $_POST['date'], $_SESSION['ir_id']);
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <tr>
                    <td><?php echo $core->getBuCode($row['bu_id']); ?></td>
                    <td><?php echo $row['left_dc']; ?></td>
                    <td><?php echo $row['right_dc']; ?></td>
                </tr>
        <?php
    }
    ?>
        </tbody>
    </table>

<?php } ?>


<?php $html_page->writeFooter(); ?>