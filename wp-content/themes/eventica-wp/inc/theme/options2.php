<?php 

add_action( 'customize_register', 'tokopress_customize_reposition_options', 20 );
function tokopress_customize_reposition_options( $wp_customize ) {
	$title = $wp_customize->get_section( 'title_tagline' )->title;
	$wp_customize->get_section( 'title_tagline' )->title = $title.' &amp; '.esc_html__( 'Favicon', 'tokopress' );

	$site_icon = $wp_customize->get_control( 'site_icon' )->label;
	$wp_customize->get_control( 'site_icon' )->label = $site_icon.' / '.esc_html__( 'Favicon', 'tokopress' );

	$wp_customize->remove_control('display_header_text');

	$wp_customize->get_section( 'header_image' )->panel = 'tokopress_options';	
	$wp_customize->get_section( 'header_image' )->priority = 5;
	$wp_customize->get_section( 'header_image' )->title = esc_html__( 'Header - Page Title', 'tokopress' );
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_panels' );
function tokopress_customize_controls_options_panels( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'panel',
		'setting'  => 'tokopress_options',
		'title'    => esc_html__( 'TP - Theme Options', 'tokopress' ),
		'priority' => 22,
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'panel',
		'setting'  => 'tokopress_pagetemplates',
		'title'    => esc_html__( 'TP - Page Templates', 'tokopress' ),
		'priority' => 23,
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_headertop' );
function tokopress_customize_controls_options_headertop( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_headertop',
		'title'    => esc_html__( 'Header - Top (Logo & Menu)', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 3,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_sticky_header_heading', 
		'label'		=> esc_html__( 'Sticky Header', 'tokopress' ),
		'section'	=> 'tokopress_options_headertop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_sticky_header', 
		'label'		=> esc_html__( 'ENABLE sticky header', 'tokopress' ),
		'section'	=> 'tokopress_options_headertop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_site_logo_heading', 
		'label'		=> esc_html__( 'Site Logo', 'tokopress' ),
		'section'	=> 'tokopress_options_headertop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'image',
		'setting'	=> 'tokopress_site_logo', 
		'label'		=> esc_html__( 'Site Logo', 'tokopress' ),
		'section'	=> 'tokopress_options_headertop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_minicart_heading', 
		'label'		=> esc_html__( 'Minicart', 'tokopress' ),
		'section'	=> 'tokopress_options_headertop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_minicart_header', 
		'label'		=> esc_html__( 'ENABLE minicart icon on header menu', 'tokopress' ),
		'section'	=> 'tokopress_options_headertop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_headertop_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_headertop\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_headertop',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_headerpagetitle' );
function tokopress_customize_controls_options_headerpagetitle( $controls ) {

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_page_title_disable', 
		'label'		=> esc_html__( 'DISABLE page title section globally.', 'tokopress' ),
		'section'	=> 'header_image',
		'priority' => 5,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_header_image_heading', 
		'label'		=> esc_html__( 'Page Title Background Image', 'tokopress' ),
		'section'	=> 'header_image',
		'priority' => 9,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_pageheader_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_pagetitle\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'header_image',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_headerscripts' );
function tokopress_customize_controls_options_headerscripts( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_headerscripts',
		'title'    => esc_html__( 'Header - Scripts', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'textarea',
		'setting'	=> 'tokopress_header_script', 
		'label'		=> esc_html__( 'Header Scripts', 'tokopress' ),
		'section'	=> 'tokopress_options_headerscripts',
		'sanitize_callback' => 'tokopress_sanitize_unfiltered',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_footerwidgets' );
function tokopress_customize_controls_options_footerwidgets( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_footerwidgets',
		'title'    => esc_html__( 'Footer - Widgets', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopresss_disable_footer_widget', 
		'label'		=> esc_html__( 'DISABLE footer widgets area', 'tokopress' ),
		'section'	=> 'tokopress_options_footerwidgets',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_footerwidgets_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_footerwidgets\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p><p><span class="dashicons dashicons-admin-generic"></span> <a href="javascript:wp.customize.panel( \'widgets\' ).focus();">'.esc_html__( 'Setup Footer Widgets', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_footerwidgets',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_footercredits' );
function tokopress_customize_controls_options_footercredits( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_footercredits',
		'title'    => esc_html__( 'Footer - Credits', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopresss_disable_footer_buttom', 
		'label'		=> esc_html__( 'DISABLE footer credits area', 'tokopress' ),
		'section'	=> 'tokopress_options_footercredits',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'textarea',
		'setting'	=> 'tokopress_footer_text', 
		'label'		=> esc_html__( 'Footer Credit Text', 'tokopress' ),
		'section'	=> 'tokopress_options_footercredits',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_footer_social_heading', 
		'label'		=> esc_html__( 'Footer - Social Icons', 'tokopress' ),
		'section'	=> 'tokopress_options_footercredits',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_hide_social', 
		'label'		=> esc_html__( 'DISABLE Social Icons', 'tokopress' ),
		'section'	=> 'tokopress_options_footercredits',
	);

	$socials = array(
		'' 				=> esc_html__( 'no icon', 'tokopress' ),
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
		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'select',
			'setting'	=> 'tokopress_social_' . $is, 
			'label'		=> sprintf( esc_html__( 'Social Icon #%s', 'tokopress' ), $is ),
			'section'	=> 'tokopress_options_footercredits',
			'choices'   => $socials,
		);
		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_social_' . $is . '_url', 
			'label'		=> sprintf( esc_html__( 'Social Icon #%s URL', 'tokopress' ), $is ),
			'section'	=> 'tokopress_options_footercredits',
		);
	}

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_footercredits_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_footercredits\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_footercredits',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_footerscripts' );
function tokopress_customize_controls_options_footerscripts( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_footerscripts',
		'title'    => esc_html__( 'Footer - Scripts', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'textarea',
		'setting'	=> 'tokopress_footer_script', 
		'label'		=> esc_html__( 'Footer Scripts', 'tokopress' ),
		'section'	=> 'tokopress_options_footerscripts',
		'sanitize_callback' => 'tokopress_sanitize_unfiltered',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_pagetemplates_home' );
function tokopress_customize_controls_options_pagetemplates_home( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_home_templates',
		'title'    => esc_html__( 'Homepage Page Template', 'tokopress' ),
		'panel'    => 'tokopress_pagetemplates',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_options_home_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Page with Homepage Page Template', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Page with Homepage Page Template', 'tokopress' ) , esc_html__( 'Page with Homepage Page Template', 'tokopress' ) ),
		'section'	=> 'tokopress_options_home_templates',
		'active_callback' => 'tokopress_is_not_home_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_home_heading_desc', 
		'label'		=> '',
		'description' => esc_html__( 'These options are for everyone who use Homepage Page Template. If you use Visual Composer to build your homepage, please IGNORE these options.', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	if( class_exists( 'Tribe__Events__Main' ) ) {

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'heading',
			'setting'	=> 'tokopress_home_heading_slider', 
			'label'		=> esc_html__( 'Events Slider', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'checkbox',
			'setting'	=> 'tokopress_home_slider_disable', 
			'label'		=> esc_html__( 'DISABLE Events Slider', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_slider_ids', 
			'label'		=> esc_html__( 'Event IDs', 'tokopress' ),
			'description'=> esc_html__( 'Separated by comma. Use this is you want to show some specific events for slider. "Number of Events" and "Event Category" options will be ignored.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'number',
			'setting'	=> 'tokopress_home_slider_numbers', 
			'label'		=> esc_html__( 'Number of Events', 'tokopress' ),
			'description'=> esc_html__( 'Use this is you want to show upcoming events for slider. Leave "Event IDs" option empty.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_slider_category', 
			'label'		=> esc_html__( 'Event Category Name', 'tokopress' ),
			'description'=> esc_html__( 'Put event category name here if you want to retrieve upcoming events from this category only. Leave "Event IDs" option empty.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_slide_button', 
			'label'		=> esc_html__( 'Button Text', 'tokopress' ),
			'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Detail', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'heading',
			'setting'	=> 'tokopress_home_heading_search', 
			'label'		=> esc_html__( 'Search Form', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'checkbox',
			'setting'	=> 'tokopress_home_search_disable', 
			'label'		=> esc_html__( 'DISABLE Search Form', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_search_text', 
			'label'		=> esc_html__( 'Placeholder Text', 'tokopress' ),
			'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Search Event &hellip;', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'heading',
			'setting'	=> 'tokopress_home_heading_upcoming_events', 
			'label'		=> esc_html__( 'Upcoming Events', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'checkbox',
			'setting'	=> 'tokopress_home_upcoming_event_disable', 
			'label'		=> esc_html__( 'DISABLE Upcoming Events', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'number',
			'default'	=> 3,
			'setting'	=> 'tokopress_home_upcoming_event_numbers', 
			'label'		=> esc_html__( 'Number of Events', 'tokopress' ),
			'description' => esc_html__( 'Number of upcoming events to be displayed.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_upcoming_event_category', 
			'label'		=> esc_html__( 'Event Category Name', 'tokopress' ),
			'description' => esc_html__( 'Put event category name here if you want to retrieve upcoming events from this category only.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_upcoming_event_exclude', 
			'label'		=> esc_html__( 'Exclude', 'tokopress' ),
			'description' => esc_html__( 'Insert Event IDs (separated by comma) to exclude.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_upcoming_event', 
			'label'		=> esc_html__( 'Section Title', 'tokopress' ),
			'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Upcoming Events', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_upcoming_event_text', 
			'label'		=> esc_html__( 'Link Text', 'tokopress' ),
			'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'All Events', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'heading',
			'setting'	=> 'tokopress_home_heading_featured_event', 
			'label'		=> esc_html__( 'Featured Single Event', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_featured_event', 
			'label'		=> esc_html__( 'Section Title', 'tokopress' ),
			'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Featured Event', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'setting'	=> 'tokopress_home_featured_event_page', 
			'label'		=> esc_html__( 'Event ID', 'tokopress' ),
			'description'=> esc_html__( 'Insert Event ID for Featured Event.', 'tokopress' ),
			'section'	=> 'tokopress_options_home_templates',
		);

	}

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_home_heading_recent_post', 
		'label'		=> esc_html__( 'Recent Updates (Blog)', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_home_recent_post_disable', 
		'label'		=> esc_html__( 'DISABLE Recent Updates (Blog)', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_home_recent_post', 
		'label'		=> esc_html__( 'Section Title', 'tokopress' ),
		'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Recent Updates', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_home_recent_post_text', 
		'label'		=> esc_html__( 'Link Text', 'tokopress' ),
		'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'All Posts', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_home_heading_subscribe', 
		'label'		=> esc_html__( 'Subscribe Form', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_home_subscribe_disable', 
		'label'		=> esc_html__( 'DISABLE Subscribe Form', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_home_subscribe_title', 
		'label'		=> esc_html__( 'Section Title', 'tokopress' ),
		'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Subscribe to our newsletter', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_home_subscribe_text', 
		'label'		=> esc_html__( 'Section Description', 'tokopress' ),
		'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'never miss our latest news and events updates', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	if ( function_exists('mc4wp_get_forms') ) {
		$forms = mc4wp_get_forms();
		$mailchimp_forms = array( '' => '&nbsp;' );
		foreach( $forms as $form ) {
			$mailchimp_forms[ $form->ID ] = $form->name;
		}
		if ( !empty( $mailchimp_forms ) ) {
			$controls[] = array( 
				'setting_type' => 'option_mod',
				'type' 		=> 'select',
				'setting'	=> 'tokopress_home_subscribe_id', 
				'label'		=> esc_html__( 'Select Mailchimp Form', 'tokopress' ),
				'section'	=> 'tokopress_options_home_templates',
				'choices'	=> $mailchimp_forms
			);
		}
	}

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_home_heading_testimonials', 
		'label'		=> esc_html__( 'Testimonials', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopresss_home_testimonials_disable', 
		'label'		=> esc_html__( 'DISABLE Testimonials', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_home_testimonials', 
		'label'		=> esc_html__( 'Section Title', 'tokopress' ),
		'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Testimonials', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_home_heading_brands', 
		'label'		=> esc_html__( 'Brand Sponsors', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopresss_disable_brands_sponsors', 
		'label'		=> esc_html__( 'DISABLE brand sponsors', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_brand_title', 
		'label'		=> esc_html__( 'Section Title', 'tokopress' ),
		'description'=> esc_html__( 'Default:', 'tokopress' ).' '.esc_html__( 'Our Sponsors', 'tokopress' ),
		'section'	=> 'tokopress_options_home_templates',
	);

	for ($i=1; $i <= 8; $i++) { 
		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'image',
			'setting'	=> "tokopress_brand_img_{$i}",
			'label'		=> sprintf( esc_html__( 'Sponsor Logo #%s', 'tokopress' ), $i ),
			'section'	=> 'tokopress_options_home_templates',
		);
		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'text',
			'default'	=> '#',
			'setting'	=> "tokopress_brand_link_{$i}",
			'label'		=> sprintf( esc_html__( 'Sponsor Link #%s', 'tokopress' ), $i ),
			'section'	=> 'tokopress_options_home_templates',
		);
	}

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_home_templates_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_home_templates\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_home_templates',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_pagetemplates_contact' );
function tokopress_customize_controls_options_pagetemplates_contact( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_contact_templates',
		'title'    => esc_html__( 'Contact Page Template', 'tokopress' ),
		'panel'    => 'tokopress_pagetemplates',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_contact_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Page with Contact Page Template', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Page with Contact Page Template', 'tokopress' ) , esc_html__( 'Page with Contact Page Template', 'tokopress' ) ),
		'section'	=> 'tokopress_options_contact_templates',
		'active_callback' => 'tokopress_is_not_contact_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_contact_heading_desc', 
		'label'		=> '',
		'description' => esc_html__( 'These options are for everyone who use Contact Page Template. We use simple contact form here. If you need advanced contact form (with more fields and setting), we recommend you to use Contact Form 7, Caldera Forms, Ninja Forms, WP Forms, or Gravity Forms plugin.', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_disable_contact_title', 
		'label'		=> esc_html__( 'DISABLE Page Title in Contact page template', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_disable_contact_sidebar', 
		'label'		=> esc_html__( 'DISABLE Sidebar in Contact page template', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_contact_heading_map', 
		'label'		=> esc_html__( 'Contact Map', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_disable_contact_map', 
		'label'		=> esc_html__( 'DISABLE Google Map in Contact page template', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'default'	=> '-6.903932',
		'setting'	=> 'tokopress_contact_lat',
		'label'		=> esc_html__( 'Latitude', 'tokopress' ),
		'description' => esc_html__( 'Insert Latitude coordinate', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'default'	=> '107.610344',
		'setting'	=> 'tokopress_contact_long',
		'label'		=> esc_html__( 'Longitude', 'tokopress' ),
		'description' => esc_html__( 'Insert Longitude coordinate', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'default'	=> esc_html__( 'Marker Title', 'tokopress' ),
		'setting'	=> 'tokopress_contact_marker_title',
		'label'		=> esc_html__( 'Marker Title', 'tokopress' ),
		'description' => esc_html__( 'Insert marker title', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'default'	=> esc_html__( 'Marker Description', 'tokopress' ),
		'setting'	=> 'tokopress_contact_marker_desc',
		'label'		=> esc_html__( 'Marker Description', 'tokopress' ),
		'description' => esc_html__( 'Insert marker description', 'tokopress' ),
		'section'	=> 'tokopress_options_contact_templates',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_contact_apikey',
		'label'		=> esc_html__( 'Google Maps API Key (recommended)', 'tokopress' ),
		'description' => esc_html__( 'Usage of the Google Maps APIs now requires a key if your domain was not active prior to June 22nd, 2016.', 'tokopress' ).' <br/><a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key">'.esc_html__( 'Click here to get your Google Maps API key', 'tokopress' ).'</a>',
		'section'	=> 'tokopress_options_contact_templates',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_ocdi' );
function tokopress_customize_controls_options_ocdi( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_ocdi',
		'title'    => esc_html__( 'One Click Demo Import', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_disable_ocdi',
		'label'		=> esc_html__( 'DISABLE One Click Demo Import', 'tokopress' ),
		'description' => esc_html__( 'If you have imported demo content or you do not need demo content, then it is better to disable One Click Demo Import feature.', 'tokopress' ),
		'section'	=> 'tokopress_options_ocdi',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_visualcomposer' );
function tokopress_customize_controls_options_visualcomposer( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_visualcomposer',
		'title'    => esc_html__( 'Visual Composer', 'tokopress' ),
		'panel'    => 'tokopress_options',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_enable_vc_license',
		'label'		=> esc_html__( 'ENABLE Visual Composer License Page', 'tokopress' ),
		'description' => esc_html__( 'It is useful if you purchase separate Visual Composer license to get direct plugin updates and official support from Visual Composer developer.', 'tokopress' ),
		'section'	=> 'tokopress_options_visualcomposer',
	);

	return $controls;
}
