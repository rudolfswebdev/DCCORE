<?php 
$cta_url = tokopress_get_event_cta_url();
$cta_target = $cta_url == '#' || strpos($cta_url, 'ticket_redirect') !== false ? '' : '_blank';
$month_format = of_get_option( 'tokopress_events_month_short_text' ) ? 'M' : 'F';
?>
<div class="tribe-events-cta clearfix">
	<div class="tribe-events-cta-date">
		<span class="mm"><?php echo tribe_get_start_date( null, false, $month_format ) ?></span>
		<span class="dd"><?php echo tribe_get_start_date( null, false, 'd' ) ?></span>
		<span class="yy"><?php echo tribe_get_start_date( null, false, 'Y' ) ?></span>
	</div>
	<?php if ( tokopress_get_event_cta_text() ) : ?>
		<div class="tribe-events-cta-btn">
			<a class="btn" href="<?php echo esc_url($cta_url); ?>" <?php if ( $cta_target ) echo 'target="'.$cta_target.'"'; ?> >
				<?php echo tokopress_get_event_cta_text(); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
