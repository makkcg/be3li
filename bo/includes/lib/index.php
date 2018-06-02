<?php

include "includes/lib/inc/jqgrid_dist.php";
include "includes/config.php";
include "includes/mailer.php";
include "includes/languages.php";
include "includes/helper.php";
include "includes/alerts_helper.php";
include "includes/domain_support.php";

session_start();
dbConnect();

if(isset($_SESSION) && isset($_SESSION['timezone']) && $_SESSION['timezone'] != ''){
    setDefaultTimeZone($_SESSION['timezone']);
}

if (isset($_POST) && isset($_POST['page']) && $_POST['page'] == "check") {
    include "modules/user/check.php";
} elseif (
        (!isset($_SESSION['secret']) || $_SESSION['secret'] != $secret_key ) ||
        ( isset($_GET) && isset($_GET['page']) && ( $_GET['page'] == "1" ) ) ||
        ( isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity'] > ( $_SESSION["session_minutes"] * 60 ) ) )
) {
    include "modules/user/login.php";
} else {
    $_SESSION['last_activity'] = time();
    if (isset($_GET['page']) && isset($_GET['page']) && ( $_GET['page'] == "2" || $_GET['page'] == "3" )) {
        $_SESSION['top_role_access_type_id'] = "0";
        showPageWithoutCheck($_GET['page']);
    } elseif (isset($_GET['page']) && isset($_GET['page']) && $_GET['page'] != "") {
        if (!showPage($_GET['page'])) {
            $_GET['page'] = 1;
            include "includes/widgets.php";
        }
    } else {
        include "includes/widgets.php";
    }
}

?>