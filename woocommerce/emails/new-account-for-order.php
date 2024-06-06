<?php
/**
 * Customer new account email.
 * 
 * These emails are sent to the customer when they create their first order and user account is created automatically by TannyBunny
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /*  %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_first_name ) ); ?></p>

<?php /*  %1$s: Site title */ ?>
<p><?php printf( esc_html__( 'Thanks for shopping on %1$s!' ), esc_html( $blogname ) );?></p>

<?php /*  %1$s: username, %2$s: password */ ?>
<p><?php printf( esc_html__( 'We have created your user account and your username is %1$s, password is %2$s.' ), '<strong>' . esc_html( $user_login ) . '</strong>', '<strong>' . esc_html( $user_pass ) . '</strong>' );?></p>

  <?php /*  %1$s: My account link */ ?>
<p><?php printf( esc_html__( 'You can access your account area to view orders, change your password, and more at: %1$s' ), make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) );  ?></p>

<?php // password has not been set by the user during the order creation, send them a link to set a new password ?>
<p><a href="<?php echo esc_attr( $set_password_url ); ?>"><?php printf( esc_html__( 'Click here to changee your password.', 'woocommerce' ) ); ?></a></p>


<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );