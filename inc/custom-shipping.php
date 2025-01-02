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
		'us_delivery_min'            => 7,
		'us_delivery_max'            => 14,
		'us_delivery_min_express'    => 7,
		'us_delivery_max_express'    => 14,
		'us_processing_time'         => 3,
		
		'us_shipping_cost'           => 0,
		'us_shipping_cost_express'   => 12,
		'us_free_delivery_countries' => '',
		
		'am_delivery_min'            => 7,
		'am_delivery_max'            => 14,
		'am_delivery_min_express'    => 7,
		'am_delivery_max_express'    => 14,
		'am_processing_time'         => 3,
		
		'am_shipping_cost'           => 0,
		'am_shipping_cost_express'   => 12,
		'am_free_delivery_countries' => '*',
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
		
		
		$USA_delivery_settings_field_set = array(
			array(
				'name' => "us_delivery_min",
				'type' => 'number',
				'label' => 'MIN delivery time, in days (Free)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_delivery_min'],
			),
			array(
				'name' => "us_delivery_max",
				'type' => 'number',
				'label' => 'MAX delivery time, in days (Free)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_delivery_max'],
			),
			array(
				'name' => "us_delivery_min_express",
				'type' => 'number',
				'label' => 'MIN delivery time, in days (Express)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_delivery_min_express'],
			),
			array(
				'name' => "us_delivery_max_express",
				'type' => 'number',
				'label' => 'MAX delivery time, in days (Express)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_delivery_max_express'],
			),
			array(
				'name' => "us_processing_time",
				'type' => 'number',
				'label' => 'Estimated processing time, in days',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_processing_time'],
			),
			array(
				'name' => "us_shipping_cost",
				'type' => 'number',
				'label' => 'Default cost of shipping',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_shipping_cost'],
			),
			array(
				'name' => "us_shipping_cost_express",
				'type' => 'number',
				'label' => 'Default cost of express shipping',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['us_shipping_cost_express'],
			),
			array(
				'name' => "us_free_delivery_countries",
				'type' => 'textarea',
				'label' => 'Countries with free delivery',
				'cols' => 30,
				'rows' => 6,
				'value' => self::$option_values['us_free_delivery_countries'],
			),
		);
		
		$Armenia_delivery_settings_field_set = array(
			array(
				'name' => "am_delivery_min",
				'type' => 'number',
				'label' => 'MIN delivery time, in days (Free)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_delivery_min'],
			),
			array(
				'name' => "am_delivery_max",
				'type' => 'number',
				'label' => 'MAX delivery time, in days (Free)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_delivery_max'],
			),
			array(
				'name' => "am_delivery_min_express",
				'type' => 'number',
				'label' => 'MIN delivery time, in days (Express)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_delivery_min_express'],
			),
			array(
				'name' => "am_delivery_max_express",
				'type' => 'number',
				'label' => 'MAX delivery time, in days (Express)',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_delivery_max_express'],
			),
			array(
				'name' => "am_processing_time",
				'type' => 'number',
				'label' => 'Estimated processing time, in days',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_processing_time'],
			),
			array(
				'name' => "am_shipping_cost",
				'type' => 'number',
				'label' => 'Default cost of shipping',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_shipping_cost'],
			),
			array(
				'name' => "am_shipping_cost_express",
				'type' => 'number',
				'label' => 'Default cost of express shipping',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'value' => self::$option_values['am_shipping_cost_express'],
			),
			array(
				'name' => "am_free_delivery_countries",
				'type' => 'textarea',
				'label' => 'Countries with free delivery',
				'cols' => 30,
				'rows' => 6,
				'value' => self::$option_values['am_free_delivery_countries'],
			),
		);
		
		?>
		<form method="POST" >

				<h2>Delivery times and costs</h2>
				<table style="width:100%" class="tbd-global-table">
					<thead>
						<th><h2>From warehouse in USA</h2>(times and costs for specific countries)</th>
						<th><h2>From warehouse in Armenia</h2>(times and costs for specific countries)</th>
					</thead>
					<tbody>
						<tr>
							<td><textarea id="tbd-delivery-estimates-us" rows="15" cols="35" name="estimates_us"><?php echo $estimates_us; ?></textarea></td>
							<td><textarea id="tbd-delivery-estimates-am" rows="15" cols="35" name="estimates_am"><?php echo $estimates_am; ?></textarea></td>
						</tr>
						<tr>
							<td>
								<h2><?php esc_html_e( 'Times and costs for the rest of the world' ); ?></h2>

								<table class="tbd-global-table">
										<tbody>
												<?php self::display_field_set( $USA_delivery_settings_field_set ); ?>
										</tbody>
								</table>
							</td>
							<td>
								<h2><?php esc_html_e( 'Times and costs for the rest of the world ' ); ?></h2>

								<table class="tbd-global-table">
										<tbody>
												<?php self::display_field_set( $Armenia_delivery_settings_field_set ); ?>
										</tbody>
								</table>
							</td>
						</tr>
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
	
	// Warehouse availability is set by site admin, for each product individually
	
	public $available_warehouse_names = array();
	public $available_warehouses = array();
	
	/**
	 * See get_delivery_settings_for_warehouse() for the ssetting format
	 */
	public $delivery_settings_am = false; // settings for the warehouse in Armenia (am)
	public $delivery_settings_us = false; // settings for the warehouse in for USA (us)
	
	/**
	 * a subset of all possible shipping options
	 * (only those that are available for the current product and current visitor)
	 * 
	 * This subset is calculated basing on admin settings (all available countries) and visitor's country
	 */
	public $shipping_options = array();
	
	
	public const RETURN_NOTICE = 'Buyers are responsible for return postage costs. If the item is not returned in its original condition, the buyer is responsible for any loss in value.';
	
	public const EXPRESS_NOTICE = 'For delivery within USA, we are using FedEx Express';
	
	public const DATE_NOTICE = 'Your order should arrive by this date if you buy today. '
					. 'To calculate an estimated delivery date you can count on, we look at things like '
					. 'the carrier\'s latest transit times, '
					. 'and where the order is shipping to and from.';
	
	/**
	 * @param WC_Product $product
	 */
	public function __construct( $product, $customer_country = 'US' ) {
		$this->product = $product;
		$this->product_id = $product->get_id();
		$this->customer_country = $customer_country;
		
		self::load_options();
		
		// get an array of options from a string "Armenia, USA"
		// make an empty array if there are no warehouses
		$warehouse_names = array_filter( array_map( 'trim', explode( ',' , $product->get_attribute( 'warehouse' ) ) ) );
		
		
		$this->available_warehouse_names = $product->get_attribute( 'warehouse' );
		$this->available_warehouses      = self::find_warehouses_by_names( $warehouse_names );
		
		$this->delivery_settings_am     = $this->get_delivery_settings_for_warehouse( 'am', $this->customer_country );
		$this->delivery_settings_us     = $this->get_delivery_settings_for_warehouse( 'us', $this->customer_country );
		
		
		$this->product_has_warehouses = is_array( $this->available_warehouses ) && ( count( $this->available_warehouses ) > 0 );
	}
	
	public static function free_shipping_countries( $warehouse = 'am' ) {
		if ( $warehouse == 'am') {
			$countries_list = array_map('trim', explode(',' , self::$option_values['am_free_delivery_countries'] ) );
		}
		else {
			$countries_list = array_map('trim', explode(',' , self::$option_values['us_free_delivery_countries'] ) );
		}
		
		return $countries_list;
	}
	
	public static function find_warehouses_by_names( array $names ) {
	
		$result = array();
		
		$warehouse_relation = array(
			'Armenia' => 'am',
			'USA'     => 'us'
		);
		
		foreach ( $names as $name ) {
			if ( array_key_exists( $name, $warehouse_relation ) ) {
				$result[ $warehouse_relation[$name] ] = $name;
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
	
	/**
	 * 
	 * @param string $mode
	 * @param string $warehouse_restriction 'us' or 'am' or empty. Empty strings allows to use any warehouse for estimations
	 * @return array
	 */
	public function get_delivery_estimate( $mode = 'standard', $warehouse_restriction = '' ) {
		
		//$delivery_country = self::get_customer_country();
		
		$min_delivery_time = 999999;
		$max_delivery_time = 10;
		
		if ( count( $this->available_warehouses ) ) {
		
			if ( ! $warehouse_restriction ) {
				$available_warehouses = $this->available_warehouses;
			}
			else {
				$available_warehouses = array( $warehouse_restriction => 'Warehouse' );
			}
			
			// iterate through warehouses to find the fastest delivery time
			foreach ( $available_warehouses as $warehouse_id => $warehouse_name ) {
				$estimate_in_days = $this->estimate_delivery_for_warehouse( $warehouse_id, $mode );

				if ( $estimate_in_days['from'] < $min_delivery_time ) {
					$min_delivery_time = $estimate_in_days['from'];
					$max_delivery_time = $estimate_in_days['to'];
				}
			}
		}
		else { // use default estimates since the product does not have warehouses listed
			
			if ( $mode == 'standard' ) {
				$min_delivery_time = self::$option_values['am_delivery_min'];
				$max_delivery_time = self::$option_values['am_delivery_max'];
			}
			else {
				$min_delivery_time = self::$option_values['am_delivery_min_express'];
				$max_delivery_time = self::$option_values['am_delivery_max_express'];
			}
		}
		
		return array( 'from' => $min_delivery_time, 'to' => $max_delivery_time );
	}
	
	// TODO
	public function is_express_shipping_available() {
		return true;
	}
	
	// TODO
	public static function is_free_shipping_available() {
		return true;
	}
	
	// TODO
	public function get_delivery_cost( $mode = 'standard', $warehouse_restriction = '' ) {
		
		$min_cost = 999;
		
		if ( ! $warehouse_restriction ) {
			$available_warehouses = $this->available_warehouses;
		}
		else {
			$available_warehouses = array( $warehouse_restriction => 'Warehouse' );
		}
			
		// iterate through warehouses to find the cheapest delivery
		foreach ( $available_warehouses as $warehouse_id => $warehouse_name ) {
			$estimate = $this->estimate_delivery_for_warehouse( $warehouse_id, $mode );

			if ( $estimate['cost'] < $min_cost ) {
				$min_cost = $estimate['cost'];
			}
		}
		
		return $min_cost;
	}
	
	public function get_delivery_date_estimate( $mode = 'standard', $warehouse_restriction = '' ) {
		$estimate_in_days = $this->get_delivery_estimate( $mode, $warehouse_restriction );
		
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
	 * @param string $warehouse_id - "am" or "us"
	 * @param string $country Two-letter country code, e.g. JP, GE, US, RU
	 * @return array
	 */
	public function get_delivery_settings_for_warehouse( string $warehouse_id, string $country ) {
		
		$option_name = self::OPTION_DELIVERY_ESTIMATES . '_' . $warehouse_id;
		
		$warehouse_delivery_data = get_option( $option_name , '' );
		
		$all_countries_settings = explode( "\r\n", $warehouse_delivery_data );
		
		// default values for standard delivery
		$from     = self::$option_values[ $warehouse_id . '_delivery_min'];
		$to       = self::$option_values[ $warehouse_id . '_delivery_max'];
		$cost     = self::$option_values[ $warehouse_id . '_shipping_cost'];

		// default values for express delivery
		$from_exp = self::$option_values[ $warehouse_id . '_delivery_min_express'];
		$to_exp   = self::$option_values[ $warehouse_id . '_delivery_max_express'];
		$cost_exp = self::$option_values[ $warehouse_id . '_shipping_cost_express'];
		
		$free_cn  = self::$option_values[ $warehouse_id . '_free_delivery_countries'];
		
		$delivery_settings = [
			'from'       => $from,
			'to'         => $to,
			'cost'       => $cost,
			'from_exp'   => $from_exp,
			'to_exp'     => $to_exp,
			'cost_exp'   => $cost_exp,
			'free_cn'    => $free_cn
		];
		
		/**
		 * Example of $country_settings string: JP,20,30,0,4,5,12
		 * 
		 * JP - Japan
		 * 20 - min delivery time is 20 days for standard shipping
		 * 30 - max delivery time is 30 days for standard shipping
		 * 0  - cost of standard shipping (it is free)
		 * 4  - min delivery time is 4 days for express shipping
		 * 5  - max delivery time is 5 days for express shipping
		 * 12 - cost of express shipping
		 *  
		 */
		
		foreach ( $all_countries_settings as $country_settings ) {
			
			$country_settings = str_getcsv( $country_settings, ',' );
			
			if ( is_array($country_settings) && count( $country_settings ) >= 6 ) {
				
				if ( strtolower($country_settings[0]) == strtolower($country) )  {
					
					$from     = $country_settings[1];
					$to       = $country_settings[2];
					$cost     = $country_settings[3];

					$from_exp = $country_settings[4];
					$to_exp   = $country_settings[5];
					$cost_exp = $country_settings[6];
					
					$delivery_settings = [
						'from'       => $from,
						'to'         => $to,
						'cost'       => $cost,
						'from_exp'   => $from_exp,
						'to_exp'     => $to_exp,
						'cost_exp'   => $cost_exp,
					];
					
					break;
				}
			}
		}
		
		return $delivery_settings;
	}
	
	/**
	 * 
	 * @param string $warehouse_id - "am" or "us"
	 * @param string $country Two-letter country code, e.g. JP, GE, US, RU
	 * @return array
	 */
	public function estimate_delivery_for_warehouse( string $warehouse_id, string $mode = 'standard' ) {
		
		if ( $warehouse_id == 'am' ) {
			$delivery_settings = $this->delivery_settings_am;
		}
		else {
			$delivery_settings = $this->delivery_settings_us;
		}
		
		if ( $mode == 'standard' ) {
			$delivery_estimate = array(
				'from'   => $delivery_settings['from'],
				'to'     => $delivery_settings['to'],
				'cost'   => $delivery_settings['cost']
			);
		}
		else {
			$delivery_estimate = array(
				'from'   => $delivery_settings['from_exp'],
				'to'     => $delivery_settings['to_exp'],
				'cost'   => $delivery_settings['cost_exp']
			);
		}
		
		return $delivery_estimate;
	}
	
	public function render_shipping_details() {
		
		$check_mark_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="check-mark"><path d="M9.059 20.473 21.26 6.15l-1.52-1.298-10.8 12.675-4.734-4.734-1.414 1.414z"></path></svg>';
		
		$delivery_date_estimated = $this->get_delivery_date_estimate();
		
		$line_about_delivery_estimate = '<li>' . $check_mark_icon . ' Arrives soon! Get it by <span class="tooltip-notice" data-tooltip="' . self::RETURN_NOTICE . '">' . $delivery_date_estimated . '</span> if you order today</li>';
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
	
	$express_date       = $shipping->get_delivery_date_estimate( 'express' );
	$standard_date      = $shipping->get_delivery_date_estimate( 'standard' );
	$express_cost       = wc_price( $shipping->get_delivery_cost( 'express') );
	
	
	$calendar_icon   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 16a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M6.5 5H3v16h18V5h-3.5V3h-2v2h-7V3h-2zm0 2v1h2V7h7v1h2V7H19v3H5V7zM5 12v7h14v-7z"></path></svg>';
	$box_icon        = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.5 15h-6c-.3 0-.5.2-.5.5s.2.5.5.5h6c.3 0 .5-.2.5-.5s-.2-.5-.5-.5m-6-1h4c.3 0 .5-.2.5-.5s-.2-.5-.5-.5h-4c-.3 0-.5.2-.5.5s.2.5.5.5m5 3h-5c-.3 0-.5.2-.5.5s.2.5.5.5h5c.3 0 .5-.2.5-.5s-.2-.5-.5-.5"></path><path d="m21.9 6.6-2-4Q19.6 2 19 2H5q-.6 0-.9.6l-2 4c-.1.1-.1.2-.1.4v14c0 .6.4 1 1 1h18c.6 0 1-.4 1-1V7c0-.2 0-.3-.1-.4M5.6 4h12.8l1 2H4.6zM4 20V8h16v12z"></path></svg>';
	$express_shipping_icon   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 12.266 16.42 6H6v1h5v2H2V7h2V4h13.58L22 11.734V18h-2.17a3.001 3.001 0 0 1-5.66 0h-2.34a3.001 3.001 0 0 1-5.66 0H4v-3H2v-2h4v3h.17a3.001 3.001 0 0 1 5.66 0h2.34a3.001 3.001 0 0 1 5.66 0H20zM18 17a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path><path d="M17.5 11 15 7h-2v4zM9 12H2v-2h7z"></path></svg>';
	$free_shipping_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
<path fill-rule="evenodd" clip-rule="evenodd" d="M16.5 6H3V17.25H3.375H4.5H4.52658C4.70854 18.5221 5.80257 19.5 7.125 19.5C8.44743 19.5 9.54146 18.5221 9.72342 17.25H15.0266C15.2085 18.5221 16.3026 19.5 17.625 19.5C18.9474 19.5 20.0415 18.5221 20.2234 17.25H21.75V12.4393L18.3107 9H16.5V6ZM16.5 10.5V14.5026C16.841 14.3406 17.2224 14.25 17.625 14.25C18.6721 14.25 19.5761 14.8631 19.9974 15.75H20.25V13.0607L17.6893 10.5H16.5ZM15 15.75V9V7.5H4.5V15.75H4.75261C5.17391 14.8631 6.07785 14.25 7.125 14.25C8.17215 14.25 9.07609 14.8631 9.49739 15.75H15ZM17.625 18C17.0037 18 16.5 17.4963 16.5 16.875C16.5 16.2537 17.0037 15.75 17.625 15.75C18.2463 15.75 18.75 16.2537 18.75 16.875C18.75 17.4963 18.2463 18 17.625 18ZM8.25 16.875C8.25 17.4963 7.74632 18 7.125 18C6.50368 18 6 17.4963 6 16.875C6 16.2537 6.50368 15.75 7.125 15.75C7.74632 15.75 8.25 16.2537 8.25 16.875Z" fill="#080341"/>
</svg>';
	
	$location_icon   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M17.083 12.189 12 21l-5.083-8.811a6 6 0 1 1 10.167 0m-1.713-1.032.02-.033a4 4 0 1 0-6.78 0l.02.033 3.37 5.84z"></path></svg>';
	
	$date_notice = TannyBunny_Custom_Shipping_Helper::DATE_NOTICE;
	$express_notice = TannyBunny_Custom_Shipping_Helper::EXPRESS_NOTICE;
	$return_notice = TannyBunny_Custom_Shipping_Helper::RETURN_NOTICE;
	?>
	
	<h4>Shipping and return policies</h4>
		
	<ul class="shipping-and-return">
		<li><?php echo $free_shipping_icon; ?>  Free shipping &mdash; get it by <span class="tooltip-notice" data-tooltip="<?php echo $date_notice; ?>"><?php echo $standard_date; ?></span></li>
		<?php if ( $shipping->is_express_shipping_available() ): ?>
			<li><?php echo $express_shipping_icon; ?>  Express shipping for <?php echo $express_cost; ?> (<span class="tooltip-notice"  data-tooltip="<?php echo $express_notice; ?>" ><?php echo $express_date; ?>)</span></li>
		<?php endif; ?>
			
		<li><?php echo $box_icon; ?> <span class="tooltip-notice" data-tooltip="<?php echo $return_notice; ?>" >Returns & exchanges accepted</span> within 14 days</li>
		<li><?php echo $location_icon; ?>  Ships from <strong><?php echo $shipping_locations; ?></strong></li>
	</ul>
	<?php
}

add_action( 'woocommerce_shipping_init', 'initialize_tannybunny_fedex_shipping_method' );

function initialize_tannybunny_fedex_shipping_method( ) {
	if ( class_exists( 'WC_Shipping_Method' ) ) {
		include 'tannybunny-custom-shipping-methods.php';
	} 
}


add_shortcode( 'tannybunny_warehouse_filter', 'tannybunny_shortcode_warehouse_filter' );


/**
 * Handler for 'tannybunny_warehouse_filter' shortcode.
 * 
 * @param array $atts
 * @param string $content
 * @return string
 */
function tannybunny_shortcode_warehouse_filter( $atts, $content = null ) {

	
	$filter_value = $_GET['wpf_filter_warehouse'] ?? false;

	switch ( $filter_value ) {
		case 'armenia':
			$selected = 'armenia';
			$warehouse_note = 'Shipping: 10-30 days to all countries. Free of charge.';
			break;
		case 'usa':
			$selected = 'usa';
			
			
			if ( TannyBunny_Custom_Shipping_Helper::is_free_shipping_available() ) {
				
				$country = TannyBunny_Custom_Shipping_Helper::get_customer_country();
				$warehouse_note = "Free shipping: 5-7 days (available for $country). Expedited shipping via FedEx is also available for " . wc_price(7.5) . '.';
			}
			else {
				$warehouse_note = 'Expedited shipping via FedEx is available for ' . wc_price(7.5) . '.';
			}
			
			break;
		case 'armenia%7Cusa':
		default:
			$selected = 'all';
			$warehouse_note = 'Products can be shipped from either the USA or Armenia. Check delivery times and costs on the product page.';
	}

	$params = $_GET;
	unset($params['wpf_filter_warehouse']);
	
	$warehouse_options = [
		'armenia' => [
			'title'				=> 'Armenia',
			'url'					=> http_build_query( array_merge( $params, [ 'wpf_filter_warehouse' => 'armenia' ]  ) ),
			'selected'		=> ( $selected == 'armenia' )
		],
		'usa' => [
			'title'				=> 'USA',
			'url'					=> http_build_query( array_merge( $params, [ 'wpf_filter_warehouse' => 'usa' ]  ) ),
			'selected'		=> ( $selected == 'usa' )
		],
		'all' => [
			'title'				=> 'All',
			'url'					=> http_build_query( $params ),
			'selected'		=> ( $selected == 'all' )
		],
	];
			
	ob_start();
	?>
	<div class="warehouse_selector_container">
		<span class="warehouse_selector_title">Shipping from:</span>
		<?php foreach ( $warehouse_options as $option ): ?>
			<div class="warehouse_selector">
					<p>
							<a class="personal tabs__nav-item catalog-tabs__nav-item <?php echo( $option['selected'] ? 'active_text' : '' ); ?>" 
								 href="/shop/?<?php echo( $option['url'] );?>"><?php echo( $option['title'] );?>
							</a>
					</p>
			</div>
		<?php	endforeach; ?>
		
		<div class="warehouse_selector_note">
			<?php echo $warehouse_note; ?>
		</div>

	</div>
  <?php
	
	
	$out = ob_get_contents();
	ob_end_clean();

	return $out;
}

