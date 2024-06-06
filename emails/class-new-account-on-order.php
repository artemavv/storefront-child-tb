<?php

/**
 * Custom WooCommerce mailer class for TannyBunny
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'TB_Email_New_Account_For_Order', false ) ) :

	/**
	 * New Account created for order.
	 *
	 * An email sent to the customer when they create an order
   * and their user account was created automatically.
	 *
	 */
	class TB_Email_New_Account_For_Order extends WC_Email {

		/**
		 * User login name.
		 *
		 * @var string
		 */
		public $user_login;

		/**
		 * User email.
		 *
		 * @var string
		 */
		public $user_email;
    
		/**
		 * User first name.
		 *
		 * @var string
		 */
		public $user_first_name;
    
    /**
		 * User last name.
		 *
		 * @var string
		 */
		public $user_last_name;

    /**
		 * User password.
		 *
		 * @var string
		 */
		public $user_pass;

		/**
		 * Magic link to set initial password.
		 *
		 * @var string
		 */
		public $set_password_url;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'new_account_for_order';
			$this->customer_email = true;
			$this->title          = __( 'New account for order', 'woocommerce' );
			$this->description    = __( 'Customer "new account for order" emails are sent to the customer when they create their first order and user account is created automatically by TannyBunny.', 'woocommerce' );
			$this->template_html  = 'emails/new-account-for-order.php';
			$this->template_plain = 'emails/plain/new-account-for-order.php';

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Your {site_title} account has been created!', 'woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Welcome to {site_title}', 'woocommerce' );
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User ID.
		 * @param string $user_pass User password.
		 */
		public function trigger( $user_id, $user_pass ) {
			$this->setup_locale();

			if ( $user_id ) {
				$this->object = new WP_User( $user_id );

				$this->user_pass          = $user_pass;
				$this->user_login         = stripslashes( $this->object->user_login );
				$this->user_email         = stripslashes( $this->object->user_email );
        $this->user_first_name    = $this->object->first_name;
        $this->user_last_name     = $this->object->last_name;
				$this->recipient          = $this->user_email;
				$this->set_password_url   = $this->generate_set_password_url();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'user_login'         => $this->user_login,
					'user_pass'          => $this->user_pass,
          'user_first_name'    => $this->user_first_name,
          'user_last_name'     => $this->user_last_name,
					'blogname'           => $this->get_blogname(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
					'set_password_url'   => $this->set_password_url,
				)
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'user_login'         => $this->user_login,
					'user_pass'          => $this->user_pass,
          'user_first_name'    => $this->user_first_name,
          'user_last_name'     => $this->user_last_name,
					'blogname'           => $this->get_blogname(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
					'set_password_url'   => $this->set_password_url,
				)
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'We look forward to seeing you soon.', 'woocommerce' );
		}

		/**
		 * Generate set password URL link for a new user.
		 * 
		 * See also Automattic\WooCommerce\Blocks\Domain\Services\Email\CustomerNewAccount and wp_new_user_notification.
		 * 
		 * @since 6.0.0
		 * @return string
		 */
		protected function generate_set_password_url() {
			// Generate a magic link so user can set initial password.
			$key = get_password_reset_key( $this->object );
			if ( ! is_wp_error( $key ) ) {
				$action                 = 'newaccount';
				return wc_get_account_endpoint_url( 'lost-password' ) . "?action=$action&key=$key&login=" . rawurlencode( $this->object->user_login );
			} else {
				// Something went wrong while getting the key for new password URL, send customer to the generic password reset.
				return wc_get_account_endpoint_url( 'lost-password' );
			}
		} 
	}

endif;

return new TB_Email_New_Account_For_Order();