<?php
//* Code goes here
$childtheme_directory = str_replace('twentytwentyone', 'kahoycrafts', get_template_directory());

require($childtheme_directory .'/classes/kahoycrafts_product_categories_widget.php');
require($childtheme_directory .'/assets/aws.phar'); // AWS SDK

function kahoycrafts_load_widget() {

	register_widget( 'kahoycrafts_product_categories_widget' );

}

add_action( 'widgets_init', 'kahoycrafts_load_widget' );
add_action( 'wp_enqueue_scripts', 'kahoy_crafts_styles', 11, 0);

function kahoy_crafts_styles() {

	wp_dequeue_style( 'twenty-twenty-one-style' );

	// Use native Html5 players
	wp_deregister_style( 'wp-mediaelement' );
	wp_deregister_script( 'wp-mediaelement' );
	wp_deregister_script( 'mediaelement-core' );
	wp_deregister_script( 'mediaelement-migrate' );
	wp_deregister_script( 'mediaelement-vimeo' );

	if ( is_front_page() or 
		 is_page('contact') or 
		 is_page('owners-bio') or 
		 is_page('free-shipping-kit') or 
		 is_page('products-feed-generator') or
		 is_blog() or is_woocommerce() ) {
		//is_account_page() - Bug with layout

		// Load partial wp and wc gutenberg block styles for performance
		wp_deregister_style( 'wp-block-library' );
		wp_deregister_style( 'wc-blocks-style' );
		wp_dequeue_style( 'twentytwentyone-jetpack' );
		wp_dequeue_style( 'woocommerce-general' );
		wp_deregister_style( 'woocommerce-layout' );
		wp_deregister_style( 'woocommerce-smallscreen' );

		wp_register_style( 'purge-block-style', get_stylesheet_directory_uri() . '/assets/css/purge-block-style.min.css', [], wp_get_theme()->get( 'Version' ) );

		wp_enqueue_style( 'woocommerce-smallscreen', get_stylesheet_directory_uri() . '/assets/css/woocommerce-smallscreen.min.css', ['purge-block-style'], wp_get_theme()->get( 'Version' ), 'only screen and (max-width: 768px)' );

		wp_enqueue_style( 'kahoy-crafts-style', get_stylesheet_directory_uri() . '/assets/css/purge-style.min.css', [], wp_get_theme()->get( 'Version' ) );

	} else {
		// Use full styles for sensitive pages
		wp_enqueue_style( 'kahoy-crafts-style', get_stylesheet_directory_uri() . '/assets/css/style.min.css', [], wp_get_theme()->get( 'Version' ) );
	}

}

function is_blog () {
    return ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag()) && 'post' == get_post_type();
}

/**
 * Enqueue scripts and styles.
 * 
 * @return void
 */
function kahoy_crafts_scripts() {
	// Owl slider
	wp_enqueue_script(
		'owl-carousel',
		get_stylesheet_directory_uri() . '/assets/js/owl.carousel.min.js',
		['jquery'],
		wp_get_theme()->get( 'Version' ),
		true
	);
	wp_enqueue_script(
		'kahoycrafts',
		get_stylesheet_directory_uri() . '/assets/js/kahoycrafts.min.js',
		['jquery'],
		wp_get_theme()->get( 'Version' ),
		true
	);
	wp_register_script(
		'cookie-consent-banner',
		get_stylesheet_directory_uri() . '/assets/js/cookie-consent-banner.min.js',
		[],
		null,
		true
	);
	wp_enqueue_script(
		'cookie-consent',
		get_stylesheet_directory_uri() . '/assets/js/cookie-consent.min.js',
		['cookie-consent-banner'],
		wp_get_theme()->get( 'Version' ),
		true
	);
	wp_enqueue_script(
		'fontawesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js',
		[],
		wp_get_theme()->get( 'Version' ),
		true
	);

}

add_action( 'wp_enqueue_scripts', 'kahoy_crafts_scripts' );

/**
 * @param array $defaults The default array of items
 * @return array 	Modified array
 */
function woo_change_breadcrumb_home_test($defaults) {

	$defaults['home'] = 'Shop';
	return $defaults;

}

add_filter('woocommerce_breadcrumb_defaults', 'woo_change_breadcrumb_home_test');

/**
 * Defer or async scripts
 */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	
	if ( $handle == 'fontawesome' || 
		 $handle == 'wpforms-mailcheck' || 
		 $handle == 'wpforms-punycode' ) {
		
		return str_replace( ' src', ' async src', $tag );
	}

	if ( $handle == 'wpforms' || 
		 $handle == 'wpforms-validation' || 
		 $handle == 'owl-carousel' || 
		 $handle == 'kahoycrafts'  || 
		 $handle == 'cookie-consent' || 
		 $handle == 'cookie-consent-banner' ) {
		
		return str_replace( ' src', ' defer src', $tag );
	}

	// WooCommerce
	if ( is_front_page() && ( 
			$handle == 'wc-cart-fragments' || 
			$handle == 'wc-add-to-cart' || 
			$handle == 'woocommerce' || 
			$handle == 'js-cookie'
		) ) {

		return str_replace( ' src', ' async src', $tag );
	}

	return $tag;

	//return str_replace( ' src', ' async defer src', $tag ); // OR do both!

}, 10, 2 );

