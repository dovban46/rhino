<?php
/**
 * RHINO functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package RHINO
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */

//start custom code

//connect styles and scripts
function rhino_enqueue_styles_and_scripts() {
	wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0', 'all' );
	wp_enqueue_style(
		'rhino-sometype-mono',
		'https://fonts.googleapis.com/css2?family=Sometype+Mono:wght@400;500;600;700&display=swap',
		array(),
		null,
		'all'
	);
    wp_enqueue_style( 'rhino-main-min-css', get_template_directory_uri() . '/dist/main.min.css', array( 'swiper-css', 'rhino-sometype-mono' ), null, 'all' );
    wp_enqueue_style( 'rhino-main-css', get_template_directory_uri() . '/dist/main.css', array( 'swiper-css', 'rhino-sometype-mono' ), null, 'all' );

	wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array( 'jquery' ), null, true );

	wp_enqueue_script(
		'rhino-main-js',
		get_template_directory_uri() . '/dist/main.min.js',
		array(),
		_S_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rhino_enqueue_styles_and_scripts' );

// add acf content
require get_template_directory() . '/inc/theme-acf.php';
require get_template_directory() . '/inc/theme-header.php';
require get_template_directory() . '/inc/theme-footer.php';
if ( ! function_exists( 'mytheme_register_nav_menu' ) ) {

	function mytheme_register_nav_menu() {
		register_nav_menus(
			array(
				'Main-menu'        => __( 'Primary Menu', 'rhino' ),
				'Main-footer-menu' => __( 'Footer Menu', 'rhino' ),
			)
		);
	}
	add_action( 'after_setup_theme', 'mytheme_register_nav_menu', 0 );
}

//add svg file
function allow_svg_uploads( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_uploads' );

/**
 * Add unique body class per page/post for page-specific styles.
 *
 * @param array $classes Body classes.
 * @return array
 */
function rhino_body_class( $classes ) {
	if ( ! is_singular() ) {
		return $classes;
	}

	$post = get_queried_object();

	if ( $post instanceof WP_Post && ! empty( $post->post_name ) ) {
		$classes[] = 'rhino-page-' . sanitize_html_class( $post->post_name );
		$classes[] = 'rhino-' . sanitize_html_class( $post->post_type ) . '-' . sanitize_html_class( $post->post_name );
	}

	return $classes;
}
add_filter( 'body_class', 'rhino_body_class' );