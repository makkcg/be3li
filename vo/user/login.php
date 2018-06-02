<?php
$html_page->writeHeader();
?>

<?php
$_SESSION['secret'] = '';
$_SESSION['ewallet_secret'] = '';
$_SESSION['last_activity'] = '';
$_SESSION['ir_id'] = '';
$_SESSION['full_name'] = '';
$_SESSION['company'] = '';
//$_SESSION['lang'] = '';
/////language

session_destroy();
////login language vars as it couldnt be in a session
$enterIRcode="Please Enter the Represintative Code : ";
$enterIRcode_ar="يرجى ادخال كود الموزع : ";
$enterPsw="Password";
$enterPsw_ar="كلمة السر";
$forgetIRCode="Forgot Represintative Code";
$forgetIRCode_ar="نسيت كود الموزع";
$forgetPsw="Forgot Password";
$forgetPsw_ar="نسين كلمة السر";
$loginbtn="Login";
$loginbtn_ar="تسجيل الدخول";
$NewAccount_ar="ليس لديك حساب دخول؟ أنشئ حساب جديد الآن!";
$NewAccount="Don't have an account? Create New Now!";
//$curlang=$_GET["lang"];
?>
<style>
.language_sw_box{
	text-align: center;
    margin: 20px 10px 5px 10px;
    font-size: 0.85em;
    font-weight: bold;
    color: #23206e;	display:none;
}
.language_box{
	display: inline-block;
	margin-top: 5px;
	padding: 0 10px;
}
.language_box img{
	width: 40px;
}
.language_box .langtitle{
	display:block;
	padding: 5px 0px;
}
</style>
<div class="col-xm-1 col-sm-1 col-md-3 col-lg-4 col-xl-4"></div>
<div class="login_box col-xm-10 col-sm-10 col-md-6 col-lg-4 col-xl-4">

    <div class="center"><br/>
	<img src="images/testlogo.png" style="height: 200;">
	<!--<div style="font-size: 1.2em;color: #8cc63f;font-family: cursive;">------------MaxHealth------------</div>-->
	</div>

    <?php
    if (isset($_GET) && isset($_GET['error']) && $_GET['error'] == "freeze") {
        ?>
        <p id='error' class="center">Sorry for the inconvenience.</p>
        <p class="center">Virtual Office is closed everyday for one hour for database update.</p>
        <p class="center">Come back after 12am.</p>
        <?php
    } else {
        ?>

        <p id="error" class="center">
            <?php
            if (isset($_GET) && isset($_GET['error']) && $_GET['error'] == "wrong_pass") {
                echo "Wrong ID and password combination.";
            }
            if (isset($_GET) && isset($_GET['error']) && $_GET['error'] == "account_deactivated") {
                echo "This account has been deactivated.";
            }
            ?>
        </p>

        <form method="post" name="login" onsubmit="return validateForm();">
            <label ><?php  if($_COOKIE["lang"]=="ar"){echo $enterIRcode_ar;}else{ echo $enterIRcode;} ?></label>
            <input name="id" class="text">
            <label ><?php  if($_COOKIE["lang"]=="ar"){echo $enterPsw_ar;}else{ echo $enterPsw;} ?></label>
            <input name="password" type="password" class="text" autocomplete="off">
            <input name="page" type="hidden" value="check">
			<!--<input name="lang" type="hidden" value=<?php //$curlang;?>>-->
            <div class="sep"></div>
			<div class="col-xm-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <button type="submit" style="width:100%;"><i class="fa fa-check-square fa-fw"></i><?php  if($_COOKIE["lang"]=="ar"){echo $loginbtn_ar;}else{ echo $loginbtn;} ?></button>
			</div>
            <div class="forgots col-xm-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <a  href="index.php?page=forgot_id"><?php  if($_COOKIE["lang"]=="ar"){echo $forgetIRCode_ar;}else{ echo $forgetIRCode;} ?></a>
                <a  href="index.php?page=forgot_pass"><?php  if($_COOKIE["lang"]=="ar"){echo $forgetPsw_ar;}else{ echo $forgetPsw;} ?></a>
				<a  href="index.php?page=register_second_step&step=1&refir=BE000010" target="_blank"><?php  if($_COOKIE["lang"]=="ar"){echo $NewAccount_ar;}else{ echo $NewAccount;} ?></a>
            </div>
        </form>
	<div class="language_sw_box" >
		<div class="language_box" >
			<a href="index.php?page=login&lang=ar&langchange=1" ><img src="images/flags/ar.png" /></a>
			<div class="langtitle" style="">عربي</div>
		</div>
		<div class="language_box">
			<a href="index.php?page=login&lang=en&langchange=1" ><img src="images/flags/en.png" /></a>
			<div class="langtitle">English</div>
		</div>
	</div>
    </div>
	<div class="col-xm-1 col-sm-1 col-md-3 col-lg-4 col-xl-4"></div>
    <script>
        function validateForm() {
            var x = document.forms["login"]["id"].value;
            if (x == '') {
                error.innerHTML = "ID field is mandatory.";
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

<?php } ?>
