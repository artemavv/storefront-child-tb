<?php
/**
 * Customer email notification for sent orders.
 * 
 * These emails are sent to the customer when their order status is changed to "Sent"
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>


$user_first_name = $order->get_billing_first_name();

<?php /*  %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Dear %s,', 'woocommerce' ), esc_html( $user_first_name ) ); ?></p>

<p><?php echo esc_html__( 'I’m glad to tell you, that your order is on the way to you already!' );?></p>

<?php /*  %1$s: tracking number */ ?>
<p><?php printf( esc_html__( 'Your tracking number is %1$s .' ), '<strong>' . esc_html( $tracking_number ) . '</strong>' );?></p>

  <?php /*  %1$s: tracker site link */ ?>
<p><?php printf( esc_html__( 'You may track your order on the post website of your country or any other tracking website, for example %1$s' ), make_clickable( esc_url( '17track.net' ) ) );  ?></p>

<p><?php echo esc_html__( 'The delivery usually takes 2-4 weeks, depending on your country.' );?></p>

<p><?php echo esc_html__( 'Hope it will arrive soon!' );?></p>

<p>Best wishes,</p>
<p><strong>Tanya & Stephan</strong></p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );