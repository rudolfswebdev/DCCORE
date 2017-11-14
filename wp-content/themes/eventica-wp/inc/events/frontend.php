<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'get_header', 'tokopress_events_action_get_header' );
function tokopress_events_action_get_header() {

	/* Hide Event Calendar Pro Related Events, use our theme */
	if ( class_exists('Tribe__Events__Pro__Main') ) {
		tokopress_remove_filter_class( 'tribe_events_single_event_after_the_meta', 'Tribe__Events__Pro__Main', 'register_related_events_view', 10 );
	}

	/* Hide Event Calendar Pro Additional Fields, use our theme */
	if ( class_exists('Tribe__Events__Pro__Single_Event_Meta') ) {
		tokopress_remove_filter( 'tribe_events_single_event_meta_primary_section_end', 'additional_fields', 10 );
	}
}

add_filter( 'tribe_meta_event_tags', 'tokopress_events_overide_markup_tags', 10, 3 );
function tokopress_events_overide_markup_tags( $list, $label, $separator ) {
	$list = get_the_term_list( get_the_ID(), 'post_tag', '<tr><th>' . $label . '</th><td class="tribe-event-tags">', $separator, '</td></tr>' );

	return $list;
}

add_action( 'woocommerce_cart_is_empty', 'tokopress_events_cart_return_to_events', 99 );
function tokopress_events_cart_return_to_events() {
	echo '<p class="return-to-events"><a class="button" href="'.tribe_get_events_link().'">'.__( 'Return To Events', 'tokopress' ).'</a></p>';
}

// add_filter( 'tribe_wootickets_stylesheet_url', 'tokopress_events_tribe_wootickets_stylesheet_url' );
function tokopress_events_tribe_wootickets_stylesheet_url( $stylesheet_url ) {
	return false;
}

add_filter( 'tribe_query_can_inject_date_field', 'tokopress_fix_tribe_query_can_inject_date_field' );
function tokopress_fix_tribe_query_can_inject_date_field( $can ) {
	global $wp_query;
	if ( isset($wp_query->query['eventDisplay']) && $wp_query->query['eventDisplay'] == 'month' ) {
		$can = false;
	}
	return $can;
}

if ( of_get_option( 'tokopress_events_hide_catalog_separator' ) ) {
	add_filter( 'tribe_events_list_the_date_headers', 'tokopress_events_list_the_date_headers' );
}
function tokopress_events_list_the_date_headers( $html ) {
	return '';
}

add_action( 'tribe_events_pro_tribe_events_shortcode_post_render', 'tokopress_events_tribe_events_shortcode_style', 99 );
function tokopress_events_tribe_events_shortcode_style() {
	wp_enqueue_style( 'style-theeventscalendar', trailingslashit( get_template_directory_uri() ) . 'style.css', array(), THEME_VERSION );
}

