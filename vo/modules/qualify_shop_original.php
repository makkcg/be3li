<?php
$core->checkEwalletPassword("qualify_shop");

$html_page->writeHeader();
$html_page->writeBody("Qualify Shop");

$sql = "SELECT binary_qualify_fees001, binary_qualify_fees002, binary_qualify_fees003, retail_qualify_fees FROM configuration ";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);

$binary_qualify_fees001 = $row['binary_qualify_fees001'];
$binary_qualify_fees002 = $row['binary_qualify_fees002'];
$binary_qualify_fees003 = $row['binary_qualify_fees003'];
$retail_qualify_fees = $row['retail_qualify_fees'];

$sql = "SELECT is_qualified, code FROM bu WHERE ir_id = '" . $_SESSION['ir_id'] . "' order by code ASC ";
$result = $database_manager->query($sql);

$case = "All your Business Units are Qualified.";

$row = mysqli_fetch_assoc($result);
if ($row['is_qualified'] == 0) {
    $case = "Non of your Business Units is Qualified.";
}
if ($row['is_qualified'] == 1) {
    $case = "Your Retail Shop is Qualified.";
}

if ($row['is_qualified'] == 2) {
    $row = mysqli_fetch_assoc($result);
    if ($row['is_qualified'] == 0) {
        $case = "Your first Business Unit (001) is Qualified.";
    } else {
        $row = mysqli_fetch_assoc($result);
        if ($row['is_qualified'] == 0) {
            $case = "Your first (001) and second Business Unit (002) are Qualified.";
        }
    }
}
?>


<?php
if ($_GET['qualify'] == "retail_ewallet" && $case == "Non of your Business Units is Qualified.") {
    $sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $ewallet = $row['ewallet'];

    if ($ewallet < $retail_qualify_fees) {
        echo "<p id='error'>Insufficient Funds.</p>";
    } else {

        // Take Funds

        $sql = "UPDATE ir SET ewallet = (ewallet - " . $retail_qualify_fees . ") WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
        $database_manager->query($sql);

        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Qualify Retail Shop', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $retail_qualify_fees) . "', '" . (string) ($ewallet - $retail_qualify_fees) . "', 'ProShops')";
        $database_manager->query($sql);

        // UPDATE IR & BU

        $sql = "UPDATE bu SET is_qualified = 1 WHERE ir_id = '" . $_SESSION['ir_id'] . "'"
                . " AND code = '001' ";
        $database_manager->query($sql);

        $sql = "UPDATE ir SET  qualification_date = '" . $core->getFormatedDateTime() . "' WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);

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
                            <a class="button inactive" href="#">Qualify BU 001</a>
                            <a class="button inactive" href="#">Qualify BU 002</a>
                            <a class="button inactive" href="#">Qualify BU 003</a>
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
    case "Non of your Business Units is Qualified.";
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
                            <a class="button" href="index.php?page=categories&title=Qualify&bu=001">Qualify BU 001</a>
                            <a class="button inactive" href="#">Qualify BU 002</a>
                            <a class="button inactive" href="#">Qualify BU 003</a>
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
                            <a class="button" href="index.php?page=qualify_shop&qualify=retail">Qualify Now</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
        break;
    case "Your Retail Shop is Qualified.";
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
                            <a class="button" href="index.php?page=categories&title=Qualify&bu=001">Qualify BU 001</a>
                            <a class="button inactive" href="#">Qualify BU 002</a>
                            <a class="button inactive" href="#">Qualify BU 003</a>
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
    case "Your first Business Unit (001) is Qualified.";
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
                            <a class="button inactive" href="#">Qualify BU 001</a>
                            <a class="button" href="index.php?page=categories&title=Qualify&bu=002">Qualify BU 002</a>
                            <a class="button inactive" href="#">Qualify BU 003</a>
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
                            <a class="button inactive" href="#">Qualify BU 001</a>
                            <a class="button inactive" href="#">Qualify BU 002</a>
                            <a class="button" href="index.php?page=categories&title=Qualify&bu=003">Qualify BU 003</a>
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

<p>
    Binary Shops have all the benefits of Retail Shops in addition to it's own.
</p>

<p>
    You can only qualify you Binary Shops in order.
</p>

<p>
    <i class="fa fa-circle fa-fw" style="color: #d79928;"></i>Available Option
</p>

<p>
    <i class="fa fa-circle fa-fw" style="color: #555;"></i>Unavailable Option
</p>


<?php $html_page->writeFooter(); ?>