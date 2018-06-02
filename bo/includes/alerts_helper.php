<?php

function prepareAlertData($data) {
    for ($i = 0; $i < count($data); $i++) {
        $data[$i]['id'] = $i + 1;
        $sql = "SELECT name FROM k8_top_page WHERE id = '" . $data[$i]['top_page_id'] . "' ";
        if ($result = mysql_query($sql)) {}else { error_log($sql);}
        $row = mysql_fetch_assoc($result);
        $data[$i]['top_page_name'] = $row['name'];
        $data[$i]['visit_page'] = "<a href='index.php?page=" . $data[$i]['top_page_id'] . "' target='_blank'>" . $_SESSION['main_language']->visit_page . "</a>";
    }
    return $data;
}

function showAlertsPage($number) {
    $g = new jqgrid();

    $function_name = "getAlerts" . $number;
    $table = $function_name();

    $actions = array("add" => false, "edit" => true, "delete" => false, "rowactions" => false,
        "export" => $export_enabled, "autofilter" => true, "search" => "advance");

    $options = getDefaultOptionsWithoutSearch($_SESSION['main_language']->alerts);
    $option['sortorder'] = "asc";

    $columns = array();

    $new_column = newIdColumn();
    $new_column['dbname'] = "id";
    $columns[] = $new_column;

    $new_column = newTextAreaColumn($_SESSION['main_language']->alert, "alert");
    $new_column['width'] = 600;
    $columns[] = $new_column;

    $new_column = newTextColumn($_SESSION['main_language']->page, "top_page_name");
    $columns[] = $new_column;

    $new_column = array(title => $_SESSION['main_language']->visit_page, name => "visit_page", dbname => "visit_page",
        width => 100, hidden => false, resizable => true, editable => false, export => false,
        "align" => "center", search => false);
    $columns[] = $new_column;

    displayGrid($g, $table, "", $columns, $options, $actions, array(), array());
}

function getAlerts000() {
    $data = array();
    $sql = "SELECT ra.top_page_id FROM k8_role_access ra
      		INNER JOIN k8_user u ON u.role_id = ra.role_id 
      		WHERE u.id = " . $_SESSION['id'] . " 
                AND ra.top_organization_id = " . $_SESSION['top_organization_id'] . " 
                AND ra.is_active = 1 
                AND ra.top_page_id IN (303, 305)";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    while ($row = mysql_fetch_assoc($result)) {
        $function_name = "getAlerts" . $row['top_page_id'];
        $data = array_merge($data, $function_name());
    }
    $data = prepareAlertData($data);
    return $data;
}

function getAlerts305() {
    $data = array();
    $sql = "SELECT id FROM k8_payment "
            . " WHERE is_paid = 0 "
            . " AND top_organization_id = " . $_SESSION['top_organization_id']
            . " AND is_active = 1 "
            . " AND due_date < NOW() ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    while ($row = mysql_fetch_assoc($result)) {
        $data[] = array(alert =>
            $_SESSION['main_language']->payment_due
            . " - " . $_SESSION['main_language']->payment . " : " . $row['id']
            , top_page_id => 216
        );
    }
    $data = prepareAlertData($data);
    return $data;
}

function getAlerts303() {
    $data = array();
    $sql = "SELECT 
    mt.id AS id,
    p.id AS product_id,
    p.min_limit AS min_limit,
    p.danger_limit AS danger_limit,
    mt.number AS number,
    DATE_SUB(DATE_ADD(mt.manufacture_date,
            INTERVAL p.validity DAY),
            INTERVAL p.expiration_notice_period DAY) AS expiration_notice_date,
    IF(p.validity = 0,
        '',
        DATE_ADD(mt.manufacture_date,
            INTERVAL p.validity DAY)) AS expiration_date,
    IF(p.validity = 0,
        0,
        IF(DATE_ADD(mt.manufacture_date,
                INTERVAL p.validity DAY) <= NOW(),
            1,
            0)) AS is_expired,
    goods_table.quantity AS quantity, goods_table.reserved AS reserved
FROM
    k8_product_patch mt
        LEFT OUTER JOIN
    k8_product p ON p.id = mt.product_id
        LEFT OUTER JOIN
    (SELECT 
        SUM(g.quantity) AS quantity, SUM(g.reserved) AS reserved, MAX(pp.id) AS product_patch_id
    FROM
        k8_goods g
    LEFT OUTER JOIN k8_product_patch pp ON pp.id = g.product_patch_id
    WHERE
        g.top_organization_id = 1
            AND g.is_active = 1
            AND pp.top_organization_id = " . $_SESSION['top_organization_id'] . " 
            AND pp.is_active = 1
    GROUP BY pp.id) AS goods_table ON goods_table.product_patch_id = mt.id
WHERE
    mt.top_organization_id = " . $_SESSION['top_organization_id'] . " 
    AND mt.is_active = 1";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    while ($row = mysql_fetch_assoc($result)) {
        if ($row['is_expired'] == 1 && $row['quantity'] + $row['reserved'] > 0) {
            $data[] = array(alert =>
                $_SESSION['main_language']->patch_expired
                . " - " . $_SESSION['main_language']->product . " : " . $row['product_id']
                . " - " . $_SESSION['main_language']->product_patch . " : " . $row['id']
                , top_page_id => 304
            );
        }
        if ($row['is_expired'] == 0 && $row['expiration_date'] != '' && isPastRegardlessTime($row['expiration_notice_date'])  && $row['quantity'] + $row['reserved'] > 0) {
            $data[] = array(alert =>
                $_SESSION['main_language']->product_patch_will_expire_soon
                . " - " . $_SESSION['main_language']->product . " : " . $row['product_id']
                . " - " . $_SESSION['main_language']->product_patch . " : " . $row['id']
                , top_page_id => 304
            );
        }
    }

    $sql = "SELECT 
    mt.id AS id,
    goods_table.quantity AS quantity,
    mt.min_limit AS min_limit,
    mt.danger_limit AS danger_limit
FROM
    k8_product mt
        LEFT OUTER JOIN
    k8_product_category pc ON pc.id = mt.product_category_id
        LEFT OUTER JOIN
    (SELECT 
        SUM(g.quantity) AS quantity,
            MAX(pp.product_id) AS product_id
    FROM
        k8_goods g
    LEFT OUTER JOIN k8_product_patch pp ON pp.id = g.product_patch_id
    WHERE
        g.top_organization_id = " . $_SESSION['top_organization_id'] . " 
            AND g.is_active = 1
            AND pp.top_organization_id = 1
            AND pp.is_active = 1
    GROUP BY pp.product_id) AS goods_table ON goods_table.product_id = mt.id
WHERE
    mt.top_organization_id = " . $_SESSION['top_organization_id'] . " 
    AND mt.is_active = 1";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    while ($row = mysql_fetch_assoc($result)) {
        if (!( $row['danger_limit'] == 0 && $row['min_limit'] == 0)) {
            if ($row['quantity'] < $row['min_limit'] && $row['quantity'] > $row['danger_limit']) {
                $data[] = array(alert =>
                    $_SESSION['main_language']->product_below_min_limit
                    . " - " . $_SESSION['main_language']->product . " : " . $row['id']
                    , top_page_id => 304
                );
            }
            if ($row['quantity'] < $row['danger_limit']) {
                $data[] = array(alert =>
                    $_SESSION['main_language']->product_below_danger_limit
                    . " - " . $_SESSION['main_language']->product . " : " . $row['id']
                    , top_page_id => 304
                );
            }
        }
    }
    $data = prepareAlertData($data);
    return $data;
}

?>