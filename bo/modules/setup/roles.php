<?php

include "modules/helpers/administrator_helper.php";

$g1 = new jqgrid();

$table1 = "k8_role";

$select1 = "SELECT mt.id AS id , mt.name AS name";
$select1 .= getDefaultSelectLines1($table1);
$select1 .= getDefaultSelectLines2();

$actions1 = getDefaultActions();

$options1 = getDefaultOptions($_SESSION['main_language']->roles);

$conditions1 = getDefaultConditions();

$columns1 = array();

$new_column = newIdColumn();
$columns1[] = $new_column;

$new_column = newMandatoryTextColumn($_SESSION['main_language']->name, "mt.name");
$columns1[] = $new_column;

addDefaultColumns($columns1, $g1);

$events1 = getDefaultEvents("1");

function insertRowInG1($data) {
    defaultInsertEventHandler($data);
}

function updateRowInG1($data) {
    $sql = "SELECT mt.created_by AS created_by FROM k8_role mt WHERE id = " . $data['id']
            . " AND top_organization_id = " . $_SESSION['top_organization_id'] 
            . " AND is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_edit_super_user);
    }
    defaultUpdateEventHandler($data, "k8_role");
}

function afterInsertRowInG1($data) {
    defaultAfterInsertEventHandler($data, "k8_role");
}

function afterUpdateRowInG1($data) {
}

function deleteRowInG1($data) {
    $sql = "SELECT mt.created_by AS created_by FROM k8_role mt WHERE id = " . $data['id']
            . " AND top_organization_id = " . $_SESSION['top_organization_id'] 
            . " AND is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_delete_super_user);
    }
    defaultDeleteEventHandler($data, "k8_role");
}

$g2 = new jqgrid();

$table2 = "k8_role_access";

$id = intval($_GET["rowid"]);

$select2 = "SELECT mt.id AS id, mt.role_id AS role_id, CONCAT(f.name, ' - ', p.name) AS top_page_id, rat.name AS top_role_access_type_id ";
$select2 .= getDefaultSelectLines1($table2);
$select2 .= "LEFT OUTER JOIN k8_top_role_access_type rat ON rat.id = mt.top_role_access_type_id  
             LEFT OUTER JOIN k8_top_page p ON p.id = mt.top_page_id 
             LEFT OUTER JOIN k8_top_folder f ON f.id = p.top_folder_id ";
$select2 .= getDefaultSelectLines2();
$select2 .= "AND mt.role_id = " . $id;

$actions2 = getDefaultActions();

$options2 = getDefaultOptions($_SESSION['main_language']->role_access);
$options2['sortname'] = "top_page_id";
$options2['sortorder'] = "asc";

$conditions2 = getDefaultConditions();

$columns2 = array();

$new_column = newIdColumn();
$columns2[] = $new_column;

$new_column = newHiddenColumn("mt.role_id");
$columns2[] = $new_column;

$custom_sql = "Select distinct p.id as k, CONCAT(f.name, ' - ', p.name) AS v FROM k8_role_access ra "
        . " INNER JOIN k8_top_page p ON p.id = ra.top_page_id "
        . " INNER JOIN k8_role r ON r.id = ra.role_id "
        . " INNER JOIN k8_top_folder f ON f.id = p.top_folder_id "
        . " WHERE r.created_by = 1 AND r.top_organization_id = '" . $_SESSION['top_organization_id'] . "' "
        . " ORDER BY p.id ASC ";
$new_column = newMandatorySelectColumn($_SESSION['main_language']->page, "top_page_id", "CONCAT(f.name, ' - ', p.name)", "k8_top_page", $g2, $custom_sql);
$columns2[] = $new_column;

$new_column = newMandatorySelectColumn($_SESSION['main_language']->access_type, "top_role_access_type_id", "rat.name", "k8_top_role_access_type", $g2);
$columns2[] = $new_column;

addDefaultColumns($columns2, $g2);

$events2 = getDefaultEvents("2");

function insertRowInG2($data) {
    $id = intval($_GET["rowid"]);
    $data["params"]["role_id"] = $id;
    $sql = "SELECT mt.created_by AS created_by FROM k8_role mt WHERE id = " . $id
            . " AND top_organization_id = " . $_SESSION['top_organization_id'] 
            . " AND is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_edit_super_user_access);
    }
    $id = intval($_GET["rowid"]);
    $data["params"]["role_id"] = $id;
    if(roleAccessDuplicate($data["params"]["role_id"], $data["params"]["top_page_id"])){
        phpgrid_error($_SESSION['main_language']->record_already_defined);
    }
    defaultInsertEventHandler($data);
}

function afterInsertRowInG2($data) {
    defaultAfterInsertEventHandler($data, "k8_role_access");
}

function updateRowInG2($data) {
    $sql = "SELECT r.created_by AS created_by FROM k8_role_access mt "
            . " INNER JOIN k8_role r ON r.id = mt.role_id "
            . " WHERE mt.id = " . $data['id']
            . " AND mt.top_organization_id = " . $_SESSION['top_organization_id'] 
            . " AND mt.is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_edit_super_user_access);
    }
    if(roleAccessDuplicate($data["params"]["role_id"], $data["params"]["top_page_id"], $data["id"])){
        phpgrid_error($_SESSION['main_language']->record_already_defined);
    }
    defaultUpdateEventHandler($data, "k8_role_access");
}

function afterUpdateRowInG2($data) {
}

function deleteRowInG2($data) {
    $sql = "SELECT r.created_by AS created_by FROM k8_role_access mt "
            . " INNER JOIN k8_role r ON r.id = mt.role_id "
            . " WHERE mt.id = " . $data['id']
            . " AND mt.top_organization_id = " . $_SESSION['top_organization_id'] 
            . " AND mt.is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['created_by'] == '1') {
        phpgrid_error($_SESSION['main_language']->cannot_delete_super_user_access);
    }
    defaultDeleteEventHandler($data, "k8_role_access");
}

displayTwoGrids($g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, 
        $g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2);
?>