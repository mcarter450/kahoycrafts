<?php
//* Code goes here
$childtheme_directory = str_replace('twentytwentyone', 'kahoycrafts', get_template_directory());

require($childtheme_directory .'/classes/kahoycrafts_product_categories_widget.php');

function kahoycrafts_load_widget() {
	register_widget( 'kahoycrafts_product_categories_widget' );
}

add_action( 'widgets_init', 'kahoycrafts_load_widget' );
add_action( 'wp_enqueue_scripts', 'kahoy_crafts_styles' );

function kahoy_crafts_styles() {
  wp_enqueue_style( 'kahoy-crafts-style', get_stylesheet_directory_uri() . '/assets/css/all.min.css', ['twenty-twenty-one-style'], wp_get_theme()->get( 'Version' ) );
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
		get_stylesheet_directory_uri() . '/assets/js/kahoycrafts.js',
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
		get_stylesheet_directory_uri() . '/fontawesome/js/all.min.js',
		[],
		wp_get_theme()->get( 'Version' ),
		true
	);
}

add_filter('woocommerce_breadcrumb_defaults', 'woo_change_breadcrumb_home_test');

/**
 * @param array $defaults The default array of items
 * @return array 	Modified array
 */
function woo_change_breadcrumb_home_test($defaults) {
	$defaults['home'] = 'Shop';
	return $defaults;
}

add_action( 'wp_enqueue_scripts', 'kahoy_crafts_scripts' );

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

//add_action( 'woocommerce_before_checkout_form', 'add_gtag_purchase_event' );
add_action( 'woocommerce_thankyou', 'add_gtag_purchase_event' );

/**
 * Disable unused jetpack CSS
 */
add_filter( 'jetpack_sharing_counts', '__return_false', 99 );
add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );
