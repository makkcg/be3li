<?php
$core->checkEwalletPassword("qualify_shop");

$html_page->writeHeader();
$html_page->writeBody("Qualify Shop (تفعيل رخصة المتجر)",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "SELECT binary_qualify_fees001, binary_qualify_fees002, binary_qualify_fees003, retail_qualify_fees, retail_qualify_com FROM configuration ";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);

$binary_qualify_fees001 = $row['binary_qualify_fees001'];
$binary_qualify_fees002 = $row['binary_qualify_fees002'];
$binary_qualify_fees003 = $row['binary_qualify_fees003'];
$retail_qualify_fees = $row['retail_qualify_fees'];
$retail_qualify_com = $row['retail_qualify_com'];

$sql = "SELECT is_qualified, code FROM bu WHERE ir_id = '" . $_SESSION['ir_id'] . "' order by code ASC ";
$result = $database_manager->query($sql);

$case = "All your Business Units are Qualified.";

$row = mysqli_fetch_assoc($result);////bu001
if ($row['is_qualified'] == 0) {///none is qualified
    $case = "Non of your Business Units is Qualified.";
}
if ($row['is_qualified'] == 1) {////retail qualified
    $case = "Your Retail Shop is Qualified.";
}

if ($row['is_qualified'] == 2) {////bu001 binary qualified
    $row = mysqli_fetch_assoc($result);//bu002
	////added by kcg , changed by kcg
	$row2 = mysqli_fetch_assoc($result);///bu003
    if ($row['is_qualified'] == 0 && $row2['is_qualified']==0) {///bu002 is not qualified
        $case = "Your first Business Unit (001) is Qualified.";
    } else {
		
        if ($row2['is_qualified'] == 0) {///bu002 is qualified bu003 is not qualified
            $case = "Your first (001) and second Business Unit (002) are Qualified.";
        }else if ($row['is_qualified'] == 0){
			 $case = "Your first (001) and Third Business Unit (003) are Qualified.";
		}else{
			$case = "All your Business Units are Qualified.";
		}
    }
}
////end added - changed by kcg
?>


<?php
/*///////////////////////////////////////////Qualify Retail Shop/////////////////////////////*/
if ($_GET['qualify'] == "retail_ewallet" && $case == "Non of your Business Units is Qualified.") {
    $sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $ewallet = $row['ewallet'];

    if ($ewallet < $retail_qualify_fees) {
        echo "<p id='error'>Insufficient Funds. الرصيد غير كافي</p>";
    } else {
		
		////Get Referral IR ID to give him the Retail qualification commission
		$sql = "SELECT referral_bu_id FROM bu WHERE ir_id = '" . $_SESSION['ir_id'] . "' AND code='001'";
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);
		$referral_IRID = $core->getIRIDsStringFromBUIDsString($row['referral_bu_id']);
		
        // Take Funds

        $sql = "UPDATE ir SET ewallet = (ewallet - " . $retail_qualify_fees . ") WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
        $database_manager->query($sql);

        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Qualify Retail Shop', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $retail_qualify_fees) . "', '" . (string) ($ewallet - $retail_qualify_fees) . "', 'ProShops')";
        $database_manager->query($sql);

        // UPDATE IR & BU

        $sql = "UPDATE bu SET is_qualified = 1 WHERE ir_id = '" . $_SESSION['ir_id'] . "' AND code = '001' ";
        $database_manager->query($sql);

        $sql = "UPDATE ir SET  qualification_date = '" . $core->getFormatedDateTime() . "' WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);
		
		////////////////////////Referral IR Retail Qualificatoin Commission /////////////////////////////
		////Give retail qualification commission to referal IR into his ewallet
		$sql = "UPDATE ir SET ewallet = (ewallet + " . $retail_qualify_com . ") WHERE ir_id = '" . $referral_IRID . "' ";
        $database_manager->query($sql);
		///Get the Ewallet of Referral IR
		$sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $referral_IRID . "' ";
		$result = $database_manager->query($sql);
		$row = mysqli_fetch_assoc($result);
		$Ref_ewallet = $row['ewallet'];
		/////transaction to Referal IR
        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $referral_IRID . "', 'New IR Qualified Retail Shop Commission', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 + $retail_qualify_com) . "', '" . (string) ($Ref_ewallet + $retail_qualify_com) . "', '".$_SESSION['ir_id']."')";
        $database_manager->query($sql);
		////////////////////////////////////////////////////////////////////////////////////////////////
		
        $case = "Your Retail Shop is Qualified.";
    }
}

if ($_GET['qualify'] == "retail") {
    $case = "Choose Payment Method.";
}

echo "<p id='error'>" . $case . "</p>";

