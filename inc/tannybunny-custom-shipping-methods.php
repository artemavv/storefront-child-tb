<?php

if ( !defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'FedEx_TannyBunny_Shipping_Method' ) ) {

	class FedEx_TannyBunny_Shipping_Method extends WC_Shipping_Method {

		/**
		 * Constructor for your shipping class 
		 * 
		 * @access public 
		 * @return void 
		 */
		public function __construct() {
			$this->id = 'tb_fedex_shipping';
			$this->method_title = __( 'FedEx Shipping for TannyBunny', 'woocommerce' );
			$this->method_description = __( 'Custom Shipping Method for TannyBunny', 'woocommerce' );
			// Availability & Countries 
			$this->availability = 'including';
			$this->availability = 'excluding';
			$this->countries = array(
				'US', // Unites States of America 
			);
			$this->init();
			$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
			$this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'FedEx Shipping', 'woocommerce' );
		}

		/**
		 * Init your settings 
		 * 
		 * @access public 
		 * @return void 
		 */
		function init() {
			// Load the settings API 
			$this->init_form_fields();
			$this->init_settings();
			// Save settings in admin if you have any defined 
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options') );

		}

		/**
		 * Define settings field for this shipping 
		 * @return void 
		 */
		function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Enable this shipping method.', 'woocommerce' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be display on site', 'woocommerce' ),
					'default' => __( 'FedEx Shipping', 'woocommerce' )
				),
				'weight' => array(
					'title' => __( 'Weight (kg)', 'woocommerce' ),
					'type' => 'number',
					'description' => __( 'Maximum allowed weight', 'woocommerce' ),
					'default' => 100
				),
			);
		}

		/**
		 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters. 
		 * 
		 * @access public 
		 * @param array $package 
		 * @return void 
		 */
		public function calculate_shipping( $package = [] ) {

			$weight = 0;
			$cost = 0;
			$country = $package["destination"]["country"];
			foreach ( $package['contents'] as $item_id => $values ) {
				$_product = $values['data'];
				$weight = 123; // $weight + $_product->get_weight() * $values['quantity'];
			}
			$weight = wc_get_weight( $weight, 'kg' );
			if ( $weight <= 10 ) {
				$cost = 3;
			} elseif ( $weight <= 30 ) {
				$cost = 5;
			} elseif ( $weight <= 50 ) {
				$cost = 10;
			} else {
				$cost = 20;
			}
			$countryZones = array(
				'US' => 0,
			);
			$zonePrices = array(
				0 => 10,
				1 => 30,
				2 => 50,
				3 => 70
			);
			$zoneFromCountry = $countryZones[$country];
			$priceFromZone = $zonePrices[$zoneFromCountry];
			$cost += $priceFromZone;
			$rate = array(
				'id' => $this->id,
				'label' => $this->title,
				'cost' => $cost
			);
			$this->add_rate( $rate );
		}
	}

}


function add_tannybunny_fedex_shipping_method( $methods ) {
	$methods['tb_fedex_shipping'] = 'FedEx_TannyBunny_Shipping_Method';
	return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'add_tannybunny_fedex_shipping_method' );

function fedex_tannybunny_validate_order( $posted ) {
	$packages = WC()->shipping->get_packages();
	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

	if ( is_array( $chosen_methods ) && in_array( 'tb_fedex_shipping', $chosen_methods ) ) {

		foreach ( $packages as $i => $package ) {
			
			if ( $chosen_methods[$i] != "tb_fedex_shipping" ) {
				continue;
			}
			
			$Fedex_Shipping_Method = new FedEx_TannyBunny_Shipping_Method();
			$weightLimit = (int) $Fedex_Shipping_Method->settings['weight'];
			$weight = 0;
			foreach ( $package['contents'] as $item_id => $values ) {
				$_product = $values['data'];
				$weight = 123; //$weight + $_product->get_weight() * $values['quantity'];
			}
			$weight = wc_get_weight( $weight, 'kg' );

			if ( $weight > $weightLimit ) {
				$message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'tutsplus' ), $weight, $weightLimit, $Fedex_Shipping_Method->title );

				$messageType = "error";
				if ( !wc_has_notice( $message, $messageType ) ) {

					wc_add_notice( $message, $messageType );
				}
			}
		}
	}
}

add_action( 'woocommerce_review_order_before_cart_contents', 'fedex_tannybunny_validate_order', 10 );
add_action( 'woocommerce_after_checkout_validation', 'fedex_tannybunny_validate_order', 10 );
