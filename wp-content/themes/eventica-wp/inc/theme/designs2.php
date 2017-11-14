<?php 

add_action( 'customize_register', 'tokopress_customize_reposition_colors', 20 );
function tokopress_customize_reposition_colors( $wp_customize ) {
	$wp_customize->get_section( 'colors' )->panel = 'tokopress_colors';	
	$wp_customize->get_section( 'colors' )->priority = 7;
	$wp_customize->get_section( 'colors' )->title = esc_html__( 'Basic Colors', 'tokopress' );

	$wp_customize->get_section( 'background_image' )->title = __( 'Background', 'tokopress' );
	$wp_customize->get_control( 'background_color' )->section = 'background_image';

	$wp_customize->remove_control('header_textcolor');
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_panel' );
function tokopress_customize_controls_colors_panel( $controls ) {

	$controls[] = array(
		'type'     => 'panel',
		'setting'  => 'tokopress_colors',
		'title'    => esc_html__( 'TP - Fonts & Colors', 'tokopress' ),
		'priority' => 21,
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_fonts' );
function tokopress_customize_controls_colors_fonts( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_fonts',
		'title'    => esc_html__( 'Basic Fonts', 'tokopress' ),
		'description' => esc_html__( 'We use the most popular google fonts here, 200 fonts, sorted by popularity. If your favourite google font is not here, please contact our support.', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 5,
	);

	$controls[] = array(
		'type'     => 'font',
		'setting'  => 'tokopress_font_body',
		'default'  => 'Noto Sans',
		'label'    => esc_html__( 'Body Font', 'tokopress' ),
		'section'  => 'tokopress_fonts',
		'selector' => 'body,.tribe-events-list .event-list-wrapper-bottom .wraper-bottom-left h2.tribe-events-list-event-title,.home-upcoming-events .upcoming-event-title,.home-recent-posts .recent-post-title,.home-featured-event .featured-event-title h2',
	);

	$controls[] = array(
		'type'     => 'font-size',
		'setting'  => 'tokopress_fontsize_body',
		'label'    => esc_html__( 'Body Font Size', 'tokopress' ),
		'section'  => 'tokopress_fonts',
		'choices'  => array(
			'max' => 50,
		),
		'style'    => 'body, .blog-list .post-inner .post-title h2 { font-size: [value]px }',
	);

	$controls[] = array(
		'type'     => 'font',
		'setting'  => 'tokopress_font_heading',
		'default'  => 'Raleway',
		'label'    => esc_html__( 'Heading Font', 'tokopress' ),
		'section'  => 'tokopress_fonts',
		'selector' => 'h1,h2,h3,h4,h5,.header-menu.sf-menu li a,.page-title .breadcrumb',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_basic' );
function tokopress_customize_controls_colors_basic( $controls ) {

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_content_text_color', 
		'label'		=> esc_html__( 'Body Text Color', 'tokopress' ),
		'section'	=> 'colors',
		'style'		=> 'body { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_content_link_color', 
		'label'		=> esc_html__( 'Link Color', 'tokopress' ),
		'section'	=> 'colors',
		'style'		=> 'a { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_content_link_color_hover', 
		'label'		=> esc_html__( 'Link Color (Hover)', 'tokopress' ),
		'section'	=> 'colors',
		'style'		=> 'a:hover { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_content_line_color', 
		'label'		=> esc_html__( 'Line/Border Color', 'tokopress' ),
		'section'	=> 'colors',
		'style'		=> '.blog-single .post-summary, .blog-single .post-meta ul li, #comments .commentslist-wrap, #comments .commentlist li.comment, #comments-block #respond, #tribe-events-content.tribe-events-single .events-single-left, #tribe-events-content.tribe-events-single .tribe-events-meta-group-details, #tribe-events-content.tribe-events-single .tribe-events-meta-group-venue, #tribe-events-content.tribe-events-single .tribe-events-meta-group-schedule, #tribe-events-content.tribe-events-single .tribe-events-meta-group-custom, #tribe-events-content.tribe-events-single .tribe-events-meta-group-organizer, .woocommerce div.product div.summary, .woocommerce-page div.product div.summary, .woocommerce div.product div.woocommerce-tabs, .woocommerce-page div.product div.woocommerce-tabs, .home-subscribe-form .form.mc4wp-form input[type="email"] { border-color: [value]; }',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_headertop' );
function tokopress_customize_controls_colors_headertop( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_headertop',
		'title'    => esc_html__( 'Header - Top (Logo & Menu)', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_bg', 
		'label'		=> esc_html__( 'Header Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.site-header { background: [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_headertop_menu_heading', 
		'label'		=> esc_html__( 'Site Menu', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_menu_color', 
		'label'		=> esc_html__( 'Menu Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.header-menu.sf-menu li a, .header-menu.sf-menu li a:visited, .mobile-menu a, .mobile-menu a:visited { color: [value]; } @media (max-width: 767px) { .header-menu.sf-menu li a, .header-menu.sf-menu li a:visited, .mobile-menu a, .mobile-menu a:visited { color: #ffffff; } }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_menu_color_hover', 
		'label'		=> esc_html__( 'Menu Color (Hover)', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.header-menu.sf-menu li a:hover, .mobile-menu a:hover { color: [value]; } @media (max-width: 767px) { .header-menu.sf-menu li a:hover, .mobile-menu a:hover { color: #ffffff; } }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_submenu_bg', 
		'label'		=> esc_html__( 'Submenu Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.header-menu.sf-menu li li a, .header-menu.sf-menu li li a:visited { background: [value]; }',
	);

	$controls[] = array( 
		'type'		=> 'color',
		'setting'	=> 'tokopress_headertop_submenu_bg_hover', 
		'label'		=> esc_html__( 'Submenu Background (Hover)', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.header-menu.sf-menu li li a:hover { background: [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_headertop_mobilemenu_heading', 
		'label'		=> esc_html__( 'Mobile Menu', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_mobilemenu_color', 
		'label'		=> esc_html__( 'Mobile Menu Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> ' @media (max-width: 767px) { .header-menu.sf-menu li a, .header-menu.sf-menu li a:visited, .mobile-menu a, .mobile-menu a:visited { color: [value] !important; } }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_mobilemenu_color_hover', 
		'label'		=> esc_html__( 'Mobile Menu Color (Hover)', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> ' @media (max-width: 767px) { .header-menu.sf-menu li a:hover, .mobile-menu a:hover { color: [value] !important; } }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_offcanvas_bg', 
		'label'		=> esc_html__( 'Off-canvas Menu Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.sb-slidebar { background: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_offcanvas_color', 
		'label'		=> esc_html__( 'Off-canvas Menu Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.menu-slidebar a { color: [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_headertop_logo_heading', 
		'label'		=> esc_html__( 'Site Logo', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_logo_bg', 
		'label'		=> esc_html__( 'Logo Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.site-branding { background: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_headertop_logo_color', 
		'label'		=> esc_html__( 'Logo Text Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_headertop',
		'style'		=> '.site-branding a, .site-logo p { color: [value]; } @media (max-width: 767px) { .header-menu.sf-menu li a, .header-menu.sf-menu li a:visited, .mobile-menu a, .mobile-menu a:visited, .header-menu.sf-menu li a:hover, .mobile-menu a:hover { color: [value] !important; } }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_headertop_goto_options', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-tools"></span> <a href="javascript:wp.customize.section( \'tokopress_options_headertop\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_headertop',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_headerpagetitle' );
function tokopress_customize_controls_colors_headerpagetitle( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_pagetitle',
		'title'    => esc_html__( 'Header - Page Title', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_page_title_heading', 
		'label'		=> esc_html__( 'Page Title', 'tokopress' ),
		'section'	=> 'tokopress_colors_pagetitle',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_header_bg', 
		'label'		=> esc_html__( 'Page Title Background', 'tokopress' ),
        'description' => esc_html__( 'If page title background image is not available', 'tokopress' ),
		'section'	=> 'tokopress_colors_pagetitle',
		'style'		=> '.page-title { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_header_color', 
		'label'		=> esc_html__( 'Page Title Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_pagetitle',
		'style'		=> '.page-title, .page-title h1 { color: [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_breadcrumb_heading', 
		'label'		=> esc_html__( 'Breadcrumb', 'tokopress' ),
		'section'	=> 'tokopress_colors_pagetitle',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_header_breadcrumb_color', 
		'label'		=> esc_html__( 'Breadcrumb Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_pagetitle',
		'style'		=> '.page-title .breadcrumb, .page-title .breadcrumb a { color: [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_pagetitle_goto_options', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-tools"></span> <a href="javascript:wp.customize.section( \'header_image\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_pagetitle',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_footerwidgets' );
function tokopress_customize_controls_colors_footerwidgets( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_footerwidgets',
		'title'    => esc_html__( 'Footer - Widgets', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footerwidget_bg', 
		'label'		=> esc_html__( 'Footer Widget Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footerwidget_color', 
		'label'		=> esc_html__( 'Footer Widget Text Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget, #footer-widget .widget .widget-inner, #footer-widget .widget.widget_recent_posts ul li .tp-entry-date, #footer-widget .widget.widget_upcoming_events ul li .tp-entry-date, #footer-widget .widget.widget_past_events ul li .tp-entry-date { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footerwidget_link_color', 
		'label'		=> esc_html__( 'Footer Widget Link Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget a { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footerwidget_link_color_hover', 
		'label'		=> esc_html__( 'Footer Widget Link Color (Hover)', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget a:hover { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footerwidget_title_color', 
		'label'		=> esc_html__( 'Footer Widget Title Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget .widget .widget-title { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footer_widget_border_color', 
		'label'		=> esc_html__( 'Footer Widget Line Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget .widget.widget_recent_posts ul li, #footer-widget .widget.widget_upcoming_events ul li, #footer-widget .widget.widget_past_events ul li { border-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footer_widget_border_top_color', 
		'label'		=> esc_html__( 'Footer Widget Border Top Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footerwidgets',
		'style'		=> '#footer-widget { border-top: 1px solid [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_footerwidgets_goto_options', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-tools"></span> <a href="javascript:wp.customize.section( \'tokopress_options_footerwidgets\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p><p><span class="dashicons dashicons-admin-generic"></span> <a href="javascript:wp.customize.panel( \'widgets\' ).focus();">'.esc_html__( 'Setup Footer Widgets', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_footerwidgets',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_footercredits' );
function tokopress_customize_controls_colors_footercredits( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_footercredits',
		'title'    => esc_html__( 'Footer - Credits', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footercredit_bg', 
		'label'		=> esc_html__( 'Footer Credit Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_footercredits',
		'style'		=> '#footer-block { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footercredit_color', 
		'label'		=> esc_html__( 'Footer Credit Text Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footercredits',
		'style'		=> '#footer-block, #footer-block .footer-credit p { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footercredit_link_color', 
		'label'		=> esc_html__( 'Footer Credit Link Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footercredits',
		'style'		=> '#footer-block, #footer-block #footer-menu #secondary-menu ul.footer-menu li a, #footer-block #footer-menu ul#social-icon li a, #footer-block .footer-credit p a { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_footercredit_link_color_hover', 
		'label'		=> esc_html__( 'Footer Credit Link Color (Hover)', 'tokopress' ),
		'section'	=> 'tokopress_colors_footercredits',
		'style'		=> '#footer-block #footer-menu #secondary-menu ul.footer-menu li a:hover { color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_backtotop_background', 
		'label'		=> esc_html__( 'Back To Top Background Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_footercredits',
		'style'		=> '#back-top, #back-top:hover { background-color: [value]; }',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_footercredits_goto_options', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-tools"></span> <a href="javascript:wp.customize.section( \'tokopress_options_footercredits\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_footercredits',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_backtotop' );
function tokopress_customize_controls_colors_backtotop( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_backtotop',
		'title'    => esc_html__( 'Footer - Back To Top', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_backtotop_background', 
		'label'		=> esc_html__( 'Back To Top Background Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_backtotop',
		'style'		=> '#back-top, #back-top:hover { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_backtotop_color', 
		'label'		=> esc_html__( 'Back To Top Icon Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_backtotop',
		'style'		=> '#back-top, #back-top:hover { color: [value]; }',
	);

	return $controls;
}

add_filter( 'tokopress_customize_controls', 'tokopress_customize_controls_colors_pagetemplates_home' );
function tokopress_customize_controls_colors_pagetemplates_home( $controls ) {

	$controls[] = array(
		'type'     => 'section',
		'setting'  => 'tokopress_colors_home_templates',
		'title'    => esc_html__( 'Homepage Page Template', 'tokopress' ),
		'panel'    => 'tokopress_colors',
		'priority' => 10,
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'warning',
		'setting'	=> 'tokopress_colors_home_warning', 
		'label'		=> sprintf( esc_html__( 'You are not in %s', 'tokopress' ), esc_html__( 'Page with Homepage Page Template', 'tokopress' ) ),
		'description' => sprintf( esc_html__( 'These settings only affect %s. Please visit %s to see live preview for these settings.', 'tokopress' ), esc_html__( 'Page with Homepage Page Template', 'tokopress' ) , esc_html__( 'Page with Homepage Page Template', 'tokopress' ) ),
		'section'	=> 'tokopress_colors_home_templates',
		'active_callback' => 'tokopress_is_not_home_page',
	);

	$controls[] = array( 
		'setting_type' => 'option_mod',
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_home_heading_desc', 
		'label'		=> '',
		'description' => esc_html__( 'These options are for everyone who use Homepage Page Template. If you use Visual Composer to build your homepage, please IGNORE these options.', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_home_heading_slider', 
		'label'		=> esc_html__( 'Events Slider', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_home_slider_detail_bg', 
		'label'		=> esc_html__( 'Events Slider - Detail Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
		'style'		=> '.home-slider-events .slide-event-detail { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_home_heading_upcoming_events', 
		'label'		=> esc_html__( 'Upcoming Events', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_home_upcoming_bg', 
		'label'		=> esc_html__( 'Upcoming Events - Background Color', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
		'style'		=> '.page-template-page_home_event-php .home-upcoming-events { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'image',
		'setting'	=> 'tokopress_home_upcoming_bg_img', 
		'label'		=> esc_html__( 'Upcoming Events - Background Image', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
		'style'		=> '.page-template-page_home_event-php .home-upcoming-events { background-image: url("[value]"); }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_home_heading_featured_event', 
		'label'		=> esc_html__( 'Featured Single Event', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
	);

	$controls[] = array( 
		'type' 		=> 'color',
		'setting'	=> 'tokopress_home_featured_title_bg', 
		'label'		=> esc_html__( 'Featured Event - Title Background', 'tokopress' ),
		'section'	=> 'tokopress_colors_home_templates',
		'style'		=> '.home-featured-event .featured-event-title { background-color: [value]; }',
	);

	$controls[] = array( 
		'type' 		=> 'heading',
		'setting'	=> 'tokopress_colors_home_templates_goto_colors', 
		'label'		=> esc_html__( 'More ...', 'tokopress' ),
		'description' => '<p><span class="dashicons dashicons-admin-appearance"></span> <a href="javascript:wp.customize.section( \'tokopress_options_home_templates\' ).focus();">'.esc_html__( 'Go to Theme Options of this section', 'tokopress' ).'</a></p>',
		'section'	=> 'tokopress_colors_home_templates',
	);

	return $controls;
}
