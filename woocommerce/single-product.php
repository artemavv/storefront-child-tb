<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="facebook-domain-verification" content="4jcscro9t5xs0a6w19b2b3o6n0i2nd" />
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
   <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod
            n.callMethod.apply(n,arguments)n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'httpsconnect.facebook.neten_USfbevents.js');
        fbq('init', '3159787910950402');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/trid=3159787910950402&ev=PageView&noscript=1"
        /></noscript> 
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>





	<header>

	 <div class="container">
        <div class="header__inner">
            <div class="header__left">
                <div class="menu-btn">
                    <div class="menu-btn__icon">
                        <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                            <g fill="none" fill-rule="evenodd" stroke="#333333">
                                <path d="M13,26.5 L88,26.5"/>
                                <path d="M13,50.5 L88,50.5"/>
                                <path d="M13,50.5 L88,50.5"/>
                                <path d="M13,74.5 L88,74.5"/>
                            </g>
                        </svg>
                    </div>
                    <div class="menu-btn__text">Menu</div>
                </div>
                <nav class="mobile-menu">
                    <ul class="mobile-menu__list">
					<?php if( have_rows('top-menu', 'option') ): ?>
			<?php while( have_rows('top-menu', 'option') ): the_row(); 
			$name = get_sub_field('link_name');
			$link = get_sub_field('link_menu');

		?>
                        <li class="mobile-menu__list-item">
                            <a class="mobile-menu__list-link" href="<?php echo $link; ?>">
                                <?php echo $name; ?>
                            </a>
                        </li>
                       
                    
					 <?php endwhile; ?>
               <?php endif; ?>
                  </ul>
                    <div class="mobile-langswitch">

                    </div>
                    <div class="mobile-menu__contacts">
                        <a class="mobile-menu__phone" href="tel:<?php the_field('phone', 'option'); ?>">
                           <?php the_field('phone', 'option'); ?>
                        </a>
                        <a class="mobile-menu__email" href="mailto:<?php the_field('email', 'option'); ?>">
                            <?php the_field('email', 'option'); ?>
                        </a>
                        <div class="mobile-menu__address"><?php the_field('adress', 'option'); ?></div>
                    </div>
                    <div class="mobile-menu__social">
                        <a href="<?php the_field('inst_link', 'option'); ?>" target="blank">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#instagram"></use>
                            </svg>
                        </a>
                    </div>
                </nav>
                <div class="header__social">
                    <a class="header__social-link" href="<?php the_field('inst_link', 'option'); ?>" title="Instagram" target="blank">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#insta-new"></use>
                        </svg>
                    </a>
                </div>
                <a class="header__phone" href="tel:<?php the_field('phone', 'option'); ?>" title="Позвонить">
                   <?php the_field('phone', 'option'); ?>
                </a>
            </div>
            <a class="logo" href="/">
                <svg class="icon">
                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#logo"></use>
                </svg>
            </a>
            <div class="header__right">
                <div class="user-panel">

                    <a class="user-panel__link user-panel__favorite" href="/wishlist">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#heart2"></use>
                        </svg>
                    </a>
                     
					<div class="lrm-hide-if-logged-in user-panel__login user-panel__link">
                        
						
                      <button class="lrm-login"  type="button" class="btn">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#user"></use>
                            </svg>
                        </button>
                    </div>
					<div class="lrm-show-if-logged-in user-panel__login user-panel__link">
                        
						
                      <button class="lrm-login"  type="button" class="btn">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#user"></use>
                            </svg>
                        </button>
                            <div  id="auth-header" class="user-panel__dropdown">
                                <a class="user-panel__dropdown-item" href="/my-account/">My account</a>
                                <a class="user-panel__dropdown-item"
                                   href="/my-account/orders/">Orders</a>
                                
                                <a class=" user-panel__dropdown-item"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                   href="/my-account/customer-logout/">Logout</a>  
                            </div>
                        
                    </div>

                    <a class="user-panel__link user-panel__cart" href="/cart">
                                            <svg class="icon">
                                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#cart"></use>
                                            </svg>
                                            <?php 
                    if (class_exists('WooCommerce' )){
                    global $woocommerce; 
                    $cartCounter = $woocommerce->cart->cart_contents_count;
                    if ($cartCounter > 0) :
                    ?>
                                            <span id="cartCounter"
                                                  class="user-panel__counter">
                                                <p style="margin-left: 7px; margin-top: 3px;"><?php echo sprintf($woocommerce->cart->cart_contents_count); ?></p>
                                            </span>
                                            <?php 
                                endif;
                    } 
                    ?>
                                        </a>
                </div>
            </div>
        </div>
    </div>
	</header><!-- #masthead -->

	
<nav class="nav nav--header">
    <div class="container">
        <div class="nav__inner">
            <nav class="menu">
			
                <ul class="menu__list">
				<?php if( have_rows('top-menu', 'option') ): ?>
			<?php while( have_rows('top-menu', 'option') ): the_row(); 
			$name = get_sub_field('link_name');
			$link = get_sub_field('link_menu');

		?>
                    <li class="menu__list-item">
                        <a class="menu__list-link" href="<?php echo $link; ?>"><?php echo $name; ?></a>
                    </li>
 <?php endwhile; ?>
               
               <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</nav>

<main style="margin-top: 30px" id="content" role="main">

		
	<div id="content" class="site-content" tabindex="-1">
		
<div class="col-full">




		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			
			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>


    
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script>
var hiddenElement = document.getElementById("productDescriptionEnd");
var btn = document.querySelector('.scrollToDescription');

function handleButtonClick() {
   hiddenElement.scrollIntoView({block: "center", behavior: "smooth"});
}

btn.addEventListener('click', handleButtonClick);

const instructionSlider = new Swiper('#instruction-slider', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 30,
        },
        1200: {
            slidesPerView: 4,
        },
    }
});
</script>

</div></div>
<?php
get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
?>