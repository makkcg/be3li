<?php
$html_page->writeHeader();
//die($core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));
$html_page->writeBody($_SESSION["language"]->menu_dashboard,$core->getewalletval($database_manager,$_SESSION['ir_id']));
/***get the products images path**/
$imgsPath=$core->getboimgPathUrl($database_manager);
/************************************/
function show_recomended_products($database_manager,$imgsPath){
	$sql="SELECT prod.`id`,prod.`catid`, cat.title catname,prod.`subcatid`, subcat.title subcatname,prod.`name`,prod.`desc`,prod.`img`,prod.`price`,prod.`vendor` FROM `product` prod left join categories cat on prod.catid=cat.id left join subcategories subcat on prod.subcatid=subcat.id WHERE prod.`recommended`=1 AND prod.`is_enabled`=1 LIMIT 8";
	$result = $database_manager->query($sql);
	 if($result){
		$rowcount=mysqli_num_rows($result);
		while($row = mysqli_fetch_assoc($result)){
			$productid=$row['id'];
			$productcat=$row['catname'];
			$productsubcat=$row['subcatname'];
			$productname=$row['name'];
			$productimg=$imgsPath.$row['img'];
			$productprice=$row['price'];
			$productdesc="";//$row['desc'];
			$productlink="?page=products&title=My%20Shop&category=".$productcat;
			
			?>
				<div class="swiper-slide">
							<a class="swiperbtn " style="" href="<?php echo $productlink; ?>">
								<img src="<?php echo $productimg; ?>" class="img-fluid swiper-lazy" alt="<?php echo $productname; ?>" title="<?php echo $productname; ?>">
								<div class="carousel-caption-vid-desc">
									<span style="text-align:center"><?php echo $productname; ?></span>
									<span style="text-align:center"><strong>Price:</strong> <?php echo $productprice; ?> LE.</span>
									<span><?php echo $productdesc; ?></span>
								</div>	
							</a>
				</div>
			<?php
		}
		
	 }else{/**end result**/
	 
	 }
}
/****function that returns the best seller items limit 8***/
function show_bestseller_products($database_manager,$imgsPath){
	$sql="SELECT count(product_id) as itemssold,prd.name name,prd.id prodid,prd.img img,prd.price price, cat.title catname, prd.catid ,subcat.title subcatname, prd.subcatid FROM `shop_order_line` prdorders left join product prd on product_id=prd.id left join categories cat on prd.catid=cat.id left join subcategories subcat on prd.subcatid=subcat.id WHERE 1 group by product_id ORDER BY itemssold DESC LIMIT 8";
	$result = $database_manager->query($sql);
	 if($result){
		
		while($row = mysqli_fetch_assoc($result)){
			$productid=$row['prodid'];
			$productcat=$row['catname'];
			$productsubcat=$row['subcatname'];
			$productname=$row['name'];
			$productimg=$imgsPath.$row['img'];
			$productprice=$row['price'];
			$itemssold=$row['itemssold'];
			$productlink="?page=products&title=My%20Shop&category=".$productcat;
			
			?>
				<div class="swiper-slide">
							<a class="swiperbtn " style="" href="<?php echo $productlink; ?>">
								<img src="<?php echo $productimg; ?>" class="img-fluid swiper-lazy" alt="<?php echo $productname; ?>" title="<?php echo $productname; ?>">
								<div class="carousel-caption-vid-desc">
									<span style="text-align:center"><?php echo $productname; ?></span>
									<span style="text-align:center"><strong>Price:</strong> <?php echo $productprice; ?> LE.</span>
									<span style="text-align:center"><strong>Sold Items :</strong><?php echo $itemssold; ?></span>
								</div>	
							</a>
				</div>
			<?php
		}
		
	 }else{/**end result**/
	 
	 }
}
?>
<script>
    function showDashboardCounter() {
        document.getElementById("dashboard-counter-button").style.display = "none";
        document.getElementById("dashboard-counter-paragraph").style.display = "none";
        document.getElementById("dashboard-counter").style.display = "block";
    }
		
		
