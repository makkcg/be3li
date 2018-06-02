<?php
startPage();
///get the Managers IRs data
	$managers_irs=array("PA0102","PA0103","PA1431","PA0514");
	$managers_names=array();
	
	for($ii=0;$ii< sizeof($managers_irs);$ii++){
		$sql2 = "select ir_id, f_name, l_name, a_name,ewallet from ir where ir_id='".$managers_irs[$ii]."'";
		if ($result2 = mysql_query($sql2)) {
			$row2 = mysql_fetch_assoc($result2);
			$errormsg="";
			$fullname_e=$row2["f_name"]." ".$row2["l_name"];
			$managers_names[]=array("ir_id"=> $row2["ir_id"], "fullname_e" => $fullname_e,"fullname_a" => $row2["a_name"],"ewallet" => $row2["ewallet"] );
		} else {
			$errormsg="error receiving ir data";
			error_log($sql2);
		}
	
	}//end for
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
        
    </div>
	<div class="col2">
		<label>Select Balance Sheet for : <span class="astrisk"> *</span></label> 
            <select name="ir_id" id="ir_id">
			<option value="" <?php
                if ($_POST['ir_id'] == "") {
                    echo "selected";
                }
            ?> >-- SELECT IR --</option>
			
			 <option value="acc01" <?php
                if ($_POST['ir_id'] == "acc01") {
                    echo "selected";
                }
            ?> >Accountant</option>
                
			<?php for($dd=0;$dd < sizeof($managers_names);$dd++){ ?>
                <option value="<?php echo $managers_names[$dd]["ir_id"]; ?>"
				<?php
                if ($_POST['ir_id'] == $managers_names[$dd]["ir_id"]) {
                    echo "selected";
                }
            ?> data-ewallet="<?php echo $managers_names[$dd]["ewallet"]; ?>" ><?php echo $managers_names[$dd]["ir_id"]." - ".$managers_names[$dd]["fullname_e"]." - ".$managers_names[$dd]["fullname_a"]; ?></option>
			<?php };//end for ?>
			</select><br class="clear"/>
			<button type="submit" class="ok"><?php echo $_SESSION['main_language']->load; ?></button>
	</div>
</form>
<div id="printable_all_ship" class="printable_all_ship"><!--- print area all pages-->
<?php
if (isset($_POST) && isset($_POST['secret']) && isset($_POST['ir_id']) && $_POST['secret'] == "ifhb9fb93bef93n4ej30rjnf" ) {
	$fromdate=$_POST['from_date'];
	$todate=$_POST['to_date'];
	$ir_id_sel=$_POST['ir_id'];
?>
	<!------------------------------------Shiping Form----------------------------->
	<div id="printtable_bal" class="printtable"><!--- print area-->
	<div class="sepbold"></div>
		
		<div class='sep'></div>
		<div class="col0" style="margin-bottom:10px;">
		<h4 style="text-align: center;">Balance Sheet Details -  for <?php echo $ir_id_sel; ?></h4>
		<div class='sep'></div>
		<div class="col1"><strong>FROM (Date) :    </strong><?php if($fromdate==""){echo " Beginning ";}else{echo $fromdate;}; ?></div>
		<div class="col2"><strong>To (Date)   :    </strong>  <?php if($todate==""){echo " Last Transaction Date ";}else{echo $todate;}; ?></div>
		<br>
		</div>
		<br>
		<div class='sep'></div>
		<table class="a4_lines">
			<tbody>
				<tr>
					<th class="a4_total">No.</th>
					<th class="a4_product">Transaction Details</th>
					<th class="a4_total">To/From</th>
					<th class="a4_total" style="min-width:100px !important;">Date</th>
					<th class="a4_total">Debit (ECs.)</th>
					<th class="a4_total">Debit (LE.)</th>
					<th class="a4_total">Credit (ECs.)</th>
					<th class="a4_total">Credit (LE.)</th>
					<th class="a4_total">Running Balance (ECs.)</th>
					<th class="a4_total">Running Balance (LEs.)</th>
				</tr>
<?php
	$sql11 = "SELECT * FROM  k8_acc_wallet_trns WHERE code = '".$_POST['ir_id']."' ";

    if (isset($_POST['from_date']) && $_POST['from_date'] != "") {
        $sql11 .= " and date(k8_acc_wallet_trns.datetime) >= '" . $_POST['from_date'] . "' ";
    }
    if (isset($_POST['to_date']) && $_POST['to_date'] != "") {
        $sql11 .= " and date(k8_acc_wallet_trns.datetime) <= '" . $_POST['to_date'] . "' ";
    }
				$no=0;
				if ($result2 = mysql_query($sql11)) {}else { error_log($sql11);}
				//$result2=0;
				if($result2){
				while ($row = mysql_fetch_assoc($result2)) {
					$no++;
					$transaction_type=$row['trns_type'];//1 credit , 2 Debit
					$bal_ec=$row['bal_ec'];
					$bal_le=$row['bal_le'];
					$val_ec=$row['val_ec'];
					$val_le=$row['val_le'];
					$date1=$row['datetime'];
					$trns_details=$row['comment'];
					$tofrom=$row['tofrom'];
					if($bal_le > 0){
						$balance_tail="";
					}else{
						$balance_tail="";
					};	
				?>
				
				<tr>
					<td><?php echo $no; ?></td>
					<td><?php echo $trns_details; ?></td>
					<td><?php echo $tofrom; ?></td>
					<td><?php echo $date1; ?></td>
					
					<?php if($transaction_type == 2){ ?>
					
					<td><?php echo -1*$val_ec; ?></td>
					<td><?php echo -1*$val_le; ?></td>
					<td></td>
					<td></td>
					<?php 
					
					}else if($transaction_type == 1){
						
					?>
					<td></td>
					<td></td>
					<td><?php echo $val_ec; ?></td>
					<td><?php echo $val_le; ?></td>
					<?php
					};
					?>
					
					<td><?php echo $bal_ec." ".$balance_tail; ?></td>
					<td><?php echo $bal_le." ".$balance_tail; ?></td>
					
				</tr>
				<?php
				//$grandtotal=$grandtotal+(((float)$row2['qnty']*($itempriceonlyEC*7))+(float)($row2['handling']*7));
				 };//end looping transactions records  ,while
				
				?>
			</tbody>
		</table>
		
		<div class="col1_mak">
		<div class="inforow endofpage" style=""><strong>Signature : </strong>...............................................................</div>
		</div>
		<div class="col2_mak">
		<div class="inforow endofpage" style="text-align:right;" dir="rtl"></div>
		</div>
	<div class="col0">
	<button type="button" id="printtable_bal_btn"   class="print_bal_btns  ok">Print</button>
	<div class="sepbold newpage"></div>
	</div>
	</div>
	<?php
	};///end of if condition for submitting form
	 //unset($_POST['ir_id']); 
};//end if $result2

	?>
	
	<!-------------------------------------------------Shipping form------------------>
	</div><!----end print all pages div-->
	
<script>
$(document).ready(function() {
	
	$(document).on('click','#printtable_bal_btn',function(){
		///get the ordernumber to print
		//var ordernumber=$(this).attr("data-orderid")
		var htmltoprint;
		//htmltoprint ="<div style='width:100%'><div></div></div>"
		html2=$('#printtable_bal').html()
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
endPage();
?>