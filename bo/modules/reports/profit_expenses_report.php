<?php
startPage();
?>

<form method="post" name="report">
    <div class="col1">
        <label ><?php echo $_SESSION['main_language']->from_date; ?>:</label>
        <input name="from_date"  value="<?php echo $_POST["from_date"]; ?>" type="date" autocomplete="off">
        <label ><?php echo $_SESSION['main_language']->to_date; ?>:</label>
        <input name="to_date"  value="<?php echo $_POST["to_date"]; ?>" type="date" autocomplete="off">
        <input type="hidden" name="secret" value="ifhb9fb93bef93n4ej30rjnf">
        <div class="sep"></div>
        <div class="clear"></div>
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->load; ?></button>
</form> 
</div>


    
    

<?php
if (isset($_POST) && isset($_POST['secret']) && $_POST['secret'] == "ifhb9fb93bef93n4ej30rjnf") {
    $total = 0;

    echo "<div class='sep'></div>";
    echo "<h3>" . $_SESSION['main_language']->all_time_report . "</h3>";
    $sql = "SELECT SUM(amount) AS profit_loss, MAX(type) AS type FROM transaction "
            . " WHERE type IN ('Account Renewal', 'Purchase', 'Qualify Binary Shop', 'Qualify Retail Shop', 'Registration') ";
    if (isset($_POST['from_date']) && $_POST['from_date'] != "") {
        $sql .= " AND date >= '" . $_POST['from_date'] . "'";
    }
    if (isset($_POST['to_date']) && $_POST['to_date'] != "") {
        $sql .= " AND date <= '" . $_POST['to_date'] . "'";
    }
    $sql .= " GROUP BY type "
            . " ORDER BY SUM(amount) DESC ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    ?>

    <table class="a4_lines">
        <tr>
            <th class="a4_product">Profit Type </th>
            <th class="a4_total">Profit (ECs)</th>
        </tr>
    <?php
    while ($row = mysql_fetch_assoc($result)) {
        $row['profit_loss'] = 0 - $row['profit_loss'];
        ?>
            <tr>
                <td><?php echo $row['type']; ?></td>
                <td><?php
            echo number_format($row['profit_loss']);
            $total += $row['profit_loss'];
            ?></td>
            </tr>
                    <?php
                }
                ?>
        <tr>
            <td style="text-align: right; font-weight: bold;">Total:</td>
            <td><?php
    echo number_format($total);
    ?></td>
        </tr>
    </table>
    <?php
}
?>

    
    
    
    

<?php
if (isset($_POST) && isset($_POST['secret']) && $_POST['secret'] == "ifhb9fb93bef93n4ej30rjnf") {
    $total = 0;

    echo "<div class='sep'></div>";
    echo "<h3>" . $_SESSION['main_language']->all_time_report . "</h3>";
    $sql = "SELECT SUM(amount) AS profit_loss, MAX(type) AS type FROM transaction "
            . " WHERE type IN ('Step 1', 'Step 2', 'Step 3', 'Step 4', 'DC Points Exchange', 'Incentive') ";
    if (isset($_POST['from_date']) && $_POST['from_date'] != "") {
        $sql .= " AND date >= '" . $_POST['from_date'] . "'";
    }
    if (isset($_POST['to_date']) && $_POST['to_date'] != "") {
        $sql .= " AND date <= '" . $_POST['to_date'] . "'";
    }
    $sql .= " GROUP BY type "
            . " ORDER BY SUM(amount) DESC ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    ?>

    <table class="a4_lines">
        <tr>
            <th class="a4_product">Expense Type </th>
            <th class="a4_total">Expense (ECs)</th>
        </tr>
    <?php
    while ($row = mysql_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?php echo $row['type']; ?></td>
                <td><?php
            echo number_format($row['profit_loss']);
            $total += $row['profit_loss'];
            ?></td>
            </tr>
                    <?php
                }
                ?>
        <tr>
            <td style="text-align: right; font-weight: bold;">Total:</td>
            <td><?php
    echo number_format($total);
    ?></td>
        </tr>
    </table>
    <?php
}
?>

    


<?php
if (isset($_POST) && isset($_POST['secret']) && $_POST['secret'] == "ifhb9fb93bef93n4ej30rjnf") {
    $total = 0;

    echo "<div class='sep'></div>";
    echo "<h3>" . $_SESSION['main_language']->all_time_report . "</h3>";
    $sql = "SELECT SUM(amount) AS profit_loss, MAX(type) AS type FROM transaction "
            . " WHERE type IN ('Step 5', 'Redeem') ";
    if (isset($_POST['from_date']) && $_POST['from_date'] != "") {
        $sql .= " AND date >= '" . $_POST['from_date'] . "'";
    }
    if (isset($_POST['to_date']) && $_POST['to_date'] != "") {
        $sql .= " AND date <= '" . $_POST['to_date'] . "'";
    }
    $sql .= " GROUP BY type "
            . " ORDER BY SUM(amount) DESC ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    ?>

    <table class="a4_lines">
        <tr>
            <th class="a4_product">Profit / Expense Type </th>
            <th class="a4_total">Profit / Expense (Rpts)</th>
        </tr>
    <?php
    while ($row = mysql_fetch_assoc($result)) {
        $row['profit_loss'] = 0 - $row['profit_loss'];
        ?>
            <tr>
                <td><?php echo $row['type']; ?></td>
                <td><?php
            echo number_format($row['profit_loss']);
            $total += $row['profit_loss'];
            ?></td>
            </tr>
                    <?php
                }
                ?>
        <tr>
            <td style="text-align: right; font-weight: bold;">Difference:</td>
            <td><?php
    echo number_format($total);
    ?></td>
        </tr>
    </table>
    <?php
}
?>

    
    
    
    
    
    
    
<?php
endPage();
?>