</script>
<div class="col-xs-12 col-md-12 col-lg-12 dashboard_afflinkbox" >Your Affiliate Link: <a href="<?php echo $core->getUrl() ."?refir=".$_SESSION['ir_id'] ; ?>" target="_blank"><?php echo $core->getUrl() ."?refir=".$_SESSION['ir_id'] ; ?></a>
</div>
<!----recommended , Best Seller products slider--->
<div class=" col-xs-12 col-md-6 col-lg-6">
	<div class="widget">
		<div class="widget-title">
			<i class="fa  fa-thumbs-up"></i><?php echo $_SESSION["language"]->dashboard_recomended_products; ?>
		</div>
		<div class="widget-contents">
			<!--recomended products Swiper -->
				<div id="recomended_products_swiper" class="recomended_products_swiper swiper-container">
					<div class="swiper-wrapper">
						<?php show_recomended_products($database_manager,$imgsPath);  ?>
					</div>

					<!-- Add Arrows -->
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
						<!-- Add Pagination -->
						<span class="togglepaginationstags" tabindex="0" role="button" aria-label=""></span>
						<div class="swiper-pagination ">
						</div>
				</div>
		</div>
	</div>
</div>

<div class=" col-xs-12 col-md-6 col-lg-6">
    <div class="widget">
		<div class="widget-title">
			<i class="fa fa-signal"></i><?php echo $_SESSION["language"]->dashboard_best_seller; ?>
		</div>
		<div class="widget-contents">	
		<!--recomended products Swiper -->
				<div id="recomended_products_swiper" class="recomended_products_swiper swiper-container">
					<div class="swiper-wrapper">
						<?php show_bestseller_products($database_manager,$imgsPath);  ?>
					</div>

					<!-- Add Arrows -->
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
						<!-- Add Pagination -->
						<span class="togglepaginationstags" tabindex="0" role="button" aria-label=""></span>
						<div class="swiper-pagination ">
						</div>
				</div>
		</div>
	</div>
</div>

<div class="sep"></div>

