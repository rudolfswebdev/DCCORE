<?php
/**
 * Eventica Child Theme functions and definitions.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'after_setup_theme', 'tokopress_load_childtheme_languages', 5 );
function tokopress_load_childtheme_languages() {
	/* this theme supports localization */
	load_child_theme_textdomain( 'tokopress', get_stylesheet_directory() . '/languages' );
		
}

/* Please add your custom functions code below this line. */


