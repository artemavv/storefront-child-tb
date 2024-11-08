<?php

/**
 * This customization file contains code which allows customers 
 * to select shipping, and to view shipping costs/time
 * 
 * and for the admin it allows to set shipping times and costs.
 * 
 * Author: Artem Avvakumov
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


class TannyBunny_Custom_Shipping_Core {
	
	public const OPTION_DELIVERY_ESTIMATES = 'tb_delivery_estimates';
	
	public const ACTION_SAVE_OPTIONS = 'Save delivery settings';
	
	public const warehouses = array( // ISO 3166 
		'us'	=> 'USA',
		'am'  => 'Armenia'
	);
	
	/**
	 * List of default values for plugin settings
	 * 
	 * @var array
	 */
	public static $default_option_values = [
		'default_delivery_min' => 7,
		'default_delivery_max' => 14,
		'default_processing_time' => 3
	];

	public static $option_values = array();
	
	public static function load_options() {
		$stored_options = get_option('tbd_options', array());

		foreach (self::$default_option_values as $option_name => $default_option_value) {
			if (isset($stored_options[$option_name])) {
				self::$option_values[$option_name] = $stored_options[$option_name];
			} else {
				self::$option_values[$option_name] = $default_option_value;
			}
		}
	}

	
	protected static function render_message( $message_text, $is_error = false ) {
		
		if ( ! $is_error )  {
			$out = '<div class="notice-info notice is-dismissible"><p>'
								. '<strong>'
								. $message_text
								. '</strong></p>'
								. '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>'
								. '</div>';
		} else {
			$out = '<div class="notice-error settings-error notice is-dismissible"><p>'
								. '<strong>'
								. $message_text
								. '</strong></p>'
								. '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>'
								. '</div>';
		}
		
		return $out;
	}
		
	protected function display_messages( $error_messages, $messages ) {
		
		$out = '';
		
		if (count($error_messages)) {
			foreach ($error_messages as $message) {

				if (is_wp_error($message)) {
					$message_text = $message->get_error_message();
				} else {
					$message_text = trim($message);
				}

				$out .= self::render_message( $message_text, true );
			}
		}

		if (count($messages)) {
			foreach ($messages as $message) {
				$out .= self::render_message( $message_text, false );
			}
		}

		return $out;
	}

	/**
	 * Returns HTML table rows each containing field, field name, and field description
	 * 
	 * @param array $field_set 
	 * @return string HTML
	 */
	public static function render_fields_row($field_set) {

		$out = '';

		foreach ($field_set as $field) {

			$value = $field['value'];

			if ((!$value) && ( $field['type'] != 'checkbox' )) {
				$value = $field['default'] ?? '';
			}

			$out .= self::display_field_in_row($field, $value);
		}

		return $out;
	}

	/**
	 * Generates HTML code for input row in table
	 * @param array $field
	 * @param array $value
	 * @return string HTML
	 */
	public static function display_field_in_row($field, $value) {

		$label = $field['label']; // $label = __($field['label'], DDB_TEXT_DOMAIN);

		$value = htmlspecialchars($value);
		$field['id'] = str_replace('_', '-', $field['name']);

		// 1. Make HTML for input
		switch ($field['type']) {
			case 'text':
				$input_HTML = self::make_text_field($field, $value);
				break;
			case 'dropdown':
				$input_HTML = self::make_dropdown_field($field, $value);
				break;
			case 'textarea':
				$input_HTML = self::make_textarea_field($field, $value);
				break;
			case 'checkbox':
				$input_HTML = self::make_checkbox_field($field, $value);
				break;
			case 'hidden':
				$input_HTML = self::make_hidden_field($field, $value);
				break;
			default:
				$input_HTML = '[Unknown field type "' . $field['type'] . '" ]';
		}


		// 2. Make HTML for table cell
		switch ($field['type']) {
			case 'hidden':
				$table_cell_html = <<<EOT
    <td class="col-hidden" style="display:none;" >{$input_HTML}</td>
EOT;
				break;
			case 'text':
			case 'textarea':
			case 'checkbox':
			default:
				$table_cell_html = <<<EOT
    <td>{$input_HTML}</td>
EOT;
		}

		return $table_cell_html;
	}

	/**
	 * Generates HTML code with TR rows containing specified field set
	 * 
	 * @param array $field
	 * @param mixed $value
	 * @return string HTML
	 */
	public static function display_field_set($field_set) {
		foreach ($field_set as $field) {

			$value = $field['value'] ?? false;

			$field['id'] = str_replace('_', '-', $field['name']);

			echo self::make_field($field, $value);
		}
	}

	/**
	 * Generates HTML code with TR row containing specified field input
	 * 
	 * @param array $field
	 * @param mixed $value
	 * @return string HTML
	 */
	public static function make_field($field, $value) {
		$label = $field['label'];

		if (!isset($field['style'])) {
			$field['style'] = '';
		}

		// 1. Make HTML for input
		switch ($field['type']) {
			case 'checkbox':
				$input_html = self::make_checkbox_field($field, $value);
				break;
			case 'text':
				$input_html = self::make_text_field($field, $value);
				break;
			case 'number':
				$input_html = self::make_number_field($field, $value);
				break;
			case 'date':
				$input_html = self::make_date_field($field, $value);
				break;
			case 'dropdown':
				$input_html = self::make_dropdown_field($field, $value);
				break;
			case 'textarea':
				$input_html = self::make_textarea_field($field, $value);
				break;
			case 'hidden':
				$input_html = self::make_hidden_field($field, $value);
				break;
			default:
				$input_html = '[Unknown field type "' . $field['type'] . '" ]';
		}

		if (isset($field['display'])) {
			$display = $field['display'] ? 'table-row' : 'none';
		} else {
			$display = 'table-row';
		}

		// 2. Make HTML for table row
		switch ($field['type']) {
			/* case 'checkbox':
			  $table_row_html = <<<EOT
			  <tr style="display:{$display}" >
			  <td colspan="3" class="col-checkbox">{$input_html}<label for="tbd_{$field['id']}">$label</label></td>
			  </tr>
			  EOT;
			  break; */
			case 'hidden':
				$table_row_html = <<<EOT
    <tr style="display:none" >
      <td colspan="3" class="col-hidden">{$input_html}</td>
    </tr>
EOT;
				break;
			case 'dropdown':
			case 'text':
			case 'number':
			case 'textarea':
			case 'checkbox':
			default:
				if (isset($field['description']) && $field['description']) {
					$table_row_html = <<<EOT
    <tr style="display:{$display}" >
      <td class="col-name" style="{$field['style']}"><label for="tbd_{$field['id']}">$label</label></td>
      <td class="col-input">{$input_html}</td>
      <td class="col-info">
        {$field['description']}
      </td>
    </tr>
EOT;
				} else {
					$table_row_html = <<<EOT
    <tr style="display:{$display}" >
      <td class="col-name" style="{$field['style']}"><label for="tbd_{$field['id']}">$label</label></td>
      <td class="col-input">{$input_html}</td>
      <td class="col-info"></td>
    </tr>
EOT;
				}
		}


		return $table_row_html;
	}

	/**
	 * Generates HTML code for hidden input
	 * @param array $field
	 * @param array $value
	 */
	public static function make_hidden_field($field, $value) {
		$out = <<<EOT
      <input type="hidden" id="tbd_{$field['id']}" name="{$field['name']}" value="{$value}">
EOT;
		return $out;
	}

	/**
	 * Generates HTML code for text field input
	 * @param array $field
	 * @param array $value
	 */
	public static function make_text_field($field, $value) {

		$size = $field['size'] ?? 25;

		$out = <<<EOT
      <input type="text" id="tbd_{$field['id']}" name="{$field['name']}" size="{$size}"value="{$value}" class="tbd-text-field">
EOT;
		return $out;
	}

	/**
	 * Generates HTML code for number field input
	 * @param array $field
	 * @param array $value
	 */
	public static function make_number_field($field, $value) {
		
		$params = '';
		
		$available_params = [ 'step', 'min', 'max' ];
		
		foreach ( $available_params as $param ) {
			if ( isset( $field[ $param ]) ) {
				$params .= "$param='{$field[ $param ]}' ";
			}
		}
		
		$out = <<<EOT
      <input type="number" id="tbd_{$field['id']}" name="{$field['name']}" $params value="{$value}" class="tbd-number-field">
EOT;
		return $out;
	}

	/**
	 * Generates HTML code for date field input
	 * @param array $field
	 * @param array $value
	 */
	public static function make_date_field($field, $value) {

		$min = $field['min'] ?? '2023-01-01';

		$out = <<<EOT
      <input type="date" id="tbd_{$field['id']}" name="{$field['name']}" value="{$value}" min="{$min}" class="tbd-date-field">
EOT;
		return $out;
	}

	/**
	 * Generates HTML code for textarea input
	 * @param array $field
	 * @param array $value
	 */
	public static function make_textarea_field($field, $value) {
		$out = <<<EOT
      <textarea id="tbd_{$field['id']}" name="{$field['name']}" cols="{$field['cols']}" rows="{$field['rows']}" value="">{$value}</textarea>
EOT;
		return $out;
	}

	/**
	 * Generates HTML code for dropdown list input
	 * @param array $field
	 * @param array $value
	 */
	public static function make_dropdown_field($field, $value) {

		$autocomplete = $field['autocomplete'] ?? false;

		$class = $autocomplete ? 'tbd-autocomplete' : '';

		$out = "<select class='$class' name='{$field['name']}' id='tbd_{$field['id']}' >";

		foreach ($field['options'] as $optionValue => $optionName) {
			$selected = ((string) $value == (string) $optionValue) ? 'selected="selected"' : '';
			$out .= '<option ' . $selected . ' value="' . $optionValue . '">' . $optionName . '</option>';
		}

		$out .= '</select>';
		return $out;
	}

	/**
	 * Generates HTML code for checkbox 
	 * @param array $field
	 */
	public static function make_checkbox_field($field, $value) {
		$chkboxValue = $value ? 'checked="checked"' : '';
		$out = <<<EOT
      <input type="checkbox" id="tbd_{$field['id']}" name="{$field['name']}" {$chkboxValue} value="1" class="tbd-checkbox-field"/>
EOT;
		return $out;
	}

}

