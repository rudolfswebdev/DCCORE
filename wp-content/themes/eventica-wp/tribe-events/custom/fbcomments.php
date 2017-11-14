<?php
if ( ! of_get_option( 'tokopress_events_fbcomments_show' ) )
	return;

$title = of_get_option( 'tokopress_events_fbcomments_custom_title' );
if ( !trim($title) ) {
	$title = __( 'Comments', 'tokopress' );
} 

?>

<div class="event-fbcomments-wrap">
	<div class="event-fbcomments-title">
		<h2><?php echo esc_html( $title ); ?></h2>
	</div>

	<div class="event-fbcomments-area">
		<div class="fb-comments" data-href="<?php echo wp_get_shortlink(); ?> " data-width="100%" data-numposts="5"></div>
	</div>
</div>
