<?php

/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme('storefront');
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if (class_exists('Jetpack')) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if (storefront_is_woocommerce_activated()) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if (is_admin()) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if (version_compare(get_bloginfo('version'), '4.7.3', '>=') && (is_admin() || is_customize_preview())) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

if (function_exists('acf_add_options_page')) {

	acf_add_options_page();
}
function woocommerce_support()
{
	add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'woocommerce_support');
function get_categories_product($categories_list = "")
{

	$get_categories_product = get_terms("product_cat", [
		"orderby" => "name", // Тип сортировки
		"order" => "ASC", // Направление сортировки
		"hide_empty" => 1, // Скрывать пустые. 1 - да, 0 - нет.
	]);

	if (count($get_categories_product) > 0) {

		$categories_list = '<ul class="main_categories_list">';

		foreach ($get_categories_product as $categories_item) {

			$categories_list .= '<li><a class="tabs__nav-item catalog-tabs__nav-item href="' . esc_url(get_term_link((int)$categories_item->term_id)) . '">' . esc_html($categories_item->name) . '</a></li>';
		}

		$categories_list .= '</ul>';
	}

	return $categories_list;
}
register_nav_menus(array( // Регистрируем 2 меню
	'top' => 'Верхнее меню',
	'left' => 'Нижнее'
));

add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

function woo_remove_product_tabs($tabs)
{

	unset($tabs['description']);
	unset($tabs['additional_information']);

	return $tabs;
}

add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
/**
 * Remove field from checkout
 * @param $fields
 * @return mixed
 */
function custom_override_checkout_fields($fields)
{
	//    unset($fields['billing']['billing_first_name']);
	//    unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	//    unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	// unset($fields['billing']['billing_city']);
	//    unset($fields['billing']['billing_postcode']);
	//    unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_state']);
	//    unset($fields['billing']['billing_phone']);
	// unset($fields['order']['order_comments']);
	//    unset($fields['billing']['billing_email']);
	unset($fields['account']['account_username']);
	unset($fields['account']['account_password']);
	unset($fields['account']['account_password-2']);
	return $fields;
}

// Удаление инлайн-скриптов из хедера
add_filter('storefront_customizer_css', '__return_false');
add_filter('storefront_customizer_woocommerce_css', '__return_false');
add_filter('storefront_gutenberg_block_editor_customizer_css', '__return_false');

add_action('wp_print_styles', static function () {
	wp_styles()->add_data('woocommerce-inline', 'after', '');
});

add_action('init', static function () {
	remove_action('wp_head', 'wc_gallery_noscript');
});
add_action('init', static function () {
	remove_action('wp_head', 'wc_gallery_noscript');
});
// Конец удаления инлайн-скриптов из хедера


remove_action('wp_head', 'feed_links_extra', 3); // убирает ссылки на rss категорий
remove_action('wp_head', 'feed_links', 2); // минус ссылки на основной rss и комментарии
remove_action('wp_head', 'rsd_link');  // сервис Really Simple Discovery
remove_action('wp_head', 'wlwmanifest_link'); // Windows Live Writer
remove_action('wp_head', 'wp_generator');  // скрыть версию wordpress

/**
 * Удаление json-api ссылок
 */
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('template_redirect', 'rest_output_link_header', 11, 0);

/**
 * Cкрываем разные линки при отображении постов блога (следующий, предыдущий, короткий url)
 */
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

/**
 * `Disable Emojis` Plugin Version: 1.7.2
 */
