<?php

function thisIsASupportedDomain() {
    return true;
}

function isSupported($domain) {
    global $supported_domains;
    $is_suported = false;
    foreach ($supported_domains as $supported_domain) {
        if ($domain == $supported_domain) {
            $is_suported = true;
        }
    }
    return true;
    return $is_suported;
}

function addtrailingSlash() {
    if (str_ireplace("index.php", "", $_SERVER['REQUEST_URI']) == $_SERVER['REQUEST_URI']) {
        header("location:" . $_SERVER['REQUEST_URI'] . "index.php");
    }
}

function forceSSL() {
    if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http") {
        $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('Location: ' . $url);
    }
}

?>