<?php
include "printouts/printouts_helper.php";

$sales_order_id = $_GET['id'];

$sql_payments = " SELECT ROUND(SUM(credit),2) AS total_paid FROM k8_payment "
        . " WHERE sales_invoice_id = '" . $sales_order_id . "' "
        . " AND is_paid = 1 "
        . " AND top_organization_id = " . $_SESSION['top_organization_id'] . " "
        . " AND is_active = 1 ";

$result_payments = mysql_query($sql_payments);
$row_payments = mysql_fetch_assoc($result_payments);
$total_paid = $row_payments['total_paid'];
if ($total_paid == ""){
    $total_paid = 0;
}

$select1 = "SELECT mt.id AS id, mt.is_invoice AS is_invoice, mt.invoice_number AS invoice_number, "
        . " mt.invoice_date AS invoice_date, mt.delivery_date AS delivery_date, p.name AS partner_id, "
        . " pc.name AS partner_contact_id, padd.name AS partner_address_id, "
        . " b.id AS branch_id, "
        . " pt.name AS payment_term_id, sp.name AS sales_person_id, "
        . " ROUND(mt.subtotal,2) AS subtotal, ROUND(mt.discount,2) AS discount, ROUND(mt.tax,2) AS tax, ROUND(mt.total,2) AS total ";
$select1 .= " FROM k8_sales_invoice mt";
$select1 .= " LEFT OUTER JOIN k8_partner p ON p.id = mt.partner_id ";
$select1 .= " LEFT OUTER JOIN k8_partner_contact pc ON pc.id = mt.partner_contact_id ";
$select1 .= " LEFT OUTER JOIN k8_partner_address padd ON padd.id = mt.partner_address_id ";
$select1 .= " LEFT OUTER JOIN k8_branch b ON b.id = mt.branch_id ";
$select1 .= " LEFT OUTER JOIN k8_user sp ON sp.id = mt.sales_person_id ";
$select1 .= " LEFT OUTER JOIN k8_payment_term pt ON pt.id = mt.payment_term_id ";
$select1 .= " WHERE mt.top_organization_id = " . $_SESSION['top_organization_id'] . " ";
$select1 .= " AND mt.is_active = 1 ";
$select1 .= " AND mt.id = " . $sales_order_id;

if ($result1 = mysql_query($select1)) {}else { error_log($select1);}
$row1 = mysql_fetch_assoc($result1);

$row_branch = getBranchRow($row1['branch_id']);
$row_organization = getOrganizationRow($_SESSION['top_organization_id']);

$select2 = "SELECT mt.id AS id, p.manual_sku AS manual_sku, p.name AS product_name, mt.sales_invoice_id AS sales_invoice_id, "
        . " p.id AS product_id, mt.quantity AS quantity, mt.line_number AS line_number, ROUND(mt.subtotal,2) AS subtotal, tx.name AS tax_name, "
        . " ROUND(mt.line_total,2) AS line_total, ROUND(mt.manager_discount,2) AS line_discount, ROUND(mt.tax,2) AS tax, mt.price AS unit_price "
        . " FROM  k8_sales_invoice_line mt ";
$select2 .= " LEFT OUTER JOIN k8_product p ON p.id = mt.product_id ";
$select2 .= " LEFT OUTER JOIN k8_tax tx ON tx.id = p.tax_id ";
$select2 .= " WHERE mt.top_organization_id = " . $_SESSION['top_organization_id'] . " "
        . " AND mt.is_active = 1 ";
$select2 .= " AND mt.sales_invoice_id = " . $row1['id'];

if ($result2 = mysql_query($select2)) {}else { error_log($select2);}

$select3 = "SELECT round(SUM(credit),2) AS total_paid FROM k8_payment "
        . " WHERE top_organization_id = ". $_SESSION['top_organization_id'] . " "
        . " AND is_active = 1 "
        . " AND is_paid = 1 "
        . " AND sales_invoice_id = " . $row1['id'];
if ($result3 = mysql_query($select3)) {}else { error_log($select3);}
$row3 = mysql_fetch_assoc($result3);
$total_paid = 0 + $row3['total_paid'];

?>