if ('Отключаем Emojis в WordPress') {

	/**
	 * Disable the emoji's
	 */
	function disable_emojis()
	{
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
		add_filter('wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2);
	}
	add_action('init', 'disable_emojis');

	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @param    array  $plugins
	 * @return   array             Difference betwen the two arrays
	 */
	function disable_emojis_tinymce($plugins)
	{
		if (is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		}

		return array();
	}

	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 * @return array                 Difference betwen the two arrays.
	 */
	function disable_emojis_remove_dns_prefetch($urls, $relation_type)
	{

		if ('dns-prefetch' == $relation_type) {

			// Strip out any URLs referencing the WordPress.org emoji location
			$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
			foreach ($urls as $key => $url) {
				if (strpos($url, $emoji_svg_url_bit) !== false) {
					unset($urls[$key]);
				}
			}
		}

		return $urls;
	}
}

/**
 * Удаляем стили для recentcomments из header'а
 */
function remove_recent_comments_style()
{
	global $wp_widget_factory;
	remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}
add_action('widgets_init', 'remove_recent_comments_style');

/**
 * Удаляем ссылку на xmlrpc.php из header'а
 */
remove_action('wp_head', 'wp_bootstrap_starter_pingback_header');

/**
 * Remove related products output
 */
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

add_filter('woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2);
function wc_remove_all_quantity_fields($return, $product)
{
	return (true);
}

add_filter('woocommerce_checkout_fields', 'override_billing_checkout_fields', 20, 1);
/**
 * Override fields from checkout
 * @param $fields
 * @return mixed
 */
function override_billing_checkout_fields($fields)
{
	$fields['billing']['billing_phone']['placeholder'] = 'Your phone number';
	$fields['billing']['billing_email']['placeholder'] = 'Your E-mail';
	$fields['billing']['billing_postcode']['placeholder'] = 'Your Postcode';
	$fields['billing']['billing_last_name']['placeholder'] = 'Your Last name';
	$fields['billing']['billing_first_name']['placeholder'] = 'Your name';
	$fields['billing']['billing_city']['placeholder'] = 'Your City';
	return $fields;
}

function remove_image_zoom_support()
{
	remove_theme_support('wc-product-gallery-zoom');
}
add_action('wp', 'remove_image_zoom_support', 100);

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);

function new_loop_shop_per_page($cols)
{
	// $cols contains the current number of products per page based on the value stored on Options –> Reading
	// Return the number of products you wanna show per page.
	$cols = 24;
	return $cols;
}

add_filter('comment_flood_filter', '__return_false');

add_filter('woocommerce_gallery_thumbnail_size', 'x_change_product_thumbnail_size', 99);
function x_change_product_thumbnail_size()
{
	return array(99999, 99999); //width & height of thumbnail
}

// Display variation's price even if min and max prices are the same
add_filter('woocommerce_available_variation', function ($value, $object = null, $variation = null) {
	if ($value['price_html'] == '') {
		$value['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';
	}
	return $value;
}, 10, 3);

add_action('wp_enqueue_scripts', function () {
	wp_dequeue_style('select2');
	wp_dequeue_script('select2');
	wp_dequeue_script('selectWoo');
}, 11);


/* Changes done by Gauri Kaushik */
function customchanges_scripts()
{
	$vsn = time();
	// enqueue style
	wp_enqueue_style('gk-custom', get_template_directory_uri() . '/assets/css/gk-custom.css', array(), $vsn);
}
add_action('wp_enqueue_scripts', 'customchanges_scripts');


// Reject account registration for emails ending with: "@baikcm.ru and @bheps.com"
/*add_action( 'woocommerce_register_post', 'reject_specific_emails_on_registration', 10, 3 );
function reject_specific_emails_on_registration( $username, $email, $validation_errors ) {
    if (( strpos($email, '@baikcm.ru') !== false ) || ( strpos($email, '@bheps.com') !== false )) {
        $validation_errors->add( 'registration-error-invalid-email',
        __( 'Your email address is not valid, check your input please.', 'woocommerce' ) );
    }
    return $validation_errors;
}*/

function prevent_email_domain($user_login, $user_email, $errors)
{
	if ((strpos($user_email, '@baikcm.ru') !== false) || (strpos($user_email, '@bheps.com') !== false)) {
		$errors->add('bad_email_domain', '<strong>ERROR</strong>: This email domain is not allowed.');
	}
}
add_action('register_post', 'prevent_email_domain', 10, 3);

// Reject checkout registration for emails ending with: "@baikcm.ru and @bheps.com"
add_action('woocommerce_after_checkout_validation', 'reject_specific_emails_checkout_validation', 10, 3);
function reject_specific_emails_checkout_validation($data, $errors)
{
	if (isset($data['billing_email']) && ((strpos($data['billing_email'], '@baikcm.ru') !== false) || (strpos($data['billing_email'], '@bheps.com') !== false))) {
		$errors->add('validation', __('Your email address is not valid. Please enter a valid email id.', 'woocommerce'));
	}
	return $validation_errors;
}


/*add_filter( 'woocommerce_get_price_html', 'wpa83367_price_html', 100, 2 );
function wpa83367_price_html( $price, $product ){
	echo $price;
    $price_array = explode(" - ",$price);
    echo"<pre>";
    print_r($price_array);
    return $price_array[0];
}*/

function show_template()
{
	if (isset($_GET['ab'])) {
		global $template;
		echo 'abtest';
		echo $template;
	}
}
add_action('wp_head', 'show_template');


add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    a[data-title="Video For Products"] {
      display:none;
    } 
    #woocommerce-embed-videos-to-product-image-gallery-update{
      display:none;
	}
  </style>';
}


/* Custom code for video autoplay in single product page */
add_action("wp_ajax_videoautoplay", "videoautoplay");
add_action("wp_ajax_nopriv_videoautoplay", "videoautoplay");