<?php
$last_renewal_date = $core->getMyLastRenewalDate($database_manager);
//$last_renewal_date="2016-02-30";
$next_renewal_date = $core->addToDate($last_renewal_date, 365);
if (strtotime($core->addToDate($last_renewal_date, 335)) < strtotime($core->getFormatedDate())) {
    ?>

    <div class="widget fullwidth">
        <div class="widget-title">
            <i class="fa fa-repeat fa-fw"></i><?php echo $_SESSION["language"]->dashboard_accRenewReminderH; ?>
        </div>
        <div class="widget-contents">
            <br/><br/><br/><br/>
            <div class="countdown countdown-container container">
                <div class="clock row">
                    <div class="clock-item clock-days countdown-time-value col-sm-6 col-md-3">
                        <div class="wrap">
                            <div class="inner">
                                <div id="canvas-days" class="clock-canvas"></div>

                                <div class="clocktext">
                                    <p class="val">0</p>
                                    <p class="type-days type-time"><?php echo $_SESSION["language"]->days; ?></p>
                                </div><!-- /.text -->
                            </div><!-- /.inner -->
                        </div><!-- /.wrap -->
                    </div><!-- /.clock-item -->

                    <div class="clock-item clock-hours countdown-time-value col-sm-6 col-md-3">
                        <div class="wrap">
                            <div class="inner">
                                <div id="canvas-hours" class="clock-canvas"></div>

                                <div class="clocktext">
                                    <p class="val">0</p>
                                    <p class="type-hours type-time"><?php echo $_SESSION["language"]->hours; ?></p>
                                </div><!-- /.text -->
                            </div><!-- /.inner -->
                        </div><!-- /.wrap -->
                    </div><!-- /.clock-item -->

                    <div class="clock-item clock-minutes countdown-time-value col-sm-6 col-md-3">
                        <div class="wrap">
                            <div class="inner">
                                <div id="canvas-minutes" class="clock-canvas"></div>

                                <div class="clocktext">
                                    <p class="val">0</p>
                                    <p class="type-minutes type-time"><?php echo $_SESSION["language"]->minutes; ?></p>
                                </div><!-- /.text -->
                            </div><!-- /.inner -->
                        </div><!-- /.wrap -->
                    </div><!-- /.clock-item -->

                    <div class="clock-item clock-seconds countdown-time-value col-sm-6 col-md-3">
                        <div class="wrap">
                            <div class="inner">
                                <div id="canvas-seconds" class="clock-canvas"></div>

                                <div class="clocktext">
                                    <p class="val">0</p>
                                    <p class="type-seconds type-time"><?php echo $_SESSION["language"]->seconds; ?></p>
                                </div><!-- /.text -->
                            </div><!-- /.inner -->
                        </div><!-- /.wrap -->
                    </div><!-- /.clock-item -->
                </div><!-- /.clock -->
            </div><!-- /.countdown-wrapper -->

            <p class="italic"><?php echo $_SESSION["language"]->timetonextrenwal; ?></p>
            <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
            <script type="text/javascript" src="js/kinetic.js"></script>
            <script type="text/javascript" src="js/jquery.final-countdown.js"></script>
            <script type="text/javascript" src="js/jquery.final-countdown.js"></script>
            <script type="text/javascript">
                    $('document').ready(function () {
                        'use strict';
                        $('.countdown').final_countdown({
                            'start': <?php echo strtotime($last_renewal_date); ?>,
                            'end': <?php echo strtotime($next_renewal_date); ?>,
                            'now': <?php echo strtotime($core->getFormatedDateTime()); ?>
                        });
                    });
            </script>
        </div>
    </div>

    <?php
} else {
    ?>
    <div class="widget fullwidth">
        <div class="widget-title">
            <i class="fa fa-envelope-o fa-fw"></i><?php echo $_SESSION["language"]->dashboard_systemannouncements; ?>
        </div>
        <div class="widget-contents">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 100px;">
                            <?php echo $_SESSION["language"]->date; ?>
                        </th>
                        <th>
                            <?php echo $_SESSION["language"]->announcement; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM announcement ORDER BY id DESC LIMIT 0 , 4";
                    $result = $database_manager->query($sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $row['date']; ?>
                            </td>
                            <td>
                                <?php echo $row['details']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
}
?>
<div class="sep"></div>



<?php
$sql = "SELECT r.ir_id AS ir_id, CONCAT(r.f_name, ' ', r.l_name) AS name, "
        . " b.referral_bu_id AS referral_bu_id, b.position_to_referral AS position_to_referral, b.is_qualified AS is_qualified, "
        . " r.registration_date AS registration_date, r.qualification_date AS qualification_date "
        . " FROM ir r "
        . " LEFT OUTER JOIN bu b ON b.code = '001' AND r.ir_id = b.ir_id"
        . " WHERE b.referral_bu_id LIKE '" . $_SESSION['ir_id'] . "%' "
        . " ORDER BY registration_date DESC "
        . " LIMIT 0, 10 ";
$result = $database_manager->query($sql);
?>



<div class="widget fullwidth">
    <div class="widget-title">
        <i class="fa fa-table fa-fw"></i> <?php echo $_SESSION["language"]->latestRefferalsH; ?>
    </div>
    <div class="widget-contents">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $_SESSION["language"]->irid; ?></th>
                    <th><?php echo $_SESSION["language"]->name; ?></th>
                    <th><?php echo $_SESSION["language"]->bu; ?></th>
                    <th><?php echo $_SESSION["language"]->placement; ?></th>
                    <th><?php echo $_SESSION["language"]->registrationdate; ?></th>
                   
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row['ir_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $core->getBUCode($row['referral_bu_id']); ?></td>
                        <td><?php echo $row['position_to_referral']; ?></td>
                        <td><?php echo $row['registration_date']; ?></td>
                       
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
 var recomended_products_swiper = new Swiper('.recomended_products_swiper', {
		  slidesPerView: 1,
		  spaceBetween: 5,
		  pagination: {
			el: '.swiper-pagination',
			clickable: true,
			renderBullet: function (index, className) {
			  return '<span class="' + className + '"></span>';
			},
		  },
		  navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
			clickable: true,
		  }
		});
		
		
</script>

<?php $html_page->writeFooter(); ?>