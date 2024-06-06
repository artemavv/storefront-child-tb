<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="yandex-verification" content="b031656759afd33c"/>
    <meta name="facebook-domain-verification" content="4jcscro9t5xs0a6w19b2b3o6n0i2nd" />
	<meta name="p:domain_verify" content="1fc6f158c319fa7c7bb61855058e221b"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">

    <?php
    wp_head();
    ?>

	<script>
       	document.addEventListener('DOMContentLoaded', function() {
			setTimeout(function(){

				// jQuery(window).on("load",function(){
					var url = window.location.pathname, 
					urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); 
					jQuery('.owl-item .sa_vert_center a.catalog-tabs__nav-item').each(function(){
                        
						// and test its normalized href against the url pathname regexp
						if(urlRegExp.test(this.href.replace(/\/$/,''))){
							jQuery(this).addClass('active_text');
                            console.log(jQuery(".active_text").parent('p').parent('.sa_vert_center').parent('.sa_vert_center_wrap').parent('.owl-item').index());
                            var index = jQuery(".active_text").parent('p').parent('.sa_vert_center').parent('.sa_vert_center_wrap').parent('.owl-item').index();
                            jQuery('#slider_3341').trigger('to.owl.carousel', [index, 500, true]);
						}
					});
				// })


              /* code for filter on shop and tags page */
               if ((window.location.href.indexOf("/shop") > -1) || (window.location.href.indexOf("/product-tag/") > -1)) {
                   if (jQuery(window).width() < 800) {
                       jQuery(".owl-next").trigger("click");
                    }
                  }
			},0)
		}, false);
	</script>
	<style>
		.active_text{
		background-color: #B35BD2;
			border: 2px solid #B35BD2;
		color: white;
		}
		
	</style>
	
</head>

<body <?php body_class(); ?>>

<header class="header">
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
                        <?php if (have_rows('top-menu', 'option')): ?>
                            <?php while (have_rows('top-menu', 'option')): the_row();
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
                        <!--                         <a href="<?php the_field('vk_link', 'option'); ?>">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#vk"></use>
                            </svg>
                        </a> -->
                        <a href="<?php the_field('inst_link', 'option'); ?>" target="blank">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#instagram"></use>
                            </svg>
                        </a>
                    </div>
                </nav>
                <div class="header__social">
                    <a class="header__social-link" target="blank" href="<?php the_field('inst_link', 'option'); ?>"
                       title="Instagram">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#insta-new"></use>
                        </svg>
                    </a>
                </div>
                <a class="header__phone" href="tel:<?php the_field('phone', 'option'); ?>" title="call">
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

                    <a href="/wishlist" class="user-panel__link user-panel__favorite" href="">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#heart2"></use>
                        </svg>
                    </a>


                    <div class="user-panel__login user-panel__link">


                        <button class="lrm-login" type="button" class="btn">
                            <svg class="icon">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#user"></use>
                            </svg>
                        </button>
                        <div id="auth-header" class="user-panel__dropdown">
                            <a class="user-panel__dropdown-item" href="/my-account/">My account</a>
                            <a class="user-panel__dropdown-item"
                               href="/my-account/orders/">Orders</a>

                            <a class=" user-panel__dropdown-item"

                               href="/my-account/customer-logout/">Logout</a>
                        </div>

                    </div>


                    <a class="user-panel__link user-panel__cart" href="/cart">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#cart"></use>
                        </svg>
                        <?php
                        if (class_exists('WooCommerce')) {
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
</header>

<nav class="nav nav--header">
    <div class="container">
        <div class="nav__inner">
            <nav class="menu">

                <ul class="menu__list">
                    <?php if (have_rows('top-menu', 'option')): ?>
                        <?php while (have_rows('top-menu', 'option')): the_row();
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