function videoautoplay() {
	global $wpdb;
	/*echo"<pre>";
	print_r($_POST);*/
	echo $image_url = $_POST['imgurl'];
	//$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));  
	$image_id = attachment_url_to_postid($image_url);
	$videolink_id_value = get_post_meta( $image_id,'videolink_id', true );
	if( !empty( $videolink_id_value ) ){
			$video_link_name = get_post_meta( $image_id, 'video_site', true );
	}
	$autoplay = get_option( 'embed_videos_autoplay' );
			$autoplay = ( empty( $autoplay ) ) ? 0 : 1;
			$rel = get_option( 'embed_videos_rel' );
			$rel = ( empty( $rel ) ) ? 0 : 1;
			$showinfo = get_option( 'embed_videos_showinfo' );
			$showinfo = ( empty( $showinfo ) ) ? 0 : 1;
			$disablekb = get_option( 'embed_videos_disablekb' );
			$disablekb = ( empty( $disablekb ) ) ? 0 : 1;
			$fs = get_option( 'embed_videos_fs' );
			$fs = ( empty( $fs ) ) ? 0 : 1;
			$controls = get_option( 'embed_videos_controls' );
			$controls = ( empty( $controls ) ) ? 0 : 1;
			$hd = get_option( 'embed_videos_hd' );
			$hd = ( empty( $hd ) ) ? 0 : 1;
	/*$parameters = "?autoplay=1&rel=".$rel."&fs=".$fs."&showinfo=".$showinfo."&disablekb=".$disablekb."&controls=".$controls."&hd=".$hd;*/
	$parameters = "?autoplay=1&rel=0&showinfo=0";
	echo $video_link = 'https://www.youtube.com/embed/'.$videolink_id_value.$parameters;
	die();
}


add_action( 'woocommerce_thankyou', 'tb_create_user_account_for_orders', 10, 1 );


/**
 * This is a callback for 'woocommerce_thankyou' action.
 * 
 * When a new order is created:
 * 
 * 1) create a new user (unless user is already logged in).
 * 2) assign this order to the freshly created user.
 * 3) send email to user with his credentials
 * 
 * 4) create autologin links for the product reviews
 * 
 * @param int $order_id
 * @return void
 */
function tb_create_user_account_for_orders( $order_id )  {
  
  
	if ( ! $order_id ) return;
  
	$user = wp_get_current_user();
	$order = wc_get_order( $order_id );
	
	// make sure that current visitor is not logged in, and order has no user attached
	if ( ( is_null($user) || ( is_object($user) && $user->ID === 0 ) ) && $order->get_status() == 'processing' && ! $order->get_customer_id()) {
  
	  $first_name = $order->get_billing_first_name();
	  $last_name  = $order->get_billing_last_name();
	  $user_email = $order->get_billing_email();
	  
	  $user_login = wc_create_new_customer_username( $user_email );
	  $user_password = wp_generate_password( 12, false );
		
	  $user_data = array(
		'user_login'  => $user_login,
		'user_email'  => $user_email,
		'first_name'  => $first_name,
		'last_name'   => $last_name,
		'display_name'  => $first_name . ' ' . $last_name,
		'user_pass'   => $user_password,
		'role' => 'customer'
	  );
  
	  $user_id = wp_insert_user( $user_data );
	  
	  if ( is_int( $user_id ) && $user_id > 0 ) {
		$order->set_customer_id( $user_id );
		$order->save();
		
		
		tb_create_user_autologin_links_for_product_review( $order, $user_id );
		
		new WC_Emails();
		
		if ( class_exists( 'TB_Email_New_Account_For_Order' ) ) {
		  $mailer = new TB_Email_New_Account_For_Order();
		  $mailer->trigger( $user_id, $user_password );
		}
		
	  }
	}
	elseif ( is_object($user) && $user->ID != 0 ) {
	  tb_create_user_autologin_links_for_product_review( $order, $user->ID );
	}
	
}

/**
 * 
 * We want user to be able to open a link sent to them in a email, and become automatically logged in
 * so they can leave a product review immediately.
 * 
 * To do so, when a new order is created, we 
 * 
 * 1) create autologin link for each order product
 * 2) save this link into order item meta
 * 3) place "Order Details" block into email template, and it will render order products and their metadata 
 * 
 * Email with "Order Details" is sent to the customer when the order is delivered.
 * 
 * @param WC_Order $order
 * @param int $user_id
 * @return void
 */
