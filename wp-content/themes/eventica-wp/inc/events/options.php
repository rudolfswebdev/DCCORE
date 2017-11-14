<?php
/**
 * Events Options Settings
 *
 * @package Theme Options
 * @author Tokopress
 *
 */

function tokopress_event_settings( $options ) {
	$options[] = array(
		'name' 	=> __( 'Events', 'tokopress' ),
		'type' 	=> 'heading'
	);

	$options[] = array(
		'name' => __( 'Events - Catalog Page', 'tokopress' ),
		'type' => 'info'
	);
	
		$options[] = array(
			'name' => __( 'Events Page Title', 'tokopress' ),
			'desc' => __( 'DISABLE page title on events catalog page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_catalog_title',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Events Sidebar', 'tokopress' ),
			'desc' => __( 'DISABLE sidebar on events catalog page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_catalog_sidebar',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Events Month and Year Separator', 'tokopress' ),
			'desc' => __( 'DISABLE month and year separator on list view of events catalog page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_catalog_separator',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Day of Week on Event Date', 'tokopress' ),
			'desc' => __( 'ENABLE day of week on list view of events catalog page.', 'tokopress' ),
			'id' => 'tokopress_events_show_catalog_dayofweek',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Recurring Events Information', 'tokopress' ),
			'desc' => __( 'DISABLE recurring event information on list view of events catalog page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_recurring_info',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Event Month Format', 'tokopress' ),
			'desc' => __( 'Show event month in short text format (3 letters)', 'tokopress' ),
			'id' => 'tokopress_events_month_short_text',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Custom Text For Events Page Title', 'tokopress' ),
			'desc' => __( 'Default: Events', 'tokopress' ),
			'id' => 'tokopress_events_custom_catalog_title',
			'std' => '',
			'type' => 'text'
		);

		$options[] = array(
			'name' => __( 'Custom Text For "Upcoming Events" Title', 'tokopress' ),
			'desc' => '',
			'id' => 'tokopress_events_custom_upcoming_events',
			'std' => '',
			'type' => 'text'
		);

	$options[] = array(
		'name' => __( 'Events - Single Event Page', 'tokopress' ),
		'type' => 'info'
	);
	
		$options[] = array(
			'name' => __( 'Single Event Page Title', 'tokopress' ),
			'desc' => __( 'DISABLE page title on single event page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_single_title',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Single Event Sidebar', 'tokopress' ),
			'desc' => __( 'DISABLE sidebar on single event page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_single_sidebar',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Single Venue Link', 'tokopress' ),
			'desc' => __( 'LINK venue name to single venue page.', 'tokopress' ),
			'id' => 'tokopress_events_show_venue_link',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Single Organizer Link', 'tokopress' ),
			'desc' => __( 'LINK organizer name to single organizer page.', 'tokopress' ),
			'id' => 'tokopress_events_show_organizer_link',
			'std' => '',
			'type' => 'checkbox'
		);

	$options[] = array(
		'name' => __( 'Events - Single Event Page - Event Gallery', 'tokopress' ),
		'type' => 'info'
	);
	
		$options[] = array(
			'name' => __( 'Event Gallery', 'tokopress' ),
			'desc' => __( 'DISABLE event gallery on single event page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_single_gallery',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Custom Text For Event Gallery Title', 'tokopress' ),
			'desc' => __( 'Default: Event Gallery', 'tokopress' ),
			'id' => 'tokopress_events_custom_gallery_title',
			'std' => '',
			'type' => 'text'
		);

	$options[] = array(
		'name' => __( 'Events - Single Event Page - Related Events', 'tokopress' ),
		'type' => 'info'
	);
	
		$options[] = array(
			'name' => __( 'Related Events', 'tokopress' ),
			'desc' => __( 'DISABLE related events on single event page.', 'tokopress' ),
			'id' => 'tokopress_events_hide_single_related',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Custom Text For Related Events Title', 'tokopress' ),
			'desc' => __( 'Default: Related Events', 'tokopress' ),
			'id' => 'tokopress_events_custom_related_title',
			'std' => '',
			'type' => 'text'
		);

		$options[] = array(
			'name' => __( 'Past Events', 'tokopress' ),
			'desc' => __( 'INCLUDE past events on related events.', 'tokopress' ),
			'id' => 'tokopress_events_include_past_related',
			'std' => '',
			'type' => 'checkbox'
		);

	$options[] = array(
		'name' => __( 'Events - Single Event Page - Comments', 'tokopress' ),
		'type' => 'info'
	);
	
		$options[] = array(
			'name' => __( 'Single Event Comment', 'tokopress' ),
			'desc' => __( 'ENABLE comment on single event page.', 'tokopress' ),
			'id' => 'tokopress_events_show_single_comment',
			'std' => '',
			'type' => 'checkbox'
		);

	$options[] = array(
		'name' => __( 'Events - Single Event Page - Facebook Comments', 'tokopress' ),
		'type' => 'info'
	);
	
		$options[] = array(
			'name' => __( 'Facebook Comments', 'tokopress' ),
			'desc' => __( 'ENABLE Facebook Comments on single event page.', 'tokopress' ),
			'id' => 'tokopress_events_fbcomments_show',
			'std' => '',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => __( 'Facebook App ID', 'tokopress' ),
			'desc' => '<a href="https://developers.facebook.com/apps/" target="_blank">'.__( 'Get your Facebook App ID', 'tokopress' ).'</a>',
			'id' => 'tokopress_events_fbcomments_appid',
			'std' => '',
			'type' => 'text'
		);

		$options[] = array(
			'name' => __( 'Custom Text For Facebook Comments Title', 'tokopress' ),
			'desc' => __( 'Default: Comments', 'tokopress' ),
			'id' => 'tokopress_events_fbcomments_custom_title',
			'std' => '',
			'type' => 'text'
		);

	$options[] = array(
		'name' => __( 'Events - Change the word Event/Events globally', 'tokopress' ),
		'type' => 'info'
	);

		$options[] = array(
			'name' => __( 'Events Label Singular', 'tokopress' ),
			'desc' => __( 'Default: Event', 'tokopress' ),
			'id' => 'tokopress_events_label_singular',
			'std' => '',
			'type' => 'text'
		);

		$options[] = array(
			'name' => __( 'Events Label Singular Lowercase', 'tokopress' ),
			'desc' => __( 'Default: event', 'tokopress' ),
			'id' => 'tokopress_events_label_singular_lowercase',
			'std' => '',
			'type' => 'text'
		);

		$options[] = array(
			'name' => __( 'Events Label Plural', 'tokopress' ),
			'desc' => __( 'Default: Events', 'tokopress' ),
			'id' => 'tokopress_events_label_plural',
			'std' => '',
			'type' => 'text'
		);

		$options[] = array(
			'name' => __( 'Events Label Plural Lowercase', 'tokopress' ),
			'desc' => __( 'Default: events', 'tokopress' ),
			'id' => 'tokopress_events_label_plural_lowercase',
			'std' => '',
			'type' => 'text'
		);

	if ( class_exists('woocommerce') ) {
		$options[] = array(
			'name' => __( 'Events - Misc', 'tokopress' ),
			'type' => 'info'
		);
		
			$options[] = array(
				'name' => __( 'Currency Locking', 'tokopress' ),
				'desc' => __( 'DISABLE currency locking to WooCommerce currency settings.', 'tokopress' ),
				'id' => 'tokopress_events_disable_wc_currency_lock',
				'std' => '',
				'type' => 'checkbox'
			);
	}

	return $options;
}
add_filter( 'of_options', 'tokopress_event_settings' );
