<?php
$html_page->writeHeader();
?>

<?php
if ($ir_id = $core->getIdFromResetCode($database_manager, $_GET['reset_code'])) {
    ?>

    <div class="login_box">

        <div class="center"><br/><img src="images/logo.png"></div>

        <p>Kindly, enter your new password.</p>
        <p id="error" class="center">
            <?php
            if ($_POST) {
                $salt = $core->generateSalt();
                $new_password = crypt($_POST['new_pass'], $salt);
                $sql = "UPDATE ir "
                        . " SET login_pass = '" . $new_password . "' "
                        . " WHERE ir_id = '" . $_POST['ir_id'] . "'";
                if ($database_manager->query($sql)) {
                    echo "Password Updated Successfully.";
                } else {
                    echo "Couldn't update you information. Try again later.";
                }
            }
            ?>
        </p>

        <form method="post" name="myform" onsubmit="return validateForm();">
            <label>IR ID <span class="astrisk"> *</span></label> 
            <input name="ir_id" type="text" value="<?php echo $ir_id; ?>"  readonly=""/> 
            <label>New Password <span class="astrisk"> *</span></label> 
            <input name="new_pass" type="password" autocomplete="off" /> 
            <label>Confirm Password <span class="astrisk"> *</span></label> 
            <input name="confirm" type="password" autocomplete="off" /> 
            <div class="sep"></div>
            <button type="submit"><i class="fa fa-check-square fa-fw"></i>Send</button>
            <div class="forgots">
                <a  href="index.php?page=login">Back to Login</a>
            </div>
        </form>

    </div>

    <script>
        function validateForm() {
            var x = document.forms["myform"]["ir_id"].value;
            if (x == '') {
                document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                return false;
            }
            var y = document.forms["myform"]["new_pass"].value;
            if (y == '') {
                document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                return false;
            }
            var z = document.forms["myform"]["confirm"].value;
            if (z == '') {
                document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
                return false;
            }
            if (y != z) {
                document.getElementById("error").innerHTML = "Password Mismatch.";
                return false;
            }
        }
    </script>
<?php } else { ?>

    <div class="login_box">
        <div class="center"><br/><img src="images/logo.png"></div>
        <p id="error" class="center">
            Link Expired. <a  href="index.php?page=login">Back to Login</a>
        </p>
    </div>

<?php } ?>