switch ($case) {
    case "All your Business Units are Qualified.":
        ?>

        <div class="halfwidth right qualifyBinary_Div">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Binary Shop (تفعيل رخصة البيع الشبكي)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $binary_qualify_fees001 . " / " . $binary_qualify_fees002 . " / " . $binary_qualify_fees003; ?> LE - جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive qulify_bu001" href="#">Qualify BU 001 (تفعيل رخصة 1)</a>
                            <a class="button inactive qulify_bu002" href="#">Qualify BU 002 (تفعيل رخصة 2)</a>
                            <a class="button inactive qulify_bu003" href="#">Qualify BU 003 (تفعيل رخصة 3)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="halfwidth left">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Retail Shop (تفعيل رخصة البيع بالتجزئة)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $retail_qualify_fees; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive" href="#">Qualify Now (فعل الآن)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
    case "Non of your Business Units is Qualified.";
        ?>

        <div class="halfwidth right qualifyBinary_Div">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Binary Shop (تفعيل رخصة البيع الشبكي)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $binary_qualify_fees001 . " / " . $binary_qualify_fees002 . " / " . $binary_qualify_fees003; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button qulify_bu001" href="index.php?page=categories&title=Qualify&bu=001">Qualify BU 001 (تفعيل رخصة 1)</a>
                            <a class="button inactive qulify_bu002" href="#">Qualify BU 002 (تفعيل رخصة 2)</a>
                            <a class="button inactive qulify_bu003" href="#">Qualify BU 003 (تفعيل رخصة 3)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="halfwidth left">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Retail Shop (تفعيل رخصة البيع بالتجزئة)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $retail_qualify_fees; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button" href="index.php?page=qualify_shop&qualify=retail">Qualify Now (فعل الآن)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
    case "Your Retail Shop is Qualified.";
        ?>

        <div class="halfwidth right qualifyBinary_Div">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Binary Shop (تفعيل رخصة البيع الشبكي)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $binary_qualify_fees001 . " / " . $binary_qualify_fees002 . " / " . $binary_qualify_fees003; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button qulify_bu001" href="index.php?page=categories&title=Qualify&bu=001">Qualify BU 001 (تفعيل رخصة 1)</a>
                            <a class="button inactive qulify_bu002" href="#">Qualify BU 002 (تفعيل رخصة 2)</a>
                            <a class="button inactive qulify_bu003" href="#">Qualify BU 003 (تفعيل رخصة 3)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="halfwidth left">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Retail Shop (تفعيل رخصة البيع بالتجزئة)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $retail_qualify_fees; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive" href="#">Qualify Now (فعل الآن)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
    case "Your first Business Unit (001) is Qualified.";
        ?>

        <div class="halfwidth right qualifyBinary_Div">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Binary Shop (تفعيل رخصة البيع الشبكي)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $binary_qualify_fees001 . " / " . $binary_qualify_fees002 . " / " . $binary_qualify_fees003; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive qulify_bu001" href="#">Qualify BU 001 (تفعيل رخصة 1)</a>
                            <a class="button qulify_bu002" href="index.php?page=categories&title=Qualify&bu=002">Qualify BU 002 (تفعيل رخصة 2)</a>
                            <a class="button qulify_bu003" href="index.php?page=categories&title=Qualify&bu=003">Qualify BU 003 (تفعيل رخصة 3)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="halfwidth left">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Retail Shop (تفعيل رخصة البيع بالتجزئة)
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees مصروفات التفعيل : </b><?php echo $retail_qualify_fees; ?> LE جنيه
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive" href="#">Qualify Now (فعل الآن)</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
    case "Your first (001) and second Business Unit (002) are Qualified.";
        ?>

        <div class="halfwidth right">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Binary Shop
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees: </b><?php echo $binary_qualify_fees001 . " / " . $binary_qualify_fees002 . " / " . $binary_qualify_fees003; ?> ECs
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive qulify_bu001" href="#">Qualify BU 001</a>
                            <a class="button inactive qulify_bu002" href="#">Qualify BU 002</a>
                            <a class="button qulify_bu003" href="index.php?page=categories&title=Qualify&bu=003">Qualify BU 003</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="halfwidth left">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Retail Shop
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees: </b><?php echo $retail_qualify_fees; ?> ECs
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive" href="#">Qualify Now</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
		case "Your first (001) and Third Business Unit (003) are Qualified.";
        ?>

        <div class="halfwidth right">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Binary Shop
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees: </b><?php echo $binary_qualify_fees001 . " / " . $binary_qualify_fees002 . " / " . $binary_qualify_fees003; ?> ECs
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive qulify_bu001" href="#">Qualify BU 001</a>
                            <a class="button qulify_bu002" href="index.php?page=categories&title=Qualify&bu=002">Qualify BU 002</a>
                            <a class="button inactive qulify_bu003" href="#">Qualify BU 003</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="halfwidth left">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <th>
                            Qualify Retail Shop
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <b>Membership Fees: </b><?php echo $retail_qualify_fees; ?> ECs
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="button inactive" href="#">Qualify Now</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
    case "Choose Payment Method.";
        ?>

        <div class="sep dotted"></div>

        <h2>Payment Details:</h2>

        <div id="ewallet_payment">
            <div class="payment_icon"><i class="fa fa-money fa-fw"></i></div>
            <h2>Pay using E Wallet</h2>
            <p class="error">You should have enough credit in your E Wallet.</p>
            <a class="button" href="index.php?page=qualify_shop&qualify=retail_ewallet">Pay & Renew</a>

        </div>

        <div id="creditcard_payment">
            <div class="payment_icon"><i class="fa fa-credit-card fa-fw"></i></div>
            <h2>Pay using Credit Card</h2>
            <p class="error">Credit Card Payments are not available at the time being.</p>
            <a class="button inactive" ><i class="fa fa-check-square fa-fw"></i>Pay & Renew</a>
        </div>

        <div class="sep dotted"></div>

    <?php
}
?>

<div class='sep'></div>

<!--<p>
    Binary Shops have all the benefits of Retail Shops in addition to it's own.
</p>-->
<p>
    تفعيل رخصة المتجر الالكتروني تمكنك من شراء منتجات لنفسك او للعملاء مع الحصول على العمولة أو الخصم الخاص بالموزعين
</p>

<!--<p>
    You can only qualify you Binary Shops in order.
</p>

<p>
    <i class="fa fa-circle fa-fw" style="color: #d79928;"></i>Available Option
</p>

<p>
    <i class="fa fa-circle fa-fw" style="color: #555;"></i>Unavailable Option
</p>-->


<?php $html_page->writeFooter(); ?>