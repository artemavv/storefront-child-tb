<?php

/**
 * Custom WooCommerce mailer class for TannyBunny
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'TB_Email_Customer_Sent_Order', false ) ) :

	/**
	 * Customer Sent Order Email.
	 *
	 * An email sent to the customer when an order is sent.
	 */
	class TB_Email_Customer_Sent_Order extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'customer_sent_order';
			$this->customer_email = true;

			$this->title          = __( 'Shipped order', 'woocommerce' );
			$this->description    = __( 'This is an order notification sent to customers containing order details after shipping order.', 'woocommerce' );
			$this->template_html  = 'emails/customer-sent-order.php';
			$this->template_plain = 'emails/plain/customer-sent-order.php';
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

  
			// Triggers for this email.
			add_action( 'woocommerce_order_status_changed', array( $this, 'trigger' ), 100, 4 );
			
      /*add_action( 'woocommerce_order_status_failed_to_sent_notification', array( $this, 'trigger' ), 10, 2 );
			add_action( 'woocommerce_order_status_on-hold_to_sent_notification', array( $this, 'trigger' ), 10, 2 );
			add_action( 'woocommerce_order_status_processing_to_sent_notification', array( $this, 'trigger' ), 10, 2 );
*/
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
			return __( 'Your {site_title} order has been shipped!', 'woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Thank you for your order', 'woocommerce' );
		}

		/**
		 * Trigger the sending of this email when the order status is changed
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id, $old_status, $new_status, $order ) {
			$this->setup_locale();

      $this->tracking_number = false;
      
      if ( $new_status == 'sent' ) {
        
        if ( is_a( $order, 'WC_Order' ) ) {
          $this->object                         = $order;
          $this->recipient                      = $this->object->get_billing_email();
          $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
          $this->placeholders['{order_number}'] = $this->object->get_order_number();

          $this->tracking_number                = $this->get_tracking_number();
          
        }

        
        if ( $this->is_enabled() && $this->get_recipient() && $this->tracking_number ) {
          $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
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
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
          'tracking_number'    => $this->tracking_number,
					'plain_text'         => false,
					'email'              => $this,
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
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
          'tracking_number'    => $this->tracking_number,
					'plain_text'         => true,
					'email'              => $this,
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
			return __( 'Thanks for using {site_url}!', 'woocommerce' );
		}
    
    

		/**
		 * Get value from the custom field containing order's tracking number
		 * 
		 * @return string
		 */
		protected function get_tracking_number() {
			
      $tracking_number = get_post_meta( $this->object->get_id(), 'tracking_number_for_armenian_post', true );
      return $tracking_number;
		} 
	}

endif;

return new TB_Email_Customer_Sent_Order();
