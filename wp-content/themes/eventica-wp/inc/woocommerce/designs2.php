<?php

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_woocommerce' );
function tokopress_customize_controls_colors_woocommerce( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_woocommerce',
		'title'    => esc_html__( 'WooCommerce', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_wc_saleflash_bg', 
		'label'		=> esc_html__( 'Sale Flash Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_woocommerce',
		'style'		=> '.woocommerce span.onsale, .woocommerce-page span.onsale { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_wc_upsells_title_bg', 
		'label'		=> esc_html__( 'Upsells - Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_woocommerce',
		'style'		=> '.woocommerce .upsells.products > h2, .woocommerce-page .upsells.products > h2 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_wc_related_title_bg', 
		'label'		=> esc_html__( 'Related Products - Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_woocommerce',
		'style'		=> '.woocommerce .related.products > h2, .woocommerce-page .related.products > h2 { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_wc_cart_table_bg', 
		'label'		=> esc_html__( 'Cart/Checkout - Table Header Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_woocommerce',
		'style'		=> '.woocommerce table.shop_table thead tr th, .woocommerce-page table.shop_table thead tr th { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_woocommerce_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.panel( \'tokopress_options_woocommerce\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_woocommerce',
	);

	return $controls;
}
