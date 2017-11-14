<?php
/**
 * Theme Options Settings
 *
 * @package Theme Options
 * @author Tokopress
 *
 */

add_action( 'admin_notices', 'tokopress_notice_from_optionframework_to_customizer' );
function tokopress_notice_from_optionframework_to_customizer() {
	$screen = get_current_screen();
	if ( $screen->id !== 'appearance_page_options-framework' ) {
		return;
	}
	echo '<div class="notice notice-warning">';
	printf( __( '<h3>We are moving to WordPress Customizer</h3><p>These theme options are available on WordPress Customizer.</p><p>We recommend you to use WordPress Customizer because it is better and has <strong>live preview</strong> feature.</p><p>We keep this theme options page for backward compatiblity.</p><p><strong><a href="%s">Go To WordPress Customizer NOW!</a></strong></p> ', 'tokopress' ), admin_url( 'customize.php' ) );
	echo '</div>';
}

/*
 * Load Option Framework
 */
define( 'OPTIONS_FRAMEWORK_DIRECTORY', THEME_URI . '/inc/libs/option-framework/' );

/**
 * Set Option Name For Option Framework
 */
function optionsframework_option_name() {
	$optionsframework_settings = get_option( 'optionsframework' );
	if ( defined( 'THEME_NAME' ) ) {
		$optionsframework_settings['id'] = THEME_NAME;
	}
	else {
		$themename = wp_get_theme();
		$themename = preg_replace("/\W/", "_", strtolower($themename) );
		$optionsframework_settings['id'] = $themename;
	}
	update_option( 'optionsframework', $optionsframework_settings );

    $defaults = optionsframework_defaults();
    $options = get_option( $optionsframework_settings['id'] );
    if ( empty( $options ) ) {
		add_option( $optionsframework_settings['id'], $defaults, '', 'yes' );
    }
}

/**
 * Get Default Options For Option Framework
 */
function optionsframework_defaults() {
    $options = null;
    $location = apply_filters( 'options_framework_location', array(get_template_directory() . '/inc/theme/options.php') );
    if ( $optionsfile = locate_template( $location ) ) {
        $maybe_options = tokopress_require_file( $optionsfile );
        if ( is_array( $maybe_options ) ) {
			$options = $maybe_options;
        } else if ( function_exists( 'optionsframework_options' ) ) {
			$options = optionsframework_options();
		}
    }
    $options = apply_filters( 'of_options', $options );
    $defaults = array();
    foreach ($options as $key => $value) {
    	if( isset($value['id']) && isset($value['std']) ) {
    		if ( $value['type'] == 'checkbox' && $value['std'] == '0' ) {
	    		$defaults[$value['id']] = false;
    		}
    		else {
	    		$defaults[$value['id']] = $value['std'];
    		}
    	}
    }
    return $defaults;
}

/* 
 * Override a default filter for 'textarea' sanitization and $allowedposttags + embed and script.
 */
add_action('admin_init','optionscheck_change_santiziation', 100);
function optionscheck_change_santiziation() {
    remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
    add_filter( 'of_sanitize_textarea', 'tokopress_of_sanitize_textarea' );
}
function tokopress_of_sanitize_textarea($input) {
    global $allowedposttags;
    $custom_allowedtags["embed"] = array(
      "src" => array(),
      "type" => array(),
      "allowfullscreen" => array(),
      "allowscriptaccess" => array(),
      "height" => array(),
          "width" => array()
      );
      $custom_allowedtags["script"] = array();
 
      $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
      $output = wp_kses( $input, $custom_allowedtags);
    return $output;
}

/**
 * Load Custom Style For Option Framework
 */
function tokopress_style_option_framework() {
	wp_enqueue_style( 'style-option-framework', OPTIONS_FRAMEWORK_DIRECTORY . '/css/option-framework.css' );
}
add_action( 'optionsframework_custom_scripts', 'tokopress_style_option_framework' );

/**
 * Header Settings
 */
