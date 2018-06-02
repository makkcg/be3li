<?php

include "languages/english.php";
include "languages/arabic.php";

function getLanguageObject($id) {
    $sql = "SELECT class FROM k8_top_language WHERE id = " . $id;
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    $class_name = $row['class'];
    return new $class_name();
}

function getGridLocaleFileName($id) {
    $sql = "SELECT grid_locale_file FROM k8_top_language WHERE id = " . $id;
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    return $row['grid_locale_file'];
}

?>