/**
 * This class displays delivery settings in admin area
 * 
 */
class TannyBunny_Custom_Shipping_Admin extends TannyBunny_Custom_Shipping_Core {
	
	const CHECK_RESULT_OK = 'ok';

	public static function add_page_to_menu() {

		add_management_page(
			__('Delivery Times'), // page title
			__('Delivery Times'), // menu title
			'manage_options',
			'tbd-settings', // menu slug
			array('TannyBunny_Custom_Shipping_Admin', 'render_settings_page') // callback.
		);
	}

	public static function do_action() {

		$result = '';

		if (isset($_POST['tbd-button-save'])) {

			switch ($_POST['tbd-button-save']) {
				case self::ACTION_SAVE_OPTIONS:
				
					
					$stored_options = get_option('tbd_options', array());

					foreach ( self::$default_option_values as $option_name => $option_value ) {
						if ( isset( $_POST[$option_name] ) ) {
							$stored_options[$option_name] = filter_input(INPUT_POST, $option_name); 
						}
					}
/*
					// special case for checkbox
					if (!isset($_POST['use_default_template'])) {
						$stored_options['use_default_template'] = false;
					} else {
						$stored_options['use_default_template'] = true;
					}
*/
					update_option('tbd_options', $stored_options);

					foreach ( self::warehouses as $warehouse_id => $warehouse_name ) {
					
						$option_name = self::OPTION_DELIVERY_ESTIMATES . '_' . $warehouse_id;
						$stored_estimates = get_option( $option_name, array() );

						if ( isset( $_POST[ 'estimates_' . $warehouse_id] ) ) {
							$stored_estimates = $_POST[ 'estimates_' . $warehouse_id];
							update_option( $option_name, $stored_estimates );
						}
					}
					
					$result = 'Saved new delivery estimates';
					
					break;
			}
		}

		return $result;
	}

