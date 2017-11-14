<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * @package	   TGM-Plugin-Activation
 * @subpackage Plugins
 * @author	   Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author	   Gary Jones <gamajo@gamajo.com>
 * @copyright  Copyright (c) 2012, Thomas Griffin
 * @license	   http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
tokopress_require_file( THEME_DIR . '/inc/libs/tgm/class-tgm-plugin-activation.php' );

add_action( 'tgmpa_register', 'tokopress_register_required_plugins' );
function tokopress_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		/* Required Plugin */
		array(
			'name'		=> 'The Events Calendar',
			'slug'		=> 'the-events-calendar',
			'version' 	=> '4.1',
			'required'	=> true,
		),

		/* Recommended Plugin */
		array(
			'name'     	=> 'Visual Composer',
			'slug'     	=> 'js_composer',
			'source'   	=> 'http://api.tokopress.com/bundles/js_composer-v5.2.zip',
			'version' 	=> '5.2',
			'required' 	=> false,
		),

		array(
			'name'     	=> 'Eventica Visual Composer & Shortcodes',
			'slug'     	=> 'eventica-visual-composer-shortcode',
			'source'   	=> 'http://api.tokopress.com/bundles/eventica-visual-composer-shortcode-v'.THEME_ADDONS_VERSION.'.zip',
			'version' 	=> THEME_ADDONS_VERSION,
			'required' 	=> false,
		),

		array(
			'name'		=> 'WooCommerce',
			'slug'		=> 'woocommerce',
			'required'	=> false,
		),

		array(
			'name'     => 'Testimonials Plugin For Eventica',
			'slug'     => 'eventica-testimonials',
			'source'   => 'http://api.tokopress.com/bundles/eventica-testimonials_v1.0.zip',
			'required' => false
		),
		
		array(
			'name'		=> 'WordPress Importer',
			'slug'		=> 'wordpress-importer',
			'source'   	=> 'http://api.tokopress.com/bundles/wordpress-importer-v2.0.2.zip',
			'version' 	=> '2.0.2',
			'required' 	=> true,
		),

		array(
			'name'		=> 'Widget Importer Exporter',
			'slug'		=> 'widget-importer-exporter',
			'required'	=> true,
		),

		array(
			'name'		=> 'Customizer Export/Import',
			'slug'		=> 'customizer-export-import',
			'required'	=> true,
		),

		array(
			'name'		=> 'Thumbnail Upscale',
			'slug'		=> 'thumbnail-upscale',
			'required'	=> true,
		),

		array(
			'name'		=> 'Force Regenerate Thumbnails',
			'slug'		=> 'force-regenerate-thumbnails',
			'required'	=> false,
		),

		array(
			'name'		=> 'MailChimp for WordPress',
			'slug'		=> 'mailchimp-for-wp',
			'version' 	=> '3.0.0',
			'required'	=> false,
		),

	);

	if ( !of_get_option( 'tokopress_disable_ocdi' ) ) {
		$plugins[] = array(
			'name'		=> 'One Click Demo Import',
			'slug'		=> 'one-click-demo-import',
			'version' 	=> '2.4.0',
			'required'	=> true,
		);
	}

	$plugins[] = array(
		'name'		=> 'The Events Calendar - Frontend Submission',
		'slug'		=> 'tec-frontend-submission',
		'source'   	=> 'http://api.tokopress.com/bundles/tec-frontend-submission-v'.THEME_TECFS_VERSION.'.zip',
		'version' 	=> THEME_TECFS_VERSION,
		'required' 	=> false,
	);
	$plugins[] = array(
		'name'		=> 'CMB2 - Metabox',
		'slug'		=> 'cmb2',
		'version' 	=> '2.2.1',
		'required'	=> ( function_exists( 'xt_tec_frontend_submission_shortcode' ) ? true : false ),
	);

	$config = array(
		'id'           => 'toko-tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'toko-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );

}

if ( !of_get_option( 'tokopress_enable_vc_license' ) ) {
	/* Set Visual Composer as Theme part and disable Visual Composer Updater */
	add_action( 'vc_before_init', 'toko_vc_set_as_theme', 9 );
	function toko_vc_set_as_theme() {
		if ( function_exists( 'vc_set_as_theme' ) ) {
			vc_set_as_theme(true);
			vc_manager()->disableUpdater(true);
		}
	}
}

add_action( 'admin_head', 'toko_fix_notice_position' );
function toko_fix_notice_position() {
	echo '<style>#update-nag, .update-nag { display: block; float: none; }</style>';
}

add_filter( 'cei_export_option_keys', 'tokopress_cei_export_option_keys' );
function tokopress_cei_export_option_keys( $keys ) {
    $keys[] = 'eventica-wp';
    return $keys;
}

add_action( 'admin_menu', 'tokopress_disable_ocdi_page', 999 );
function tokopress_disable_ocdi_page() {
	if ( ( class_exists('OCDI_Plugin') ) && of_get_option( 'tokopress_disable_ocdi' ) ) {
		$page = remove_submenu_page( 'themes.php', 'pt-one-click-demo-import' );
	}
}

