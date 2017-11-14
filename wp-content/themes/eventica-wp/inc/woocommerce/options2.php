<?php

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_options_woocommerce' );
function tokopress_customize_controls_options_woocommerce( $controls ) {

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'panel',
		'setting'  => 'tokopress_options_woocommerce',
		'title'    => esc_html__( 'TP - WooCommerce', 'tokopress' ),
		'priority' => 24,
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_wc_shop',
		'title'    => esc_html__( 'WC - Shop Page', 'tokopress' ),
		'panel'    => 'tokopress_options_woocommerce',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_options_wc_shop_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Shop Page', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Shop Page', 'tokopress' ) , esc_html__( 'Shop Page', 'tokopress' ) ),
		'section'	=> 'tokopress_options_wc_shop',
		'active_callback' => 'tokopress_wc_is_not_shop_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_shop_title', 
		'label'		=> esc_html__( 'DISABLE page title on main shop page.', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_shop_sidebar', 
		'label'		=> esc_html__( 'DISABLE sidebar on shop page.', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_catalog_ordering', 
		'label'		=> esc_html__( 'DISABLE catalog ordering dropdown', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'number',
		'setting'	=> 'tokopress_wc_products_per_page', 
		'label'		=> esc_html__( 'Products Per Page', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'select',
		'default'   => '2',
		'setting'	=> 'tokopress_wc_products_column_per_row', 
		'label'		=> esc_html__( 'Products Column Per Row', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
		'choices' 	=> array(
				'2' => '2',
				'3' => '3',
				'4' => '4'
			),
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_products_sale_flash', 
		'label'		=> esc_html__( 'DISABLE products sale flash', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_products_rating', 
		'label'		=> esc_html__( 'DISABLE products rating', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_products_price',
		'label'		=> esc_html__( 'DISABLE products price', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_products_cart_button',
		'label'		=> esc_html__( 'DISABLE products "add to cart" button', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_wc_shop_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_woocommerce\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_wc_shop',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_wc_product',
		'title'    => esc_html__( 'WC - Single Product', 'tokopress' ),
		'panel'    => 'tokopress_options_woocommerce',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_options_wc_product_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Single Product', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Single Product', 'tokopress' ) , esc_html__( 'Single Product', 'tokopress' ) ),
		'section'	=> 'tokopress_options_wc_product',
		'active_callback' => 'tokopress_wc_is_not_product_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_product_sidebar',
		'label'		=> esc_html__( 'DISABLE sidebar on single product page.', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_product',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'radio-buttonset',
		'setting'	=> 'tokopress_wc_product_image_layout',
		'label'		=> esc_html__( 'Product Image Layout', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_product',
		'default'	=> 'full',
		'choices' 	=> array(
				'full' => esc_html__( 'Full Width', 'tokopress' ),
				'half' => esc_html__( 'Half Width', 'tokopress' ),
			),
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_product_sale_flash',
		'label'		=> esc_html__( 'DISABLE product sale flash', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_product',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_product_price',
		'label'		=> esc_html__( 'DISABLE product price', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_product',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_product_cart_button',
		'label'		=> esc_html__( 'DISABLE product "add to cart" button', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_product',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_product_meta_tags',
		'label'		=> esc_html__( 'DISABLE product meta (categories/tags)', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_product',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_wc_product_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_woocommerce\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_wc_product',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_wc_related',
		'title'    => esc_html__( 'WC - Related Product', 'tokopress' ),
		'panel'    => 'tokopress_options_woocommerce',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_options_wc_related_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Single Product', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Single Product', 'tokopress' ) , esc_html__( 'Single Product', 'tokopress' ) ),
		'section'	=> 'tokopress_options_wc_related',
		'active_callback' => 'tokopress_wc_is_not_product_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_related_products',
		'label'		=> esc_html__( 'DISABLE related products on single product page', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_related',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'select',
		'setting'	=> 'tokopress_wc_products_related_column',
		'label'		=> esc_html__( 'Column Per Row', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_related',
		'default' 	=> '2',
		'choices' 	=> array(
				'2' => '2',
				'3' => '3',
				'4' => '4'
			),
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'select',
		'setting'	=> 'tokopress_wc_products_related_number',
		'label'		=> esc_html__( 'Number of Related Products', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_related',
		'default' 	=> '2',
		'choices' 	=> array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12'
			),
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_wc_related_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_woocommerce\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_wc_related',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_wc_upsells',
		'title'    => esc_html__( 'WC - Up-Sells Product', 'tokopress' ),
		'panel'    => 'tokopress_options_woocommerce',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_options_wc_upsells_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Single Product', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Single Product', 'tokopress' ) , esc_html__( 'Single Product', 'tokopress' ) ),
		'section'	=> 'tokopress_options_wc_upsells',
		'active_callback' => 'tokopress_wc_is_not_product_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_upsells_products',
		'label'		=> esc_html__( 'DISABLE up-sells products on single product page', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_upsells',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'select',
		'setting'	=> 'tokopress_wc_products_upsells_column',
		'label'		=> esc_html__( 'Column Per Row', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_upsells',
		'default' 	=> '2',
		'choices' 	=> array(
				'2' => '2',
				'3' => '3',
				'4' => '4'
			),
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'select',
		'setting'	=> 'tokopress_wc_products_upsells_number',
		'label'		=> esc_html__( 'Number of Up-Sells Products', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_upsells',
		'default' 	=> '2',
		'choices' 	=> array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12'
			),
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_wc_upsells_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_woocommerce\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_wc_upsells',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_wc_crosssells',
		'title'    => esc_html__( 'WC - Cross-Sells Product', 'tokopress' ),
		'panel'    => 'tokopress_options_woocommerce',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_options_wc_crosssells_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Cart Page', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Cart Page', 'tokopress' ) , esc_html__( 'Cart Page', 'tokopress' ) ),
		'section'	=> 'tokopress_options_wc_crosssells',
		'active_callback' => 'tokopress_wc_is_not_cart_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'checkbox',
		'setting'	=> 'tokopress_wc_hide_crosssells_products',
		'label'		=> esc_html__( 'DISABLE Cross-sells products on cart page', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_crosssells',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_options_wc_crosssells_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_colors_woocommerce\' ).focus();">'.esc_html__( 'Go to Theme Colors of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_options_wc_crosssells',
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'     => 'section',
		'setting'  => 'tokopress_options_wc_redirect',
		'title'    => esc_html__( 'WC - Redirect', 'tokopress' ),
		'panel'    => 'tokopress_options_woocommerce',
		'priority' => 10,
	);

	$controls[] = array(
		'setting_type' => 'option_mod',
		'type'		=> 'text',
		'setting'	=> 'tokopress_wc_red_cus_login',
		'label'		=> esc_html__( 'Redirect URL After Customer Login', 'tokopress' ),
		'section'	=> 'tokopress_options_wc_redirect',
	);

	return $controls;
}
