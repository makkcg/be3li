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


    echo "<div class='sep'></div>";
    echo "<h3>" . $_SESSION['main_language']->all_time_report . "</h3>";
    $sql = "SELECT MAX(product_name) AS name, COUNT(sol.id) AS total_sales FROM shop_order_line sol"
            . " INNER JOIN shop_order so ON so.id = sol.shop_order_id ";
           // . " WHERE so.is_paid = 1 ";
    if (isset($_POST['from_date']) && $_POST['from_date'] != "") {
        $sql .= " AND date(so.datetime) >= '" . $_POST['from_date'] . "'";
    }
    if (isset($_POST['to_date']) && $_POST['to_date'] != "") {
        $sql .= " AND date(so.datetime) <= '" . $_POST['to_date'] . "'";
    }
    $sql .= " GROUP BY sol.product_name "
            . " ORDER BY COUNT(sol.id) DESC ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    ?>
	<div id="printtable"><!--- print area-->
    <table class="a4_lines">
        <tr>
            <th class="a4_product"><?php echo $_SESSION['main_language']->product; ?></th>
            <th class="a4_total"><?php echo $_SESSION['main_language']->sold_quantity; ?></th>
        </tr>
    <?php
    while ($row = mysql_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo number_format($row['total_sales']); ?></td>
            </tr>
        <?php
    }
    ?>
    </table>
	</div><!---end print area-->
	<div class='sep'></div>
	<div class="col1">
	<button type="button" id="print_rep"  class="ok">Print</button>
	</div>
<script>
$(document).ready(function() {
	$(document).on('click','#print_rep',function(){
		var htmltoprint;
		htmltoprint ="<div style='width:100%'><h3>Best Sellers Report</h3><div>From:<?php if (isset($_POST['from_date']) && $_POST['from_date'] != "") { echo $_POST['from_date'];}else{echo "All";};  ?> to : <?php if (isset($_POST['to_date']) && $_POST['to_date'] != "") { echo $_POST['to_date'];}else{echo "All";} ?></div></div>"
		html2=$('#printtable').html()
	htmltoprint = htmltoprint + html2
		
		//PrintElem(htmltoprint)
		Popup(htmltoprint)
	});
});//end doc ready
function PrintElem(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'Print', 'height=500,width=700');
       // mywindow.document.write('<html><head><title>فاتورة</title>');
        /*optional stylesheet*/ //
		mywindow.document.write(' <link rel="stylesheet" type="text/css" href="./css/style.css" />');
		//mywindow.document.write(' <link rel="stylesheet" type="text/css" href="./css/grid.css" />');
		//mywindow.document.write(' <link rel="stylesheet" type="text/css" href="css/invprint.css" />');
       // mywindow.document.styleSheets="css/invprint.css"
		mywindow.document.write('<style>body{background:none !important;padding: 5mm;};@page {size: 7in 9.25in;size: A4;margin: 27mm 16mm 27mm 16mm;}</style>');
		mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        //mywindow.print().delay( 5800 );
		//mywindow.close();
        return true;
    }

</script>
        <?php
    }
    ?>

<?php
endPage();
?>