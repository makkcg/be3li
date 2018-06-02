<?php

function userAccessDuplicate($user_id, $branch_id, $user_access_id = "''"){
   $sql = "SELECT * FROM k8_user_branch_access WHERE user_id = " . $user_id . " "
            . " AND id != " . $user_access_id . " "
            . "AND branch_id = " . $branch_id . " "
            . "AND top_organization_id = " . $_SESSION['top_organization_id'] . " "
            . "AND is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $count = mysql_num_rows($result);
    if ($count == 0){
        return false;
    }
    return true;
}

function roleAccessDuplicate($role_id, $top_page_id, $role_access_id = "''"){
   $sql = "SELECT * FROM k8_role_access WHERE role_id = " . $role_id . " "
            . "AND top_page_id = " . $top_page_id . " "
           . " AND id != " . $role_access_id . " "
            . "AND top_organization_id = " . $_SESSION['top_organization_id'] . " "
            . "AND is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $count = mysql_num_rows($result);
    if ($count == 0){
        return false;
    }
    return true;
}

?>