add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );

 /*Change the breadcrumb home link URL from / to /shop.
 @return string New URL for Home link item. / */
function woo_custom_breadrumb_home_url() {

    return '/shop/';

}

add_filter('wp_sitemaps_posts_query_args', 'kahoycrafts_disable_sitemap_specific_page', 10, 2);

/**
 * Exclude woocommerce pages from sitemap.xml
 *
 * @param array $args
 * @param string $post_type
 * @return array Array of args
 */
function kahoycrafts_disable_sitemap_specific_page($args, $post_type) {

	if ('page' !== $post_type) return $args;
	
	$args['post__not_in'] = isset($args['post__not_in']) ? $args['post__not_in'] : [];

	$args['post__not_in'][] = 70;
	$args['post__not_in'][] = 71;
	$args['post__not_in'][] = 72; // exclude page with ID = 72
	
	return $args;

}

/**
 * Conversion tracking for WC order
 */
function add_gtag_purchase_event( $order_id ) {

	if (! $order_id ) {
		return; // no order id
	}

	if ( $order = wc_get_order( $order_id ) ) {

		$script = "
<script>
gtag('event', 'conversion', {
	'send_to': 'AW-10818559065/WOdmCKLJo6UDENm42KYo',
	'value': {$order->get_total()},
	'currency': '{$order->get_currency()}',
	'transaction_id': '{$order_id}'
});
</script>
		";

		echo $script;

	}

}

/**
 * Action that fires during form entry processing after initial field validation.
 *
 * @link   https://wpforms.com/developers/wpforms_process/
 *
 * @param  array  $fields    Sanitized entry field. values/properties.
 * @param  array  $entry     Original $_POST global.
 * @param  array  $form_data Form data and settings.
 *
 */
function wpf_dev_process( $fields, $entry, $form_data ) {

	// Optional, you can limit to specific forms. Below, we restrict output to
	// form #5.
	if ( $form_data['settings']['form_class'] == 'newsletter-signup' ) {
		$name = sanitize_text_field( $fields[1]['value'] );
		$email = sanitize_email( $fields[2]['value'] );
	}
	else {
		return $fields;
	}

	$sdk = new Aws\Sdk([
		'region' => 'us-west-2',
		'version' => 'latest'
	]);

	$client = $sdk->createSesV2();

	try {

		$client->createContact([
			'AttributesData' => '{"Name": "'. $name .'"}',
			'ContactListName' => 'KahoyCraftsMailingList', // REQUIRED
			'EmailAddress' => $email, // REQUIRED
			'TopicPreferences' => [
				[
					'SubscriptionStatus' => 'OPT_IN', // REQUIRED
					'TopicName' => 'News', // REQUIRED
				],
				// ...
			],
			'UnsubscribeAll' => false,
		]);

		return $fields;

	}
	catch (Exception $e) {

		wpforms()->process->errors[$form_data[ 'id' ]]['2'] = __( 'Email address is malformed or already exists.' );
	}

	
}

add_action( 'wpforms_process', 'wpf_dev_process', 10, 3 );

function newsletter_checkout_field($checkout) {

	echo '<div class="newsletter-checkout-field">';

	$checked = $checkout->get_value( 'newsletter_optin' ) ? $checkout->get_value( 'newsletter_optin' ) : 1;

	woocommerce_form_field('newsletter_optin', 
		array(

			'type' => 'checkbox',

			'class' => array(

				'optin-field-class form-row-wide'

			),

			'required' => true,

			'label' => __('Keep me up to date on news and exclusive offers via email'),

		) ,

		$checked
	);

	echo '</div>';

}

add_action('woocommerce_after_order_notes', 'newsletter_checkout_field');

/**
 * Store newsletter optin choice
 */
function newsletter_checkout_field_update_order_meta($order_id) {

	if ( isset($_POST['newsletter_optin']) ) {
		update_post_meta( $order_id, 'newsletter_optin', sanitize_key($_POST['newsletter_optin']) );
	}

}

add_action('woocommerce_checkout_update_order_meta', 'newsletter_checkout_field_update_order_meta');

function video_shortcode_override( $markup, $attr, $content, $id ) {
	
	$default_types = wp_get_video_extensions();
	$type = '';

	foreach ( $default_types as $type ) {
		if ( isset( $attr[$type] ) ) {
			break;
		}
	}

	if ($type) {

		$markup .= '
<div class="wp-video">
	<video class="wp-video-shortcode" id="video-'. $id .'" width="'. $attr['width'] .'" height="'. $attr['height'] .'" preload="'. $attr['preload'] .'" controls="controls">
		<source type="video/'. $type .'" src="'. $attr[$type] .'">
		<a href="'. $attr[$type] .'">'. $attr[$type] .'</a>
	</video>
</div>';

	}

	return $markup;

}

// Override video tag
add_filter( 'wp_video_shortcode_override', 'video_shortcode_override', 10, 4);

//add_action( 'woocommerce_before_checkout_form', 'add_gtag_purchase_event' );
add_action( 'woocommerce_thankyou', 'add_gtag_purchase_event', 10, 4 );

/**
 * Disable unused jetpack CSS
 */
//add_filter( 'jetpack_sharing_counts', '__return_false', 99 );
add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );
