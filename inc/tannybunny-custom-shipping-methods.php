<?php

if ( !defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'FromUSA_TannyBunny_Shipping_Method' ) ) {

	class From_USA_Paid_Shipping_Method extends WC_Shipping_Method {

		public function __construct() {
			
			$this->id = 'tb_paid_usa_shipping';
			$this->method_title = __( 'Paid Shipping from USA', 'woocommerce' );
			$this->method_description = __( 'Custom Shipping Method for TannyBunny', 'woocommerce' );
			
			//$this->availability = 'including'; 
			
			// exclude countries where free shipping is available
			$this->availability = 'excluding';
			$this->countries = TannyBunny_Custom_Shipping_Helper::free_shipping_countries( 'us' );
			
			$this->init();
			
			$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
			$this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Paid Shipping', 'woocommerce' );
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
					'default' => __( 'From USA', 'woocommerce' )
				)
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

			$cost = 0;
			$wc_product = false;
			
			$delivery_from_usa = true;
			
			foreach ( $package['contents'] as $item_id => $values ) {
				$wc_product = new WC_Product( $values['product_id'] );
				
				$warehouse_names = array_filter( array_map( 'trim', explode( ',' , $wc_product->get_attribute( 'warehouse' ) ) ) );
				$available_warehouses      = TannyBunny_Custom_Shipping_Helper::find_warehouses_by_names( $warehouse_names );
				
				if ( ! array_key_exists( 'us', $available_warehouses ) ) { // this product is not available in US warehouse
					$delivery_from_usa = false;
					break;
				}
			}
			
			if ( $delivery_from_usa && $wc_product ) {
			
				$country = $package["destination"]["country"];
				
				$shipping = new TannyBunny_Custom_Shipping_Helper( $wc_product, $country );
				$dates = $shipping->get_delivery_date_estimate( 'standard', 'us' );
				$cost = $shipping->get_delivery_cost( 'standard', 'us' );
				
				$rate = array(
					'id' => $this->id,
					'label' => $this->title . ' (Arrives: ' . $dates . ')',
					'cost' => $cost
				);
				$this->add_rate( $rate );
			}			
		}
	}

}


function add_tannybunny_fedex_shipping_method( $methods ) {
	$methods['tb_paid_usa_shipping'] = 'From_USA_Paid_Shipping_Method';
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

//add_action( 'woocommerce_review_order_before_cart_contents', 'fedex_tannybunny_validate_order', 10 );
//add_action( 'woocommerce_after_checkout_validation', 'fedex_tannybunny_validate_order', 10 );
