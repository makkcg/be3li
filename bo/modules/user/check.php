<?php
$myemail = $_POST['email'];
$mypassword = $_POST['password'];

$myemail = stripslashes($myemail);
$mypassword = stripslashes($mypassword);
$myemail = mysql_real_escape_string($myemail);
$mypassword = mysql_real_escape_string($mypassword);

$sql = "SELECT password FROM k8_user WHERE email='$myemail'"
            . " AND is_active = 1 ";
if ($result = mysql_query($sql)) {}else { error_log($sql);}
$row = mysql_fetch_assoc($result);
$mypassword = crypt($mypassword, $row['password']);

$sql = "SELECT u.id AS user_id, u.name AS user_name, u.top_language_id AS top_language_id, 
    u.top_organization_id AS top_organization_id, 
    o.logo AS top_organization_logo, o.cr AS top_organization_cr, o.tax_id AS top_organization_tax_id,
    o.name as top_organization_name, b.country AS main_branch_country, 
    o.website AS top_organization_website
    FROM k8_user u
LEFT OUTER JOIN k8_top_organization o ON u.top_organization_id = o.id
LEFT OUTER JOIN k8_branch b ON b.id = o.main_branch_id
WHERE u.email='$myemail' and u.password='$mypassword'"
            . " AND u.is_active = 1 ";
if ($result = mysql_query($sql)) {}else { error_log($sql);}
$count = mysql_num_rows($result);

if ($count == 1) {
    $row = mysql_fetch_assoc($result);
    $_SESSION["secret"] = $secret_key;
    $_SESSION['last_activity'] = time();
    $_SESSION["id"] = $row['user_id'];
    $_SESSION["name"] = $row['user_name'];
    $_SESSION["top_organization_id"] = $row['top_organization_id'];
    $_SESSION["top_organization_logo"] ="/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAeAAD/4QNtaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6QjQ4NzFGMkQ5RTFDRTgxMUJCRDk4ODE4OERBOERFM0QiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDBGOTg3MDAxQzlFMTFFOEI1OEVDM0UwRjczOTg2NUYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDBGOTg2RkYxQzlFMTFFOEI1OEVDM0UwRjczOTg2NUYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QjQ4NzFGMkQ5RTFDRTgxMUJCRDk4ODE4OERBOERFM0QiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QjQ4NzFGMkQ5RTFDRTgxMUJCRDk4ODE4OERBOERFM0QiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94Onht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";// base64_encode($row['top_organization_logo']);
    $_SESSION["top_organization_name"] = $row['top_organization_name'];
    $_SESSION["top_organization_cr"] = $row['top_organization_cr'];
    $_SESSION["top_organization_tax_id"] = $row['top_organization_tax_id'];
    $_SESSION["top_organization_website"] = $row['top_organization_website'];
    $_SESSION["main_branch_country"] = $row['main_branch_country'];
    $main_language_id = $row['top_language_id'];
    
    $sql = "SELECT details FROM k8_configuration "
            . " WHERE variable = 'top_theme_id' "
            . " AND top_organization_id = " . $_SESSION['top_organization_id'];
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    $top_theme_id = $row['details'];
    
    $sql = " SELECT url FROM k8_top_theme WHERE id = " . $top_theme_id;
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    $top_theme_url = $row['url'];
    $_SESSION["top_theme_url"] = $top_theme_url;
    
    $sql = "SELECT details FROM k8_configuration "
            . " WHERE variable = 'session_time' "
            . " AND top_organization_id = " . $_SESSION['top_organization_id'];
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    $_SESSION["session_minutes"] = $row['details'];
    
    $_SESSION['main_language'] = getLanguageObject($main_language_id);
    $_SESSION['grid_locale_file'] = getGridLocaleFileName($main_language_id);
    if ($main_language_id == "1"){
        $secondary_language_id = "2";
    }else{
        $secondary_language_id = "1";
    }
    $_SESSION['secondary_language'] = getLanguageObject($secondary_language_id);
    
    $sql = "SELECT details FROM k8_configuration "
            . " WHERE variable = 'timezone' "
            . " AND top_organization_id = " . $_SESSION['top_organization_id'];
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    $_SESSION["timezone"] = $row['details'];
    setDefaultTimeZone($_SESSION['timezone']);
    
    $date = getFormatedDateTime();
    $sql = "INSERT INTO k8_time_log VALUES ( NULL , '" . $_SESSION["id"] . "', '" . $date . "', NULL, '" . $_SESSION['timezone'] . "')";
    if (!mysql_query($sql)) { error_log($sql); }
    
    if (thisIsASupportedDomain()) {
        header("location:index.php");
    } else {
        header("location:http://cloud-sw.com/ERP/remote/not_supported.php?domain=" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    }
} else {
    header("location:index.php?error=wrong_pass");
}
?>