add_action( 'admin_notices', 'tokopress_notice_to_disable_ocdi' );
function tokopress_notice_to_disable_ocdi() {
	$screen = get_current_screen();
	if ( $screen->id !== 'appearance_page_pt-one-click-demo-import' ) {
		return;
	}
	echo '<div class="notice notice-error">';
	printf( __( '<p>If you have imported demo content or you do not need demo content, then it is better to disable One Click Demo Import feature.</p><p><strong><a href="%s">Disable One Click Demo Import NOW!</a></strong></p> ', 'tokopress' ), admin_url( 'customize.php?autofocus[control]='.THEME_NAME.'[tokopress_disable_ocdi]' ) );
	echo '</div>';
}

if ( !of_get_option( 'tokopress_disable_ocdi' ) ) {
	add_filter( 'pt-ocdi/import_files', 'tokopress_ocdi_import_files' );
}
function tokopress_ocdi_import_files() {
	$notices = array(
		__( 'Regenerate thumbnails using Force Regenerate Thumbnails plugin', 'tokopress' ),
		__( 'Go to Settings - Permalinks and click "Save Changes" button', 'tokopress' ),
	);
	$import_notice = __( 'After you import this demo, you will have to:', 'tokopress' ).'<ol><li>'.implode( '</li><li>', $notices ).'</li></ol>';
    return array(
        array(
            'import_file_name'           => 'Eventica Demo',
            'categories'                 => array( 'TokoPress' ),
            'import_file_url'            => 'http://import.tokopress.com/eventica/01_dummy_contents.xml',
            'import_widget_file_url'     => 'http://import.tokopress.com/eventica/02_dummy_widgets.wie',
            'import_customizer_file_url' => 'http://import.tokopress.com/eventica/03_dummy_settings.dat',
            'import_preview_image_url'   => '',
            'import_notice'              => $import_notice,
        ),
    );
}

add_action( 'pt-ocdi/before_content_import', 'tokopress_ocdi_before_content_import' );
function tokopress_ocdi_before_content_import( $selected_import ) {
  	$catalog = array(
		'width' 	=> '400',
		'height'	=> '200',
		'crop'		=> 1 
	);
	$single = array(
		'width' 	=> '850',
		'height'	=> '650',
		'crop'		=> 1
	);
	$thumbnail = array(
		'width' 	=> '150',
		'height'	=> '150',
		'crop'		=> 1
	);
	update_option( 'shop_catalog_image_size', $catalog ); 
	update_option( 'shop_single_image_size', $single ); 
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 
}

add_action( 'pt-ocdi/after_import', 'tokopress_ocdi_after_import' );
function tokopress_ocdi_after_import() {
    $header_menu = get_term_by( 'name', 'Header Menu', 'nav_menu' );
    $footer_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', 
    	array(
            'header_menu' => $header_menu->term_id,
            'footer_menu' => $footer_menu->term_id,
        )
    );

    $front_page_id = get_page_by_title( 'Home Events' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

    $shop_page_id = get_page_by_title( 'Shop' );
    $cart_page_id = get_page_by_title( 'Cart' );
    $checkout_page_id = get_page_by_title( 'Checkout' );
    $myaccount_page_id = get_page_by_title( 'My Account' );

    update_option( 'woocommerce_shop_page_id', $shop_page_id->ID );
    update_option( 'woocommerce_cart_page_id', $cart_page_id->ID );
    update_option( 'woocommerce_checkout_page_id', $checkout_page_id->ID );
    update_option( 'woocommerce_myaccount_page_id', $myaccount_page_id->ID );

    if ( class_exists('WC_Admin_Notices') ) {
	    WC_Admin_Notices::remove_notice( 'install' );
    }

}

add_filter('vc_load_default_templates','tokopress_load_vc_templates');
function tokopress_load_vc_templates( $args ) {
	$args2 = array ( 
		array(
			'name'=> '1. '.__('Eventica - Home','tokopress'),
			'image_path'=> THEME_URI . '/img/vc-homepage.png', 
			'content'=>'[vc_row full_width="stretch_row_content_no_spaces" css=".vc_custom_1425161144334{margin-bottom: 0px !important;}"][vc_column width="1/1"][eventica_events_slider per_page="4" container="yes"][/vc_column][/vc_row][vc_row css=".vc_custom_1425351223401{margin-bottom: 0px !important;}"][vc_column width="1/1"][eventica_events_search][/vc_column][/vc_row][vc_row css=".vc_custom_1425353909117{margin-bottom: 0px !important;padding-top: 30px !important;background-color: #cccccc !important;}" full_width="stretch_row"][vc_column width="1/1"][eventica_upcoming_events numbers="3" columns="3" columns_tablet="2" title_hide="no" title_color="#ffffff"][/vc_column][/vc_row][vc_row css=".vc_custom_1425351865698{margin-bottom: 0px !important;}"][vc_column width="1/3" css=".vc_custom_1425353892342{padding-top: 30px !important;}"][eventica_recent_posts numbers="3" columns="1" columns_tablet="2" title_hide="no"][/vc_column][vc_column width="2/3" css=".vc_custom_1425351892447{margin-bottom: 0px !important;}"][eventica_featured_event title_hide="no" columns="1"][eventica_subscribe_form][/vc_column][/vc_row][vc_row css=".vc_custom_1425351905476{margin-bottom: 0px !important;}"][vc_column width="1/1"][eventica_testimonials numbers="" title_hide="no" title_text=""][/vc_column][/vc_row][vc_row css=".vc_custom_1425351917990{margin-bottom: 0px !important;}"][vc_column width="1/1"][eventica_brand_sponsors title_hide="no"][/vc_column][/vc_row]', 
		),
	);
	return array_merge( $args, $args2 );
}
