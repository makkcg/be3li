<?php;$core->checkEwalletPassword("my_account");
$html_page->writeHeader();
?>

<?php
$html_page->writeBody("My Account",$core->getewalletval($database_manager,$_SESSION['ir_id']));
?>


<script>
    function validateForm()
    {
        var y = document.forms["myform"]["f_name"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Full Name Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;			
        }						if (y=='0') {            document.getElementById("error").innerHTML = "Full Name Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;			        }
        var y = document.forms["myform"]["l_name"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Family Name Mandatory fields cannot be left blank.";						window.scrollTo(0,0);
            return false;
        }						if (y=='0') {            document.getElementById("error").innerHTML = "Family Name Mandatory fields cannot be left blank.";						window.scrollTo(0,0);            return false;        }
        var y = document.forms["myform"]["a_name"].value;
        if (y == '' || y=='0') {
            document.getElementById("error").innerHTML = "Arabic Name Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }
        var y = document.forms["myform"]["email"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Email Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }						if ( y=='0') {            document.getElementById("error").innerHTML = "Email Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;        }				
        if (validateEmail(y) == false) {
            document.getElementById("error").innerHTML = "Please type your email correctly.";			window.scrollTo(0,0);
            return false;
        }
        var y = document.forms["myform"]["mobile"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Mobile Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }						if (y=='0') {            document.getElementById("error").innerHTML = "Mobile Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;        }
        var y = document.forms["myform"]["phone"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Phone Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }						if (y=='0') {            document.getElementById("error").innerHTML = "Phone Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;        }
        var y = document.forms["myform"]["country"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Country Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }				var y = document.forms["myform"]["city"].value;        if (y == '') {            document.getElementById("error").innerHTML = "City Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;        }				 var y = document.forms["myform"]["area"].value;        if (y == '') {            document.getElementById("error").innerHTML = "Area Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;        }				 var y = document.forms["myform"]["address"].value;        if (y == '') {            document.getElementById("error").innerHTML = "Address Mandatory fields cannot be left blank.";			window.scrollTo(0,0);            return false;        }
        var y = document.forms["myform"]["valid_id"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Valid ID Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }
        var y = document.forms["myform"]["birth_date"].value;
        if (y == '') {
            document.getElementById("error").innerHTML = "Birth Date Mandatory fields cannot be left blank.";			window.scrollTo(0,0);
            return false;
        }
        if (validateAge(y) == false) {
            document.getElementById("error").innerHTML = "You must me over 21 years old to register.";			window.scrollTo(0,0);
            return false;			
        }
        return true;				window.scrollTo(0,0);
    }
    function validateEmail(email) {
        var re =/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
    function validateAge(birth_date) {
        return calculateAge(birth_date) >= 21;
    }
    function calculateAge(birth_date) {
        the_date = new Date(birth_date);
        var ageDifMs = Date.now() - the_date.getTime();
        var ageDate = new Date(ageDifMs); // miliseconds from epoch
        return Math.abs(ageDate.getUTCFullYear() - 1970);
    }	</script>

<p id="error">
    <?php
    if ($_POST) {

        if (isset($_SESSION['ir_id']) && $_SESSION['ir_id']!="") {
            $sql = "UPDATE ir "
                    . " SET f_name = '" . $_POST['f_name'] . "' "
                    . " , l_name = '" . $_POST['l_name'] . "' "
                    . " , a_name = '" . $_POST['a_name'] . "' "
                    . " , title = '" . $_POST['title'] . "' "
                    . " , mobile = '" . $_POST['mobile'] . "' "
                    . " , phone = '" . $_POST['phone'] . "' "
                    . " , address = '" . $_POST['address'] . "' "
                    . " , area = '" . $_POST['area'] . "' "
                    . " , city = '" . $_POST['city'] . "' "
                    . " , country = '" . $_POST['country'] . "' "
                    . " , valid_id = '" . $_POST['valid_id'] . "' "
                    . " , valid_id_type = '" . $_POST['valid_id_type'] . "' "
                    . " , nationality = '" . $_POST['nationality'] . "' "
                    . " , birth_date = '" . $_POST['birth_date'] . "' "
                    . " , beneficiary = '" . $_POST['beneficiary'] . "' "
                    . " , relationship = '" . $_POST['relationship'] . "' "
                    . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
            if ($database_manager->query($sql)) {
                echo "Information updated successfully.";
            } else {
                echo "Couldn't update you information. Try again later.";
            }
        } else {
            echo "The email: ".$_POST['email']." is linked to another account.";
        }
    }
    $sql = "SELECT * from ir WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    ?>
</p>

<form method="post" name="myform" onsubmit="return validateForm();" class="col-xs-12 col-md-12 col-lg-12 col-xl-12 myaccountform">

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Title <span class="astrisk"> *</span></label> 
    <select name="title" class="col-xs-12 col-md-9 col-lg-9 col-xl-9">
        <option value="Mr." <?php if ($row['title'] == "Mr.") { echo "selected";  } ?>>Mr.</option>
        <option value="Mrs." <?php
        if ($row['title'] == "Mrs.") {
            echo "selected";
        }
        ?>>Mrs.</option>
        <option value="Miss" <?php
                if ($row['title'] == "Miss") {
                    echo "selected";
                }
        ?>>Miss</option>
    </select> <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Full Name (English) <span class="astrisk"> *</span></label> 
    <input name="f_name" type="text" value="<?php echo $row['f_name'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Family Name <span class="astrisk"> *</span></label> 
    <input name="l_name" type="text" value="<?php echo $row['l_name'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Arabic Name <span class="astrisk"> *</span></label> 
    <input name="a_name" type="text" value="<?php echo $row['a_name'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Email <span class="astrisk"> *</span></label> 
    <input name="email" type="text" value="<?php echo $row['email'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" readonly/>  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Mobile <span class="astrisk"> *</span></label> 
    <input name="mobile" type="text" value="<?php echo $row['mobile'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Phone <span class="astrisk"> *</span></label> 
    <input name="phone" type="text" value="<?php echo $row['phone'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Country <span class="astrisk"> *</span></label> 
    <select name="country" class="col-xs-12 col-md-9 col-lg-9 col-xl-9">		<option value="Egypt" data-cities="القاهرة,الجيزة,الإسكندرية,العاشر من رمضان,مدينه 6 اكتوبر,العريش,أسيوط,أسوان,بني سويف,بنها,برج العرب,دمنهور,دمياط,العين السخنه,الوادي الجديد,فايد,الفيوم,حلوان,الغردقة,الإسماعيلية,كفر الشيخ,الأقصر,المحلة الكبرى,المنصورة,مرسى مطروح,المنيا,منوفيه,الصالحية الجديدة,بورسعيد,القليوبيه,قنا,مدينة السادات,شرم الشيخ,شبين الكوم,مدينة سوهاج,السويس,طنطا,الزقازيق" <?php if ($row['country'] == "مصر") {echo "selected";} ?> >مصر</option>
    </select><br class="clear"/> 
	 <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">City <span class="astrisk"> *</span></label> 	 <div class="col-xs-12 col-md-3 col-lg-3 col-xl-3"><?php echo $row['city']; ?></div>	<select class="col-xs-12 col-md-6 col-lg-6 col-xl-6" name="city" id="city" onchange="populate_areas()" >	<option value="" selected data-areas=""></option>								<option label="القاهرة" zoneid="1" data-areas=" مدينة مايو15 ,ارض الجولف, الازهر والجمالية  , الاميريه, الأزهر, البساتين, التجمع الثالث  - القطامية, التجمع الخامس, الحرفيين ? التجاريين, الحلمية, الرحاب, الزاوية والشرابية , الزمالك, الزيتون, السيده زينب, السيده نفيسه, الضاهر, العباسية, العبور , القصر العيني, القلعة , المرج, المطرية, المعادى الجديدة , المقطم, الموسكى باب الشعرية , النزهة الجديدة , أبو رواش, بولاق, جاردن سيتي, حدائق القبه, حدائق المعادى - المعادى القديمة , دار السلام والبساتين , دجلة, رمسيس, زهراء المعادى , زهراء مدينة نصر, شبرا, شبرا الخيمه, عابدين, عين شمس, غمرة, مدينة العبور, مدينة نصر, مساكن شيراتون, مصر الجديده  , مصر القديمةوسط البلد" value="القاهرة">القاهرة</option> 								 <option label="الجيزة" zoneid="1" data-areas="العجوزة, الكيت كات, الجيزة والأهرامات, الحرانيه, الدقي, المنصورية, المنيل, المهندسين, إمبابة, صفط اللبنفيصل" value="الجيزة">الجيزة</option>								 								 <option label="الإسكندرية " zoneid="2" data-areas="أبو قير, الابراهيمية, الأزاريطة, الشاطبي, العجمى, العصافرة, المعمورة, المنتزة, سيدي جابر, المندرة, المنشية, بحرى, برج العرب, بولكلي, جليم, رشدي, زيزينيا, سان ستيفانو, سبورتنج, سموحة, سيدي بشر, فليمنغ, فيكتوريا, كليوباترا, لوران, محرم بك, محطة الرمل, مصطفى كامل ,ميامي, كفر عبده" value="الإسكندرية">الإسكندرية </option>								 								 <option label="العاشر من رمضان" zoneid="1" data-areas="العاشر من رمضان" value="العاشر من رمضان">العاشر من رمضان</option>								 								 <option label="مدينه 6 اكتوبر" zoneid="1" data-areas="مدينه 6 اكتوبر" value="مدينه 6 اكتوبر">مدينه 6 اكتوبر</option>								 								 <option label="العريش" zoneid="8" data-areas="العريش" value="العريش">العريش</option>								 								 <option label="أسيوط " zoneid="6" data-areas="أسيوط " value="أسيوط">أسيوط </option>								 								 <option label="أسوان" zoneid="8" data-areas="أسوان" value="أسوان">أسوان</option>								 								 <option label="بني سويف" zoneid="6" data-areas="بني سويف" value="بني سويف">بني سويف</option>								 								 <option label="بنها" zoneid="4" data-areas="بنها" value="بنها">بنها</option>								 								 <option label="برج العرب" zoneid="4" data-areas="برج العرب" value="برج العرب">برج العرب</option>								 								 <option label="دمنهور" zoneid="3" data-areas="دمنهور" value="دمنهور">دمنهور</option>								 								 <option label="دمياط" zoneid="3" data-areas="دمياط" value="دمياط">دمياط</option>								 								 <option label="العين السخنه" zoneid="4" data-areas="العين السخنه" value="العين السخنه">العين السخنه</option>								 								 <option label="الوادي الجديد" zoneid="8" data-areas="الوادي الجديد" value="الوادي الجديد">الوادي الجديد</option>								 								 <option label="فايد" zoneid="4" data-areas="فايد" value="فايد">فايد</option>								 								 <option label="الفيوم" zoneid="6" data-areas="الفيوم" value="الفيوم">الفيوم</option>								 								 <option label="حلوان" zoneid="1" data-areas="حلوان" value="حلوان">حلوان</option>								 								 <option label="الغردقة" zoneid="8" data-areas="الغردقة" value="الغردقة">الغردقة</option>								 								 <option label="الإسماعيلية" zoneid="3" data-areas="الإسماعيلية" value="الإسماعيلية">الإسماعيلية</option>								 								 <option label="كفر الشيخ" zoneid="5" data-areas="كفر الشيخ" value="كفر الشيخ">كفر الشيخ</option>								 								 <option label="الأقصر" zoneid="8" data-areas="الأقصر" value="الأقصر">الأقصر</option>								 								 <option label="المحلة الكبرى" zoneid="5" data-areas="المحلة الكبرى" value="المحلة الكبرى">المحلة الكبرى</option>								 								 <option label="المنصورة" zoneid="5" data-areas="المنصورة" value="المنصورة">المنصورة</option>								 								 <option label="مرسى مطروح " zoneid="8" data-areas="مرسى مطروح " value="مرسى مطروح">مرسى مطروح </option>								 								 <option label="المنيا" zoneid="6" data-areas="المنيا" value="المنيا">المنيا</option>								 								 <option label="منوفيه" zoneid="5" data-areas="منوفيه" value="منوفيه">منوفيه</option>								 								 <option label="الصالحية الجديدة" zoneid="5" data-areas="الصالحية الجديدة" value="الصالحية الجديدة">الصالحية الجديدة</option>								 								 <option label="بورسعيد" zoneid="3" data-areas="بورسعيد" value="بورسعيد">بورسعيد</option>								 								 <option label="القليوبيه" zoneid="3" data-areas="القليوبيه" value="القليوبيه">القليوبيه</option>								 								 <option label="قنا" zoneid="8" data-areas="قنا" value="قنا">قنا</option>								 								 <option label="مدينة السادات" zoneid="5" data-areas="مدينة السادات" value="مدينة السادات">مدينة السادات</option>								 								 <option label="شرم الشيخ" zoneid="8" data-areas="شرم الشيخ" value="شرم الشيخ">شرم الشيخ</option>								 								 <option label="شبين الكوم" zoneid="4" data-areas="شبين الكوم" value="شبين الكوم">شبين الكوم</option>								 								 <option label="مدينة سوهاج" zoneid="7" data-areas="مدينة سوهاج" value="مدينة سوهاج">مدينة سوهاج</option>								 								 <option label="السويس" zoneid="3" data-areas="السويس" value="السويس">السويس</option>								 								 <option label="طنطا" zoneid="4" data-areas="طنطا" value="طنطا">طنطا</option>								 								 <option label="الزقازيق" zoneid="4" data-areas="الزقازيق" value="الزقازيق">الزقازيق</option>																																																																																			   			</select><br class="clear">		 <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Area <span class="astrisk"> *</span></label> 	 <div class="col-xs-12 col-md-3 col-lg-3 col-xl-3"><?php echo $row['area']; ?></div>	<select class="col-xs-12 col-md-6 col-lg-6 col-xl-6" name="area"  id="area">		<option value="" selected=""></option>		</select> <br class="clear">		<label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Address <span class="astrisk"> *</span></label>     <textarea name="address" class="col-xs-12 col-md-9 col-lg-9 col-xl-9"><?php echo $row['address'] ?></textarea> <br class="clear"/>   
    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Valid ID <span class="astrisk"> *</span></label> 
    <input name="valid_id" type="text" value="<?php echo $row['valid_id'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Valid ID Type <span class="astrisk"> *</span></label> 
    <input name="valid_id_type" type="text" value="<?php echo $row['valid_id_type'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9"  />  <br class="clear"/>

    <label style="display:none;" class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Nationality <span class="astrisk"> *</span></label> 
    <input style="display:none;" name="nationality" type="text" value="<?php echo $row['nationality'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3">Birth Date <span class="astrisk"> *</span></label> 
    <input name="birth_date" type="date" value="<?php echo $row['birth_date'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" />  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3" style="display:none;">Beneficiary Name <span class="astrisk"> *</span></label> 
    <input name="beneficiary" type="text" value="<?php echo $row['beneficiary'] ?>" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" style="display:none;"/>  <br class="clear"/>

    <label class="col-xs-12 col-md-3 col-lg-3 col-xl-3" style="display:none;">Relationship <span class="astrisk"> *</span></label> 
    <select name="relationship" class="col-xs-12 col-md-9 col-lg-9 col-xl-9" style="display:none;">
        <option value="Spouse" <?php
        if ($row['relationship'] == "Spouse") {
            echo "selected";
        }
        ?>>Spouse</option>
        <option value="Father" <?php
        if ($row['relationship'] == "Father") {
            echo "selected";
        }
        ?>>Father</option>
        <option value="Mother" <?php
        if ($row['relationship'] == "Mother") {
            echo "selected";
        }
        ?>>Mother</option>
        <option value="Son" <?php
        if ($row['relationship'] == "Son") {
            echo "selected";
        }
        ?>>Son</option>
        <option value="Daughter" <?php
        if ($row['relationship'] == "Daughter") {
            echo "selected";
        }
        ?>>Daughter</option>
        <option value="Other" <?php
        if ($row['relationship'] == "Other") {
            echo "selected";
        }
        ?>>Other</option>
    </select>  <br class="clear"/>

    <div class="sep"></div>

    <button type="submit" onclick="validateForm()" class="col-xs-12 col-md-12 col-lg-12 col-xl-12"><i class="fa fa-check-square fa-fw"></i>Update</button>
</form>
<?php $html_page->writeFooter(); ?>