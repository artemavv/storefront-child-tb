<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

$image = get_field('product_cover');

if (!$image) {
    $image = get_the_post_thumbnail_url();
}
?>
<div class="col-lg-4 col-6 col">
    <div class="product-card">
        <div class="product-card__inner">

            <a href="<?php the_permalink(); ?>" class="product-card__img">
                <?php echo woocommerce_get_product_thumbnail(); ?>
            </a>
            <div class="product-card__innerDesc">
                <h4 class="product-card__title"><?php the_title(); ?></h4>
                <ul class="product-card__list">
                    <li class="product-card__list-item">
                        Material: <span><?php global $product;
                                        /*echo"<pre>";
                    print_r($product);*/
                                        echo ($product->get_attribute('materials') ? $product->get_attribute('materials') : 'N/A'); ?></span>
                    </li>
                </ul>

                <div class="product-card__price">
                    <?php
                    $price = number_format((float)$product->get_variation_price('min', true), 2, '.', '');
                    $price_fm =  do_shortcode('[woo_multi_currency_exchange product_id="" currency="" price="'.$price.'" original_price=""]');
                    echo $price_fm;
                    ?>
                </div>

            </div>
            <button class="product-card__btn product-card__btn_add-favorite add-to-favorites" data-action="add" data-page="catalog">
                <a role="button" tabindex="0" aria-label="Add to Wishlist" class="tinvwl_add_to_wishlist_button tinvwl-icon-heart no-txt tinvwl-position-after" data-tinv-wl-list="[]" data-tinv-wl-product="<?php echo get_the_ID(); ?>" data-tinv-wl-action="addto"><svg class="icon">

                        <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#heart"></use>
                    </svg></a>
            </button>
            <div class="product-add__box-btn-wrap" id="product-add-to-cart__box-1518"><a href="<?php echo do_shortcode('[add_to_cart_url id="' . get_the_ID() . '"]'); ?>">
                    <button class="product-card__btn product-card__btn_add-cart btn btn-outline-primary" data-id="1518" data-quantity="1" data-page="catalog" data-variation="" data-material="79">
                        <svg class="icon">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#cart"></use>
                        </svg>
                    </button></a>
            </div>

        </div>
    </div>
</div>