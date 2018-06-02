<?php
startPage();
?>

<form method="post" name="report">
    <div class="col1_mak">
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
    
<div id="printable_all_ship" class="printable_all_ship"><!--- print area all pages-->
<?php
if (isset($_POST) && isset($_POST['secret']) && $_POST['secret'] == "ifhb9fb93bef93n4ej30rjnf") {
    //echo "<div class='sep'></div>";
    //echo "<h3>" . $_SESSION['main_language']->all_time_report . "</h3>";
    $sql = "SELECT  shop_order.id ,  shop_order.ir_id ,ir.f_name, ir.l_name,  shop_order.customer_id ,  shop_order.datetime ,  shop_order.name ,  shop_order.email ,  shop_order.phone ,  shop_order.mobile ,  shop_order.address ,  shop_order.area ,  shop_order.city ,  shop_order.country ,  shop_order.ewallet_amount 
	FROM  shop_order,ir WHERE ir.ir_id =shop_order.ir_id AND shop_order.datetime >0";
           // . " WHERE so.is_paid = 1 ";
    if (isset($_POST['from_date']) && $_POST['from_date'] != "") {
        $sql .= " and date(shop_order.datetime) >='" . $_POST['from_date'] . "'";
    }
    if (isset($_POST['to_date']) && $_POST['to_date'] != "") {
        $sql .= " and date(shop_order.datetime) <= '" . $_POST['to_date'] . "';";
    }
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
	while ($row = mysql_fetch_assoc($result)) {
    ?>
	<!------------------------------------Shiping Form----------------------------->
	<div id="printtable_<?php echo "order".$row["id"]; ?>" class="printtable  "><!--- print area-->
	<page>
	<div class="sepbold"></div>
		<div class=" printheader">
			<div class="col0">
				<h3 style="text-align: center;">وثيقة استلام منتجات / خدمات</h3>
				<div class="sep"></div>
				<div class="col1_mak border_r">
					<h4>Supplier</h4>
					<img style="height:50px;margin:10px 0;" src="&#10;data:image/png;base64,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">
					<div class="inforow" style=""><strong>Address: </strong>El Mohandsien, Giza, ARE.</div>
					<div class="inforow" style=""><strong>Phone: </strong></div>
					<div class="inforow" style=""><strong>Email: </strong>info@proshopsllc.com</div>
					<div class="inforow" style=""><strong>Website: </strong>www.proshopsllc.com</div>
				</div>
				<div class="col2_mak">
					<h4>Ship To</h4>
					<div class="inforow" style=""><strong>IR: </strong><span class="isir"><?php if($row['customer_id']==0 || $row['customer_id']==NULL){echo $row['ir_id'];}else{ echo "NOT IR-Customer_ID: ".$row['customer_id']." By ".$row['f_name']." ".$row['l_name'];}; ?></span></div>
					
					<div class="inforow" style=""><strong>Order #: </strong><?php echo $row['id']; ?></div>
					<div class="inforow" style=""><strong>Order Date: </strong><?php echo $row['datetime']; ?></div>
					<div class="inforow" style=""><strong>Full Name: </strong><?php echo $row['name']; ?></div>
					<div class="inforow" style=""><strong>Phone/Mob: </strong><?php echo $row['phone']." / ". $row['mobile']; ?></div>
					<div class="inforow" style=""><strong>Email: </strong><?php echo $row['email']; ?></div>
					<div class="inforow" style=""><strong>Address: </strong><?php echo $row['address']." , ".$row['area']." , ".$row['city']." , ".$row['country']; ?></div>
					
				</div>
			</div>
		</div>
		<div class='sep'></div>
		<div class="col0">
		<h4 style="text-align: center;">Order Details</h4>
		</div>
		<table class="a4_lines">
			<tbody>
				<tr>
					<th class="a4_total">Category</th>
					<th class="a4_product">Product</th>
					<th class="a4_total">Price (LE.)</th>
					<th class="a4_total">Shipping/Handling (LE.)</th>
					<th class="a4_total">Quantity</th>
					<th class="a4_total">Total (LE)</th>
				</tr>
				<?php
				 $sql2 = "SELECT shop_order_line.product_name,product.name,shop_order_line.price,shop_order_line.product_id ,COUNT( * ) as qnty,product.handling,product.category FROM shop_order_line, product where shop_order_line.shop_order_id=".$row['id']." and product.id=shop_order_line.product_id GROUP BY shop_order_line.product_name";
				 if ($result2 = mysql_query($sql2)) {}else { error_log($sql2);}
				 $grandtotal=0;
				 while ($row2 = mysql_fetch_assoc($result2)) {
				 $itempriceonlyEC =((float)$row2['price']-(float)$row2['handling']);
				?>
				
				<tr>
					<td><?php echo $row2['category']; ?></td>
					<td><?php echo $row2['name']; ?></td>
					<td><?php echo ($itempriceonlyEC*7); ?></td>
					<td><?php echo ((float)$row2['handling']*7); ?></td>
					<td><?php echo $row2['qnty']; ?></td>
					<td><?php echo (((float)$row2['qnty']*($itempriceonlyEC*7))+(float)($row2['handling']*7)); ?></td>
				</tr>
				<?php
				$grandtotal=$grandtotal+(((float)$row2['qnty']*($itempriceonlyEC*7))+(float)($row2['handling']*7));
				 };//end looping ordeer products ,while
				
				?>
				<tr>
					
					<th class="a4_product"></th>
					<th class="a4_total">Grand Total</th>
					<th class="a4_total"></th>
					<th class="a4_total"></th>
					<th class="a4_total"></th>
					<th class="a4_total"><?php echo $grandtotal; ?></th>
				</tr>
			</tbody>
		</table>
		
		<div class="col1_mak">
		<div class="inforow endofpage" style=""><strong>Customer Signature : </strong>...............................................................</div>
		</div>
		<div class="col2_mak">
		<div class="inforow endofpage" style="text-align:right;" dir="rtl"><strong>اقر أنا الموقع هنا أنني استلمت المنتج/الخدمة كما هو مبين على الموقع الإلكتروني في البيان أعلاه.</strong></div>
		</div>
	<div class="col0">
	<button type="button" id="print_rep_<?php echo "order".$row["id"]; ?>" data-orderid='<?php echo "order".$row["id"]; ?>'   class="print_order  ok">Print</button>
	<div class="sepbold newpage"></div>
	</div>
	
	</page>
	</div>
	<?php
	};//end looping through orders, while
	
	?>
	
	<!-------------------------------------------------Shipping form------------------>
	</div><!----end print all pages div-->
	
<script>
$(document).ready(function() {
	
	$(document).on('click','#print_all',function(){
		var htmltoprint;
		//htmltoprint ="<div style='width:100%'><div></div></div>"
		html2=$('#printable_all_ship').html()
	htmltoprint = html2
		
		//PrintElem(htmltoprint)
		Popup(htmltoprint)
	});///print all btn
	
	$(document).on('click','.print_order',function(){
		///get the ordernumber to print
		var ordernumber=$(this).attr("data-orderid")
		var htmltoprint;
		//htmltoprint ="<div style='width:100%'><div></div></div>"
		html2=$('#printtable_'+ordernumber).html()
	htmltoprint =  html2
		
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
        var mywindow = window.open('', 'Print', 'height=700,width=900mm');
       // mywindow.document.write('<html><head><title>فاتورة</title>');
        /*optional stylesheet*/ //
		mywindow.document.write(' <link rel="stylesheet" type="text/css" href="./css/style.css" />');
		//mywindow.document.write(' <link rel="stylesheet" type="text/css" href="./css/grid.css" />');
		//mywindow.document.write(' <link rel="stylesheet" type="text/css" href="css/invprint.css" />');
       // mywindow.document.styleSheets="css/invprint.css"
		mywindow.document.write('<style>body{background:none !important;padding: 0mm;margin:0;}#printable_all_ship{margin:0 auto;padding:0;}.print_order{display:none;}.printtable{width:210mm; height:297mm;margin:0;padding:0;}@page {size: A4;margin: 27mm 16mm 27mm 16mm;} div.printtable {page-break-before:always;} div.chapter, div.newpage {page-break-after:always;} @media print { @page {size: A4;margin: 27mm 16mm 27mm 16mm;} div.printtable {page-break-before:always;} div.chapter, div.newpage {page-break-after: always;} }</style>');
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
<div class="col0">
	<button type="button" id="print_all"   class="print_order_all ok">Print All</button>
	<div class="sepbold"></div>
	</div>
<?php
endPage();
?>