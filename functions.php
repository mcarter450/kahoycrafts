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
  wp_enqueue_style( 'twenty-twenty-one-style', get_template_directory_uri().'/style.css' );
  wp_enqueue_style( 'kahoy-crafts-style', get_stylesheet_directory_uri() . '/style.css', [], wp_get_theme()->get( 'Version' ) );
  wp_enqueue_style( 'owl-carousel', get_stylesheet_directory_uri() . '/assets/css/owl.carousel.min.css', []);
  wp_enqueue_style( 'owl-carousel-theme', get_stylesheet_directory_uri() . '/assets/css/owl.theme.default.min.css', []);
  wp_enqueue_style( 'owl-overrides', get_stylesheet_directory_uri() . '/assets/css/owl-overrides.css', []);
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

