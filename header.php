<?php
/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> <?php twentytwentyone_the_html_classes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
	<?php if ( is_single() ): ?>
		<meta property="og:url"                content="<?php echo get_the_permalink(); ?>" />
		<meta property="og:type"               content="article" />
		<meta property="og:title"              content="<?php echo strip_tags( get_the_title() ); ?>" />
		<meta property="og:description"        content="<?php echo strip_tags( substr(get_the_excerpt(), 0, 255) ); ?>..." />
		<?php if ( $og_thumbnail = get_the_post_thumbnail_url() ): ?>
			<meta property="og:image"              content="<?php echo $og_thumbnail; ?>" />
		<?php endif; ?>
	<?php endif; ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'twentytwentyone' ); ?></a>

	<?php get_template_part( 'template-parts/header/site-header' ); ?>

	<div id="content" class="site-content">
		<div id="primary" class="content-area">
			<main id="main" class="site-main">
