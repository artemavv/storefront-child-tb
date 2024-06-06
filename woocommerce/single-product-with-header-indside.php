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
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
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
<!--                         <div class="select">
                            <label>
                                <select>
                                    <option>Рус</option>
                                    <option>Eng</option>
                                </select>
                            </label>
                        </div> -->
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
<!--                         <a href="<?php the_field('vk_link', 'option'); ?>">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#vk"></use>
                            </svg>
                        </a> -->
                        <a href="<?php the_field('inst_link', 'option'); ?>">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#instagram"></use>
                            </svg>
                        </a>
                    </div>
                </nav>
                <div class="header__social">
<!--                     <a class="header__social-link" href="https://vk.com/tanny.bunny" title="VK">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#vk-new"></use>
                        </svg>
                    </a> -->
                    <a class="header__social-link" href="<?php the_field('inst_link', 'option'); ?>" title="Instagram">
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

<!--                     <ul class="user-panel__link lang-switch">
                        <li class="lang-switch__item">
                            <a href="#">Рус</a>
                        </li>
                        <li class="lang-switch__item">
                            <a href="#">Eng</a>
                        </li>
                    </ul> -->

                    <div class="user-panel__link mob-not-show">
                        <button type="button" class="btn" id="headerSearchBtn">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#search-header"></use>
                            </svg>
                        </button>
                    </div>

                    <a class="user-panel__link user-panel__favorite" href="{{ route('lk') . '#favorites' }}">
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
    <div class="header__search-area" id="headerSearchArea">
        <form action="/" method="get" class="form-inline header__search">
            <input placeholder="Поиск по товарам" type="text" name="q" class="form-control header__search-field">
            <button id="headerSearchCloseBtn" type="button">
                <svg>
                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#close"></use>
                </svg>
            </button>

            <div class="search-result"></div>
        </form>
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



</div></div>
<?php
get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
?>