	public static function render_settings_page() {

		$action_results = '';

		if (isset($_POST['tbd-button-save'])) {
			$action_results = self::do_action();
		}

		echo $action_results;
		
		self::load_options();
		?>

			<h1><?php esc_html_e('Delivery estimates'); ?></h1>
			
			<br>
		
		<?php 
		self::render_estimates_form();
	}

	public static function render_estimates_form() {

		$estimates_us = get_option( self::OPTION_DELIVERY_ESTIMATES . '_us' );
		$estimates_am = get_option( self::OPTION_DELIVERY_ESTIMATES . '_am' );
		
		
		$delivery_settings_field_set = array(
			array(
				'name' => "default_delivery_min",
				'type' => 'number',
				'label' => 'Estimated minimum delivery time, in days',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['default_delivery_min'],
			),
			array(
				'name' => "default_delivery_max",
				'type' => 'number',
				'label' => 'Estimated maximum delivery time, in days',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['default_delivery_max'],
			),
			array(
				'name' => "default_processing_time",
				'type' => 'number',
				'label' => 'Estimated processing time, in days',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['default_processing_time'],
			),
		);
		?>
		<form method="POST" >

				<table class="tbd-global-table">
					<thead>
						<th>From warehouse in USA</th>
						<th>From warehouse in Armenia</th>
					</thead>
					<tbody>
						<tr>
							<td><textarea id="tbd-delivery-estimates-us" rows="30" cols="15" name="estimates_us"><?php echo $estimates_us; ?></textarea></td>
							<td><textarea id="tbd-delivery-estimates-am" rows="30" cols="15" name="estimates_am"><?php echo $estimates_am; ?></textarea></td>
						</tr>
					</tbody>
				</table>

				<h2><?php esc_html_e( 'Rest of the world' ); ?></h2>

				<table class="tbd-global-table">
						<tbody>
								<?php self::display_field_set( $delivery_settings_field_set ); ?>
						</tbody>
				</table>
				
				<p class="submit">  
						<input type="submit" id="tbd-button-save" name="tbd-button-save" class="button button-primary" style="" value="<?php echo self::ACTION_SAVE_OPTIONS; ?>" />
				</p>

		</form>

		<?php
	}
}


