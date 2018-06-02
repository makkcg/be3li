<?php
$html_page->writeHeader();
$html_page->writeBody($_SESSION['lang']->retailnewtork_header,$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

$error="";

$level1_irs= $core->getLevelXRetailNetwork($database_manager,$_SESSION['ir_id'],1);///level 1 downlines
$level2_irs=$core->getLevelXRetailNetwork($database_manager,$_SESSION['ir_id'],2);/////level 2 downlines

//echo "Lvel Two array dumb <br>";
//var_dump($level2_irs);
?>
<div class="retail_network_main_wrapper">
	<div class="row">
		<div class="divTable" style="width: 100%;" >
		<div class="divTableBody">
			<div class="divTableRow">
				<div class="divTableCell divTableCellcol1"><strong><?php echo $_SESSION["language"]->retailnewtork_level1; ?></strong></div>
				<div class="divTableCell"><!---level 1 retail irs--->
				<?php
				$activeIRsL1=0;
					for ($i = 0; $i < count($level1_irs); $i++) {
						$binorretail="";
						$comment="(Not Qualified)";
						
						if($level1_irs[$i]['is_qualified']>0){$activeIRsL1++;}
						
						if($level1_irs[$i]['is_qualified']>-1){
							if($level1_irs[$i]['is_qualified']==1){//qualified retail
									$binorretail="ir_box_div_qual1";
									$comment="(Qualified Retail)";
							}elseif ($level1_irs[$i]['is_qualified']==2){
									$binorretail="ir_box_div_qual2";
									$comment="(Qualified Binary)";
							}
						
						$uplinearr=$core->getUpReferralLevelX($database_manager,$level1_irs[$i]['ir'],1);
							
							echo '<div class="ir_box_div tooltip '.$binorretail.' ">'.$level1_irs[$i]['ir'].'<span class="tooltiptext">'.$level1_irs[$i]['name'].'<br> upL1:  '.$uplinearr['ir'].'</span></div>';
							
						//echo '<div class="ir_box_div tooltip '.$binorretail.' ">'.$level1_irs[$i]['ir'].'<span class="tooltiptext">'.$level1_irs[$i]['name'].'  '.$comment.'</span></div>';
						
						}
					}
				?>
				</div>
				<div class="divTableCell divTableCellcol1"><strong><?php echo $_SESSION["language"]->retailnewtork_countactive; ?></strong> <?php echo $activeIRsL1; ?></div>
			</div>
			<div class="divTableRow">
				<div class="divTableCell divTableCellcol1"><strong><?php echo $_SESSION["language"]->retailnewtork_level2; ?></strong></div>
				<div class="divTableCell">
				<?php
					$activeIRsL2=0;
					for ($i = 0; $i < count($level2_irs); $i++) {
						$binorretail="";
						$comment="(Not Qualified)";
						if($level2_irs[$i]['is_qualified']>0){$activeIRsL2++;}
						
						if($level2_irs[$i]['is_qualified']>-1){
							if($level2_irs[$i]['is_qualified']==1){//qualified retail
									$binorretail="ir_box_div_qual1";
									$comment="(Qualified Retail)";
							}elseif ($level2_irs[$i]['is_qualified']==2){
									$binorretail="ir_box_div_qual2";
									$comment="(Qualified Binary)";
							}
							$uplinearr=$core->getUpReferralLevelX($database_manager,$level2_irs[$i]['ir'],3);
							
							echo '<div class="ir_box_div tooltip '.$binorretail.' ">'.$level2_irs[$i]['ir'].'<span class="tooltiptext">'.$level2_irs[$i]['name'].'<br> upL2:  '.$uplinearr[1]['ir'].'<br> upL1:  '.$uplinearr[0]['ir'].'</span></div>';
							
							//var_dump($uplinearr);
							
						//echo '<div class="ir_box_div tooltip '.$binorretail.' ">'.$level2_irs[$i]['ir'].'<span class="tooltiptext">'.$level2_irs[$i]['name'].'  '.$comment.'</span></div>';
						}
					}
					
				?>
				</div>
				<div class="divTableCell divTableCellcol1"><strong><?php echo $_SESSION["language"]->retailnewtork_countactive; ?></strong> <?php echo $activeIRsL2 ?></div>
			</div>
		</div>
		</div>
		<!-- DivTable.com -->
	</div>
	<div class="row">
		<div> </div>
		<div>
		<?php
		
		
		?>
		</div>
	</div>
</div>

<div class="center">
    <p id="error"><?php echo $error; ?></p>
</div>

<?php $html_page->writeFooter(); ?>