<?php

add_filter( 'tribe_events_pro_customizer_is_active', '__return_false' );
add_filter( 'tribe_customizer_is_active', '__return_false' );

add_action( 'get_header', 'tokopress_customize_tec_get_header' );
add_action( 'admin_head', 'tokopress_customize_tec_get_header' );
function tokopress_customize_tec_get_header() {
	if ( class_exists('Tribe__Customizer') ) {
		tokopress_remove_filter_class( 'customize_register', 'Tribe__Customizer', 'register', 15 );
		tokopress_remove_filter_class( 'wp_print_footer_scripts', 'Tribe__Customizer', 'print_css_template', 15 );
	}
}

add_action( 'customize_register', 'tokopress_customize_reposition_tec', 20 );
function tokopress_customize_reposition_tec( $wp_customize ) {
	$wp_customize->remove_panel('tribe_customizer');
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_tec' );
function tokopress_customize_controls_colors_tec( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_tec',
		'title'    => esc_html__( 'The Events Calendar', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_tec_heading_featured', 
		'label'		=> esc_html__( 'Featured Event (TEC4.4+)', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_featured_bg', 
		'label'		=> esc_html__( 'Featured Event Background Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-list .tribe-events-loop .tribe-event-featured .even-list-wrapper, .tribe-events-list #tribe-events-day.tribe-events-loop .tribe-event-featured .even-list-wrapper, .tribe-events-list .tribe-events-photo-event.tribe-event-featured .tribe-events-event-details, #tribe-events-content table.tribe-events-calendar .type-tribe_events.tribe-event-featured { background: [value] !important; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_tec_heading_event_list', 
		'label'		=> esc_html__( 'Event List Box', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_date_bg', 
		'label'		=> esc_html__( 'Event List Box - Event Date Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-list .tribe-events-event-date { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_cost_bg', 
		'label'		=> esc_html__( 'Event List Box - Event Cost Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-list .event-list-wrapper-bottom .wraper-bottom-right { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_separator_bg', 
		'label'		=> esc_html__( 'Event List Separator Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-list .tribe-events-loop .tribe-events-list-separator-month, .tribe-events-list .tribe-events-loop .tribe-events-day-time-slot h5 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_tec_heading_event_calendar', 
		'label'		=> esc_html__( 'Events Calendar', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_calendar_day_bg',  
		'label'		=> esc_html__( 'Events Calendar - Header (Day) Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-calendar thead th { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_calendar_day_border', 
		'label'		=> esc_html__( 'Events Calendar - Header (Day) Border', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-calendar thead th { border-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_calendar_current_bg', 
		'label'		=> esc_html__( 'Events Calendar - Current Date Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-], .tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-] > a { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_tec_heading_single_event', 
		'label'		=> esc_html__( 'Single Event', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_attendees_bg', 
		'label'		=> esc_html__( 'Single Event - Events Attendees Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.event-attendees-wrap .event-attendees-title h2 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_gallery_bg', 
		'label'		=> esc_html__( 'Single Event - Events Gallery Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.event-gallery-wrap .event-gallery-title h2 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_related_bg', 
		'label'		=> esc_html__( 'Single Event - Related Events Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> 'related-event-wrap .related-event-title h2 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_events_fbcomments_bg', 
		'label'		=> esc_html__( 'Single Event - Facebook Comments Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_tec',
		'style'		=> '.event-fbcomments-wrap .event-fbcomments-title h2 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_tec_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.panel( \'tokopress_options_tec\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_tec',
	);

	return $controls;
}
