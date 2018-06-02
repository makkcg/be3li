<?php

include "modules/helpers/administrator_helper.php";

$g1 = new jqgrid();

$table1 = "k8_user";

$select1 = "SELECT mt.id, mt.name, mt.name_tr, mt.email, r.name AS role_id, "
        . " mt.password, tl.name AS top_language_id,  "
        . "'' AS new_password , '' AS confirm_password";
$select1 .= getDefaultSelectLines1($table1);
$select1 .= " LEFT OUTER JOIN k8_role r ON r.id = mt.role_id ";
$select1 .= " LEFT OUTER JOIN k8_top_language tl ON tl.id = mt.top_language_id ";
$select1 .= getDefaultSelectLines2();

$actions1 = getDefaultActions();

$conditions1 = getDefaultConditions();

$options1 = getDefaultOptions($_SESSION['main_language']->users);

$conditions1 = getDefaultConditions();

$columns1 = array();

$new_column = newIdColumn();
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn($_SESSION['main_language']->name, "mt.name");
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn($_SESSION['main_language']->email, "mt.email");
$new_column["show"] = array("list" => true, "add" => true, "edit" => false, "view" => true);
$columns1[] = $new_column;

$new_column = newMandatorySelectColumn($_SESSION['main_language']->role, "role_id", "r.name", "k8_role", $g1);
$columns1[] = $new_column;

$new_column = newMandatorySelectColumn($_SESSION['main_language']->main_language, "top_language_id", "tl.name", "k8_top_language", $g1);
//$new_column['editable'] = false;
$new_column["show"] = array("list" => true, "add" => true, "edit" => true, "view" => true);
$columns1[] = $new_column;

$new_column = newTextColumn($_SESSION['main_language']->password, "mt.password");
$new_column["width"] = "400";
$new_column["editable"] = "false";
$columns1[] = $new_column;

$new_column = newPasswordColumn($_SESSION['main_language']->new_password, "new_password");
$new_column["show"] = array("list" => false, "add" => true, "edit" => true, "view" => false);
$new_column['editoptions']['defaultvalue'] = "";
$columns1[] = $new_column;

$new_column = newPasswordColumn($_SESSION['main_language']->confirm_password, "confirm_password");
$new_column["show"] = array("list" => false, "add" => true, "edit" => true, "view" => false);
$new_column['editoptions']['defaultvalue'] = "";
$columns1[] = $new_column;

addDefaultColumns($columns1, $g1);

$events1 = array(on_insert => array("insertRowInG1", null, false), on_after_insert => array("afterInsertRowInG1",
        null, true), on_update => array("updateRowInG1", null, false), on_delete => array("deleteRowInG1", null, false));

function insertRowInG1($data) {
    $data['params']['top_language_id'] = "1";
    if ($data['params']['new_password'] == '') {
        phpgrid_error($_SESSION['main_language']->password_is_manditory_at_this_stage);
    }
    if ($data['params']['new_password'] != $data['params']['confirm_password']) {
        phpgrid_error($_SESSION['main_language']->password_mismatch);
    }

    $sql = "SELECT * FROM k8_user WHERE email='" . $data['params']['email'] . "'";
    // Not organization or is_active check here because email is unique
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $count = mysql_num_rows($result);
    if ($count >= 1) {
        phpgrid_error($_SESSION['main_language']->email_address_already_exists);
    }

    defaultInsertEventHandler($data, "k8_user");
    $salt = generateSalt();
    $data['params']['password'] = crypt($data['params']['new_password'], $salt);
    mysql_query("INSERT INTO k8_user (name, name_tr, email, password, role_id, top_organization_id, created, created_by, top_language_id) 
		VALUES('" . $data['params']['name'] . "', '" . $data['params']['name_tr'] . "', '" . $data['params']['email'] . "', '" . $data['params']['password']
            . "', '" . $data['params']['role_id'] . "', '" . $data['params']
            ['top_organization_id'] . "', '" . $data['params']['created'] . "', '" . $data['params']['created_by'] . "', '" . $data['params']['top_language_id'] . "')");
}

function afterInsertRowInG1($data) {
    defaultAfterInsertEventHandler($data, "k8_user");
}

function updateRowInG1($data) {
    $salt = generateSalt();
    $sql = "SELECT mt.created_by AS created_by FROM k8_user mt WHERE id = " . $data['id']
            . " AND mt.top_organization_id = " . $_SESSION['top_organization_id']
            . " AND mt.is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_edit_administrator_or_super_user);
    }
    
    defaultUpdateEventHandler($data, "k8_user");
    if ($data["params"]["new_password"] != '') {
        if ($data['params']['new_password'] != $data['params']['confirm_password']) {
            phpgrid_error($_SESSION['main_language']->password_mismatch);
        } else {
            $data["params"]["password"] = crypt($data["params"]["new_password"], $salt);
            mysql_query("UPDATE k8_user SET name = '" . $data['params']['name'] . "', name_tr = '" .
                    $data['params']['name_tr'] . "'
		, password = '" . $data["params"]["password"] . "', role_id = '" . $data["params"]["role_id"]
                    . "', modified = '" . $data["params"]["modified"] . "', modified_by = 
		'" . $data["params"]["modified_by"] . "', top_language_id = '".$data['params']['top_language_id'] . "'
		WHERE id = '" . $data['id'] . "'
		");
        }
    } else {
        mysql_query("UPDATE k8_user SET name = '" .
                $data['params']['name'] . "', name_tr = '" .
                $data['params']['name_tr'] . "', role_id = '" . $data["params"]["role_id"] .
                "', modified = '" . $data["params"]["modified"] . "', 
                modified_by = '" . $data["params"]["modified_by"] . "', top_language_id = '".$data['params']['top_language_id'] . "'
                WHERE id = '" . $data['id'] . "'
	");
    }
}

function deleteRowInG1($data) {
    $sql = "SELECT mt.created_by AS created_by FROM k8_user mt WHERE id = " . $data['id'] . " "
            . " AND mt.top_organization_id = " . $_SESSION['top_organization_id']
            . " AND mt.is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_delete_administrator_or_super_user);
    }
    defaultDeleteEventHandler($data, "k8_user");
}

displayGrid($g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1);
?>