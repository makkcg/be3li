<?php
$core->checkEwalletPassword("order");

$html_page->writeHeader();
$html_page->writeBody("Order #" . $_GET['id'],$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sql = "SELECT * FROM shop_order "
        . " WHERE id = '" . $_GET['id'] . "'"
        . " AND ir_id = '" . $_SESSION['ir_id'] . "' ";
$result = $database_manager->query($sql);
if (mysqli_num_rows($result) > 0){
$row = mysqli_fetch_assoc($result);
?>


    <table class="order-info">
        <tbody>
            <tr>
                <th>
                    Order Number: 
                </th>
                <td>
                    #<?php echo $row['id']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Name: 
                </th>
                <td>
                    <?php echo $row['name']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Email: 
                </th>
                <td>
                    <?php echo $row['email']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Phone: 
                </th>
                <td>
                    <?php echo $row['phone']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Mobile: 
                </th>
                <td>
                    <?php echo $row['mobile']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Address: 
                </th>
                <td>
                    <?php echo $row['address']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Area: 
                </th>
                <td>
                    <?php echo $row['area']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    City: 
                </th>
                <td>
                    <?php echo $row['city']; ?>
                </td>
            </tr>
            <tr>
                <th>
                    Country: 
                </th>
                <td>
                    <?php echo $row['country']; ?>
                </td>
            </tr>
        </tbody>
    </table>
<div class="sep dotted"></div>

<?php


$sql = "SELECT * FROM shop_order_line "
        . " WHERE shop_order_id = '" . $row['id'] . "'";
$result = $database_manager->query($sql);
?>


<table class="table table-striped">
    <thead>
        <tr>
            <th class="product-column">Product</th>
            <th class="price-column">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_ewallet = 0;
        $total_rpts = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $total_ewallet += $row['price']
            ?>
            <tr>
                <td class="product-column"><?php echo $row['product_name']; ?></td>
                <?php if ($_GET['title'] != "Redeem Shop") { ?>
                    <td class="price-column"><?php echo $row['price']; ?> ECs</td>
                <?php } else { ?> 
                    <td class="price-column"><?php echo $row['rpts']; ?> Rpts</td>
                <?php } ?>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="total-product-column">Total</td>
            <?php if ($_GET['title'] != "Redeem Shop") { ?>
                <td class="total-price-column"><?php echo $total_ewallet; ?> ECs</td>
            <?php } else { ?> 
                <td class="total-price-column"><?php echo $total_rpts; ?> Rpts</td>
            <?php } ?>
        </tr>
    </tbody>
</table>
<?php }else {
    ?>
<p id="error">You don not have access to this information.</p>
<?php
}
?>

<?php $html_page->writeFooter(); ?>