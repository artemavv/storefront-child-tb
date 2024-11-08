<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce\Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

the_title( '<h1 class="product_title entry-title product-info__title">', '</h1>' );
global $product;


$shipping = new TannyBunny_Custom_Shipping_Helper( $product );

$material = $product->get_attribute('materials');
?>
<div class="product-info">
    <ul class="product-info__list">
        <li class="product-info__list-item">
            <span>SKU</span>
            <span><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span>
        </li>
        <?php if ($material) : ?>
        <li class="product-info__list-item">
            <span>Material</span>
            <span><?php echo $material;?></span>
        </li>
				<?php endif; ?>
				<?php if ( $shipping->product_has_warehouses ) : ?>
        <li class="product-info__list-item">
            <span>Shipping</span>
            <span><?php echo $shipping->render_warehouse_options(); ?></span>
        </li>
        <?php endif; ?>
    </ul>
		
		<?php echo $shipping->render_shipping_details(); ?>
		
    <div class="product-collapse scrollToDescription">
        <button class="product-collapse__btn">
            <span>Go to the description</span>
            <svg class="icon">
                <use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#chevron-down"></use>
            </svg>
        </button>
    </div>
</div>
