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
    'us_filter_message_usa'      => 'Free shipping for USA!',
    'us_filter_message_free'     => 'Free shipping: 5-7 working days ( available for {country} )',
    'us_filter_message_standard' => 'Expedited shipping via FedEx is available for $7.5',
    'us_filter_message_everywhere' => 'Products can be shipped from either the USA or Armenia. Check delivery times and costs on the product page.',
		
		'am_delivery_min'            => 7,
		'am_delivery_max'            => 14,
		'am_delivery_min_express'    => 7,
		'am_delivery_max_express'    => 14,
		'am_processing_time'         => 3,
		
		'am_shipping_cost'           => 0,
		'am_shipping_cost_express'   => 12,
		'am_free_delivery_countries' => '*',
    'am_filter_message_free'     => 'Shipping: 10-30 days to all countries. Free of charge.',
    'am_filter_message_standard' => 'Shipping: 10-30 days to all countries for $10'
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
      case 'note':
				$input_html = self::make_note($field, $value);
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
	 * Generates HTML code for a note
	 * @param array $field
	 * @param array $value
	 */
	public static function make_note($field, $value) {

		$out = "<p>$value</p>";
      
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
  
  
  public static function get_warehouse_note( $string_name, $country_name = '' ) {
    
    if ( $country_name ) {
      $country = TannyBunny_Custom_Shipping_Helper::get_customer_country();
      $country_name = TannyBunny_Custom_Shipping_Helper::get_country_name( $country );
    }
    
    $warehouse_note = TannyBunny_Custom_Shipping_Helper::$option_values[ $string_name ];
    
    $result = self::extract_and_convert_price( str_replace( '{country}', $country_name, $warehouse_note ) );
   
    return $result;
  }
  
  public static function extract_and_convert_price( $string ) {
    
    $matches = array();
    
    if ( preg_match('/\{(\d+(?:[,.]\d+)?)\}/', $string, $matches) ) {
      $price = $matches[1];
      
      
      $result = str_replace( '{' . $price . '}', self::convert_price( floatval($price) ), $string );
    }
    else {
      $result = $string;
    }
    
    return $result;
  }
  
  
  public static function convert_price( $price ) { 
    
    $wmc = WOOMULTI_CURRENCY_Data::get_ins();

    $currency = $wmc->get_current_currency();

    $selected_currencies = $wmc->get_list_currencies();

    if ( $currency && isset( $selected_currencies[ $currency ] ) && is_array( $selected_currencies[ $currency ] ) ) {

      $price = round( wmc_get_price( $price, $currency ), 1 );

      $data   = $selected_currencies[ $currency ];
      $format = WOOMULTI_CURRENCY_Data::get_price_format( $data['pos'] );
      $args   = array(
        'currency'     => $currency,
        'price_format' => $format
      );

      if ( isset( $data['decimals'] ) ) {
        $args['decimals'] = absint( $data['decimals'] );
      }

      $converted_price = wc_price( $price, $args );
    }
    
    return $converted_price;
  }

  public static function get_country_name( $country_code = 'JP' ) {
		
		$countries = [
			"Afghanistan" => "AF",
			"Åland Islands" => "AX",
			"Albania" => "AL",
			"Algeria" => "DZ",
			"American Samoa" => "AS",
			"Andorra" => "AD",
			"Angola" => "AO",
			"Anguilla" => "AI",
			"Antarctica" => "AQ",
			"Antigua and Barbuda" => "AG",
			"Argentina" => "AR",
			"Armenia" => "AM",
			"Aruba" => "AW",
			"Australia" => "AU",
			"Austria" => "AT",
			"Azerbaijan" => "AZ",
			"Bahamas" => "BS",
			"Bahrain" => "BH",
			"Bangladesh" => "BD",
			"Barbados" => "BB",
			"Belarus" => "BY",
			"Belgium" => "BE",
			"Belize" => "BZ",
			"Benin" => "BJ",
			"Bermuda" => "BM",
			"Bhutan" => "BT",
			"Bolivia" => "BO",
			//"Bonaire, Sint Eustatius and Saba" => "BQ",
			"Bosnia and Herzegovina" => "BA",
			"Botswana" => "BW",
			"Bouvet Island" => "BV",
			"Brazil" => "BR",
			"British IOT" => "IO", //"British Indian Ocean Territory" => "IO",
			"Brunei Darussalam" => "BN",
			"Bulgaria" => "BG",
			"Burkina Faso" => "BF",
			"Burundi" => "BI",
			"Cambodia" => "KH",
			"Cameroon" => "CM",
			"Canada" => "CA",
			"Cape Verde" => "CV",
			"Cayman Islands" => "KY",
			"Central African Republic" => "CF",
			"Chad" => "TD",
			"Chile" => "CL",
			"China" => "CN",
			"Christmas Island" => "CX",
			"Cocos (Keeling) Islands" => "CC",
			"Colombia" => "CO",
			"Comoros" => "KM",
			"Congo" => "CG",
			"Congo" => "CD",
			"Cook Islands" => "CK",
			"Costa Rica" => "CR",
			"Côte d'Ivoire" => "CI",
			"Croatia" => "HR",
			"Cuba" => "CU",
			"Curaçao" => "CW",
			"Cyprus" => "CY",
			"Czech Republic" => "CZ",
			"Denmark" => "DK",
			"Djibouti" => "DJ",
			"Dominica" => "DM",
			"Dominican Republic" => "DO",
			"Ecuador" => "EC",
			"Egypt" => "EG",
			"El Salvador" => "SV",
			"Equatorial Guinea" => "GQ",
			"Eritrea" => "ER",
			"Estonia" => "EE",
			"Ethiopia" => "ET",
			"Falkland Islands (Malvinas)" => "FK",
			"Faroe Islands" => "FO",
			"Fiji" => "FJ",
			"Finland" => "FI",
			"France" => "FR",
			"French Guiana" => "GF",
			"French Polynesia" => "PF",
			"French Southern Territories" => "TF",
			"Gabon" => "GA",
			"Gambia" => "GM",
			"Georgia" => "GE",
			"Germany" => "DE",
			"Ghana" => "GH",
			"Gibraltar" => "GI",
			"Greece" => "GR",
			"Greenland" => "GL",
			"Grenada" => "GD",
			"Guadeloupe" => "GP",
			"Guam" => "GU",
			"Guatemala" => "GT",
			"Guernsey" => "GG",
			"Guinea" => "GN",
			"Guinea-Bissau" => "GW",
			"Guyana" => "GY",
			"Haiti" => "HT",
			"Heard Island and McDonald Islands" => "HM",
			"Vatican City" => "VA",
			"Honduras" => "HN",
			"Hong Kong" => "HK",
			"Hungary" => "HU",
			"Iceland" => "IS",
			"India" => "IN",
			"Indonesia" => "ID",
			"Iran" => "IR",
			"Iraq" => "IQ",
			"Ireland" => "IE",
			"Isle of Man" => "IM",
			"Israel" => "IL",
			"Italy" => "IT",
			"Jamaica" => "JM",
			"Japan" => "JP",
			"Jersey" => "JE",
			"Jordan" => "JO",
			"Kazakhstan" => "KZ",
			"Kenya" => "KE",
			"Kiribati" => "KI",
			"North Korea" => "KP",
			"Korea" => "KR",
			"Kuwait" => "KW",
			"Kyrgyzstan" => "KG",
			"Laos" => "LA",
			"Latvia" => "LV",
			"Lebanon" => "LB",
			"Lesotho" => "LS",
			"Liberia" => "LR",
			"Libya" => "LY",
			"Liechtenstein" => "LI",
			"Lithuania" => "LT",
			"Luxembourg" => "LU",
			"Macao" => "MO",
			"Macedonia" => "MK",
			"Madagascar" => "MG",
			"Malawi" => "MW",
			"Malaysia" => "MY",
			"Maldives" => "MV",
			"Mali" => "ML",
			"Malta" => "MT",
			"Marshall Islands" => "MH",
			"Martinique" => "MQ",
			"Mauritania" => "MR",
			"Mauritius" => "MU",
			"Mayotte" => "YT",
			"Mexico" => "MX",
			"Micronesia" => "FM", //"Micronesia, Federated States of" => "FM",
			"Moldova" => "MD",
			"Monaco" => "MC",
			"Mongolia" => "MN",
			"Montenegro" => "ME",
			"Montserrat" => "MS",
			"Morocco" => "MA",
			"Mozambique" => "MZ",
			"Myanmar" => "MM",
			"Namibia" => "NA",
			"Nauru" => "NR",
			"Nepal" => "NP",
			"Netherlands" => "NL",
			"New Caledonia" => "NC",
			"New Zealand" => "NZ",
			"Nicaragua" => "NI",
			"Niger" => "NE",
			"Nigeria" => "NG",
			"Niue" => "NU",
			"Norfolk Island" => "NF",
			"Northern Mariana Islands" => "MP",
			"Norway" => "NO",
			"Oman" => "OM",
			"Pakistan" => "PK",
			"Palau" => "PW",
			"Palestine, State of" => "PS",
			"Panama" => "PA",
			"Papua New Guinea" => "PG",
			"Paraguay" => "PY",
			"Peru" => "PE",
			"Philippines" => "PH",
			"Pitcairn" => "PN",
			"Poland" => "PL",
			"Portugal" => "PT",
			"Puerto Rico" => "PR",
			"Qatar" => "QA",
			"Réunion" => "RE",
			"Romania" => "RO",
			"Russia" => "RU",
			"Rwanda" => "RW",
			"Saint Barthélemy" => "BL",
			"Saint Helena" => "SH",
			"Saint Kitts and Nevis" => "KN",
			"Saint Lucia" => "LC",
			"Saint Martin" => "MF",
			"Saint Pierre and Miquelon" => "PM",
			"Saint Vincent and the Grenadines" => "VC",
			"Samoa" => "WS",
			"San Marino" => "SM",
			"Sao Tome and Principe" => "ST",
			"Saudi Arabia" => "SA",
			"Senegal" => "SN",
			"Serbia" => "RS",
			"Seychelles" => "SC",
			"Sierra Leone" => "SL",
			"Singapore" => "SG",
			"Sint Maarten" => "SX",
			"Slovakia" => "SK",
			"Slovenia" => "SI",
			"Solomon Islands" => "SB",
			"Somalia" => "SO",
			"South Africa" => "ZA",
			"South Georgia" => "GS",
			"South Sudan" => "SS",
			"Spain" => "ES",
			"Sri Lanka" => "LK",
			"Sudan" => "SD",
			"Suriname" => "SR",
			"Svalbard and Jan Mayen" => "SJ",
			"Eswatini" => "SZ",
			"Sweden" => "SE",
			"Switzerland" => "CH",
			"Syria" => "SY",
			"Taiwan" => "TW",
			"Tajikistan" => "TJ",
			"Tanzania" => "TZ",
			"Thailand" => "TH",
			"Timor-Leste" => "TL",
			"Togo" => "TG",
			"Tokelau" => "TK",
			"Tonga" => "TO",
			"Trinidad and Tobago" => "TT",
			"Tunisia" => "TN",
			"Turkey" => "TR",
			"Turkmenistan" => "TM",
			"Turks and Caicos Islands" => "TC",
			"Tuvalu" => "TV",
			"Uganda" => "UG",
			"Ukraine" => "UA",
			"United Arab Emirates" => "AE",
			"United Kingdom" => "GB",
			"United States" => "US",
			"United States Minor Outlying Islands" => "UM",
			"Uruguay" => "UY",
			"Uzbekistan" => "UZ",
			"Vanuatu" => "VU",
			"Venezuela" => "VE",
			"Viet Nam" => "VN",
			"Virgin Islands, British" => "VG",
			"Virgin Islands, U.S." => "VI",
			"Wallis and Futuna" => "WF",
			"Western Sahara" => "EH",
			"Yemen" => "YE",
			"Zambia" => "ZM",
			"Zimbabwe" => "ZW"
		];
		
		if ( in_array( $country_code, $countries ) ) {
		
			$codes = array_flip($countries);
			return $codes[$country_code];
		}
		
		return $country_code;
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
				'step' => 0.1,
				'value' => self::$option_values['us_shipping_cost'],
			),
			array(
				'name' => "us_shipping_cost_express",
				'type' => 'number',
				'label' => 'Default cost of express shipping',
				'min' => 0,
				'max' => 100,
				'step' => 0.1,
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
      array(
				'name' => "us_filter_message_usa",
				'type' => 'text',
        'size' => 45,
				'label' => 'Filter note when shipping from USA to USA',
				'value' => self::$option_values['us_filter_message_usa'],
			),
      array(
				'name' => "us_filter_message_free",
				'type' => 'text',
        'size' => 45,
				'label' => 'Filter note when free shipping from USA is available',
				'value' => self::$option_values['us_filter_message_free'],
			),
      array(
				'name' => "us_filter_message_standard",
				'type' => 'text',
        'size' => 45,
				'label' => 'Filter note when free shipping from USA is NOT available',
				'value' => self::$option_values['us_filter_message_standard'],
			),
      array(
				'name' => "note",
				'type' => 'note',
				'value' => '<strong>{country}</strong> will be automatically replaced by the visitor\'s country name'
        . '<br><strong>{123}</strong> will be replaced with $123 converted to visitor\'s currency',
			),
      array(
				'name' => "us_filter_message_everywhere",
				'type' => 'text',
        'size' => 45,
				'label' => 'Filter note when search is for both shipping from USA and Armenia',
				'value' => self::$option_values['us_filter_message_everywhere'],
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
      array(
				'name' => "am_filter_message_free",
				'type' => 'text',
        'size' => 45,
				'label' => 'Filter note when free shipping from Armenia is available',
				'value' => self::$option_values['am_filter_message_free'],
			),
      array(
				'name' => "am_filter_message_standard",
				'type' => 'text',
        'size' => 45,
				'label' => 'Filter note when free shipping from Armenia is NOT available',
				'value' => self::$option_values['am_filter_message_standard'],
			),
		);
		
		?>
		<form method="POST" >

				<h2>Delivery times and costs</h2>
        
        <pre>
[1] - minimum estimated delivery time for standard shipping
[2] - maximum estimated delivery time for standard shipping
[3] - cost of standard shipping ( put 0 there to make it free)
[4] - minimum estimated delivery time for express shipping (set to 0 when express shipping is not available)
[5] - maximum estimated delivery time for express shipping (set to 0 when express shipping is not available)
[6] - cost of express shipping
        </pre>
        
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

        <h4>List of countries with free shipping from USA</h4>
        <?php echo self::render_free_delivery_countries_list( self::$option_values['us_free_delivery_countries'] ); ?>
        
				<p class="submit">  
						<input type="submit" id="tbd-button-save" name="tbd-button-save" class="button button-primary" style="" value="<?php echo self::ACTION_SAVE_OPTIONS; ?>" />
				</p>

		</form>

		<?php
	}
  
  public static function render_free_delivery_countries_list( $countries_csv ) {
    
    $list_html = '<ul>';
    $countries = array_map( 'trim', explode( ',', $countries_csv ) );
    
    sort( $countries );
    
    foreach ( $countries as $country_code ) {
      $country_name = self::get_country_name( $country_code ); 
      $list_html .= "<li><strong>$country_code</strong> - $country_name </li>";
    }
    
    $list_html .= '</ul>';
    
    return $list_html;
  }
}

