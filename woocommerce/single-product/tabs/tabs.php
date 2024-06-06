<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

global $post;

$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) );

if ( ! empty( $product_tabs ) ) : ?>
<div class="container mt-4" id="productDescription">
		<h2 class="title mb-4"><?php echo esc_html( $heading ); ?></h2>
		<div class="row">
			<div class="col-lg-12">
				<div class="product-info__description">
					<?php the_content(); ?>
					<span id="productDescriptionEnd"></span>
				</div>
			</div>
		</div>
</div>

<section class="instruction">
		<h2 class="title"><?php the_field('instruction_header', 'option'); ?></h2>
		
		<div id="instruction-slider" class="swiper-container instruction-slider">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<div class="instruction__item">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/img/instruction/step-1.png" alt="">
						<p>
							<?php the_field('instruction_step_1', 'option'); ?>
						</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="instruction__item">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/img/instruction/step-2.png" alt="">
						<p>
							<?php the_field('instruction_step_2', 'option'); ?>
						</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="instruction__item">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/img/instruction/step-3.png" alt="">
						<p>
							<?php the_field('instruction_step_3', 'option'); ?>
						</p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="instruction__item">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/img/instruction/step-4.png" alt="">
						<p>
							<?php the_field('instruction_step_4', 'option'); ?>
						</p>
					</div>
				</div>
			</div>
			<div class="swiper-button-prev">
				<svg class="icon">
					<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-left"></use>
				</svg>
			</div>
			<div class="swiper-button-next">
				<svg class="icon">
					<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/svg/sprite/sprite.svg#arrow-right"></use>
				</svg>
			</div>
		</div>
</section>

	<div class="woocommerce-tabs wc-tabs-wrapper">
		<ul class="tabs wc-tabs" role="tablist">
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
				<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
					<a href="#tab-<?php echo esc_attr( $key ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
	</div>

<?php endif; ?>