class TannyBunny_Custom_Shipping_Helper extends TannyBunny_Custom_Shipping_Core {
	
	private $product = false;
	private $product_id = false;
	
	public $product_has_warehouses = false;
	
	// Set by site admin, for each product individually
	public $available_warehouses = array();
	
	/**
	 * a subset of all possible shipping options
	 * (only those that are available for the current product and current visitor)
	 * 
	 * This subset is calculated basing on admin settings (all available countries) and visitor's country
	 */
	public $shipping_options = array();
	
	/**
	 * @param WC_Product $product
	 */
	public function __construct( $product ) {
		$this->product = $product;
		$this->product_id = $product->get_id();
		
		self::load_options();
		
		// get an array of options from a string "Armenia, USA"
		// make an empty array if there are no warehouses
		$warehouse_names = array_filter( array_map( 'trim', explode( ',' , $product->get_attribute( 'warehouse' ) ) ) );
		
		
		$this->available_warehouse_names = $product->get_attribute( 'warehouse' );
		$this->available_warehouses = self::find_warehouses_by_names( $warehouse_names );
		//echo('<pre>' . print_r( $this->available_warehouses, 1 ) . '</pre>' );
		$this->product_has_warehouses = is_array( $this->available_warehouses ) && ( count( $this->available_warehouses ) > 0 );
		

	}
	
	
	public static function find_warehouses_by_names( array $names ) {
	
		$result = array();
		
		$warehouse_ids = array(
			'Armenia' => 'am',
			'USA'     => 'us'
		);
		
		foreach ( $names as $name ) {
			if ( array_key_exists( $name, $warehouse_ids ) ) {
				$result[ $warehouse_ids[$name] ] = $name;
			}
		}
		
		return $result;
	}
	
