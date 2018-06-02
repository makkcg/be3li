<?php

class HTMLPage {

    function writeHeader() {
		
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title><?php echo $_SESSION["language"]->htmlhead ;?></title>
				<!-- Bootstrap -->
				<link href="css/bs/bootstrap.min.css" rel="stylesheet">
				
                <meta name="Keywords" content="<?php echo $_SESSION["language"]->htmlhead ;?>" />
                <meta name="Description" content="<?php echo $_SESSION["language"]->htmlhead ;?>" />
                <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="http://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet" type="text/css"></link>
                <meta charset="UTF-8"/>
                <link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'></link>
				<link href='css/swiper.min.css' rel='stylesheet' type='text/css'></link>	
				<?php 
					if($_SESSION["language"]->langdirection=="rtl"){
						echo " <link href='css/style_rtl.css' rel='stylesheet' type='text/css'></link>";
					}else{
						echo " <link href='css/style.css' rel='stylesheet' type='text/css'></link>";
					}
				?>
				<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"></link>
				<!-- jQuery 3(necessary for Bootstrap's JavaScript plugins) -->
							<script src="js/jquery-3.2.1.min.js"></script>
							
							<script src="js/bootstrap.min.js"></script>
							<script src="js/swiper.min.js"></script>
			</head>
                        <body class="bodybackground bodybgfontAndColor">
						<!--------------------------------------------body area----------------------------------------->
						<div class="row">
						<div class="fullpagecontainer" >
                            <?php
                        }

                        function writeBody($title,$ewalletval) {
							

                            ?>
                            <!-----------responsive side nav menu------->
                            <nav class="navbar navbar-inverse sidebar" role="navigation">
    <div class="container-fluid">
    	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-sidebar-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#"><img src="images/testlogo.png" class="site_logo" alt="Be3li"></a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-sidebar-navbar-collapse-1">
			<ul class="nav navbar-nav">
             <?php
        		
				$this->addSideNavResponsive($_SESSION["language"]->menu_dashboard , "dashboard", "fa-home");
                $this->addSideNavResponsive($_SESSION["language"]->menu_ewallet , "ewallet", "fa-money");
				$this->addSideNavResponsive($_SESSION["language"]->menu_myshop , "categories&title=My Shop", "fa-shopping-bag");			
				$this->addSideNavResponsive($_SESSION["language"]->menu_binarynetwork , "genealogy_tree", "fa-crosshairs");
				$this->addSideNavResponsive($_SESSION["language"]->menu_referredlist , "group_list", "fa-table");             
				$this->addSideNavResponsive($_SESSION["language"]->menu_totalcommission , "total_commission", "fa-calculator");	
				$this->addSideNavResponsive($_SESSION["language"]->menu_orderhistory , "purchase_history", "fa-th-list");
				$this->addSideNavResponsive($_SESSION["language"]->menu_businesstools , "business_tools", "fa-suitcase");	
				$this->addSideNavResponsive($_SESSION["language"]->menu_renewal , "account_renewal", "fa-repeat");                                        
            ?>
				
			</ul>
		</div>
	</div>
</nav>
                            <!-----------end responsive side nav menu---->
                            
                            <div id="left-container_new" class="col-xs-3 col-md-3 col-lg-3 mainmenustyling" style="display:none;">
                                <div id="logo_new" class="col-xs-12 col-md-12 col-lg-12">
                                    <a href="index.php?page=dashboard"><img src="images/testlogo.png" class="img-responsive"></a>
                                </div>
                                <ul id="nav" class="col-xs-12 col-md-12 col-lg-12">
                                    <?php;
									/*$this->addMenuItem($_SESSION["language"]->menu_dashboard , "dashboard", "fa-home");*/
                                    ?>
                                </ul>
                            </div>
                            <!--<div id="right-container1" class="col-xs-9 col-md-9 col-lg-9">-->
                            <div class="main">
                            <div id="right-container1" class=" ">
							<div id="header" class="col-xs-12 col-md-12 col-lg-12">
                                    <div id="page-title1" class="col-xs-4 col-md-4 col-lg-4">
                                        <?php echo $title; ?>
                                    </div>
                                    <div id="header-menu" class="col-xs-8 col-md-8 col-lg-8">
									<a class="yourewallet_val" href="index.php?page=ewallet">eWallet: <strong><?php echo $ewalletval; ?> LE</strong></a>
									<a class="yourcart" href="index.php?page=cart&title=My Shop">Cart <i class="fa fa-shopping-cart"></i></a>
                                        <?php
                                        /*$this->addHeaderMenuItem($_SESSION["language"]->menu_dashboard , "dashboard", "fa-home");*/

                                        ?>
                                    </div>
                                </div>
                                <div id="top-bar" class="">
                                    <p class="col-xs-12 col-md-3 col-lg-3">
									<b ><?php //echo $_SESSION["language"]->Welcome ;?></b> <?php echo $_SESSION['full_name']." - ".$_SESSION['ir_id']; ?>
                                        <!--<b style='font-size: 20px; color: #d79928;'>Welcome</b> --><?php //echo $_SESSION['full_name']; ?>
                                    </p>
                                    <ul class="col-xs-12 col-md-9 col-lg-9">
                                        <?php
                                        $this->addTopMenuItem($_SESSION["language"]->menu_logout , "login", "fa-sign-out");
                                        $this->addTopMenuItem($_SESSION["language"]->menu_changepassword , "change_password", "fa-shield");
                                        $this->addTopMenuItem($_SESSION["language"]->menu_myaccount , "my_account", "fa-user");
										$this->addTopMenuItem($_SESSION["language"]->menu_registernewir , "register_first_step&refir=".$_SESSION['ir_id'], "fa-user-plus");
                                        ?>
                                    </ul>
                                </div>
                                
                                </div>
                                <div id="page" class="col-xs-12 col-md-12 col-lg-12">
                                    <?php
                                }

                                private function addMenuItem($name, $filename, $icon) {
                                    ?>
                                    <li class="mnu_itmLi"><a class="<?php
                                        if ($_GET['page'] == $filename) {
                                            echo "mnu_itmstyle active";
                                        }else{
											echo "mnu_itmstyle";
										}
                                        ?>" class="col-xs-12 col-md-12 col-lg-12 mnu_itmstyle" href="index.php?page=<?php echo $filename; ?>"><i class="fa <?php echo $icon; ?> fa-fw"></i> <?php echo $name; ?></a></li>
                                           <?php
                                       }
								private function addSideNavResponsive($name, $filename, $icon) {
                                    ?>
                                        <li class="<?php if($_GET['page']==$filename){echo "active";}else{echo "notactive";}; ?>"><a href="index.php?page=<?php echo $filename; ?>"> <?php echo $name; ?><span style="font-size:16px;" class="pull-right hidden-xs showopacity glyphicon glyphicon-home fa <?php echo $icon; ?> fa-fw"></span></a></li>
                                    <?php
                                }
                                    
                                    private function addTopMenuItem($name, $filename, $icon) {
                                    ?>
                                    <li class="topmnu_itmLi"><a class="<?php
                                        if ($_GET['page'] == $filename) {
                                            echo "topmnu_itmstyle active";
                                        }else{
    										echo "topmnu_itmstyle";
										}
                                        ?>" href="index.php?page=<?php echo $filename; ?>"><i class="fa <?php echo $icon; ?> fa-fw"></i> <?php echo $name; ?></a></li>
                                           <?php
                                     }
                                       
								private function addHeaderMenuItem($name, $filename, $icon) {
                                           ?>
                                    <a class="<?php
                                    if ($_GET['page'] == $filename) {
                                        echo "top2mnu_itmstyle active";
                                    }else{
										echo "top2mnu_itmstyle";
									}
                                    ?> button col-xs-3 col-md-2 col-lg-2 top2mnu_itmLi"   href="index.php?page=<?php echo $filename; ?>"><i class="fa <?php echo $icon; ?> fa-fw"></i> <?php echo $name; ?></a>
                                       <?php
                                   }

                                   function writeFooter() {
                                       ?>
                                </div>
                                <div id="footer">
								<?php echo $_SESSION["language"]->footer_copyright ; ?>
                                </div>
                            </div>
							
							<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
							<!--<script src="js/jquery.min.js"></script>
							<!-- jQuery 3(necessary for Bootstrap's JavaScript plugins) -->
							<script src="js/jquery-3.2.1.min.js"></script>
							
							<script src="js/bootstrap.min.js"></script>
							<script src="js/swiper.min.js"></script>
							<!-- Include all compiled plugins (below), or include individual files as needed -->
							<script src="js/commonjs.js"></script>
							
                        </body>
						</div><!--- 12cols page container--->
						</div><!--- page row--->
                        </html>
                        <?php
                    }

                    function drowBU($bu_id, $qualified, $left_1, $right_1, $left_2 = "", $right_2 = "") {
                        ?>
                        <table class="bu bu<?php echo substr($bu_id, -1); ?>">
                            <tr class="qualfied<?php echo $qualified; ?>">
                                <td><?php echo $bu_id; ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 0px;"><table style="width: 100%;">
                                        <tr>
                                            <td class="referrals">
                                                L:<?php echo $left_1; ?>
                                            </td>
                                            <td class="referrals">
                                                R:<?php echo $right_1; ?>
                                            </td>
                                        </tr>
                                    </table></td>
                            </tr>
                            <?php if ($left_2 != "" && $right_2 != "") { ?>
                                <tr>
                                    <td style="padding: 0px;"><table style="width: 100%;">
                                            <tr>
                                                <td class="referrals_second_line">
                                                    L:<?php echo $left_2; ?>
                                                </td>
                                                <td class="referrals_second_line">
                                                    R:<?php echo $right_2; ?>
                                                </td>
                                            </tr>
                                        </table></td>
                                </tr>
                            <?php } ?>
                        </table>

                        <?php
                    }//end function
					
					function drawBU_andIR_new($IRID,$IRname, $bu_id, $qualified, $leftcounter, $rightcounter, $isBU1orIR0,$bucode) {
						///check if bu or IR
						if($isBU1orIR0>0){///draw bu box
						switch($bucode){
							case "001":
							case 001:
						?>
							<!-----Row that contains Main IR box for BU 001--->
							<div  class="col-xs-12 col-md-12 col-lg-12 row1">
							<!-----Main IR box for BU 001--->
								<div class="col-xs-4 col-md-4 col-lg-4"></div>
								<div class="col-xs-4 col-md-4 col-lg-4 block">
								<div class="col-xs-12 col-md-12 col-lg-12 bold bu001_header">
									<?php echo $IRname." - ".$IRID;?>
								</div>
								<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
									<?php echo "bu".$bucode;?>
								</div>
								<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										<span class="LRcount">Left:</span> <span class="LRcount bu001_L"><?php echo $leftcounter; ?></span>
									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<span class="LRcount">Right:</span> <span class="LRcount bu001_R"><?php echo $rightcounter; ?></span>
									</div>
								</div>
									
								</div>
								<div class="col-xs-4 col-md-4 col-lg-4">

								</div>
							<!-----END Main IR box for BU 001--->
							</div><!---end row 1 that contains bu001 for main IR-->
						<?php
						break;
							case "002":
							case 002:
						?>
							<div class="col-xs-2 col-md-2 col-lg-2"></div>
							<div class="col-xs-2 col-md-2 col-lg-2 block">
								<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
									
									<?php echo "BU".$bucode; ?>
								</div>
								<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										 <span class="col-xs-6 col-md-6 col-lg-6 LRcount bu001_L"><?php echo $leftcounter; ?></span>
									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<span class=" col-xs-6 col-md-6 col-lg-6 LRcount bu001_R"><?php echo $rightcounter; ?></span>
									</div>
								</div>
							</div>
							<div class="col-xs-4 col-md-4 col-lg-4"></div>
						<?php
						break;
						case "003":
						case 003:
						?>
							<div class="col-xs-2 col-md-2 col-lg-2 block">
								<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
									<?php echo "BU".$bucode; ?>
								</div>
								<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										 <span class="col-xs-6 col-md-6 col-lg-6 LRcount bu001_L"><?php echo $leftcounter; ?></span>
									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<span class=" col-xs-6 col-md-6 col-lg-6 LRcount bu001_R"><?php echo $rightcounter; ?></span>
									</div>
								</div>
							</div>
							<div class="col-xs-2 col-md-2 col-lg-2"></div>
						<?php
						break;
						case "004":
						case 004:
						?>
							<div class="col-xs-1 col-md-1 col-lg-1"></div>
							<div class="col-xs-1 col-md-1 col-lg-1 block">
								<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
									<?php echo "BU".$bucode; ?>
								</div>
								<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										 <span class="col-xs-6 col-md-6 col-lg-6 LRcount bu001_L"><?php echo $leftcounter; ?></span>
									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<span class=" col-xs-6 col-md-6 col-lg-6 LRcount bu001_R"><?php echo $rightcounter; ?></span>
									</div>
								</div>
							</div>
							<div class="col-xs-2 col-md-2 col-lg-2"></div>
						<?php
						break;
						case "005":
						case 005:
						?>
							<div class="col-xs-1 col-md-1 col-lg-1 block">
							<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
								<?php echo "BU".$bucode; ?>
							</div>
							<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
								<div class="col-xs-6 col-md-6 col-lg-6">
									 <span class="col-xs-6 col-md-6 col-lg-6 LRcount bu001_L"><?php echo $leftcounter; ?></span>
								</div>
								<div class="col-xs-6 col-md-6 col-lg-6">
									<span class=" col-xs-6 col-md-6 col-lg-6 LRcount bu001_R"><?php echo $rightcounter; ?></span>
								</div>
							</div>
						</div>
						<div class="col-xs-2 col-md-2 col-lg-2">
							
						</div>
						<?php
						break;
						case "006":
						case 006:
						?>
							<div class="col-xs-1 col-md-1 col-lg-1 block">
							<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
								<?php echo "BU".$bucode; ?>
							</div>
							<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
								<div class="col-xs-6 col-md-6 col-lg-6">
									 <span class="col-xs-6 col-md-6 col-lg-6 LRcount bu001_L"><?php echo $leftcounter; ?></span>
								</div>
								<div class="col-xs-6 col-md-6 col-lg-6">
									<span class=" col-xs-6 col-md-6 col-lg-6 LRcount bu001_R"><?php echo $rightcounter; ?></span>
								</div>
							</div>
						</div>
						<div class="col-xs-2 col-md-2 col-lg-2">
							
						</div>
						<?php
						break;
						case "007":
						case 007:
						?>
							<div class="col-xs-1 col-md-1 col-lg-1 block">
								<div class="col-xs-12 col-md-12 col-lg-12 bu_row">
									<?php echo "BU".$bucode; ?>
								</div>
								<div class="col-xs-12 col-md-12 col-lg-12 LR_row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										 <span class="col-xs-6 col-md-6 col-lg-6 LRcount bu001_L"><?php echo $leftcounter; ?></span>
									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<span class=" col-xs-6 col-md-6 col-lg-6 LRcount bu001_R"><?php echo $rightcounter; ?></span>
									</div>
								</div>
							</div>
							<div class="col-xs-1 col-md-1 col-lg-1">
								
							</div>
						<?php
						break;
						}///end switch bu codes
						}else{///draw IR box
							if( $IRID !="" || $IRID !=0){///if there is IR draw the box else draw empty IR box
						?>
							<div class="col-xs-1 col-md-1 col-lg-1 block2">
								<?php echo '<a class="geneologyIR_link" href="' . '?page=genealogy_tree&ir_id='. $IRID .'">'; ?>
									<div class="col-xs-12 col-md-12 col-lg-12 bold irbox_header">
										<?php echo $IRname." - ".$IRID;?>
									</div>
								</a>
							</div>
						
						<?php
							}else{///empty IR box
							
							?>
								<div class="col-xs-1 col-md-1 col-lg-1 block2">
									<div class="col-xs-12 col-md-12 col-lg-12 bold irbox_header irbox_header_noIR">
										Empty
									</div>
								</div>
							<?php
							}///end else IR is empty
						}///end if IR or BU 
                        ?>

                        <?php
                    }///end draw IR or BU function

                }///end html class
                ?>