<?php
include "printouts/printouts_helper.php";

$payment_id = $_GET['id'];

$sql = "SELECT mt.id, mt.receipt_number AS receipt_number, si.invoice_number AS invoice_number, "
        . " mt.payment_date AS payment_date, ROUND(credit,2) AS credit, pm.name AS payment_method_id, rb.name AS received_by_id, "
        . " mt.check_number AS check_number, p.name AS partner_id, "
        . " pc.name AS partner_contact_id, padd.name AS partner_address_id, "
        . " b.id AS branch_id FROM k8_payment mt ";
$sql .= " LEFT OUTER JOIN k8_sales_invoice si ON si.id = mt.sales_invoice_id ";
$sql .= " LEFT OUTER JOIN k8_payment_method pm ON pm.id = mt.payment_method_id ";
$sql .= " LEFT OUTER JOIN k8_partner p ON p.id = si.partner_id ";
$sql .= " LEFT OUTER JOIN k8_partner_contact pc ON pc.id = si.partner_contact_id ";
$sql .= " LEFT OUTER JOIN k8_partner_address padd ON padd.id = si.partner_address_id ";
$sql .= " LEFT OUTER JOIN k8_branch b ON b.id = si.branch_id ";
$sql .= " LEFT OUTER JOIN k8_user rb ON rb.id = mt.received_by_id ";
$sql .= " WHERE mt.top_organization_id = " . $_SESSION['top_organization_id'] . " ";
$sql .= " AND mt.is_active = 1 ";
$sql .= " AND mt.id = " . $payment_id;

if ($result = mysql_query($sql)) {}else { error_log($sql);}
$row = mysql_fetch_assoc($result);

$row_branch = getBranchRow($row['branch_id']);
$row_organization = getOrganizationRow($_SESSION['top_organization_id']);

?>

<div class="a4_col1">
    <table>
        <tr>
            <th><?php echo $language->payment_method; ?> : </th>
            <td><?php echo $row['payment_method_id']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->check_number; ?> : </th>
            <td><?php echo $row['check_number']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->customer; ?> : </th>
            <td><?php echo $row['partner_id']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->customer_contact; ?> : </th>
            <td><?php echo $row['partner_contact_id']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->customer_address; ?> : </th>
            <td><?php echo $row['partner_address_id']; ?></td>
        </tr>
    </table>
</div>
<div class="a4_col2">
    <table>
        <tr>
            <th><?php echo $language->date_time; ?> : </th>
            <td><?php echo $row['payment_date']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->document_number; ?> : </th>
            <td><?php echo $row['receipt_number']; ?></td>
        </tr>
        <tr>
            <th><?php echo $language->c_r ?> : </th>
            <td><?php echo $row_organization['o_cr']; ?></td>
        </tr>
    </table>
    <table>
        <tr>
            <th>
                <?php
                echo $row_organization['o_name'] . "</th></tr><tr><th>" . $row_branch['b_country'] . ", " . $row_branch['b_city'] . ", " .
                $row_branch['b_address1'] . " " . $row_branch['b_address2'];
                ?>
            </th>
        </tr>
    </table>
</div>

<div class="clear"></div><br/>

<table class="a4_lines">
    <tr>
        <td class="a4_small_cell"><?php echo $language->amount; ?></td>
        <td class="a4_product"><?php echo $row['credit']; ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell"><?php echo $language->amount_in_words; ?></td>
        <td class="a4_product"><?php echo $language->convert_number_to_words($row['credit']); ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell"><?php echo $language->payment_of_invoice_number; ?></td>
        <td class="a4_product"><?php echo $row['invoice_number']; ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell"><?php echo $language->received_by; ?></td>
        <td class="a4_product"><?php echo $row['received_by_id']; ?></td>
    </tr>
</table>