	public function render_warehouse_options() {
		
		$out = '';
		$sep = '';
		
		foreach ( $this->available_warehouses as $warehouse ) {
			
			$out .= $sep . 'from ' . $warehouse;
			$sep = ', ';
		}
		
		return $out;
	}
	
	public static function get_customer_country() {
		
		if ( class_exists( 'WC_Geolocation' ) ) {
			$location = WC_Geolocation::geolocate_ip();

			if ( is_array($location) && isset($location['country']) && $location['country'] != '') {
				return $location['country'];
			}
		}
		
		return 'US';
	}
	
	public function get_delivery_estimate() {
		
		$delivery_country = self::get_customer_country();
		
		//echo('$delivery_country<pre>' . print_r( $delivery_country , 1 ) . '</pre>' );
		
		$min_delivery_time = 999999;
		$max_delivery_time = 10;
		
		if ( count( $this->available_warehouses ) ) {
			
			// iterate through warehouses to find the fastest delivery time
			foreach ( $this->available_warehouses as $warehouse_id => $warehouse_name ) {
				$estimate_in_days = $this->get_delivery_estimate_for_warehouse( $warehouse_id, $delivery_country );

				if ( $estimate_in_days['from'] < $min_delivery_time ) {
					$min_delivery_time = $estimate_in_days['from'];
					$max_delivery_time = $estimate_in_days['to'];
				}
			}
		}
		else { // use default estimates since the product does not have warehouses listed
			$min_delivery_time = self::$option_values['default_delivery_min'];
			$max_delivery_time = self::$option_values['default_delivery_max'];
		}
		
		//echo('<pre>' . print_r( [ self::$option_values, $this->available_warehouses, $min_delivery_time, $max_delivery_time] , 1 ) . '</pre>' );
		return array( 'from' => $min_delivery_time, 'to' => $max_delivery_time );
	}
	
	public function get_delivery_date_estimate() {
		$estimate_in_days = $this->get_delivery_estimate();
		
		$from_timestamp = time() + $estimate_in_days['from'] * DAY_IN_SECONDS;
		$to_timestamp   = time() + $estimate_in_days['to'] * DAY_IN_SECONDS;
		
		$from_month = date( 'M', $from_timestamp );
		$to_month   = date( 'M', $to_timestamp );
		
		if ( $from_month === $to_month ) { // output like "Nov 13-25"
			$from = date( 'M j', $from_timestamp );
			$to   = date( 'j', $to_timestamp );
			$out  = "$from-$to";
			
		} else { // output like "Sep 13-Oct 25"
			$from = date( 'M j', $from_timestamp );
			$to   = date( 'M j', $to_timestamp );
			$out  = "$from-$to";
		}
		
		return $out;
	}
	
	/**
	 * 
	 * @param string $warehouse - "Armenia" or "USA"
	 * @param string $country Two-letter country code, e.g. JP, GE, US, RU
	 * @return array
	 */
	public function get_delivery_estimate_for_warehouse( string $warehouse, string $country ) {
		
		$option_name = self::OPTION_DELIVERY_ESTIMATES . '_' . $warehouse;
		
		$warehouse_data = get_option( $option_name , '' );
		
		$estimates = explode( "\r\n", $warehouse_data );
		
		//echo( '>>>' . $warehouse . '<pre>' . print_r( $estimates , 1 ) . '</pre>---' . $country );
		
		$from = self::$option_values['default_delivery_min'];
	  $to   = self::$option_values['default_delivery_max'];
			
		foreach ( $estimates as $country_estimate ) {
			
			$country_estimate = str_getcsv( $country_estimate, ',' );
			
			if ( is_array($country_estimate) && count( $country_estimate ) >= 3 ) {
				
				if ( strtolower($country_estimate[0]) == strtolower($country) )  {
					$from = 		$country_estimate[1];
					$to   = 		$country_estimate[2];
				}
			}
		}
		
		return array( 'from' => $from, 'to' => $to );
	}
	