function tb_create_user_autologin_links_for_product_review( $order, $user_id ) {
  
  
	$order_meta = $order->get_meta_data();
	
	$added_autologin_links = false;
	
	foreach ( $order_meta as $meta ) {
	  if ( $meta->key == '_added_autologin_links' ) {
		$added_autologin_links = true;
		break;
	  }
	}
	
	if ( ! $added_autologin_links ) {
	  $items = $order->get_items();
  
	  foreach ( $items as $item_id => $item ) {
  
		if ( function_exists( 'pkg_autologin_generate_for_order_product' ) ) {
			$autologin_code = pkg_autologin_generate_for_order_product( $user_id ); 
		}
		else {
			$autologin_code = '';
		}
		
		
		$product = $item->get_product();
		$product_page_url = $product->get_permalink();
		
		if ( parse_url($product_page_url, PHP_URL_QUERY) ) { // check if url has "?param=query"

			$autologin_link = $product_page_url . '&autologin_code=' . $autologin_code;
		}
		else {
			$autologin_link = $product_page_url . '?autologin_code=' . $autologin_code;
		}
		
		wc_update_order_item_meta( $item_id, '_autologin_link', $autologin_link);
		wc_update_order_item_meta( $item_id, 'Leave Review', '<a href="' . $autologin_link . '">Write a review</a>');
	  }
  
	  $order->update_meta_data( '_added_autologin_links', 1 );
	  $order->save();
	}
	
}

  
/**
 * Filter for 'woocommerce_email_classes'
 * 
 * Adds our custom email classes for WooCommerce
 * 
 * @param array $emails
 * @return array
 */
function tb_add_custom_mailer_classes( $emails ) {
  
  // Send email to a customer when a new order is created
  if ( ! isset( $emails[ 'TB_Email_New_Account_For_Order' ] ) ) {
      $emails[ 'TB_Email_New_Account_For_Order' ] = include_once( 'emails/class-new-account-for-order.php' );
  }
  
  // Send email to a customer when their order is shipped
  if ( ! isset( $emails[ 'TB_Email_Customer_Sent_Order' ] ) ) {
      $emails[ 'TB_Email_Customer_Sent_Order' ] = include_once( 'emails/class-customer-sent-order.php' );
  }

  return $emails;
}

add_filter( 'woocommerce_email_classes', 'tb_add_custom_mailer_classes' );

/**
 * Additional shortcodes for "Email Template Customizer" 
 * 
 * this function is needed for our custom emails that are sent to customers ( see tb_add_custom_mailer_classes() )
 * 
 * @see plugins/email-template-customizer-for-woo/includes/utils.php for the filter signature
 */
add_filter( 'viwec_register_replace_shortcode', 'tb_additional_shortcodes_for_email_customizer', 10, 3 );

function tb_additional_shortcodes_for_email_customizer( $shortcodes, $object, $args ) {
  
  if ( $object && is_a( $object, 'WC_Order' ) ) {
    $tracking_number = get_post_meta( $object->get_id(), 'tracking_number_for_armenian_post', true );

    // note that each custom shortcode must be a separate array
    $shortcodes[] = array( '{tracking_number}' => $tracking_number );
    
    $user_id = $object->get_customer_id();
    $set_password_url = tb_generate_set_password_url( $user_id );
    
    $shortcodes[] = array( '{set_password_url}' => $set_password_url );
  }
  
  
  return $shortcodes;
}


function tb_generate_set_password_url( $user_id ) {

	$user = get_user_by( 'id', $user_id );
	
	if ( $user && is_a( $user, 'WC_User' ) ) {
	  $key = get_password_reset_key( $user );
	  if ( ! is_wp_error( $key ) ) {
		$action                 = 'newaccount';
		return wc_get_account_endpoint_url( 'lost-password' ) . "?action=$action&key=$key&login=" . rawurlencode( $user->user_login );
	  } else {
		// Something went wrong while getting the key for new password URL, send customer to the generic password reset.
		return wc_get_account_endpoint_url( 'lost-password' );
	  }
	}
	
	return '';
  }


  if ( ! function_exists( 'pkg_autologin_generate_for_order_product' ) ) {
	function pkg_autologin_generate_for_order_product( $user_id ) {
  
	  $new_code = pkg_autologin_generate_code();
	  update_user_meta($user_id, PKG_AUTOLOGIN_USER_META_KEY, $new_code);
	  return $new_code;
	}
  }
  
  
  if ( isset( $_GET['test_autologin']) ) {
  
	$user_id = intval($_GET['test_autologin']);
  
	if ( $user_id > 0 ) {
	  if ( function_exists( 'pkg_autologin_generate_for_order_product' ) && function_exists('pkg_autologin_generate_code') ) {
		$autologin_code = pkg_autologin_generate_for_order_product( $user_id ); 
  
		echo(" FOR $user_id autologin_code = $autologin_code " );
	  }
	  else {
		echo( 'NO FUN');
	  }
  
	  die();
	} 
  }
  
  