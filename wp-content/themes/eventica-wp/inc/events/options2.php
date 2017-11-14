<?php

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_tec' );
function tokopress_customize_controls_options_tec( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'panel',
		'setting'  => 'tokopress_options_tec',
		'title'    => esc_html__( 'TP - The Events Calendar', 'tokopress' ),
		'priority' => 24,
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_tec_catalog',
		'title'    => esc_html__( 'Events Page', 'tokopress' ),
		'panel'    => 'tokopress_options_tec',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_catalog_title',
		'label'		=> esc_html__( 'DISABLE page title on events catalog page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_catalog_sidebar',
		'label'		=> esc_html__( 'DISABLE sidebar on events catalog page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_catalog_separator',
		'label'		=> esc_html__( 'DISABLE month and year separator on list view of events catalog page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_show_catalog_dayofweek',
		'label'		=> esc_html__( 'ENABLE day of week on list view of events catalog page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_recurring_info',
		'label'		=> esc_html__( 'DISABLE recurring event information on list view of events catalog page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_month_short_text',
		'label'		=> esc_html__( 'Show event month in short text format (3 letters)', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_custom_catalog_title',
		'label'		=> esc_html__( 'Custom Text For Events Page Title', 'tokopress' ),
		'description' => esc_html__( 'Default: Events', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_custom_upcoming_events',
		'label'		=> esc_html__( 'Custom Text For "Upcoming Events" Title', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_tec_catalog_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_tec\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_tec_catalog',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_tec_single',
		'title'    => esc_html__( 'Single Event Page', 'tokopress' ),
		'panel'    => 'tokopress_options_tec',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_single_title',
		'label'		=> esc_html__( 'DISABLE page title on single event page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_single_sidebar',
		'label'		=> esc_html__( 'DISABLE sidebar on single event page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_show_venue_link',
		'label'		=> esc_html__( 'LINK venue name to single venue page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_show_organizer_link',
		'label'		=> esc_html__( 'LINK organizer name to single organizer page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_events_gallery_heading',
		'label'		=> esc_html__( 'Event Gallery', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_single_gallery',
		'label'		=> esc_html__( 'DISABLE event gallery on single event page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_custom_gallery_title',
		'label'		=> esc_html__( 'Custom Text For Event Gallery Title', 'tokopress' ),
		'description' => esc_html__( 'Default: Event Gallery', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_events_related_heading',
		'label'		=> esc_html__( 'Related Events', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_hide_single_related',
		'label'		=> esc_html__( 'DISABLE related events on single event page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_custom_related_title',
		'label'		=> esc_html__( 'Custom Text For Related Events Title', 'tokopress' ),
		'description' => esc_html__( 'Default: Related Events', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_include_past_related',
		'label'		=> esc_html__( 'INCLUDE past events on related events.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_events_comment_heading',
		'label'		=> esc_html__( 'Comments', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_show_single_comment',
		'label'		=> esc_html__( 'ENABLE comment on single event page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_events_fbcomments_heading',
		'label'		=> esc_html__( 'Facebook Comments', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_events_fbcomments_show',
		'label'		=> esc_html__( 'ENABLE Facebook Comments on single event page.', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_fbcomments_appid',
		'label'		=> esc_html__( 'Facebook App ID', 'tokopress' ),
		'description' => '<a href="https://developers.facebook.com/apps/" target="_blank">'.__( 'Get your Facebook App ID', 'tokopress' ).'</a>',
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_fbcomments_custom_title',
		'label'		=> esc_html__( 'Custom Text For Facebook Comments Title', 'tokopress' ),
		'description' => esc_html__( 'Default: Comments', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_tec_single_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_tec\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_tec_single',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_tec_label',
		'title'    => esc_html__( 'Change Event/Events words globally', 'tokopress' ),
		'panel'    => 'tokopress_options_tec',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_label_singular',
		'label'		=> esc_html__( 'Events Label Singular', 'tokopress' ),
		'description' => esc_html__( 'Default: Event', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_label',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_label_singular_lowercase',
		'label'		=> esc_html__( 'Events Label Singular Lowercase', 'tokopress' ),
		'description' => esc_html__( 'Default: event', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_label',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_label_plural',
		'label'		=> esc_html__( 'Events Label Plural', 'tokopress' ),
		'description' => esc_html__( 'Default: Events', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_label',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'text',
		'setting'	=> 'tokopress_events_label_plural_lowercase',
		'label'		=> esc_html__( 'Events Label Plural Lowercase', 'tokopress' ),
		'description' => esc_html__( 'Default: events', 'tokopress' ),
		'section'	=> 'tokopress_options_tec_label',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_tec_label_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_tec\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_tec_label',
	);

	if ( class_exists('woocommerce') ) {

		$controls[] = array(
			'setting_type' => 'option_mod',
			'type'     => 'section',
			'setting'  => 'tokopress_options_tec_currency',
			'title'    => esc_html__( 'WooCommerce Currency Locking', 'tokopress' ),
			'panel'    => 'tokopress_options_tec',
			'priority' => 10,
		);

		$controls[] = array( 
			'setting_type' => 'option_mod',
			'type' 		=> 'checkbox',
			'setting'	=> 'tokopress_events_disable_wc_currency_lock',
			'label'		=> esc_html__( 'DISABLE currency locking to WooCommerce currency settings.', 'tokopress' ),
			'section'	=> 'tokopress_options_tec_currency',
		);

	}

	return $controls;
}
