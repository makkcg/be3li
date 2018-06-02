<?php
$html_page->writeHeader();
$html_page->writeBody($_GET['title'] . "<i class='fa fa-angle-double-right fa-fw'></i>" . $_GET['category'],$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$sqlxx= "SELECT baseUrl, URLsubfolder from configuration";
$resultxx = $database_manager->query($sqlxx);
$rowxx = mysqli_fetch_assoc($resultxx);
?>
<div id="shop-menu">
    <a class=" button" href="index.php?page=cart&title=<?php echo $_GET['title']; ?>"><i class="fa fa-cart-arrow-down fa-fw"></i> Cart</a>
</div>
<?php
while ($row = mysqli_fetch_assoc($result)) {

        ?>
        <div class="widget product">
            <div class="widget-contents">
                <table class="table table-striped">      
                    <tbody>
                        <tr>
                            <td>
                                <img style="max-width:250px;max-height:250px;" src="<?php echo $rowxx['baseUrl'].'/'.$rowxx['URLsubfolder'].'/bo/'.$row['img']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <th>
                                                <?php echo $row['name']; ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>
                                    <table>
                                        <tbody>
                                            <?php if ($_GET['title'] == "My Shop" || $_GET['title'] == "Qualify") { ?>
                                                <tr><th>Price</th><th>Shipping</th><th>Commission</th></tr>
                                                <tr><td><?php echo $row['price']; ?> ECs</td><td><?php echo $row['handling']; ?> ECs</td><td><?php echo $row['dcpts']; ?> DCpts</td></tr>
                                            <?php } else { ?>
                                                <tr><th>Price</th></tr>
                                                <tr><td><?php echo $row['rpts']; ?> Rpts</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    </th>
                        </tr>
                    </tbody>
                </table> 
                </td>
                </tr>
                <tr>
                    <td>
                        <a class="button" href="index.php?page=cart&title=<?php echo $_GET['title']; ?>&id=<?php echo $row['id']; ?>">Add to Cart</a>
                    </td>
                </tr>
                </tbody>
                </table>
            </div>
        </div>

        <?php
}
?>

<?php $html_page->writeFooter(); ?>