<?php

function getBranchRow($branch_id) {
    if (substr($_GET['printout'], -7, 3) != "_tr") {
        $sql_branch = "SELECT address1 AS b_address1, address2 AS b_address2, city AS b_city, country AS b_country, CONCAT(tel1, ' ', tel2) AS b_tel ";
    } else {
        $sql_branch = "SELECT address1_tr AS b_address1, address2_tr AS b_address2, "
                . " city_tr AS b_city, country_tr AS b_country, CONCAT(tel1, '-', tel2) AS b_tel ";
    }
    $sql_branch .= " FROM k8_branch ";
    $sql_branch .= " WHERE id = " . $branch_id . " ";
    $sql_branch .= " AND top_organization_id = " . $_SESSION['top_organization_id'] . " ";
    $sql_branch .= " AND is_active = 1 ";
    $result_branch = mysql_query($sql_branch);
    return mysql_fetch_assoc($result_branch);
}

function getOrganizationRow($organization_id) {
    if (substr($_GET['printout'], -7, 3) != "_tr") {
        $sql_organization = "SELECT name AS o_name, cr AS o_cr, tax_id AS o_tax_id, sales_email "
                . "AS o_sales_email, website AS o_website, main_branch_id AS o_main_branch_id  ";
    } else {
        $sql_organization = "SELECT name_tr AS o_name, cr AS o_cr, tax_id AS o_tax_id, sales_email "
                . "AS o_sales_email, website AS o_website, main_branch_id AS o_main_branch_id ";
    }
    $sql_organization .= " FROM k8_top_organization ";
    $sql_organization .= " WHERE id = " . $organization_id . " ";
    $result_organization = mysql_query($sql_organization);
    return mysql_fetch_assoc($result_organization);
}

?>