add_filter( 'tribe_json_ld_event_data', 'tokopress_events_tribe_json_ld_event_data', 99 );
function tokopress_events_tribe_json_ld_event_data( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	foreach ( $data as $event_id => $event ) {
		if ( ! is_object( $event ) ) {
			continue;
		}
		if ( isset( $event->offers ) ) {
			if ( !is_array( $event->offers ) && is_object( $event->offers ) ) {
				if ( !isset( $event->offers->availability ) ) {
					$data[$event_id]->offers->availability = 'InStock';
				}
				if ( !isset( $event->offers->priceCurrency ) ) {
					$currency = '';
					if ( function_exists('get_woocommerce_currency') ) {
						$currency = get_woocommerce_currency();
					}
					else {
						$symbol = get_post_meta( $event_id, '_EventCurrencySymbol', true );
						if ( strlen($symbol) == 3 ) {
							$currency = $symbol;
						}
						else {
							if ( $symbol == '$' ) {
								$currency = 'USD';
							}
							elseif ( $symbol == 'A$' || $symbol == 'AU$' ) {
								$currency = 'AUD';
							}
							elseif ( $symbol == 'C$' || $symbol == 'Can$' ) {
								$currency = 'CAD';
							}
							elseif ( $symbol == '€' ) {
								$currency = 'EUR';
							}
							elseif ( $symbol == '£' ) {
								$currency = 'GBP';
							}
							elseif ( $symbol == '¥' ) {
								$currency = 'JPY';
							}
							elseif ( $symbol == 'Rp' ) {
								$currency = 'IDR';
							}
							elseif ( $symbol == '₹' ) {
								$currency = 'INR';
							}
							elseif ( $symbol == 'RM' ) {
								$currency = 'MYR';
							}
						}
					}
					if ( $currency ) {
						$data[$event_id]->offers->priceCurrency = $currency;
					}
				}
				if ( !isset( $event->offers->validFrom ) ) {
					$data[$event_id]->offers->validFrom = get_the_time( 'c', $event_id );
				}
			}
			if ( is_array( $event->offers ) ) {
				foreach ( $event->offers as $offer_id => $offer ) {
					if ( !isset( $offer->availability ) ) {
						$data[$event_id]->offers[$offer_id]->availability = 'InStock';
					}
					if ( !isset( $offer->priceCurrency ) ) {
						$currency = '';
						if ( function_exists('get_woocommerce_currency') ) {
							$currency = get_woocommerce_currency();
						}
						else {
							$symbol = get_post_meta( $event_id, '_EventCurrencySymbol', true );
							if ( strlen($symbol) == 3 ) {
								$currency = $symbol;
							}
							else {
								if ( $symbol == '$' ) {
									$currency = 'USD';
								}
								elseif ( $symbol == 'A$' || $symbol == 'AU$' ) {
									$currency = 'AUD';
								}
								elseif ( $symbol == 'C$' || $symbol == 'Can$' ) {
									$currency = 'CAD';
								}
								elseif ( $symbol == '€' ) {
									$currency = 'EUR';
								}
								elseif ( $symbol == '£' ) {
									$currency = 'GBP';
								}
								elseif ( $symbol == '¥' ) {
									$currency = 'JPY';
								}
								elseif ( $symbol == 'Rp' ) {
									$currency = 'IDR';
								}
								elseif ( $symbol == '₹' ) {
									$currency = 'INR';
								}
								elseif ( $symbol == 'RM' ) {
									$currency = 'MYR';
								}
							}
						}
						if ( $currency ) {
							$data[$event_id]->offers[$offer_id]->priceCurrency = $currency;
						}
					}
					if ( !isset( $offer->validFrom ) ) {
						$data[$event_id]->offers[$offer_id]->validFrom = get_the_time( 'c', $event_id );
					}
				}
			}
		}
		if ( !isset( $event->performer ) ) {
			if ( function_exists('tribe_get_organizer_ids') && function_exists('tribe_get_organizer') ) {
				$organizers = array();
				$organizer_ids = tribe_get_organizer_ids();
				if ( !empty( $organizer_ids ) ) {
					foreach ( $organizer_ids as $key => $organizer_id ) {
						$organizers[] = (object) array(
							'@type' => 'PerformingGroup',
							'name' => tribe_get_organizer($organizer_id),
						);
					}
				}
				if ( !empty($organizers) ) {
					$data[$event_id]->performer = $organizers;
				}
			}
		}
		if ( !isset( $event->location ) ) {
			if ( tribe_has_venue( $event_id ) ) {
				$venue_id       = tribe_get_venue_id( $event_id );
				$venue_data     = Tribe__Events__JSON_LD__Venue::instance()->get_data( $venue_id );
				$data[$event_id]->location = reset( $venue_data );
			}
		}
	}
	return $data;
}

add_action( 'wp_footer', 'tokopress_events_fbcomments_script' );
function tokopress_events_fbcomments_script() {
	if ( ! of_get_option( 'tokopress_events_fbcomments_show' ) )
		return;
	$appid = trim( of_get_option('tokopress_events_fbcomments_appid') );
	if ( !$appid ) {
		$appid = '606571753066413';
	}
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=<?php esc_attr($appid); ?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php 
}
