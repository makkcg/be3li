<?php
$html_page->writeHeader();
/***get the products images path**/
$imgsPath=$core->getboimgPathUrl($database_manager);
/************************************/
$html_page->writeBody($_GET['title'],$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

function create_products_menu($database_manager){
	$sqlcat = "SELECT `id`,`title`,`desc`,`artitle`,`ardesc`,`img`,`comment` FROM `categories` WHERE `is_enabled`=1 ";
	$resultcat = $database_manager->query($sqlcat);
	while ($row = mysqli_fetch_assoc($resultcat)) {
		$catid=$row['id'];
		$catname=$row['title'];
		$catnameAr=$row['artitle'];
		$catdesc=$row['desc'];
		$catdescAr=$row['ardesc'];
		$catimg=$imgsPath.$row['img'];
		$categorylink="?page=categories&title=My%20Shop&catid=".$catid;
		
		/****get subcats of the catid***/
		$sqlsubcat = "SELECT `id`,`title`,`desc`,`artitle`,`ardesc`,`img`,`catid` FROM `subcategories` WHERE `is_enabled`=1 AND `catid`='".$catid."' AND id!=1";
	$resultsubcat = $database_manager->query($sqlsubcat);
	$rowssubcatcount=mysqli_num_rows($resultsubcat);
	if($rowssubcatcount<1){
	?>
	<li id="nav__item--432" class="nav__item nav__menu-item">
		<a class="nav__link nav__link--toplevel" href="<?php echo $categorylink;?>"><?php echo $catname;?></a>
	</li>
	<?php
	} else{/***end if no sub cats found***/
	?>
				<li id="nav__item--178" class="nav__item nav__menu-item nav__menu-item--has-children" tabindex="0">
                    <span class="nav__link nav__link--has-dropdown">
                       <?php echo $catname;?>
                        <svg class="icon icon--dropdown" viewBox="0 0 24 24" style="height: 1em; width: 1em">
                            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"></path>
                        </svg>
                    </span>
                    <ul class="nav__dropdown">
						<li class="nav__menu-item nav__item--repeated">
                            <a class="nav__link" href="<?php echo $categorylink;?>"><?php echo $catname;?></a>
                        </li>
						<?php /***loop through sub cats***/
						while ($row1 = mysqli_fetch_assoc($resultsubcat)) {
							$subcatid=$row1['id'];
							$subcatname=$row1['title'];
							$subcatnameAr=$row1['artitle'];
							$subcatdesc=$row1['desc'];
							$subcatdescAr=$row1['ardesc'];
							//$subcatimg=$imgsPath.$row1['img'];
							$subcategorylink="?page=categories&title=My%20Shop&catid=".$catid."&subcatid=".$subcatid;
						?>
                        
						<li class="nav__menu-item">
                            <a class="nav__link" href="<?php echo $subcategorylink;?>"><?php echo $subcatname;?></a>
                        </li>
						<?php
						}/***end while sub cats***/
						?>
                    </ul>
                </li>
	<?php
	}/***end else if sub cats found**/
}/***end while categories***/
}/***end function products menu***/


function show_main_categories($database_manager,$imgsPath){
	$sqlcat = "SELECT `id`,`title`,`desc`,`artitle`,`ardesc`,`img`,`comment` FROM `categories` WHERE `is_enabled`=1 ";
	$resultcat = $database_manager->query($sqlcat);
	while ($row = mysqli_fetch_assoc($resultcat)) {
		$catid=$row['id'];
		$catname=$row['title'];
		$catnameAr=$row['artitle'];
		$catdesc=$row['desc'];
		$catdescAr=$row['ardesc'];
		$catimg=$imgsPath.$row['img'];
		$categorylink="?page=categories&title=My%20Shop&catid=".$catid;
		?>
			<div class="col-xs-12 col-md-3 col-lg-3 col-xl-3 cat-box-wrap">
								<a class="swiperbtn " style="" href="<?php echo $categorylink; ?>">
								<div class="product_box_title"><strong><?php echo $catname; ?></strong></div>
									<img src="<?php echo $catimg; ?>" class="img-fluid swiper-lazy" alt="<?php echo $catname; ?>" title="<?php echo $catname; ?>">
									<div class="carousel-caption-vid-desc">
										
										<span><?php //echo $catdesc; ?></span>
									</div>	
								</a>
			</div>

		<?php 
	
	}/**end while**/
}/***end show cat function***/

/***function to show subcategories based on cat id***/
function show_sub_categories_or_products($database_manager,$imgsPath,$catid){
	$sqlcat = "SELECT `id`,`title`,`desc`,`artitle`,`ardesc`,`img`,`catid` FROM `subcategories` WHERE `is_enabled`=1 AND `catid`='".$catid."' AND id!=1";
	$resultcat = $database_manager->query($sqlcat);
	$rowscount=mysqli_num_rows($resultcat);
	if($resultcat && $rowscount>0){
		while ($row = mysqli_fetch_assoc($resultcat)) {
			$subcatid=$row['id'];
			$subcatname=$row['title'];
			$subcatnameAr=$row['artitle'];
			$subcatdesc=$row['desc'];
			$subcatdescAr=$row['ardesc'];
			$subcatimg=$imgsPath.$row['img'];
			$subcategorylink="?page=categories&title=My%20Shop&catid=".$catid."&subcatid=".$subcatid;
			?>	
				<div class="col-xs-12 col-md-3 col-lg-3 col-xl-3 cat-box-wrap">
								<a class="swiperbtn " style="" href="<?php echo $subcategorylink; ?>">
								<div class="product_box_title"><strong><?php echo $subcatname; ?></strong></div>
									<img src="<?php echo $subcatimg; ?>" class="img-fluid swiper-lazy" alt="<?php echo $subcatname; ?>" title="<?php echo $subcatname; ?>">
									<div class="carousel-caption-vid-desc">
										
										<span><?php //echo $catdesc; ?></span>
									</div>	
								</a>
				</div>

			<?php 
		
		}/**end while**/
	}else{/***end if result in sub cat, if no result load the products of main cat***/
		$sqlproduct = "SELECT id, name,desc,img,price,p_l0_com FROM `product` WHERE `is_enabled`=1 AND `catid`='".$catid."'";
		$resultproduct = $database_manager->query($sqlproduct);
		$rowscount=mysqli_num_rows($resultproduct);
		if($resultproduct && $rowscount>0){
		while ($row = mysqli_fetch_assoc($resultproduct)) {
			$productid=$row['id'];
			$productname=$row['name'];
			$productimg=$imgsPath.$row['img'];
			$productdesc=$row['desc'];
			$productprice=$row['price'];
			$productcashback=$row['p_l0_com'];
			if(isset($_GET['subcatid'])){
				$subcatid=$_GET['subcatid'];
			}else{
				$subcatid=0;
			}
			$productlink="?page=products&title=My%20Shop&catid=".$catid."&subcatid=".$subcatid."&productid=".$productid;
			?>		
			<div class="col-xs-12 col-md-4 col-lg-4 col-xl-4 cat-box-wrap">
									<div class="swiperbtn swiperbtnproduct" style="" >
									<div class="product_box_title"><strong><?php echo $productname; ?></strong></div>
										<img src="<?php echo $productimg; ?>" class="img-fluid swiper-lazy" alt="<?php echo $productname; ?>" title="<?php echo $productname; ?>">
										
										<div class="carousel-caption-vid-desc">
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 buttons_container">
												 <a class="col-xs-12 col-md-5 col-lg-5 col-xl-5 btn btn-primary product_addtocart_btn" href="index.php?page=cart&title=<?php echo $_GET['title']; ?>&id=<?php echo $productid; ?>">Add to Cart</a>
												 <div class="col-xs-0 col-md-2 col-lg-2 col-xl-2"></div>
												 <a class="col-xs-12 col-md-5 col-lg-5 col-xl-5 btn btn-danger product_addtofav_btn" href="#">Add to Favourits</a>
											 </div>
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 product_box_title product_box_price"><strong>Price : </strong><?php echo $productprice; ?> LE</div>
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 product_box_title"><strong>Cashback : </strong><?php echo $productcashback; ?> LE</div>
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 product_box_title product_box_desc"><?php readmoretrim($productdesc,80,$productlink); ?></div>
											
											
											 
										</div>
									</div>
			</div>

			<?php 
			}/***end while products of main cat*/
		}/****end if products result***/
	}/***end else if no result in sub cat***/
}/**end function***/

function show_products($database_manager,$imgsPath,$catid,$subcatid){
	$sqlproduct = "SELECT id, name, `desc`, img, p_l0_com, price FROM `product` WHERE `is_enabled`=1 AND `catid`='".$catid."' AND `subcatid`='".$subcatid."'";
	//echo $sqlproduct;
		$resultproduct = $database_manager->query($sqlproduct);
		$rowscount= mysqli_num_rows($resultproduct);
		if($resultproduct && $rowscount>0){
		while ($row = mysqli_fetch_assoc($resultproduct)) {
			$productid=$row['id'];
			$productname=$row['name'];
			$productimg=$imgsPath.$row['img'];
			$productdesc=$row['desc'];
			$productprice=$row['price'];
			$productcashback=$row['p_l0_com'];
			if(isset($_GET['subcatid'])){
				$subcatid=$_GET['subcatid'];
			}else{
				$subcatid=0;
			}
			
			
			$productlink="?page=products&title=My%20Shop&catid=".$catid."&subcatid=".$subcatid."&productid=".$productid;
			?>		
			<div class="col-xs-12 col-md-4 col-lg-4 col-xl-4 cat-box-wrap">
									<div class="swiperbtn swiperbtnproduct" style="" >
									<div class="product_box_title"><strong><?php echo $productname; ?></strong></div>
										<img src="<?php echo $productimg; ?>" class="img-fluid swiper-lazy" alt="<?php echo $productname; ?>" title="<?php echo $productname; ?>">
										
										<div class="carousel-caption-vid-desc">
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 buttons_container">
												 <a class="col-xs-12 col-md-5 col-lg-5 col-xl-5 btn btn-primary product_addtocart_btn" href="index.php?page=cart&title=<?php echo $_GET['title']; ?>&id=<?php echo $productid; ?>">Add to Cart</a>
												 <div class="col-xs-0 col-md-2 col-lg-2 col-xl-2"></div>
												 <a class="col-xs-12 col-md-5 col-lg-5 col-xl-5 btn btn-danger product_addtofav_btn" href="#">Add to Favourits</a>
											 </div>
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 product_box_title product_box_price"><strong>Price : </strong><?php echo $productprice; ?> LE</div>
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 product_box_title"><strong>Cashback : </strong><?php echo $productcashback; ?> LE</div>
											<div class="col-xs-12 col-md-12 col-lg-12 col-xl-12 product_box_title product_box_desc"><?php readmoretrim($productdesc,80,$productlink); ?></div>
											
											
											 
										</div>
									</div>
			</div>

			<?php 
			}/***end while products of main cat*/
		}else{/****end if products result***/
			
			echo 'No Products to show!';
		}/****end else if no result from products***/
		
}/****end function***/

/****function to genereate breadcrumb***/
function generate_breadcrumb($database_manager,$catid,$subcatid,$productid){
	$breadcrumb='<a href="index.php?page=categories&title=My%20Shop" class="breadcrumb-item">All Categories</a>';
	if(!is_null($catid) || !$catid=="" || !$catid==0){
		$sqlcat = "SELECT `title`,`artitle` FROM `categories` WHERE `is_enabled`=1 AND `id`='".$catid."'";
		$resultcat = $database_manager->query($sqlcat);
		$row = mysqli_fetch_assoc($resultcat);
		$catname=$row['title'];
		$breadcrumb.='<a class="breadcrumb-sep">   &gt;   </a><a href="index.php?page=categories&title=My%20Shop&catid='.$catid.'" class="breadcrumb-item">'.$catname.'</a>';
	}
	if(!is_null($subcatid) || !$subcatid=="" || !$subcatid==0){
		$sqlcat = "SELECT `title`,`artitle` FROM `subcategories` WHERE `is_enabled`=1 AND id='".$subcatid."' ";
		$resultcat = $database_manager->query($sqlcat);
		$row = mysqli_fetch_assoc($resultcat);
		$subcatname=$row['title'];
		$breadcrumb.='<a class="breadcrumb-sep">   &gt;   </a><a href="index.php?page=categories&title=My%20Shop&catid='.$catid.'&subcatid='.$subcatid.'" class="breadcrumb-item">'.$subcatname.'</a>';
	}
	if(!is_null($productid) || !$productid=="" || !$productid==0){
		$sqlcat = "SELECT `name` FROM `product` WHERE `is_enabled`=1 AND `id`=".$productid;
		$resultcat = $database_manager->query($sqlcat);
		$row = mysqli_fetch_assoc($resultcat);
		$sproductname=$row['name'];
		$breadcrumb.='<a class="breadcrumb-sep">   &gt;   </a><a href="index.php?page=categories&title=My%20Shop&catid='.$catid.'&subcatid='.$subcatid.'&productid='.$productid.'" class="breadcrumb-item">'.$sproductname.'</a>';
	}
	
	echo $breadcrumb;
} /***end function **/

/***function to trim the long text and add readmore for product description***/
function readmoretrim($string,$maxlength,$readmorelink){
	// strip tags to avoid breaking any html
$string = strip_tags($string);
if (strlen($string) > $maxlength) {

    // truncate string
    $stringCut = substr($string, 0, $maxlength);
    $endPoint = strrpos($stringCut, ' ');

    //if the string doesn't contain any space then it will cut without word basis.
    $string = $endPoint? substr($stringCut, 0, $endPoint):substr($stringCut, 0);
    $string .= '... <a href="'.$readmorelink.'">Read More</a>';
}
echo $string;
}
?>
<!------page show-------------------------------------------------------------------->
<div class=" col-xs-12 col-md-12 col-lg-12 productmenu_wrap">
	<nav id="xnav" class="xnav">

        <div id="nav__outer-wrap" class="nav__outer-wrap">
            
            <ul id="nav__inner-wrap" class="nav__inner-wrap">
                <?php 
				create_products_menu($database_manager);
				?>
                <li id="nav__item--right-spacer" class="nav__item nav__item--right-spacer"></li>
            </ul>
        </div>
        <button id="nav__scroll--left" class="nav__scroll nav__scroll--left hide">‹</button>
        <button id="nav__scroll--right" class="nav__scroll nav__scroll--right hide">›</button>
    </nav>
	<div class="nav__placeholder"></div>
</div>
<!---breadcrumb-->
<div class=" col-xs-12 col-md-12 col-lg-12">
	<div class="dcbbreadcrumb">
				<?php generate_breadcrumb($database_manager,$_GET['catid'],$_GET['subcatid'],$_GET['productid']); ?>
	</div>
</div>
<!---show page content cat or subcat or product id-->
<div class=" page_content_wrap">
	
		<!--Main products Categories boxes show -->
				<div  class="cat_products_wrap">
						<?php 
						/***if no category selected show all cats**/
						if(!isset($_GET['catid']) || $_GET['catid']==0 || $_GET['catid']==""){
							show_main_categories($database_manager,$imgsPath);
						}else{/***else if cat or sub cat was set***/
							if(isset($_GET['subcatid']) && $_GET['subcatid']!=0 && $_GET['subcatid']!=""){
								show_products($database_manager,$imgsPath,$_GET['catid'],$_GET['subcatid']); 
							}else{
								/***check if there is sub cats in the selected cat, if not show the products***/
								show_sub_categories_or_products($database_manager,$imgsPath,$_GET['catid']);	
							}
						}
						  						
						?>
				</div>
</div>
<!------/page show-------------------------------------------------------------------->
<script>
 var recomended_products_swiper = new Swiper('.recomended_products_swiper', {
		  slidesPerView: 4,
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
		

'use strict';
!function utilities() 
{
    // setup "gc" root object for all custom global variables and functions
    // Why gc? I work at Goshen College. Learn more at goshen.edu!
    var gc = {};

    // debounce
    gc.debounce = function(func, wait) {var timeout;return function() {var context = this, args = arguments;var later = function() {timeout = null;func.apply(context, args);};clearTimeout(timeout);timeout = setTimeout(later, wait);};};

    // setup window resize debouncer that triggers callbacks (passing them starting & ending width)
    // window.innerWidth is cached as gc.width to prevent layout thrashing: https://gist.github.com/paulirish/5d52fb081b3570c81e3a
    gc.width = window.innerWidth;
    var callbacks = {}; // 'name' => callbackFunction

    window.addEventListener('resize', gc.debounce( function handleWindowResize()
    {
        var endWidth = window.innerWidth;
        for (var name in callbacks) {
            if (callbacks.hasOwnProperty(name)) {
                callbacks[name](gc.width, endWidth);
            }
        }
        gc.width = endWidth;
    }, 200 ));

    gc.addResizeCallback = function( name, callbackFunction ) {
        if ( !callbacks.hasOwnProperty(name) ) {
            callbacks[name] = callbackFunction;
        }
    }

    gc.removeResizeCallback = function( name ) {
        delete callbacks[name];
    }

    // make gc globally accessible!
    window.gc = gc;
}();

/**
 * Setup buttons on horizontally-scrolling nav bar
 * 
 * Active/deactivate site nav bar to toggle visibility of wrappers.
 *      When wrappers are visible, dropdowns can be seen and scrolled over, but nothing at the top of the page can be clicked
 *      When wrappers are invisible, dropdowns cannot be seen but the rest of the page can be interacted with fine
 */
!function scrollSiteNavBar() 
{
    /**
     * Setup buttons for horizontal scrolling
     */
    var component = document.getElementById('nav');

    // 'wrapper' that gets scrolled. Changes depending on screen width
    var outerWrap = document.getElementById('nav__outer-wrap'); // for <480 wide screens
    var innerWrap = document.getElementById('nav__inner-wrap'); // for 480+ wide screens

    // don't run if there's no site nav bar on this page (e.g. homepage)
    if (outerWrap === null) { return; }

    // buttons for automatic scrolling
    var leftBtn = document.getElementById('nav__scroll--left');
    var rightBtn = document.getElementById('nav__scroll--right');

    // spacer on the right side of menu that rightBtn covers up
    var rightSpacerWidth = 28;

    // sticky left-aligned header on 480+ wide screens
    var header = document.getElementById('nav__heading');

    // initialize buttons once font has been loaded (the size of the wrappers change) (timeout of 3s by default)
    /*var font = new FontFaceObserver('Arial');*/
	setTimeout(init, 200);
   /* font.load()
        .then(function () 
        {
            // Hack: use setTimeout to ensure the correct clientWidth of 'header' has been calculated.
            // (Perhaps this is a bug w/ the FontFaceObserver library?)
            setTimeout(init, 200);
        })
        .catch(function () 
        {
            setTimeout(init, 200);
        });*/

    // Initialize component
    function init() 
    {
        // Update component properties based on screen width
        reset(gc.width);

        // add click listeners on buttons
        leftBtn.addEventListener('click', scrollLeft);
        rightBtn.addEventListener('click', scrollRight);
        
        // on scroll, show/hide buttons (e.g. don't show scroll-left button when you're already on the left side)
        // re-calculate wrapper.scrollLeft every time, of course, b/c this changes with scrolling
        outerWrap.addEventListener('scroll', gc.debounce( function () { toggleButtons(wrapper.scrollLeft); }, 100 ));
        innerWrap.addEventListener('scroll', gc.debounce( function () { toggleButtons(wrapper.scrollLeft); }, 100 ));

        // reset when screen width changes
        gc.addResizeCallback('siteNavWrapper', function (startWidth, endWidth) {
            reset(endWidth);
        });
    }

    /*
        the "wrapper" is the element with accurate scrollLeft and scrollWidth
            at < 480px, wrapper is outerWrap
            at >= 480px, wrapper is innerWrap
    */
    var wrapper, limit, amount;

    // Update component properties so it adapts to screen width
    function reset(screenWidth) 
    {
        if (screenWidth < 480) {
            // scrolling menu is fullwidth. header scrolls as well
            wrapper = outerWrap;
            // left button is up against left side of screen
            leftBtn.style.left = 0;
      // limit is the amount of pixels that the navigation can be horizontally scrolled
            // here, scroll area is entire width of screen.
            limit = wrapper.scrollWidth - screenWidth;
            // scroll by 250 each time button is pressed
            amount = 250;
        }
        else {
            // scrolling menu is almost fullwidth. header is fixed in place on left side
            wrapper = innerWrap;
            // left button should be to the right of the header
            leftBtn.style.left = header.clientWidth +'px';
            // scroll area is VISIBLE width of scrollable area (the "window" you can see)
            limit = wrapper.scrollWidth - wrapper.clientWidth;
            // scroll by 300 every time button is pressed
            amount = 300;
        }

        // show/hide spacer depending on whether scrolling is possible
        if (limit <= 0) {
            // no scrolling is possible. hide spacer
            component.classList.remove('nav--scrollable');
        }
        // scrolling is possible. show spacer if hidden
        else {
            component.classList.add('nav--scrollable');
            // adjust limit to take into account the spacer that was just added
            limit += rightSpacerWidth;
        }

        // calculate which buttons should be visible
        toggleButtons(wrapper.scrollLeft);
    }

    function scrollLeft() {
        scroll( -amount );
    }
    function scrollRight() {
        scroll( amount );
    }

    function scroll(amount) {
        var start = wrapper.scrollLeft;
        var end = start + amount;

        tween( start, end, 1000, easeInOutQuad);
    }

    function toggleButtons(scrollPos) {
         console.log('toggleButtons', scrollPos, 'of', limit);

        // screen too wide for scrolling
        if (limit <= 0) {
            hide(leftBtn);
            hide(rightBtn);
        }
        // leftmost position (give 10px so it hides a bit prematurely)
        else if (scrollPos <= 10) {
            hide(leftBtn);
            show(rightBtn);
        }
        // rightmost position (compensate for rightSpacer)
        else if (scrollPos >= limit - rightSpacerWidth) {
            hide(rightBtn);
            show(leftBtn);
        }
        // anywhere in between
        else {
            show(leftBtn);
            show(rightBtn);
        }
    }

    function show(elem) {
        elem.classList.remove('hide');
    // why the delay? so buttons can fade in/out (transitions defined in CSS classes)
        setTimeout(function () {
            elem.classList.add('nav__scroll--visible');
        }, 100);
    }
    function hide(elem) {
        elem.classList.remove('nav__scroll--visible');
        setTimeout(function () {
            elem.classList.add('hide');
        }, 300);
    }

    function tween(start, end, duration, easing) {
        var delta = end - start;
        var startTime = performance.now();
        var tweenLoop = function (time) {
            var t = (!time ? 0 : time - startTime);
            var factor = easing(null, t, 0, 1, duration);
            wrapper.scrollLeft = start + delta * factor;
            if (t < duration && wrapper.scrollLeft != end) {
                requestAnimationFrame(tweenLoop);
            }
        }
        tweenLoop();
    };

    function easeInOutQuad(x, t, b, c, d) 
    {
        if ((t/=d/2) < 1) return c/2*t*t + b;
        return -c/2 * ((--t)*(t-2) - 1) + b;
    }

    /**
     * Setup activation/deactivation
     * Both wrappers are very tall so that the dropdown menus (below nav) can be seen.
     * However, this means that the wrappers cover up the page below the menu so things
     * can't be interacted with. Thus, we need to listen for when the menu's being interacted
     * with and show/hide the wrappers as needed.
     */
    handle(activate, true);

    function activate() 
    {
        requestAnimationFrame(function () {
            component.classList.add('nav--hovered');
            handle(deactivate, true);
        });
        handle(activate, false);
    }

    function deactivate(evt) 
    {
        if (evt.target === outerWrap || evt.target === innerWrap) {
            component.classList.remove('nav--hovered');
            handle(deactivate, false);
            handle(activate, true);
        }
    }

    function handle(callback, addOrRemove) 
    {
        if (addOrRemove) {
            outerWrap.addEventListener('touchstart', callback);
            outerWrap.addEventListener('mouseover', callback);
        }
        else {
            outerWrap.removeEventListener('touchstart', callback);
            outerWrap.removeEventListener('mouseover', callback);
        }
    }
}();

/**
 * Keep dropdowns open when their child links are focused by a keyboard
 */
!function accessibleDropdowns() 
{
    // for site nav bar, always setup dropdowns
    var nav = document.getElementById('nav');
    var siteNavOptions = {
        selector: '.nav__item',
        onFocusIn: function(elem) {
            elem.classList.add('nav__item--has-focus');
            nav.classList.add('nav--focused');
        },
        onFocusOut: function(elem) {
            elem.classList.remove('nav__item--has-focus');
        },
        onAllFocusOut: function() {
            nav.classList.remove('nav--focused');
        }
    }
    init( siteNavOptions );

    /**
     * Listen for "focusin" and "focusout" events and toggle dropdowns accordingly
     * @param  {Object} options { 
     *  selector: '.nav__item', // dropdown
     *  onFocusIn: function(elem) {...}, // dropdown focused
     *  onFocusOut: function(elem) {...} // dropdown unfocused
     *  onAllFocusOut: function() {...} // all dropdowns unfocused
     * }
     */
    function init( options ) 
    {
        var focusedDropdownId = '';
        var lastFocusTime = 0;
        $(options.selector).on('focusin', function (evt)
        {
            // a new dropdown was focused
            lastFocusTime = window.performance.now();
            if (this.id !== focusedDropdownId) {
                focusedDropdownId = this.id;
                // display dropdown (until unfocused)
                options.onFocusIn( this );
            }
        });
        $(options.selector).on('focusout', function (evt)
        {
            // Remove unfocused dropdown.
            // Wait a bit (25ms) first b/c the event firing of focus in/out is unpredictable and we need to be sure
            // that focusedDropdownId is set correctly before hiding and dropdowns
            var self = this;
            var wait = 25;
            setTimeout(function () 
            {
                // Hide unfocused dropdown if...
                // 1. a different dropdown has been focused
                if (self.id !== focusedDropdownId) {
                    options.onFocusOut( self );
                }
                // 2. a new item in this dropdown hasn't been focused
                else if ( window.performance.now() - lastFocusTime > wait * 2 ) {
                    focusedDropdownId = '';
                    options.onFocusOut( self );
                    options.onAllFocusOut();
                }
            }, wait);
        });
    }
}();		
</script>


<?php $html_page->writeFooter(); ?>