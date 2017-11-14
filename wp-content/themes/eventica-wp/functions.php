<?php
/**
 * Functions file.
 *
 * @package Themes 
 * @author Tokopress
 *
 */

if ( ! isset( $content_width ) ) $content_width = 960;

define( 'THEME_NAME' , 'eventica-wp' );
define( 'THEME_VERSION', '1.14.0' );
define( 'THEME_ADDONS_VERSION', '1.13.0' );
define( 'THEME_TECFS_VERSION', '1.3.2' );

define( 'THEME_DIR' , get_template_directory() );
define( 'THEME_URI', get_template_directory_uri() );

/**
 * Flush rewrite rules.
 */
add_action( 'after_switch_theme', 'tokopress_flush_rewrite_rules' );
function tokopress_flush_rewrite_rules() {	
	flush_rewrite_rules();
}

/**
 * After Theme Setup
 */
add_action( 'after_setup_theme', 'tokopress_setup' );
function tokopress_setup() {

	// load Text Domain
	load_theme_textdomain( 'tokopress', THEME_DIR . '/languages' );

	// imgae size
	add_image_size( 'blog-thumbnail', 400, 200, true );

	// Register Nav Menu
	register_nav_menus( array(
			'header_menu'	=> __( 'Header Menu', 'tokopress' ),
			'footer_menu'	=> __( 'Footer Menu', 'tokopress' )
		) );

	// Theme Support
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );
	
	// Title Tag
	add_theme_support( 'title-tag' );

	// Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// Feature
	add_theme_support( 'automatic-feed-links' );

	// style editor
	add_editor_style( 'style-editor.css' );

	// Custom Header
	$custom_header = array(
		'flex-width'    		=> true,
		'width'         		=> 1200,
		'flex-height'   		=> true,
		'height'        		=> 210,
		'default-image' 		=> false,
		'uploads'       		=> true,
	);
	add_theme_support( 'custom-header', $custom_header );

	// Custom Background
	$custom_bg = array(
		'default-color' => '',
		'default-image' => '',
		'wp-head-callback' => 'tokopress_custom_background_cb',
	);
	add_theme_support( 'custom-background', $custom_bg );

	add_post_type_support( 'tribe_events', 'custom-header' );

	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	
}

