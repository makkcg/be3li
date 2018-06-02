<?php
if (isset($_SESSION) && isset($_SESSION['secret']) && $_SESSION['secret'] != '') {
    $time = date($date_time_format, $_SESSION['last_activity']);
    $sql = "INSERT INTO k8_time_log VALUES ( NULL , '" . $_SESSION["id"] . "', NULL,'" . $time . "', '" . $_SESSION['timezone'] . "')";
    if (!mysql_query($sql)) { error_log($sql); }
}
$_SESSION['secret'] = '';
$_SESSION['id'] = '';
$_SESSION['name'] = '';
$_SESSION['last_activity'] = '';
$_SESSION["top_organization_id"] = '';
$_SESSION["session_minutes"] = '';
$_SESSION["top_organization_logo"] = '';
$_SESSION["top_organization_name"] = '';
$_SESSION["top_organization_cr"] = '';
$_SESSION["top_organization_website"] = '';
$_SESSION["main_branch_country"] = '';
$_SESSION["top_theme_url"] = '';
$_SESSION['main_language'] = '';
$_SESSION['secondary_language'] = '';
$_SESSION['grid_locale_file'] = "";
$_SESSION['timezone'] = "";
session_destroy();
include "includes/header.php";
?>

<div class="login_box">

    <div class="center"><br/>
	<img src="media/testlogo.png" style="height: 150;">
	<!--<div style="font-size: 1.2em;color: #8cc63f;font-family: cursive;">------------KCG------------</div>-->
	</div>

    <p id="error" class="center">
        <?php
        if (isset($_GET) && isset($_GET['error']) && $_GET['error'] == "wrong_pass") {
            echo "Wrong e-mail and password combination.";
        }
        ?>
    </p>

    <form method="post" name="login" onsubmit="return validateForm();">
        <label >E-mail:</label>
        <input name="email" class="text">
        <label >Password:</label>
        <input name="password" type="password" class="text" autocomplete="off">
        <input name="page" type="hidden" value="check">
        <div class="sep"></div>
        <button type="submit" class="ok">Login</button>
    </form>

</div>

<script>
    function validateForm() {
        var x = document.forms["login"]["email"].value;
        if (x == '') {
            error.innerHTML = "E-mail field is mandatory.";
            return false;
        }
        var atpos = x.indexOf("@");
        var dotpos = x.lastIndexOf(".");
        if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= x.length) {
            error.innerHTML = "Not a valid e-mail address";
            return false;
        }
        var x = document.forms["login"]["password"].value;
        if (x == '') {
            error.innerHTML = "Password field is mandatory.";
            return false;
        }
        return true;
    }
</script>