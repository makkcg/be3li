<?php
if ($_POST) {
    $ewallet_pass = $_POST['ewallet_pass'];
    $ewallet_pass = stripslashes($ewallet_pass);
    $ewallet_pass = $database_manager->realEscapeString($ewallet_pass);

    $sql = "SELECT ewallet_pass FROM ir WHERE ir_id='" . $_SESSION['ir_id'] . "'";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);

    $ewallet_pass = crypt($ewallet_pass, $row["ewallet_pass"]);
    if ($ewallet_pass == $row["ewallet_pass"]) {
        $_SESSION['ewallet_secret'] = "hd48fun44949j49vn4r9vjn49j4f9v4jfjfFF";
        header("Location: " . $core->getURL() . "?page=" . $_GET['redirect']);
    } else {
        $error = "Wrong E-Wallet Password";
    }
}

$html_page->writeHeader();
$html_page->writeBody("E-Wallet Password",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

?>

<script>
    function validateForm() {
        var x = document.forms["myform"]["ewallet_pass"].value;
        if (x == '') {
            document.getElementById("error").innerHTML = "Mandatory fields cannot be left blank.";
            return false;
        }
        return true;
    }
</script>

<p id="error"><?php if(isset($error)) {echo $error;} ?></p>

<form method="post" name="myform" onsubmit="return validateForm();" >

    <label>E-Wallet Password <span class="astrisk"> *</span></label> 
    <input name="ewallet_pass" type="password" autocomplete="off"/>  <br class="clear"/>

    <div class="sep"></div>
    <button type="submit"><i class="fa fa-check-square fa-fw"></i>Validate</button>
</form>

<?php $html_page->writeFooter(); ?>