add_action( 'widgets_init', 'tokopress_setup_sidebars' );
function tokopress_setup_sidebars() {
	register_sidebar( array(
		'id'            => 'primary',
		'name'          => _x( 'Primary', 'sidebar', 'tokopress' ),
		'description'   => __( 'The main (primary) widget area, most often used as a sidebar.', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
	register_sidebar( array(
		'id'            => 'event',
		'name'          => _x( 'Event', 'sidebar', 'tokopress' ),
		'description'   => __( 'The events page widget area', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
	register_sidebar( array(
		'id'            => 'shop',
		'name'          => _x( 'Shop', 'sidebar', 'tokopress' ),
		'description'   => __( 'The shop/product page widget area', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
	register_sidebar( array(
		'id'            => 'footer-one',
		'name'          => _x( 'Footer #1', 'sidebar', 'tokopress' ),
		'description'   => __( 'The first footer widget area', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
	register_sidebar( array(
		'id'            => 'footer-two',
		'name'          => _x( 'Footer #2', 'sidebar', 'tokopress' ),
		'description'   => __( 'The second footer widget area', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
	register_sidebar( array(
		'id'            => 'footer-three',
		'name'          => _x( 'Footer #3', 'sidebar', 'tokopress' ),
		'description'   => __( 'The third footer widget area', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
	register_sidebar( array(
		'id'            => 'footer-four',
		'name'          => _x( 'Footer #4', 'sidebar', 'tokopress' ),
		'description'   => __( 'The fourth footer widget area', 'tokopress' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
}

/**
 * Add main stylesheet file to <head> section.
 */
function tokopress_styles_theme() {

	wp_enqueue_style( 'style-vendors', trailingslashit( get_template_directory_uri() ) . 'style-vendors.css', array(), THEME_VERSION );

    /* If using a child theme, auto-load the parent theme style. */
    if ( is_child_theme() ) {
        wp_enqueue_style( 'style-parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(), THEME_VERSION );
    }

    /* Always load active theme's style.css. */
    wp_enqueue_style( 'style-theme', get_stylesheet_uri(), array(), THEME_VERSION );
	
	ob_start();
	do_action('tokopress_custom_styles');
	$custom_styles = ob_get_clean();
	
	if ( $custom_styles ) 
		wp_add_inline_style( 'style-theme', $custom_styles );
}
add_action( 'wp_enqueue_scripts', 'tokopress_styles_theme', 99 );

/**
 * Theme Scripts
 */
function tokopress_scripts() {

	// Superfish
	wp_enqueue_script( 'tokopress-superfish-js', THEME_URI . '/js/superfish.js', array( 'jquery' ), '', true );

	// Slidebars
	wp_enqueue_script( 'tokopress-slidebars-js', THEME_URI . '/js/slidebars.js', array( 'jquery' ), '', true );

	// Sticky
	if ( of_get_option( 'tokopress_sticky_header' ) ) {
		wp_enqueue_script( 'tokopress-sticky-js', THEME_URI . '/js/jquery.sticky.js', array( 'jquery' ), '', true );
	}

	// Magnific Popup
	wp_register_script( 'tokopress-magnific-popup', THEME_URI . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), '', true );
	if ( is_singular('tribe_events') ) {
		wp_enqueue_script( 'tokopress-magnific-popup' );
	}

	// OWL Carousel
	wp_register_script( 'tokopress-owl-js', THEME_URI . '/js/owl.carousel.min.js', array( 'jquery' ), '2.0', true );
	if( class_exists( 'woocommerce' ) && is_product() ) {
		wp_enqueue_script( 'tokopress-owl-js' );
	}
	if( is_page_template( 'page_home_event.php' ) ) {
		wp_enqueue_script( 'tokopress-owl-js' );
	}

	// Comments Reply
	if ( is_singular() ) {
		wp_enqueue_script( "comment-reply" );
	}

	// Gmaps
	if( is_page_template( 'page_contact.php' ) ) {
		if( $apikey = of_get_option( 'tokopress_contact_apikey' ) ) {
	  		wp_enqueue_script( 'tokopress-maps-js', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key='.$apikey, array( 'jquery' ), '3', true );
		}
		else {
	  		wp_enqueue_script( 'tokopress-maps-js', 'https://maps.googleapis.com/maps/api/js?v=3.exp', array( 'jquery' ), '3', true );
		}
  		wp_enqueue_script( 'tokopress-gmaps-js', trailingslashit( THEME_URI ) . 'js/gmaps.js', array( 'jquery' ), '0.4.12', true );
  	}

}
add_action( 'wp_enqueue_scripts', 'tokopress_scripts' );

/* This is needed to make it fully compatible with Visual Composer */
function tokopress_scripts_late() {
	// Theme script
	wp_enqueue_script( 'tokopress-js', THEME_URI . '/js/eventica.js', array( 'jquery' ), THEME_VERSION, true );
}
add_action( 'wp_footer', 'tokopress_scripts_late', 11 );

function tokopress_include_file( $file ) {
	include_once( $file );
}
function tokopress_require_file( $file ) {
	require_once( $file );
}

include_once( trailingslashit( THEME_DIR ) . 'inc/libs/option-framework/options-framework.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/libs/tokopress-customize.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/libs/tokopress-general.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/libs/tokopress-post-meta.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/libs/tokopress-breadcrumb.php' );

include_once( trailingslashit( THEME_DIR ) . 'inc/theme/functions.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/frontend.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/designs2.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/options2.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/options.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/plugins.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/widgets.php' );
include_once( trailingslashit( THEME_DIR ) . 'inc/theme/update.php' );

if( class_exists( 'Tribe__Events__Main' ) ) {
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/functions.php' );
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/gallery.php' );
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/frontend.php' );
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/options2.php' );
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/options.php' );
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/designs2.php' );
	include_once( trailingslashit( THEME_DIR ) . 'inc/events/metabox.php' );
	if ( class_exists( 'Tribe__Events__Tickets__Woo__Main' ) ) {
		include_once( trailingslashit( THEME_DIR ) . 'inc/events/wootickets.php' );
	}
}

if( class_exists( 'woocommerce' ) ) {
	include_once( trailingslashit( THEME_DIR  ) . 'inc/woocommerce/frontend.php' );
	include_once( trailingslashit( THEME_DIR  ) . 'inc/woocommerce/options2.php' );
	include_once( trailingslashit( THEME_DIR  ) . 'inc/woocommerce/options.php' );
	include_once( trailingslashit( THEME_DIR  ) . 'inc/woocommerce/designs2.php' );
	include_once( trailingslashit( THEME_DIR  ) . 'inc/woocommerce/functions.php' );
}
