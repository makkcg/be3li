<?php
$html_page->writeHeader();
?>
<div class="login_box">

    <div class="center"><br/><img src="images/logo.png"></div>

    <p>Kindly, Enter you email address to reset your password.</p>
    <p id="error" class="center">
        <?php
        if ($_POST) {		
            if ($ir_id = $core->getIdFromEmail($database_manager, $_POST['email'])) {
                $msg = "Use this link to reset your password: ". $core->getUrl() . "?page=forgot_reset&reset_code=" . $core->getPasswordResetCode($database_manager, $ir_id) . "   ";
                if ($core->email($database_manager, $_POST['email'], "Forgot my Password", $msg)) {
                    echo "An email was sent to you containing your password reset link.";
                } else {
                    echo "Could not send you an email.";
                }
            } else {
                echo "This email is not registered in our database.";
            }
        }
        ?>
    </p>

    <form method="post" name="myform" onsubmit="return validateForm();">
        <label >Email:</label>
        <input name="email" class="text">
        <div class="sep"></div>
        <button type="submit"><i class="fa fa-check-square fa-fw"></i>Send</button>
        <div class="forgots">
            <a  href="index.php?page=login">Back to Login</a>
        </div>
    </form>

</div>

<script>
    function validateForm() {
        var y = document.forms["myform"]["email"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        if (validateEmail(y) == false) {
            document.getElementById("error").innerHTML = "Please type your email correctly.";
            return false;
        }
        return true;
    }
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
</script>