/**
 * This class instantiates a helper for the specified product,
 * and gathers all delivery info related TO THAT PRODUCT.
 * 
 * Each method of this class acts upon the delivery settings for the specified product!
 * 
 * Warehouse availability is set by site admin, for each product individually.
 * 
 * This class finds delivery settings for the specified product and uses them to calculate delivery times.
 */
class TannyBunny_Custom_Shipping_Helper extends TannyBunny_Custom_Shipping_Core {
	
	private $product = false;
	private $product_id = false;
	private $customer_country = false;
	
	public $product_has_warehouses = false;
	
	/**
	 * may be "Armenia, USA" or "Armenia" or "USA" 
	 * @var string 
	 */
	public $available_warehouse_names = ''; 
	
	/**
	 * array of warehouses, of format [ 'Armenia' => 'am' ]
	 * @var array
	 */
	public $available_warehouses = array();
	
	/**
	 * See get_delivery_settings_for_warehouse() for the setting format
	 */
	public $country_delivery_settings_am = false; // country settings for the warehouse in Armenia (am)
	public $country_delivery_settings_us = false; // country settings for the warehouse in for USA (us)
	
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
	
	
	public const DELIVERY_NOT_FOUND = -1;
	/**
	 * @param WC_Product $product
	 */
	public function __construct( $product, $customer_country = 'US' ) {
		$this->product = $product;
		$this->product_id = $product->get_id();
		$this->customer_country = $customer_country;
		
		self::load_options();
		
		
		// string "Armenia, USA" or similar
		$this->available_warehouse_names = $product->get_attribute( 'warehouse' );
		
		// get an array of options from a string "Armenia, USA"
		// also, make sure this is an empty array if there are no warehouses ( by using 'array_filter')
		$warehouse_names = array_filter( array_map( 'trim', explode( ',' , $this->available_warehouse_names ) ) );
		
    $this->available_warehouse_names2 = implode( ' or ', $warehouse_names);
		
		$this->available_warehouses      = self::find_warehouses_by_names( $warehouse_names );
		
		$this->country_delivery_settings_am     = $this->get_delivery_settings_for_warehouse( 'am', $this->customer_country );
		$this->country_delivery_settings_us     = $this->get_delivery_settings_for_warehouse( 'us', $this->customer_country );
		
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
	
	/**
	 * Returns array of warehouses, of format [ 'Armenia' => 'am' ]
	 * 
	 * @param array $names
	 * @return array
	 */
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
	
	/**
	 * Used in {THEME_DIR}/woocommerce/single-product/title.php
	 * 
	 * @return string
	 */
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
		
		$result = 'US'; // default value
		
		if ( class_exists( 'WC_Geolocation' ) ) {
			$location = WC_Geolocation::geolocate_ip();

			if ( is_array($location) && isset($location['country']) && $location['country'] != '') {
				$result = $location['country'];
			}
		}
		
		if ( isset( $_GET['tb_debug_customer_country'] ) ) {
			$result = $_GET['tb_debug_customer_country'];
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param string $mode
	 * @param string $warehouse_restriction 'us' or 'am' or empty. Empty strings allows to use any warehouse for estimations
	 * @return array
	 */
	public function get_delivery_estimate( $mode = 'standard', $warehouse_restriction = '' ) {
		
		//$delivery_country = self::get_customer_country();
		tb_log(" get_customer_country " . $this->customer_country . '' );
		
		$min_delivery_time = 999999;
		$max_delivery_time = 1;
		
		if ( count( $this->available_warehouses ) ) {
		
			if ( ! $warehouse_restriction ) {
				$available_warehouses = $this->available_warehouses;
			}
			else {
				$available_warehouses = array( $warehouse_restriction => 'Warehouse' );
			}
			
			// iterate through warehouses to find the fastest delivery time
			foreach ( $available_warehouses as $warehouse_id => $warehouse_name ) {
				$estimate_in_days = $this->estimate_delivery_for_warehouse( $warehouse_id, $mode ); // may return false

				//tb_log(" estimate_in_days  $warehouse_id, $mode <pre>" . print_r( $estimate_in_days , 1 ) . '</pre>' );
				
				if ( is_array( $estimate_in_days ) ) {
					if ( $estimate_in_days['from'] < $min_delivery_time ) {
						$min_delivery_time = $estimate_in_days['from'];
						$max_delivery_time = $estimate_in_days['to'];
					}
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
	
	/**
	 * Checks whether there are free shipping method available for the specified warehouse and country
	 * @return bool
	 */
	public static function is_free_shipping_available_for_country( $warehouse_id = 'us', $country = 'JP' ) {
		
		$country_delivery_settings = self::get_delivery_settings_for_warehouse( $warehouse_id, strtoupper($country) );
		
		//echo('$country_delivery_settings<pre>' . print_r( $country_delivery_settings , 1 ) . '</pre>' );
		if ( $country_delivery_settings['cost'] == 0 ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks whether there are at least one free shipping method available
* 
   * @param string $warehouse_restriction 'us' or 'am' or empty. Empty strings allows to use any warehouse for estimations
	 * @return bool
	 */
	public function is_free_shipping_available( $warehouse_restriction = false ) {
		
		$result = false;
						
		foreach ( $this->available_warehouses as $warehouse_id => $warehouse_name ) {
      
      if ( ! $warehouse_restriction || ( $warehouse_restriction == $warehouse_id )) {
        $estimate = $this->get_delivery_settings_for_warehouse( $warehouse_id, $this->customer_country );

        if ( is_array($estimate) && $estimate['cost'] == 0 )  {
          $result = true;
        }
      }
		}
		
		return $result;
	}
	
	/**
	 * Checks whether there are standard (non-express) delivery method available for the current product
	 * ( from Armenia, or from US ) and for the current visitor's country
	 * 
	 * @return bool
	 */
	public function is_standard_shipping_available( $target = false ) {
		
		$result = false;
		
    foreach ( $this->available_warehouses as $warehouse_id => $warehouse_name ) {
      
      if ( $warehouse_id == 'am' && ( $target == 'am' || $target == false ) ) {
        $delivery_settings = $this->country_delivery_settings_am;
      }
      elseif ( $warehouse_id == 'us' && ($target == 'us' || $target == false ) ) {
        $delivery_settings = $this->country_delivery_settings_us;
      }

      if ( is_array($delivery_settings) && $delivery_settings['cost'] >= 0 ) {
        $result = true;
        break;
      }
    }
    
		return $result;
	}
	
	/**
	 * Checks whether there are at least one free shipping method available
	 * 
	 * @param $target warehouse restriction ( 'am' or 'us'). Or "false", if no restriction
	 * @return bool
	 */
	public function is_express_shipping_available( $target = false ) {
		
		$result = false;
		$delivery_settings = false;

		foreach ( $this->available_warehouses as $warehouse_id => $warehouse_name ) {
			
			if ( $warehouse_id == 'am' && ( $target == 'am' || $target == false ) ) {
				$delivery_settings = $this->country_delivery_settings_am;
			}
			elseif ( $warehouse_id == 'us' && ($target == 'us' || $target == false ) ) {
				$delivery_settings = $this->country_delivery_settings_us;
			}

			if ( is_array($delivery_settings) && $delivery_settings['from_exp'] > 0 && $delivery_settings['to_exp'] > 0 ) {
				$result = true;
				break;
			}
		}

		
		return $result;
	}
	
	/**
	 * Calculates expected delivery costs given the mode and restrictions.
	 * Returns DELIVERY_NOT_FOUND if there are no possible way to delvier the product (given the restrictions).
	 * 
	 * @param string $mode "standard" or "express"
	 * @param string $warehouse_restriction "am" or "us"
	 * @return integer
	 */
	public function get_delivery_cost( $mode = 'standard', $warehouse_restriction = '' ) {
		
		$min_cost = 999;
		$found_delivery = false;
		
		if ( ! $warehouse_restriction ) {
			$available_warehouses = $this->available_warehouses;
		}
		else {
			$available_warehouses = array( $warehouse_restriction => 'Warehouse' );
		}
		
		if ( ! $this->product_has_warehouses ) { // when no warehouses are expicitly listed for the product assume that product will be shipped from Armenia
			
			if ( $warehouse_restriction != 'am' ) {
				return self::DELIVERY_NOT_FOUND; // product does not have any non-armenian warehouses
			}
			else {
				$available_warehouses = array( 'am' => 'Armenia' );
			}
		}
			
		// iterate through warehouses to find the cheapest delivery
		foreach ( $available_warehouses as $warehouse_id => $warehouse_name ) {
			$estimate = $this->estimate_delivery_for_warehouse( $warehouse_id, $mode );

			if ( isset($estimate['cost']) && $estimate['cost'] < $min_cost ) {
				
				$found_delivery = true;
				$min_cost = $estimate['cost'];
			}
		}
		
		if ( $found_delivery ) {
			return $min_cost;
		}
		else {
			return self::DELIVERY_NOT_FOUND;
		}
	}
	
	/**
	 * Returns a string with estimated dates of delivery. e.g "20 Nov-30 Dec"
	 * 
	 * @param string $mode "standard" or "express" or "free"
	 * @param string $warehouse_restriction "am" or "us"
	 * @return string e.g "20 Nov-30 Dec"
	 */
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
	 * Returns a string with estimated dates of delivery. e.g "20 Nov-30 Dec"
	 * 
	 * @param string $warehouse_restriction "am" or "us"
	 * @return string e.g "20 Nov-30 Dec"
	 */
	public function get_delivery_date_range( $warehouse_restriction = '' ) {
		
		$out = "???";
		
		if ( $this->is_standard_shipping_available() ) {
			$standard_estimate = $this->get_delivery_estimate( 'standard', $warehouse_restriction );
		}
		else {
			$standard_estimate = false;
		}
		
		if ( $this->is_express_shipping_available() ) {
			$express_estimate = $this->get_delivery_estimate( 'express', $warehouse_restriction );
		}
		else {
			$express_estimate = $standard_estimate;
		}
		
		if ( $standard_estimate && $express_estimate ) {
			// Assuming that express delivery always faster or same as standard
			$from_timestamp = time() + $express_estimate['from'] * DAY_IN_SECONDS; // minimum possible date
			$to_timestamp   = time() + $standard_estimate['to'] * DAY_IN_SECONDS; // maximum possible date
		}
		else if ( $express_estimate ) {
			$from_timestamp = time() + $express_estimate['from'] * DAY_IN_SECONDS;
			$to_timestamp   = time() + $express_estimate['to'] * DAY_IN_SECONDS;
		}
		else if ( $standard_estimate ) {
			$from_timestamp = time() + $standard_estimate['from'] * DAY_IN_SECONDS;
			$to_timestamp   = time() + $standard_estimate['to'] * DAY_IN_SECONDS;
		}
		
		if ( $from_timestamp && $to_timestamp ) {
			$from_month = date( 'M', $from_timestamp );
			$to_month   = date( 'M', $to_timestamp );

			if ( $from_month === $to_month ) { // need to output it like "Nov 13-25"
				$from = date( 'M j', $from_timestamp );
				$to   = date( 'j', $to_timestamp );
				$out  = "$from-$to";
			} 
			else { // need to output it like "Sep 13-Oct 25"
				$from = date( 'M j', $from_timestamp );
				$to   = date( 'M j', $to_timestamp );
				$out  = "$from-$to";
			}
		}
		
		return $out;
	}
	
	
	public static function get_list_of_free_delivery_countries( $warehouse_id ) {
		$countries_list = self::$option_values[ $warehouse_id . '_free_delivery_countries'];
		return array_map( 'strtoupper', array_map('trim', explode( ',', $countries_list ) ) );
	}
	
	public static function is_free_delivery_available_for_country( $country, $warehouse_id ) {
		
		$result = false;
		
		$countries_list = self::get_list_of_free_delivery_countries( $warehouse_id );
		
		// * - indicates all countries
		if ( in_array( strtoupper($country), $countries_list ) || in_array( '*', $countries_list ) ) {
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * Gets delivery settings for the specified country
	 * 
	 * @param string $warehouse_id - "am" or "us"
	 * @param string $country Two-letter country code, e.g. JP, GE, US, RU
	 * @return array
	 */
	public static function get_delivery_settings_for_warehouse( string $warehouse_id, string $country ) {
		
		$option_name = self::OPTION_DELIVERY_ESTIMATES . '_' . $warehouse_id;
		
		$warehouse_delivery_data = get_option( $option_name , '' );
		
		$all_countries_settings = explode( "\r\n", $warehouse_delivery_data );
		
		$processing = self::$option_values[ $warehouse_id . '_processing_time'];
		
		// default values for standard delivery
		$from     = self::$option_values[ $warehouse_id . '_delivery_min'];
		$to       = self::$option_values[ $warehouse_id . '_delivery_max'];
		$cost     = self::$option_values[ $warehouse_id . '_shipping_cost'];

		// default values for express delivery
		$from_exp = self::$option_values[ $warehouse_id . '_delivery_min_express'];
		$to_exp   = self::$option_values[ $warehouse_id . '_delivery_max_express'];
		$cost_exp = self::$option_values[ $warehouse_id . '_shipping_cost_express'];
		
		$free_cn  = self::get_list_of_free_delivery_countries( $warehouse_id );
		
		$delivery_settings = [
			'from'       => $from,
			'to'         => $to,
			'cost'       => $cost,
			'from_exp'   => $from_exp,
			'to_exp'     => $to_exp,
			'cost_exp'   => $cost_exp,
			'free_cn'    => $free_cn // list of countries with free shipping
		];
		
		
		/**
		 * Example of $country_settings string: JP,20,30,0,4,5,12
		 * 
		 * JP - Japan
		 * 20 - min delivery time is 20 days for standard shipping
		 * 30 - max delivery time is 30 days for standard shipping
		 * 0  - cost of standard shipping (it is free)
		 * 4  - min delivery time is 4 days for express shipping (set to 0 when express shipping is not available)
		 * 5  - max delivery time is 5 days for express shipping (set to 0 when express shipping is not available)
		 * 12 - cost of express shipping
		 *  
		 * 
		 */
		
		foreach ( $all_countries_settings as $country_settings ) {
			
			$country_settings = str_getcsv( $country_settings, ',' );
			
			if ( is_array($country_settings) && count( $country_settings ) >= 6 ) {
				
				if ( strtoupper($country_settings[0]) == strtoupper($country) )  {
									
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
						'free_cn'    => $free_cn, // use default list of countries with free shipping
					];
					
					break;
				}
			}
		}
		
		// special check for availability of free shipping
		if ( self::is_free_delivery_available_for_country( $country, $warehouse_id ) ) {
			$delivery_settings['cost'] = 0;
		}
    else {
      if ( $warehouse_id == 'us' ) { // if a country is not in "FREE DELIVERY" list, make the standard delivery unavailable (when shipping from USA) 
        
        $delivery_settings['cost'] = self::DELIVERY_NOT_FOUND;
      }
    }
		
		// include processing time required by warehouse
		
		$processing_days = $processing;
		
		$saturday = ( date('w') == 6 );
		$sunday = ( date('w') == 0 );
		
		if ( $saturday ) {
			$processing_days += 2;
		}
		else if ( $sunday ) {
			$processing_days += 1;
		}
		
		
		$delivery_settings['from']     += $processing_days;
		$delivery_settings['to']       += $processing_days;
		
		if ( $delivery_settings['from_exp'] > 0 ) { $delivery_settings['from_exp'] += $processing_days; }
		if ( $delivery_settings['to_exp'] > 0 ) { $delivery_settings['to_exp'] += $processing_days; }
		
		
	tb_log($delivery_settings);
	
		return $delivery_settings;
	}
	
	/**
	 * Returns array of delivery parameters.
	 * 
	 * for all parameters, see get_delivery_settings_for_warehouse()
	 * 
	 * @param string $warehouse_id - "am" or "us"
	 * @param string $mode "standard" or "express" or "free"
	 * @return array
	 */
	public function estimate_delivery_for_warehouse( string $warehouse_id, string $mode = 'standard' ) {
		
		if ( $warehouse_id == 'am' ) {
			$delivery_settings = $this->country_delivery_settings_am;
		}
		else {
			$delivery_settings = $this->country_delivery_settings_us;
		}
		
		$delivery_estimate = false;
		
    if ( $mode == 'free' && $this->is_free_shipping_available( $warehouse_id ) ) {
			$delivery_estimate = array(
				'from'   => $delivery_settings['from'],
				'to'     => $delivery_settings['to'],
				'cost'   => 0
			);
		}
		else if ( $mode == 'standard' && $this->is_standard_shipping_available( $warehouse_id ) ) {
			$delivery_estimate = array(
				'from'   => $delivery_settings['from'],
				'to'     => $delivery_settings['to'],
				'cost'   => $delivery_settings['cost']
			);
		}
		else if ( $mode == 'express' && $this->is_express_shipping_available( $warehouse_id ) ) {
			
			if ( $delivery_settings['from_exp'] > 0 && $delivery_settings['to_exp'] > 0 ) {
				$delivery_estimate = array(
					'from'   => $delivery_settings['from_exp'],
					'to'     => $delivery_settings['to_exp'],
					'cost'   => $delivery_settings['cost_exp']
				);
			}
		}
		
		//tb_log(" delivery_estimate $warehouse_id $mode <pre>" . print_r( $delivery_estimate , 1 ) . '</pre>' );
		return $delivery_estimate;
	}
	
	public function render_shipping_details() {
		
		$check_mark_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="check-mark"><path d="M9.059 20.473 21.26 6.15l-1.52-1.298-10.8 12.675-4.734-4.734-1.414 1.414z"></path></svg>';
		
		$country = $this->get_country_name( $this->customer_country );
		
		$delivery_date_estimated = $this->get_delivery_date_range();
		
		$line_about_delivery_estimate = '<li>' . $check_mark_icon . ' Arrives to ' . $country . ' by <span class="tooltip-notice" data-tooltip="' . self::RETURN_NOTICE . '">' . $delivery_date_estimated . '</span> if you order today</li>';
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
	
	$country = TannyBunny_Custom_Shipping_Helper::get_customer_country();
	
	$shipping = new TannyBunny_Custom_Shipping_Helper( $product, $country );
	
  $country_name = $shipping->get_country_name( $country );
  
	$shipping_locations = $shipping->available_warehouse_names2 ?: 'Armenia';
	
	$express_date       = $shipping->get_delivery_date_estimate( 'express' );
	$free_date_am       = $shipping->get_delivery_date_estimate( 'free', 'am'  );
  $free_date_us       = $shipping->get_delivery_date_estimate( 'free', 'us'  );
  $standard_date_am   = $shipping->get_delivery_date_estimate( 'standard', 'am' );
	$standard_date_us   = $shipping->get_delivery_date_estimate( 'standard', 'us' );
	$express_cost       = $shipping->get_delivery_cost( 'express' );

	$express_cost_fm = TannyBunny_Custom_Shipping_Core::convert_price( $express_cost );

  /*
	$wmc = WOOMULTI_CURRENCY_Data::get_ins();

	$currency = $wmc->get_current_currency();

	$selected_currencies = $wmc->get_list_currencies();

	if ( $currency && isset( $selected_currencies[ $currency ] ) && is_array( $selected_currencies[ $currency ] ) ) {

		$express_cost = round( wmc_get_price( $express_cost, $currency ), 1 );
		
		$data   = $selected_currencies[ $currency ];
		$format = WOOMULTI_CURRENCY_Data::get_price_format( $data['pos'] );
		$args   = array(
			'currency'     => $currency,
			'price_format' => $format
		);

		if ( isset( $data['decimals'] ) ) {
			$args['decimals'] = absint( $data['decimals'] );
		}

		$express_cost_fm = wc_price( $express_cost, $args );
	}
	else {
		$express_cost_fm = wc_price( $express_cost ); 
	}
   * 
   */

	
	
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
		<?php if ( $shipping->is_free_shipping_available( 'us' ) ): ?>
			<li><?php echo $free_shipping_icon; ?>  Free shipping to <?php echo $country_name; ?> &mdash; get it by <span class="tooltip-notice" data-tooltip="<?php echo $date_notice; ?>"><?php echo $free_date_us; ?></span></li>
    <?php elseif ( $shipping->is_free_shipping_available( 'am' ) ): ?>
			<li><?php echo $free_shipping_icon; ?>  Free shipping to <?php echo $country_name; ?> &mdash; get it by <span class="tooltip-notice" data-tooltip="<?php echo $date_notice; ?>"><?php echo $free_date_am; ?></span></li>
		<?php elseif ( $shipping->is_standard_shipping_available( 'us' ) ): ?>
			<li><?php echo $free_shipping_icon; ?>  Standard shipping to <?php echo $country_name; ?>&mdash; get it by <span class="tooltip-notice" data-tooltip="<?php echo $date_notice; ?>"><?php echo $standard_date_us; ?></span></li>
    <?php elseif ( $shipping->is_standard_shipping_available( 'am' ) ): ?>
			<li><?php echo $free_shipping_icon; ?>  Standard shipping to <?php echo $country_name; ?>&mdash; get it by <span class="tooltip-notice" data-tooltip="<?php echo $date_notice; ?>"><?php echo $standard_date_am; ?></span></li>
		<?php endif; ?>
		<?php if ( $shipping->is_express_shipping_available() ): ?>
			<li><?php echo $express_shipping_icon; ?>  Express shipping for <?php echo $express_cost_fm; ?> (<span class="tooltip-notice"  data-tooltip="<?php echo $express_notice; ?>" ><?php echo $express_date; ?>)</span></li>
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

  TannyBunny_Custom_Shipping_Helper::load_options();
  
	switch ( $filter_value ) {
		case 'from-armenia':
			$selected = 'from-armenia';
			$warehouse_note = TannyBunny_Custom_Shipping_Helper::get_warehouse_note( 'am_filter_message_free' );
			break;
		case 'from-usa':
			$selected = 'from-usa';
			
			$country = TannyBunny_Custom_Shipping_Helper::get_customer_country();
      
      if ( $country == 'US' ) {
        //"Shipping to USA: 2-5 days. Expedited shipping via FedEx is also available for " . wc_price(7.5) . '.'; 
        $warehouse_note = TannyBunny_Custom_Shipping_Core::get_warehouse_note( 'us_filter_message_usa', 'USA' );        
      }
      else {
        $country_name = TannyBunny_Custom_Shipping_Helper::get_country_name( $country );

        if ( TannyBunny_Custom_Shipping_Helper::is_free_shipping_available_for_country( 'us', $country ) ) {
          //$warehouse_note = "Free shipping to $country_name: 5-7 days. Expedited shipping via FedEx is also available for " . wc_price(7.5) . '.';
          $warehouse_note = TannyBunny_Custom_Shipping_Core::get_warehouse_note( 'us_filter_message_free', $country_name );
          
        }
        else {
          //$warehouse_note = "Shipping to $country_name: 5-7 days."; // Expedited shipping via FedEx is available for " . wc_price(7.5) . '.';
          $warehouse_note = TannyBunny_Custom_Shipping_Core::get_warehouse_note( 'us_filter_message_standard', $country_name );
        }
      }
			
			break;
		case 'armenia%7Cusa':
		default:
			$selected = 'all';
			$warehouse_note = TannyBunny_Custom_Shipping_Core::get_warehouse_note( 'us_filter_message_everywhere', $country_name );
	}

	$params = $_GET;
	unset($params['wpf_filter_warehouse']);
	
	$warehouse_options = [
		'armenia' => [
			'title'				=> 'Armenia',
			'url'					=> http_build_query( array_merge( $params, [ 'wpf_filter_warehouse' => 'from-armenia' ]  ) ),
			'selected'		=> ( $selected == 'from-armenia' )
		],
		'usa' => [
			'title'				=> 'USA',
			'url'					=> http_build_query( array_merge( $params, [ 'wpf_filter_warehouse' => 'from-usa' ]  ) ),
			'selected'		=> ( $selected == 'from-usa' )
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

/**
 * Make sure that stock levels are reduced only for products shipped from USA warehouse
 * 
 * @param WC_Order $order
 * @return int
 */
function reduce_product_stock_for_usa_only( $qty, $order, $item ) {
	
	// Attribute name and value to check
	$warehouse_attribute = 'pa_warehouse';
	$warehouse_usa = 74;
	
	$reduce_stock = false; // by default, DO NOT reduce stock for purchased products
	
	$shipping_from_usa = false;
	
	foreach( $order->get_shipping_methods() as $shipping_method ) {
		
		if ( $shipping_method->get_method_id() == 'tb_usa_shipping' ) {
			$shipping_from_usa = true;
			break;
		}
  }
	
	if ( $shipping_from_usa ) { // enable reducing stock quantity for USA shipping only
		
		$product = $item->get_product();

		if ( $product ) {

			if ( is_a( $product, 'WC_Product_Variation' ) ) {
				$parent_product =	wc_get_product( $product->get_parent_id() );
				$product_attributes = $parent_product->get_attributes();
			}
			else {
				$product_attributes = $product->get_attributes();
			}

			// Check if the product has the specified attribute and value

			if ( isset( $product_attributes[$warehouse_attribute] ) 
					&& in_array( $warehouse_usa, $product_attributes[$warehouse_attribute]->get_options(), true ) ) {

				$reduce_stock = true; // this product is located in USA warehouse
			}
		}
	}
	
	tb_log(" shipping_from_usa  -  [ $shipping_from_usa ] reduce_product_stock_for_usa_only  - RESULT [ $reduce_stock ] " );
	
	// Prevent stock reduction if the conditions are not met
	if ( ! $reduce_stock ) {
		return 0;
	}
	else {
		return $qty;
	}
}

/**
 * Make sure that stock levels are reduced only for products shipped from USA warehouse
 * 
 * Filter order item quantity.
 *
 * @param int|float             $quantity Quantity.
 * @param WC_Order              $order    Order data.
 * @param WC_Order_Item_Product $item Order item data.
 */
add_filter( 'woocommerce_order_item_quantity', 'reduce_product_stock_for_usa_only', 10, 3 );


/**
 * Logs are the best friend of a debugger
 */
function tb_log( $data ) {

	$filename = pathinfo( __FILE__, PATHINFO_DIRNAME ) . DIRECTORY_SEPARATOR . 'tb-log.txt';
	
	file_put_contents( $filename, date( "Y-m-d H:i:s" ) . " | " . print_r( $data, 1 ) . "\r\n\r\n", FILE_APPEND );
	
}
