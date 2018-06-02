<?php
$html_page->writeHeader();
$html_page->writeBody("Change Password(s)");
?>
<script>
    function validateForm1()
    {
        var x = document.forms["myform1"]["old_pass"].value;
        if (x == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        var y = document.forms["myform1"]["new_pass"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        var z = document.forms["myform1"]["confirm"].value;
        if (z == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        if (y != z) {
            document.getElementById("error").innerHTML = "Password Mismatch.";
            return false;
        }
        return true;
    }
</script>

<p id="error">
    <?php
    if ($_POST) {
        $old_password = $_POST['old_pass'];
        $old_password = stripslashes($old_password);
        $old_password = $database_manager->realEscapeString($old_password);

        $sql = "SELECT " . $_POST['password'] . " FROM ir WHERE ir_id='" . $_SESSION['ir_id'] . "'";
        $result = $database_manager->query($sql);
        $row = mysqli_fetch_assoc($result);
        $old_password = crypt($old_password, $row[$_POST['password']]);

        if ($old_password == $row[$_POST['password']]) {
            $salt = $core->generateSalt();
            $new_password = crypt($_POST['new_pass'], $salt);
            $sql = "UPDATE ir "
                    . " SET " . $_POST['password'] . " = '" . $new_password . "' "
                    . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
            if ($database_manager->query($sql)) {
                echo "Password Updated Successfully.";
            } else {
                echo "Couldn't update you information. Try again later.";
            }
        } else {
            echo "Wrong Old Password";
        }
    }
    ?>
</p>


<h2>Change Login Password:</h2>

<form method="post" name="myform1" onsubmit="return validateForm1();" >

    <label>Old Password <span class="astrisk"> *</span></label> 
    <input name="old_pass" type="password" autocomplete="off"/>  <br class="clear"/>
    <label>New Password <span class="astrisk"> *</span></label> 
    <input name="new_pass" type="password" autocomplete="off" />  <br class="clear"/>
    <label>Confirm Password <span class="astrisk"> *</span></label> 
    <input name="confirm" type="password" autocomplete="off" />  <br class="clear"/>

    <div class="sep"></div>
    <input name="password" type="hidden" value="login_pass" /> 

    <button type="submit"><i class="fa fa-check-square fa-fw"></i>Update</button>
</form>

<div class="sep dotted"></div>


<script>
    function validateForm2() {
        var x = document.forms["myform2"]["old_pass"].value;
        if (x == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        var y = document.forms["myform2"]["new_pass"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        var z = document.forms["myform2"]["confirm"].value;
        if (z == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        if (y != z) {
            document.getElementById("error").innerHTML = "Password Mismatch.";
            return false;
        }
        return true;
    }
</script>

<h2>Change E-Wallet Password:</h2>

<form method="post" name="myform2" onsubmit="return validateForm2();" >

    <label>Old Password <span class="astrisk"> *</span></label> 
    <input name="old_pass" type="password" autocomplete="off"/>  <br class="clear"/>
    <label>New Password <span class="astrisk"> *</span></label> 
    <input name="new_pass" type="password" autocomplete="off" />  <br class="clear"/>
    <label>Confirm Password <span class="astrisk"> *</span></label> 
    <input name="confirm" type="password" autocomplete="off" />  <br class="clear"/>

    <div class="sep"></div>
    <input name="password" type="hidden" value="ewallet_pass" /> 

    <button type="submit"><i class="fa fa-check-square fa-fw"></i>Update</button>
</form>

<div class="sep"></div>
