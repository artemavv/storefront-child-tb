<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.3
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
			}
			?>
		</ul>
	<?php endif; ?>
	<div class="form-row place-order">
		<noscript>
			<?php
			/* translators: $1 and $2 opening and closing emphasis tags respectively */
			printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
			?>
			<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
		</noscript>


		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <?php $yandex = "ym(88676318,'reachGoal','clickOnPlaceOrder')"; ?>

		<?php echo apply_filters( 'woocommerce_order_button_html', '<button onclick="' . $yandex . '" type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

        <hr/>

        <div class="checkout">

            <div class="questions-block">

                <div class="questions-block-img" style="background-image: url('/wp-content/uploads/2022/11/tanya.jpg')">
                </div>

                <div class="questions-block-info">
                    <h3>Have any questions about payment or ear cuffs?</h3>
                    <p>Contact me on any messenger<br/>Tanya - CEO TannyBunny</p>
                    <div class="questions-block-info-links">
                        <a href="https://www.messenger.com/t/101656739201598/" target="_blank">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M27 0H5C2.23858 0 0 2.23858 0 5V27C0 29.7614 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 29.7614 0 27 0Z" fill="#1877F2"/>
                                <path d="M24 16C24 11.6 20.4 8 16 8C11.6 8 8 11.6 8 16C8 20 10.9 23.3 14.7 23.9V18.3H12.7V16H14.7V14.2C14.7 12.2 15.9 11.1 17.7 11.1C18.6 11.1 19.5 11.3 19.5 11.3V13.3H18.5C17.5 13.3 17.2 13.9 17.2 14.5V16H19.4L19 18.3H17.1V24C21.1 23.4 24 20 24 16Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/37498424830" target="_blank">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M27 0H5C2.23858 0 0 2.23858 0 5V27C0 29.7614 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 29.7614 0 27 0Z" fill="#25D366"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.6 10.3C20.1 8.8 18.1 8 16 8C11.6 8 8 11.6 8 16C8 17.4 8.40001 18.8 9.10001 20L8 24L12.2 22.9C13.4 23.5 14.7 23.9 16 23.9C20.4 23.9 24 20.3 24 15.9C24 13.8 23.1 11.8 21.6 10.3ZM16 22.6C14.8 22.6 13.6 22.3 12.6 21.7L12.4 21.6L9.89999 22.3L10.6 19.9L10.4 19.6C9.69999 18.5 9.39999 17.3 9.39999 16.1C9.39999 12.5 12.4 9.5 16 9.5C17.8 9.5 19.4 10.2 20.7 11.4C22 12.7 22.6 14.3 22.6 16.1C22.6 19.6 19.7 22.6 16 22.6ZM19.6 17.6C19.4 17.5 18.4 17 18.2 17C18 16.9 17.9 16.9 17.8 17.1C17.7 17.3 17.3 17.7 17.2 17.9C17.1 18 17 18 16.8 18C16.6 17.9 16 17.7 15.2 17C14.6 16.5 14.2 15.8 14.1 15.6C14 15.4 14.1 15.3 14.2 15.2C14.3 15.1 14.4 15 14.5 14.9C14.6 14.8 14.6 14.7 14.7 14.6C14.8 14.5 14.7 14.4 14.7 14.3C14.7 14.2 14.3 13.2 14.1 12.8C14 12.5 13.8 12.5 13.7 12.5C13.6 12.5 13.5 12.5 13.3 12.5C13.2 12.5 13 12.5 12.8 12.7C12.6 12.9 12.1 13.4 12.1 14.4C12.1 15.4 12.8 16.3 12.9 16.5C13 16.6 14.3 18.7 16.3 19.5C18 20.2 18.3 20 18.7 20C19.1 20 19.9 19.5 20 19.1C20.2 18.6 20.2 18.2 20.1 18.2C20 17.7 19.8 17.7 19.6 17.6Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="https://ig.me/m/tannybunny.jewelry" target="_blank">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M27 0H5C2.23858 0 0 2.23858 0 5V27C0 29.7614 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 29.7614 0 27 0Z" fill="#F00073"/>
                                <path d="M16 9.2C18.2 9.2 18.5 9.2 19.4 9.2C20.2 9.2 20.6 9.4 20.9 9.5C21.3 9.7 21.6 9.8 21.9 10.1C22.2 10.4 22.4 10.7 22.5 11.1C22.6 11.4 22.7 11.8 22.8 12.6C22.8 13.5 22.8 13.7 22.8 16C22.8 18.3 22.8 18.5 22.8 19.4C22.8 20.2 22.6 20.6 22.5 20.9C22.3 21.3 22.2 21.6 21.9 21.9C21.6 22.2 21.3 22.4 20.9 22.5C20.6 22.6 20.2 22.7 19.4 22.8C18.5 22.8 18.3 22.8 16 22.8C13.7 22.8 13.5 22.8 12.6 22.8C11.8 22.8 11.4 22.6 11.1 22.5C10.7 22.3 10.4 22.2 10.1 21.9C9.8 21.6 9.6 21.3 9.5 20.9C9.4 20.6 9.3 20.2 9.2 19.4C9.2 18.5 9.2 18.3 9.2 16C9.2 13.7 9.2 13.5 9.2 12.6C9.2 11.8 9.4 11.4 9.5 11.1C9.7 10.7 9.8 10.4 10.1 10.1C10.4 9.8 10.7 9.6 11.1 9.5C11.4 9.4 11.8 9.3 12.6 9.2C13.5 9.2 13.8 9.2 16 9.2ZM16 7.7C13.7 7.7 13.5 7.7 12.6 7.7C11.7 7.7 11.1 7.9 10.6 8.1C10.1 8.3 9.6 8.6 9.1 9.1C8.6 9.6 8.4 10 8.1 10.6C7.9 11.1 7.8 11.7 7.7 12.6C7.7 13.5 7.7 13.8 7.7 16C7.7 18.3 7.7 18.5 7.7 19.4C7.7 20.3 7.9 20.9 8.1 21.4C8.3 21.9 8.6 22.4 9.1 22.9C9.6 23.4 10 23.6 10.6 23.9C11.1 24.1 11.7 24.2 12.6 24.3C13.5 24.3 13.8 24.3 16 24.3C18.2 24.3 18.5 24.3 19.4 24.3C20.3 24.3 20.9 24.1 21.4 23.9C21.9 23.7 22.4 23.4 22.9 22.9C23.4 22.4 23.6 22 23.9 21.4C24.1 20.9 24.2 20.3 24.3 19.4C24.3 18.5 24.3 18.2 24.3 16C24.3 13.8 24.3 13.5 24.3 12.6C24.3 11.7 24.1 11.1 23.9 10.6C23.7 10.1 23.4 9.6 22.9 9.1C22.4 8.6 22 8.4 21.4 8.1C20.9 7.9 20.3 7.8 19.4 7.7C18.5 7.7 18.3 7.7 16 7.7Z" fill="white"/>
                                <path d="M16 11.7C13.6 11.7 11.7 13.6 11.7 16C11.7 18.4 13.6 20.3 16 20.3C18.4 20.3 20.3 18.4 20.3 16C20.3 13.6 18.4 11.7 16 11.7ZM16 18.8C14.5 18.8 13.2 17.6 13.2 16C13.2 14.5 14.4 13.2 16 13.2C17.5 13.2 18.8 14.4 18.8 16C18.8 17.5 17.5 18.8 16 18.8Z" fill="white"/>
                                <path d="M20.4 12.6C20.9523 12.6 21.4 12.1523 21.4 11.6C21.4 11.0477 20.9523 10.6 20.4 10.6C19.8477 10.6 19.4 11.0477 19.4 11.6C19.4 12.1523 19.8477 12.6 20.4 12.6Z" fill="white"/>
                            </svg>

                        </a>
                    </div>
                </div>

            </div>

            <hr/>

        </div>
		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
		<?php wc_get_template( 'checkout/terms.php' ); ?>
	</div>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
