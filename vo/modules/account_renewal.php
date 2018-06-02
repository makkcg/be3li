<?php
$html_page->writeHeader();
$html_page->writeBody("Renewal تجديد الاشتراك",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));
?>


<?php
$last_renewal_date = $core->getMyLastRenewalDate($database_manager);
$next_renewal_date = $core->addToDate($last_renewal_date, 365);

if (strtotime($core->addToDate($last_renewal_date, 335)) < strtotime($core->getFormatedDate()) && $_GET['action'] == 'renew') {
    $sql = "SELECT ewallet FROM ir WHERE ir_id = '" . $_SESSION['ir_id'] . "' ";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $ewallet = $row['ewallet'];

    $sql = "SELECT renewal_fees from configuration";
    $result = $database_manager->query($sql);
    $row = mysqli_fetch_assoc($result);
    $fees = $row['renewal_fees'];

    if ($ewallet >= $fees) {
        $sql = "UPDATE ir SET last_renewal_date = '" . $core->getFormatedDate() . "', ewallet = ewallet - " . $fees . " "
                . " WHERE ir_id = '" . $_SESSION['ir_id'] . "'";
        $database_manager->query($sql);

        $sql = "INSERT INTO transaction (ir_id, type, date, amount, balance, comments) ";
        $sql .= " VALUES ('" . $_SESSION['ir_id'] . "', 'Account Renewal', '";
        $sql .= $core->getFormatedDateTime() . "', '" . (string) (0 - $fees) . "', '" . (string) ($ewallet - $fees) . "', 'ProShops')";
        $database_manager->query($sql);

        $error = "Account renewed successfully.";

        $last_renewal_date = $core->getMyLastRenewalDate($database_manager);
        $next_renewal_date = $core->addToDate($last_renewal_date, 365);
    } else {
        $error = "Insufficient fund.";
    }
}
?>
<div class="center">
    <br/><br/><br/><br/>
    <div class="countdown countdown-container container">
        <div class="clock row">
            <div class="clock-item clock-days countdown-time-value col-sm-6 col-md-3">
                <div class="wrap">
                    <div class="inner">
                        <div id="canvas-days" class="clock-canvas"></div>

                        <div class="clocktext">
                            <p class="val">0</p>
                            <p class="type-days type-time">DAYS</p>
                        </div><!-- /.text -->
                    </div><!-- /.inner -->
                </div><!-- /.wrap -->
            </div><!-- /.clock-item -->

            <div class="clock-item clock-hours countdown-time-value col-sm-6 col-md-3">
                <div class="wrap">
                    <div class="inner">
                        <div id="canvas-hours" class="clock-canvas"></div>

                        <div class="clocktext">
                            <p class="val">0</p>
                            <p class="type-hours type-time">HOURS</p>
                        </div><!-- /.text -->
                    </div><!-- /.inner -->
                </div><!-- /.wrap -->
            </div><!-- /.clock-item -->

            <div class="clock-item clock-minutes countdown-time-value col-sm-6 col-md-3">
                <div class="wrap">
                    <div class="inner">
                        <div id="canvas-minutes" class="clock-canvas"></div>

                        <div class="clocktext">
                            <p class="val">0</p>
                            <p class="type-minutes type-time">MINUTES</p>
                        </div><!-- /.text -->
                    </div><!-- /.inner -->
                </div><!-- /.wrap -->
            </div><!-- /.clock-item -->

            <div class="clock-item clock-seconds countdown-time-value col-sm-6 col-md-3">
                <div class="wrap">
                    <div class="inner">
                        <div id="canvas-seconds" class="clock-canvas"></div>

                        <div class="clocktext">
                            <p class="val">0</p>
                            <p class="type-seconds type-time">SECONDS</p>
                        </div><!-- /.text -->
                    </div><!-- /.inner -->
                </div><!-- /.wrap -->
            </div><!-- /.clock-item -->
        </div><!-- /.clock -->
    </div><!-- /.countdown-wrapper -->

    <p class="italic">Time to my the next Renewal!</p>
    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="js/kinetic.js"></script>
    <script type="text/javascript" src="js/jquery.final-countdown.js"></script>
    <script type="text/javascript" src="js/jquery.final-countdown.js"></script>
    <script type="text/javascript">
        $('document').ready(function () {
            'use strict';
            $('.countdown').final_countdown({
                'start': <?php echo strtotime($last_renewal_date); ?>,
                'end': <?php echo strtotime($next_renewal_date); ?>,
                'now': <?php echo strtotime($core->getFormatedDateTime()); ?>
            });
        });
    </script>
    <p id="error"><?php echo $error; ?></p>
    <?php
    if (strtotime($core->addToDate($last_renewal_date, 335)) < strtotime($core->getFormatedDate())) {
        ?>
        <div class="sep dotted"></div>

        <h2>Payment Details:</h2>

        <div id="ewallet_payment">
            <div class="payment_icon"><i class="fa fa-money fa-fw"></i></div>
            <h2>Pay using E Wallet:</h2>
            <p class="error">You should have enough credit in your E Wallet.</p>
            <a class="button" href="index.php?page=account_renewal&action=renew">Pay & Renew</a>

        </div>

        <div id="creditcard_payment">
            <div class="payment_icon"><i class="fa fa-credit-card fa-fw"></i></div>
            <h2>Pay using Credit Card:</h2>
            <p class="error">Credit Card Payments are not available at the time being.</p>
            <a class="button inactive" ><i class="fa fa-check-square fa-fw"></i>Pay & Renew</a>
        </div>

        <div class="sep dotted"></div>


        <?php
    }
    ?>
</div>

<?php $html_page->writeFooter(); ?>