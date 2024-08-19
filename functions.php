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
  
  
add_action("woocommerce_checkout_after_order_review", "display_info_block_for_checkout");


function display_info_block_for_checkout() {
  ?>

    <div class="checkout checkout-info-block">

        <div class="questions-block">

            <div class="questions-block-img" style="background-image: url('/wp-content/uploads/2022/11/tanya.jpg')">
            </div>

            <div class="questions-block-info">
                <h3>Have any questions about payment or ear cuffs?</h3>
                <p>Contact me on any messenger<br/>Tanya - CEO TannyBunny</p>
                <div class="questions-block-info-links">
                    <a href="https://www.messenger.com/t/101656739201598/" target="_blank">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M27 0H5C2.23858 0 0 2.23858 0 5V27C0 29.7614 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 29.7614 0 27 0Z" fill="#1877F2"/>
                            <path d="M24 16C24 11.6 20.4 8 16 8C11.6 8 8 11.6 8 16C8 20 10.9 23.3 14.7 23.9V18.3H12.7V16H14.7V14.2C14.7 12.2 15.9 11.1 17.7 11.1C18.6 11.1 19.5 11.3 19.5 11.3V13.3H18.5C17.5 13.3 17.2 13.9 17.2 14.5V16H19.4L19 18.3H17.1V24C21.1 23.4 24 20 24 16Z" fill="white"/>
                        </svg>
                    </a>
                    <a href="https://wa.me/37498424830" target="_blank">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M27 0H5C2.23858 0 0 2.23858 0 5V27C0 29.7614 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 29.7614 0 27 0Z" fill="#25D366"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.6 10.3C20.1 8.8 18.1 8 16 8C11.6 8 8 11.6 8 16C8 17.4 8.40001 18.8 9.10001 20L8 24L12.2 22.9C13.4 23.5 14.7 23.9 16 23.9C20.4 23.9 24 20.3 24 15.9C24 13.8 23.1 11.8 21.6 10.3ZM16 22.6C14.8 22.6 13.6 22.3 12.6 21.7L12.4 21.6L9.89999 22.3L10.6 19.9L10.4 19.6C9.69999 18.5 9.39999 17.3 9.39999 16.1C9.39999 12.5 12.4 9.5 16 9.5C17.8 9.5 19.4 10.2 20.7 11.4C22 12.7 22.6 14.3 22.6 16.1C22.6 19.6 19.7 22.6 16 22.6ZM19.6 17.6C19.4 17.5 18.4 17 18.2 17C18 16.9 17.9 16.9 17.8 17.1C17.7 17.3 17.3 17.7 17.2 17.9C17.1 18 17 18 16.8 18C16.6 17.9 16 17.7 15.2 17C14.6 16.5 14.2 15.8 14.1 15.6C14 15.4 14.1 15.3 14.2 15.2C14.3 15.1 14.4 15 14.5 14.9C14.6 14.8 14.6 14.7 14.7 14.6C14.8 14.5 14.7 14.4 14.7 14.3C14.7 14.2 14.3 13.2 14.1 12.8C14 12.5 13.8 12.5 13.7 12.5C13.6 12.5 13.5 12.5 13.3 12.5C13.2 12.5 13 12.5 12.8 12.7C12.6 12.9 12.1 13.4 12.1 14.4C12.1 15.4 12.8 16.3 12.9 16.5C13 16.6 14.3 18.7 16.3 19.5C18 20.2 18.3 20 18.7 20C19.1 20 19.9 19.5 20 19.1C20.2 18.6 20.2 18.2 20.1 18.2C20 17.7 19.8 17.7 19.6 17.6Z" fill="white"/>
                        </svg>
                    </a>
                    <a href="https://ig.me/m/tannybunny.jewelry" target="_blank">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M27 0H5C2.23858 0 0 2.23858 0 5V27C0 29.7614 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 29.7614 0 27 0Z" fill="#F00073"/>
                            <path d="M16 9.2C18.2 9.2 18.5 9.2 19.4 9.2C20.2 9.2 20.6 9.4 20.9 9.5C21.3 9.7 21.6 9.8 21.9 10.1C22.2 10.4 22.4 10.7 22.5 11.1C22.6 11.4 22.7 11.8 22.8 12.6C22.8 13.5 22.8 13.7 22.8 16C22.8 18.3 22.8 18.5 22.8 19.4C22.8 20.2 22.6 20.6 22.5 20.9C22.3 21.3 22.2 21.6 21.9 21.9C21.6 22.2 21.3 22.4 20.9 22.5C20.6 22.6 20.2 22.7 19.4 22.8C18.5 22.8 18.3 22.8 16 22.8C13.7 22.8 13.5 22.8 12.6 22.8C11.8 22.8 11.4 22.6 11.1 22.5C10.7 22.3 10.4 22.2 10.1 21.9C9.8 21.6 9.6 21.3 9.5 20.9C9.4 20.6 9.3 20.2 9.2 19.4C9.2 18.5 9.2 18.3 9.2 16C9.2 13.7 9.2 13.5 9.2 12.6C9.2 11.8 9.4 11.4 9.5 11.1C9.7 10.7 9.8 10.4 10.1 10.1C10.4 9.8 10.7 9.6 11.1 9.5C11.4 9.4 11.8 9.3 12.6 9.2C13.5 9.2 13.8 9.2 16 9.2ZM16 7.7C13.7 7.7 13.5 7.7 12.6 7.7C11.7 7.7 11.1 7.9 10.6 8.1C10.1 8.3 9.6 8.6 9.1 9.1C8.6 9.6 8.4 10 8.1 10.6C7.9 11.1 7.8 11.7 7.7 12.6C7.7 13.5 7.7 13.8 7.7 16C7.7 18.3 7.7 18.5 7.7 19.4C7.7 20.3 7.9 20.9 8.1 21.4C8.3 21.9 8.6 22.4 9.1 22.9C9.6 23.4 10 23.6 10.6 23.9C11.1 24.1 11.7 24.2 12.6 24.3C13.5 24.3 13.8 24.3 16 24.3C18.2 24.3 18.5 24.3 19.4 24.3C20.3 24.3 20.9 24.1 21.4 23.9C21.9 23.7 22.4 23.4 22.9 22.9C23.4 22.4 23.6 22 23.9 21.4C24.1 20.9 24.2 20.3 24.3 19.4C24.3 18.5 24.3 18.2 24.3 16C24.3 13.8 24.3 13.5 24.3 12.6C24.3 11.7 24.1 11.1 23.9 10.6C23.7 10.1 23.4 9.6 22.9 9.1C22.4 8.6 22 8.4 21.4 8.1C20.9 7.9 20.3 7.8 19.4 7.7C18.5 7.7 18.3 7.7 16 7.7Z" fill="white"/>
                            <path d="M16 11.7C13.6 11.7 11.7 13.6 11.7 16C11.7 18.4 13.6 20.3 16 20.3C18.4 20.3 20.3 18.4 20.3 16C20.3 13.6 18.4 11.7 16 11.7ZM16 18.8C14.5 18.8 13.2 17.6 13.2 16C13.2 14.5 14.4 13.2 16 13.2C17.5 13.2 18.8 14.4 18.8 16C18.8 17.5 17.5 18.8 16 18.8Z" fill="white"/>
                            <path d="M20.4 12.6C20.9523 12.6 21.4 12.1523 21.4 11.6C21.4 11.0477 20.9523 10.6 20.4 10.6C19.8477 10.6 19.4 11.0477 19.4 11.6C19.4 12.1523 19.8477 12.6 20.4 12.6Z" fill="white"/>
                        </svg>

                    </a>
                </div>
            </div>

        </div>

        <hr/>

    </div>
  <?php
}