<div class="a4_col1">
    <table>
        <tr>
            <th><?php echo $language->payment_term; ?> : </th>
            <td><?php echo $row1['payment_term_id']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->total_paid; ?> : </th>
            <td><?php echo $total_paid; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->balance_due; ?> : </th>
            <td><?php echo $row1['total'] - $total_paid; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->sales_person; ?> : </th>
            <td><?php echo $row1['sales_person_id']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->customer; ?> : </th>
            <td><?php echo $row1['partner_id']; ?></td>
        </tr>
        <?php if($row1['partner_contact_id'] != ""){ ?>
        <tr>
            <th><?php echo $language->customer_contact; ?> : </th>
            <td><?php echo $row1['partner_contact_id']; ?></td>
        </tr>
        <?php } ?>
        <?php if($row1['partner_address_id'] != ""){ ?>
        <tr>
            <th><?php echo $language->customer_address; ?> : </th>
            <td><?php echo $row1['partner_address_id']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
<div class="a4_col2">
    <table>
        <tr>
            <th><?php echo $language->document_number; ?> : </th>
            <td><?php echo $row1['invoice_number']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->date_time; ?> : </th>
            <td><?php echo $row1['invoice_date']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->delivery_date; ?> : </th>
            <td><?php echo $row1['delivery_date']; ?></td>
        </tr>
        <?php if($row_organization['o_cr'] != ""){ ?>
        <tr>
            <th><?php echo $language->c_r ?> : </th>
            <td><?php echo $row_organization['o_cr']; ?></td>
        </tr>
        <?php } ?>
        <?php if($row_organization['o_tax_id'] != ""){ ?>
        <tr>
            <th><?php echo $language->tax_id ?> : </th>
            <td><?php echo $row_organization['o_tax_id']; ?></td>
        </tr>
        <?php } ?>
    </table>
    <table>
        <tr>
            <th>
                <?php
                echo $row_organization['o_name'] . "</th></tr><tr><th>" . $row_branch['b_country'] . ", " . $row_branch['b_city'] . ", " .
                $row_branch['b_address1'] . " " . $row_branch['b_address2'] . "</th></tr><tr><th>" . $row_branch['b_tel'] . "</th></tr><tr><th>" .
                $row_organization['o_website'];
                ?>
            </th>
        </tr>
    </table>
</div>

<div class="clear"></div><br>

<table class="a4_lines"> 
    
    <tr>
        <th><?php echo $language->hash; ?></th>
        <th><?php echo $language->product; ?></th>
        <th><?php echo $language->unit_price; ?></th>
        <th><?php echo $language->quantity; ?></th>
        <th><?php echo $language->subtotal; ?></th>
        <?php if ($row1['discount'] > 0){ ?><th><?php echo $language->discount; ?></th><?php } ?>
        <?php if ($row1['tax'] > 0){ ?><th><?php echo $language->tax; ?></th><?php } ?>
        <th><?php echo $language->total; ?></th>
    </tr>
    <?php
    $new_number = 0;
    while ($row2 = mysql_fetch_assoc($result2)) {
        $new_number++;
        ?>
        <tr>
            <td><?php echo $new_number; ?></td>
            <td><?php echo $row2['product_name']; ?></td>
            <td><?php echo $row2['unit_price']; ?></td>
            <td><?php echo $row2['quantity']; ?></td>
            <td><?php echo $row2['subtotal']; ?></td>
            <?php if ($row1['discount'] > 0){ ?><td><?php echo $row2['line_discount']; ?></td><?php } ?>
            <?php if ($row1['tax'] > 0){ ?><td><?php echo "(".$row2['tax_name'].") ".$row2['tax']; ?></td><?php } ?>
            <td><?php echo $row2['line_total']; ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <?php if ($row1['tax'] > 0){ ?><td></td><?php } ?>
        <?php if ($row1['discount'] > 0){ ?><td></td><?php } ?>
        <td class="a4_summary"><?php echo $language->subtotal; ?></td class="a4_summary">
        <td class="a4_summary"><?php echo $row1['subtotal'] ?></td class="a4_summary">
    </tr>
    
    <?php if ($row1['discount'] > 0){ ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <?php if ($row1['tax'] > 0){ ?><td></td><?php } ?>
        <td></td>
        <td class="a4_summary"><?php echo $language->discount . " (" . round($row1['discount'] / $row1['subtotal'],2) * 100 . "%)"; ?></td class="a4_summary">
        <td class="a4_summary"><?php echo $row1['discount'] ?></td class="a4_summary">
    </tr>
    <?php } ?>
    <?php if ($row1['tax'] > 0){ ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <?php if ($row1['discount'] > 0){ ?><td></td><?php } ?>
        <td class="a4_summary"><?php echo $language->tax; ?></td class="a4_summary">
        <td class="a4_summary"><?php echo $row1['tax'] ?></td class="a4_summary">
    </tr>
    <?php } ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <?php if ($row1['tax'] > 0){ ?><td></td><?php } ?>
        <?php if ($row1['discount'] > 0){ ?><td></td><?php } ?>
        <td class="a4_summary"><?php echo $language->total; ?></td class="a4_summary">
        <td class="a4_summary"><?php echo $row1['total'] . " " . $language->egp; ?></td class="a4_summary">
    </tr>
</table>