	public function render_shipping_details() {
		
		$check_mark_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="check-mark"><path d="M9.059 20.473 21.26 6.15l-1.52-1.298-10.8 12.675-4.734-4.734-1.414 1.414z"></path></svg>';
		
		$delivery_date_estimated = $this->get_delivery_date_estimate();
		
		$return_notice = 'Buyers are responsible for return postage costs. If the item is not returned in its original condition, the buyer is responsible for any loss in value.';
		
		$line_about_delivery_estimate = '<li>' . $check_mark_icon . ' Arrives soon! Get it by <span class="tooltip-notice" data-notice="' . $return_notice . '">' . $delivery_date_estimated . '</span> if you order today</li>';
		$line_about_delivery_conditions = '<li>' . $check_mark_icon . ' Returns and exchanges accepted</li>';
		
		$out = '<ul class="shipping-details">'
				. $line_about_delivery_estimate
				. $line_about_delivery_conditions
		. '</ul>';
		
		return $out;
	}
}

add_action( 'admin_menu', array('TannyBunny_Custom_Shipping_Admin', 'add_page_to_menu') );

add_action( 'woocommerce_after_add_to_cart_form', 'display_shipping_conditions_block' );

function display_shipping_conditions_block() {
	
	global $product;
	
	$shipping = new TannyBunny_Custom_Shipping_Helper( $product );
	
	$shipping_locations = $shipping->available_warehouse_names ?: 'Armenia';
	$earliest_date      = $shipping->get_delivery_date_estimate();
	
	$calendar_icon   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 16a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M6.5 5H3v16h18V5h-3.5V3h-2v2h-7V3h-2zm0 2v1h2V7h7v1h2V7H19v3H5V7zM5 12v7h14v-7z"></path></svg>';
	$box_icon        = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.5 15h-6c-.3 0-.5.2-.5.5s.2.5.5.5h6c.3 0 .5-.2.5-.5s-.2-.5-.5-.5m-6-1h4c.3 0 .5-.2.5-.5s-.2-.5-.5-.5h-4c-.3 0-.5.2-.5.5s.2.5.5.5m5 3h-5c-.3 0-.5.2-.5.5s.2.5.5.5h5c.3 0 .5-.2.5-.5s-.2-.5-.5-.5"></path><path d="m21.9 6.6-2-4Q19.6 2 19 2H5q-.6 0-.9.6l-2 4c-.1.1-.1.2-.1.4v14c0 .6.4 1 1 1h18c.6 0 1-.4 1-1V7c0-.2 0-.3-.1-.4M5.6 4h12.8l1 2H4.6zM4 20V8h16v12z"></path></svg>';
	$shipping_icon   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 12.266 16.42 6H6v1h5v2H2V7h2V4h13.58L22 11.734V18h-2.17a3.001 3.001 0 0 1-5.66 0h-2.34a3.001 3.001 0 0 1-5.66 0H4v-3H2v-2h4v3h.17a3.001 3.001 0 0 1 5.66 0h2.34a3.001 3.001 0 0 1 5.66 0H20zM18 17a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path><path d="M17.5 11 15 7h-2v4zM9 12H2v-2h7z"></path></svg>';
	$location_icon   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M17.083 12.189 12 21l-5.083-8.811a6 6 0 1 1 10.167 0m-1.713-1.032.02-.033a4 4 0 1 0-6.78 0l.02.033 3.37 5.84z"></path></svg>';
	?>
	
	<h4>Shipping and return policies</h4>
		
	<ul class="shipping-and-return">
		<li><?php echo $calendar_icon; ?>  Arrives soon! Get it by <span class="tooltip-notice" ><?php echo $earliest_date; ?>!</span></li>
		<li><?php echo $box_icon; ?> <span class="tooltip-notice" >Returns & exchanges accepted</span> within 14 days</li>
		<li><?php echo $shipping_icon; ?>  Free shipping</li>
		<li><?php echo $location_icon; ?>  Ships from <strong><?php echo $shipping_locations; ?></strong></li>
	</ul>
	<?php
}