<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Contact
 *
 * @package storefront
 */

get_header(); ?>
<main id="content" role="main">


   
	 <div class="contacts">
        <div class="container">
            <h2 class="title" style="margin-top: 50px">Contacts</h2>
            <div class="contacts__inner">
                <div class="contacts__info">
										<div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#cart"></use>
                        </svg>
                        <h4 class="contacts__info-title">Warehouse (For Shipments & Returns in the USA)</h4>
                        <div class="contacts__info-text"><?php the_field('warehouse_address', 'option'); ?></div>
												<div class="contacts__info-text" style="padding-top: 20px; font-style: italic;"><?php the_field('warehouse_address_note', 'option'); ?></div>
                    </div>
                    <div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#map"></use>
                        </svg>
                        <h4 class="contacts__info-title">Company Registration & Billing Address</h4>
                        <div class="contacts__info-text"><?php the_field('registration_address', 'option'); ?></div>
												<div class="contacts__info-text" style="padding-top: 20px; font-style: italic;"><?php the_field('registration_address_note', 'option'); ?></div>
                    </div>
                    <div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#phone"></use>
                        </svg>
                        <h4 class="contacts__info-title">Phone</h4>
                        <a class="contacts__info-link" href="tel:<?php the_field('phone', 'option'); ?>"><?php the_field('phone', 'option'); ?></a>
                    </div>
                    <div class="contacts__info-item">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#mail"></use>
                        </svg>
                        <h4 class="contacts__info-title">E-mail</h4>
                        <a class="contacts__info-link"
                           href="mailto:<?php the_field('email', 'option'); ?>"><?php the_field('email', 'option'); ?></a>
                    </div>
                </div>

                <div class="contacts__map">
                    <?php the_field('map', 'option'); ?>
                </div>
            </div>
        </div>
    </div>
	

<?php
get_footer();