function tokopress_header_settings( $options ) {

	$options[] = array(
		'name' 	=> __( 'Header', 'tokopress' ),
		'type' 	=> 'heading'
	);

		if ( function_exists( 'wp_site_icon' ) ) {
			$options[] = array(
				'name' 	=> __( 'Favicon', 'tokopress' ),
				'type' 	=> 'info',
				'desc' 	=> sprintf( __( 'Go to <a href="%s">Appearance - Customize - Site Identity</a> to customize Favicon (Site Icon).', 'tokopress' ), admin_url( 'customize.php?autofocus[control]=site_icon' ) ),
			);
		}
		else {
			$options[] = array(
				'name' 	=> __( 'Favicon', 'tokopress' ),
				'id' 	=> 'tokopress_favicon',
				'type' 	=> 'upload',
			);
		}

		$options[] = array(
			'name' 	=> __( 'Sticky Header', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' 	=> __( 'Sticky Header', 'tokopress' ),
				'desc' 	=> __( 'ENABLE sticky header', 'tokopress' ),
				'id' 	=> 'tokopress_sticky_header',
				'std' 	=> '',
				'type' 	=> 'checkbox'
			);

		$options[] = array(
			'name' 	=> __( 'Header Section', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' 	=> __( 'Site Logo', 'tokopress' ),
				'id' 	=> 'tokopress_site_logo',
				'type' 	=> 'upload'
			);

			$options[] = array(
				'name' 	=> __( 'Minicart Icon', 'tokopress' ),
				'desc' 	=> __( 'ENABLE minicart icon on header menu', 'tokopress' ),
				'id' 	=> 'tokopress_minicart_header',
				'std' 	=> '',
				'type' 	=> 'checkbox'
			);

		$options[] = array(
			'name' 	=> __( 'Page Title Section', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' 	=> __( 'Page Title', 'tokopress' ),
				'desc' 	=> __( 'DISABLE page title section globally.', 'tokopress' ),
				'id' 	=> 'tokopress_page_title_disable',
				'std' 	=> '',
				'type' 	=> 'checkbox'
			);

		$options[] = array(
			'name' 	=> __( 'Header Scripts', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' 	=> __( 'Header Script', 'tokopress' ),
				'desc' 	=> __( 'You can put any script here, for example your Google Analytics scripts.', 'tokopress' ),
				'id' 	=> 'tokopress_header_script',
				'std' 	=> '',
				'type' 	=> 'textarea'
			);

	return $options;
}
add_filter( 'of_options', 'tokopress_header_settings' );

/**
 * Footer Settings
 */
function tokopress_footer_settings( $options ) {
	$theme_name = wp_get_theme();

	$options[] = array(
		'name' 	=> __( 'Footer', 'tokopress' ),
		'type' 	=> 'heading'
	);

		$options[] = array(
			'name' 	=> __( 'Footer Section', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' 	=> __( 'Footer Widget', 'tokopress' ),
				'desc' 	=> __( 'DISABLE footer widget', 'tokopress' ),
				'id' 	=> 'tokopresss_disable_footer_widget',
				'std' 	=> '',
				'type' 	=> 'checkbox'
			);

			$options[] = array(
				'name' 	=> __( 'Footer Buttom', 'tokopress' ),
				'desc' 	=> __( 'DISABLE footer buttom', 'tokopress' ),
				'id' 	=> 'tokopresss_disable_footer_buttom',
				'std' 	=> '',
				'type' 	=> 'checkbox'
			);

			$options[] = array(
				'name' 	=> __( 'Footer Credit Text', 'tokopress' ),
				'desc' 	=> '',
				'id' 	=> 'tokopress_footer_text',
				'type' 	=> 'textarea'
			);

		$options[] = array(
			'name' 	=> __( 'Social Icon', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' => __( 'DISABLE Social icons', 'tokopress' ),
				'desc' => __( 'DISABLE social icons in footer', 'tokopress' ),
				'id' => 'tokopress_hide_social',
				'type' => 'checkbox'
			);

			$socials = array(
				'' 				=> '&nbsp;',
				'rss' 			=> 'RSS Feed',
				'envelope-o' 	=> 'E-mail',
				'twitter' 		=> 'Twitter',
				'facebook' 		=> 'Facebook',
				'google-plus' 	=> 'gPlus',
				'youtube' 		=> 'Youtube',
				'flickr' 		=> 'Flickr',
				'linkedin' 		=> 'Linkedin',
				'pinterest' 	=> 'Pinterest',
				'dribbble' 		=> 'Dribbble',
				'github' 		=> 'Github',
				'lastfm' 		=> 'LastFm',
				'vimeo-square' 	=> 'Vimeo',
				'tumblr' 		=> 'Tumblr',
				'instagram' 	=> 'Instagram',
				'soundcloud' 	=> 'Sound Cloud',
				'behance' 		=> 'Behance',
				'deviantart' 	=> 'Daviant Art'
			);

			for( $is=1;$is<=5;$is++ ) {
				$options[] = array(
					'name' => sprintf( __( 'Social #%s', 'tokopress' ), $is ),
					'desc' => '',
					'id' => 'tokopress_social_' . $is,
					'type' => 'select',
					'options' => $socials
				);
				$options[] = array(
					'name' => sprintf( __( 'Social URL #%s', 'tokopress' ), $is ),
					'desc' => '',
					'id' => 'tokopress_social_' . $is . '_url',
					'type' => 'text'
				);
			}

		$options[] = array(
			'name' 	=> __( 'Footer Scripts', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name' 	=> __( 'Footer Script', 'tokopress' ),
				'desc' 	=> __( 'You can put any script here, for example your Google Analytics scripts.', 'tokopress' ),
				'id' 	=> 'tokopress_footer_script',
				'std' 	=> '',
				'type' 	=> 'textarea'
			);

	return $options;
}
add_filter( 'of_options', 'tokopress_footer_settings' );

/**
 * Homepage Settings
 */
function tokopress_homepage_settings( $options ) {
	$options[] = array(
		'name' 	=> __( 'Home Template', 'tokopress' ),
		'type' 	=> 'heading'
	);

	/**
	 * Events Options
	 */
	if( class_exists( 'Tribe__Events__Main' ) ) {

		$options[] = array(
			'name' 	=> __( 'Events Slider', 'tokopress' ),
			'type' 	=> 'info'
		);

			$options[] = array(
				'name'	=> __( 'Events Slider', 'tokopress' ),
				'desc'	=> __( 'DISABLE Events Slider', 'tokopress' ),
				'id' 	=> 'tokopress_home_slider_disable',
				'type' 	=> 'checkbox'
			);

			$options[] = array(
				'name'=> __( 'Event IDs', 'tokopress' ),
				'desc'=> __( 'Separated by comma. Use this is you want to show some specific events for slider. "Number of Events" and "Event Category" options will be ignored.', 'tokopress' ),
				'id'=> 'tokopress_home_slider_ids',
				'type'=> 'text'
			);

			$options[] = array(
				'name'=> __( 'Number of Events', 'tokopress' ),
				'desc'=> __( 'Use this is you want to show upcoming events for slider. Leave "Event IDs" option empty.', 'tokopress' ),
				'id'=> 'tokopress_home_slider_numbers',
				'type'=> 'text',
				'std'=> '4'
			);

			$options[] = array(
				'name'=> __( 'Event Category Name', 'tokopress' ),
				'desc'=> __( 'Put event category name here if you want to retrieve upcoming events from this category only. Leave "Event IDs" option empty.', 'tokopress' ),
				'id'=> 'tokopress_home_slider_category',
				'type'=> 'text',
				'std'=> ''
			);

			$options[] = array(
				'name'=> __( 'Button Text', 'tokopress' ),
				'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Detail', 'tokopress' ),
				'id'=> 'tokopress_home_slide_button',
				'type'=> 'text',
				'std'=> ''
			);

		$options[] = array(
			'name' => __( 'Search Form', 'tokopress' ),
			'type' => 'info'
		);

			$options[] = array(
				'name'	=> __( 'Search Form', 'tokopress' ),
				'desc'	=> __( 'DISABLE Search Form', 'tokopress' ),
				'id' 	=> 'tokopress_home_search_disable',
				'type' 	=> 'checkbox'
			);

			$options[] = array(
				'name'=> __( 'Placeholder Text', 'tokopress' ),
				'desc'=> __( 'Default:', 'tokopress' ).' '._x( 'Search Event &hellip;', 'placeholder', 'tokopress' ),
				'id'=> 'tokopress_home_search_text',
				'type'=> 'text',
				'std'=> ''
			);

		$options[] = array(
			'name' => __( 'Upcoming Events', 'tokopress' ),
			'type' => 'info'
		);

			$options[] = array(
				'name'	=> __( 'Upcoming Events', 'tokopress' ),
				'desc'	=> __( 'DISABLE Upcoming Events', 'tokopress' ),
				'id' 	=> 'tokopress_home_upcoming_event_disable',
				'type' 	=> 'checkbox'
			);

			$options[] = array(
				'name'=> __( 'Number of Events', 'tokopress' ),
				'desc'=> __( 'Number of upcoming events to be displayed.', 'tokopress' ),
				'id'=> 'tokopress_home_upcoming_event_numbers',
				'type'=> 'text',
				'std'=> '3'
			);

			$options[] = array(
				'name'=> __( 'Event Category Name', 'tokopress' ),
				'desc'=> __( 'Put event category name here if you want to retrieve upcoming events from this category only.', 'tokopress' ),
				'id'=> 'tokopress_home_upcoming_event_category',
				'type'=> 'text',
				'std'=> ''
			);

			$options[] = array(
				'name'=> __( 'Exclude', 'tokopress' ),
				'desc'=> __( 'Insert Event IDs (separated by comma) to exclude.', 'tokopress' ),
				'id'=> 'tokopress_home_upcoming_event_exclude',
				'type'=> 'text'
			);

			$options[] = array(
				'name'=> __( 'Section Title', 'tokopress' ),
				'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Upcoming Events', 'tokopress' ),
				'id'=> 'tokopress_home_upcoming_event',
				'type'=> 'text',
				'std'=> ''
			);

			$options[] = array(
				'name'=> __( 'Link Text', 'tokopress' ),
				'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'All Events', 'tokopress' ),
				'id'=> 'tokopress_home_upcoming_event_text',
				'type'=> 'text',
				'std'=> ''
			);

		$options[] = array(
			'name' => __( 'Featured Event', 'tokopress' ),
			'type' => 'info'
		);

			$options[] = array(
				'name'=> __( 'Section Title', 'tokopress' ),
				'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Featured Event', 'tokopress' ),
				'id'=> 'tokopress_home_featured_event',
				'type'=> 'text'
			);
			$options[] = array(
				'name'=> __( 'Event ID', 'tokopress' ),
				'desc'=> __( 'Insert Event ID for Featured Event.', 'tokopress' ),
				'id'=> 'tokopress_home_featured_event_page',
				'type'=> 'text'
			);
	}

	/**
	 * Post Options
	 */
	$options[] = array(
		'name' => __( 'Recent Updates (Blog)', 'tokopress' ),
		'type' => 'info'
	);

		$options[] = array(
			'name'	=> __( 'Recent Updates (Blog)', 'tokopress' ),
			'desc'	=> __( 'DISABLE Recent Updates (Blog)', 'tokopress' ),
			'id' 	=> 'tokopress_home_recent_post_disable',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name'=> __( 'Section Title', 'tokopress' ),
			'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Recent Updates', 'tokopress' ),
			'id'=> 'tokopress_home_recent_post',
			'type'=> 'text'
		);

		$options[] = array(
			'name'=> __( 'Link Text', 'tokopress' ),
			'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'All Events', 'tokopress' ),
			'id'=> 'tokopress_home_recent_post_text',
			'type'=> 'text',
			'std'=> ''
		);

	/**
	 * MailChimp Options
	 */
	$options[] = array(
		'name' => __( 'Subscribe Form', 'tokopress' ),
		'type' => 'info'
	);

		$options[] = array(
			'name'	=> __( 'Subscribe Form', 'tokopress' ),
			'desc'	=> __( 'DISABLE Subscribe Form', 'tokopress' ),
			'id' 	=> 'tokopress_home_subscribe_disable',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name'=> __( 'Section Title', 'tokopress' ),
			'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Subscribe to our newsletter', 'tokopress' ),
			'id'=> 'tokopress_home_subscribe_title',
			'type'=> 'text'
		);

		$options[] = array(
			'name'=> __( 'Section Description', 'tokopress' ),
			'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'never miss our latest news and events updates', 'tokopress' ),
			'id'=> 'tokopress_home_subscribe_text',
			'type'=> 'text'
		);

		if ( function_exists('mc4wp_get_forms') ) {
			$forms = mc4wp_get_forms();
			$mailchimp_forms = array( '' => '&nbsp;' );
			foreach( $forms as $form ) {
				$mailchimp_forms[ $form->ID ] = $form->name;
			}
			if ( !empty( $mailchimp_forms ) ) {
				$options[] = array(
					'name' => __( 'Select Mailchimp Form', 'tokopress' ),
					'desc' => '',
					'id' => 'tokopress_home_subscribe_id',
					'type' => 'select',
					'options' => $mailchimp_forms
				);
			}
		}

	$options[] = array(
		'name' 	=> __( 'Testimonials', 'tokopress' ),
		'type' 	=> 'info'
	);

		$options[] = array(
			'name' 	=> __( 'Testimonials', 'tokopress' ),
			'desc' 	=> __( 'DISABLE Testimonials', 'tokopress' ),
			'id' 	=> 'tokopresss_home_testimonials_disable',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name'=> __( 'Section Title', 'tokopress' ),
			'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Testimonials', 'tokopress' ),
			'id'=> 'tokopress_home_testimonials',
			'type'=> 'text',
			'std'=> ''
		);

	$options[] = array(
		'name' 	=> __( 'Brand Sponsors', 'tokopress' ),
		'type' 	=> 'info'
	);

		$options[] = array(
			'name' 	=> __( 'Brand Sponsors', 'tokopress' ),
			'desc' 	=> __( 'DISABLE brand sponsors', 'tokopress' ),
			'id' 	=> 'tokopresss_disable_brands_sponsors',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
				'name'=> __( 'Section Title', 'tokopress' ),
				'desc'=> __( 'Default:', 'tokopress' ).' '.__( 'Our Sponsors', 'tokopress' ),
				'id'=> 'tokopress_brand_title',
				'type'=> 'text'
			);

		for ($i=1; $i <= 8; $i++) { 
			$options[] = array(
				'name' 	=> sprintf( __( 'Sponsor Logo #%s', 'tokopress' ), $i ),
				'id' 	=> "tokopress_brand_img_{$i}",
				'std' 	=> '',
				'type' 	=> 'upload'
			);
			$options[] = array(
				'name' 	=> sprintf( __( 'Sponsor Link #%s', 'tokopress' ), $i ),
				'id' 	=> "tokopress_brand_link_{$i}",
				'std' 	=> '#',
				'type' 	=> 'text'
			);
		}

	return $options;
}
add_filter( 'of_options', 'tokopress_homepage_settings' );

/**
 * Contact Tab Options
 */
function tokopress_contact_settings( $options ) {
	$options[] =array(
		'name' => __( 'Contact Template', 'tokopress' ),
		'type' => 'heading'
	);

		$options[] = array(
			'name' 	=> __( 'Contact Form', 'tokopress' ),
			'type' 	=> 'info',
			'desc' 	=> __( 'We use simple contact form here. If you need <b>advanced contact form</b> (with more fields and setting), we recommend you to use Contact Form 7, Ninja Forms, or Gravity Forms plugin.', 'tokopress' ),
		);

		$options[] = array(
			'name' 	=> __( 'DISABLE Page Title', 'tokopress' ),
			'desc' 	=> __( 'DISABLE Page Title in Contact page template', 'tokopress' ),
			'id' 	=> 'tokopress_disable_contact_title',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name' 	=> __( 'DISABLE Sidebar', 'tokopress' ),
			'desc' 	=> __( 'DISABLE Sidebar in Contact page template', 'tokopress' ),
			'id' 	=> 'tokopress_disable_contact_sidebar',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name' 	=> __( 'Contact Map', 'tokopress' ),
			'type' 	=> 'info',
			'desc' 	=> '',
		);

		$options[] = array(
			'name' 	=> __( 'DISABLE Google Map', 'tokopress' ),
			'desc' 	=> __( 'DISABLE Google Map in Contact page template', 'tokopress' ),
			'id' 	=> 'tokopress_disable_contact_map',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name'=> __( 'Latitude', 'tokopress' ),
			'desc'=> __( 'Insert Latitude coordinate', 'tokopress' ),
			'id'=> 'tokopress_contact_lat',
			'type'=> 'text',
			'std'=> '-6.903932'
		);
		$options[] = array(
			'name'=> __( 'Longitude', 'tokopress' ),
			'desc'=> __( 'Insert Longitude coordinate', 'tokopress' ),
			'id'=> 'tokopress_contact_long',
			'type'=> 'text',
			'std'=> '107.610344'
		);
		$options[] = array(
			'name'=> __( 'Marker Title', 'tokopress' ),
			'desc'=> __( 'Insert marker title', 'tokopress' ),
			'id'=> 'tokopress_contact_marker_title',
			'type'=> 'text',
			'std'=> 'Marker Title'
		);
		$options[] = array(
			'name'=> __( 'Marker Description', 'tokopress' ),
			'desc'=> __( 'Insert marker description', 'tokopress' ),
			'id'=> 'tokopress_contact_marker_desc',
			'type'=> 'textarea',
			'std'=> 'Marker Content'
		);
		$options[] = array(
			'name'=> __( 'Google Maps API Key (optional)', 'tokopress' ),
			'desc'=> __( 'Usage of the Google Maps APIs now requires a key if your domain was not active prior to June 22nd, 2016.', 'tokopress' ).' <br/><a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key">'.__( 'Click here to get your Google Maps API key', 'tokopress' ).'</a>',
			'id'=> 'tokopress_contact_apikey',
			'type'=> 'text',
			'std'=> ''
		);

	return $options;
}
add_filter( 'of_options', 'tokopress_contact_settings' );

/**
 * Misc Tab Options
 */
function tokopress_misc_settings( $options ) {
	$options[] =array(
		'name' => __( 'Misc', 'tokopress' ),
		'type' => 'heading'
	);

		$options[] = array(
			'name' 	=> __( 'DISABLE One Click Demo Import', 'tokopress' ),
			'desc' 	=> __( 'DISABLE One Click Demo Import. If you have imported demo content or you do not need demo content, then it is better to disable One Click Demo Import feature.', 'tokopress' ),
			'id' 	=> 'tokopress_disable_ocdi',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

		$options[] = array(
			'name' 	=> __( 'ENABLE Visual Composer License Page', 'tokopress' ),
			'desc' 	=> __( 'ENABLE Visual Composer License Page. It is useful if you purchase Visual Composer license to get direct plugin updates and official support from Visual Composer developer.', 'tokopress' ),
			'id' 	=> 'tokopress_enable_vc_license',
			'std' 	=> '',
			'type' 	=> 'checkbox'
		);

	return $options;
}
add_filter( 'of_options', 'tokopress_misc_settings', 25 );
