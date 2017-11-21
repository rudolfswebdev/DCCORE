<?php
/*
*		Plugin Name: WP GoToWebinar
*		Plugin URI: https://www.northernbeacheswebsites.com.au
*		Description: Show upcoming GoToWebinars on any post or page or in a widget and register users on your website. 
*		Version: 11.1
*		Author: Martin Gibson
*		Author URI:  https://www.northernbeacheswebsites.com.au
*		Text Domain: wp-gotowebinar   
*		Support: https://www.northernbeacheswebsites.com.au/contact
*		Licence: GPL2
*/

// Assign global variables
global $gotowebinar_is_pro;
$gotowebinar_is_pro = "NO";


//the first YES/NO in the array is if the feature is pro, the second YES/NO in the array is if a save settings button is necessary
global $gotowebinar_pro_features;
$gotowebinar_pro_features = array('General Options' => array('NO','YES'), 'Translation' => array('NO','YES'), 'Clear Cache' => array('NO','NO'), 'FAQ' => array('NO','NO'), 'Support' => array('NO','NO'), 'Log' => array('NO','NO'), 'Webinar Product Manager' => array('YES','NO'), 'Create a Webinar' => array('YES','NO'), 'Integration' => array('YES','YES'), 'Pro Options' => array('YES','YES'), 'Performance' => array('YES','NO'), 'Toolbar Countdown' => array('YES','YES'));

global $time_zone_list;
$time_zone_list = array("Pacific/Tongatapu"=>13, "Pacific/Fiji"=>12, "Pacific/Auckland"=>12, "Asia/Magadan"=>11, "Asia/Vladivostok"=>10, "Australia/Hobart"=>10, "Pacific/Guam"=>10, "Australia/Sydney"=>10, "Australia/Brisbane"=>10, "Australia/Darwin"=>9.5, "Australia/Adelaide"=>9.5, "Asia/Yakutsk"=>9, "Asia/Seoul"=>9, "Asia/Tokyo"=>9, "Asia/Taipei"=>8, "Australia/Perth"=>8, "Asia/Singapore"=>8, "Asia/Irkutsk"=>8, "Asia/Shanghai"=>8, "Asia/Krasnoyarsk"=>7, "Asia/Bangkok"=>7, "Asia/Jakarta"=>7, "Asia/Rangoon"=>6.5, "Asia/Colombo"=>6, "Asia/Dhaka"=>6, "Asia/Novosibirsk"=>6, "Asia/Katmandu"=>5.75, "Asia/Calcutta"=>5.5, "Asia/Karachi"=>5, "Asia/Yekaterinburg"=>5, "Asia/Kabul"=>4.5, "Asia/Tbilisi"=>4, "Asia/Muscat"=>4, "Asia/Tehran"=>3.5, "Africa/Nairobi"=>3, "Europe/Moscow"=>3, "Asia/Kuwait"=>3, "Asia/Baghdad"=>3, "Asia/Jerusalem"=>2, "Europe/Helsinki"=>2, "Africa/Harare"=>2, "Africa/Cairo"=>2, "Europe/Bucharest"=>2, "Europe/Athens"=>2, "Africa/Malabo"=>1, "Europe/Warsaw"=>1, "Europe/Brussels"=>1, "Europe/Prague"=>1, "Europe/Amsterdam"=>1, "GMT"=>0, "Europe/London"=>0, "Africa/Casablanca"=>0, "Atlantic/Cape_Verde"=>-1, "Atlantic/Azores"=>-1, "America/Buenos_Aires"=>-3, "America/Sao_Paulo"=>-3, "America/St_Johns"=>-3, "America/Santiago"=>-4, "America/Caracas"=>-4, "America/Halifax"=>-4, "America/Indianapolis"=>-5, "America/New_York"=>-5, "America/Bogota"=>-5, "America/Mexico_City"=>-6, "America/Chicago"=>-6, "America/Denver"=>-7, "America/Phoenix"=>-7, "America/Los_Angeles"=>-8, "America/Anchorage"=>-9, "Pacific/Honolulu"=>-10, "MIT"=>-11);

//get plugin version number
function wpgotowebinar_plugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

//disable updates if pro version
function wp_gotowebinar_disable_updates( $value ) {
    global $gotowebinar_is_pro;
    if(isset($value->response['wp-gotowebinar/wp-gotowebinar.php']) && $gotowebinar_is_pro == "YES"){        
        unset($value->response['wp-gotowebinar/wp-gotowebinar.php']);
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'wp_gotowebinar_disable_updates' );

// Add a link to our plugin in the admin menu under Settings > GoToWebinar
function wp_gotowebinar_wp_menu() {
    global $gotowebinar_wp_settings_page;
    $gotowebinar_wp_settings_page = add_options_page(
        'WP GoToWebinar Options',
        'WP GoToWebinar',
        'manage_options',
        'wp-gotowebinar',
        'wp_gotowebinar_options_page'    
    );
}
add_action('admin_menu','wp_gotowebinar_wp_menu');
add_action( 'admin_init', 'wp_gotowebinar_settings_init' );


//add a settings link on the plugin page
function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=wp-gotowebinar">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

//add custom links to plugin on plugins page
function wp_gotowebinar_custom_plugin_row_meta( $links, $file ) {
   if ( strpos( $file, 'wp-gotowebinar.php' ) !== false ) {
      $new_links = array(
               '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VGVE97KF74FVN" target="_blank">' . __('Donate') . '</a>',
               '<a href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/" target="_blank">' . __('Pro Version') . '</a>',
               '<a href="http://wordpress.org/support/plugin/wp-gotowebinar" target="_blank">' . __('Support Forum') . '</a>',
            );
      $links = array_merge( $links, $new_links );
   }
   return $links;
}
add_filter( 'plugin_row_meta', 'wp_gotowebinar_custom_plugin_row_meta', 10, 2 );

//Gets, sets and renders options
require('inc/options-output.php');

// Create our main options page
function wp_gotowebinar_options_page(){
    require('inc/options-page-wrapper.php');
}


//get translations from plugin folder
add_action('plugins_loaded', 'wp_gotowebinar_translations');
function wp_gotowebinar_translations() {
	load_plugin_textdomain( 'wp-gotowebinar', false, dirname( plugin_basename(__FILE__) ) . '/inc/lang/' );
}


//common function to get upcoming webinars
function wp_gotowebinar_upcoming_webinars($transientName, $transientDuration){
    //get options
    $options = get_option('gotowebinar_settings');
    //get transient
    $getTransient = get_transient($transientName);
    //if transient doesn't exist or caching disabled do this
    if ($getTransient != false && !isset($options['gotowebinar_disable_cache'])){
        $jsondata = $getTransient; 
        $json_response = 200;
        return array($jsondata,$json_response);
    } //otherwise do this 
    else { 
        $json_feed = wp_remote_get( 'https://api.getgo.com/G2W/rest/organizers/'.$options['gotowebinar_organizer_key'].'/upcomingWebinars', array(
        'headers' => array(
        'Authorization' => $options['gotowebinar_authorization'],
	    ),));
        //store the data and response in a variable
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $json_feed )), true);
        $json_response = wp_remote_retrieve_response_code($json_feed);    
        //if response is successful set the transient    
        if($json_response == 200){    
        set_transient($transientName,$jsondata, $transientDuration);  
        }   
        //return the data and response
        return array($jsondata,$json_response);
    } //end else  
} //end function


//function to check authentication status
function wp_gotowebinar_authentication_check(){
    //get options
    $options = get_option('gotowebinar_settings');
        $json_feed = wp_remote_get( 'https://api.getgo.com/G2W/rest/organizers/'.$options['gotowebinar_organizer_key'].'/upcomingWebinars', array(
        'headers' => array(
        'Authorization' => $options['gotowebinar_authorization'],
	    ),));
        //store the data and response in a variable
        $json_response = wp_remote_retrieve_response_code($json_feed);    
        //return the data and response
        return $json_response;
} //end function


// Add shortcode
if(file_exists(get_stylesheet_directory().'/wp-gotowebinar/shortcode.php')) {
require(get_stylesheet_directory().'/wp-gotowebinar/shortcode.php');      
} else {
require('inc/shortcode.php');    
}

// Add registration shortcode
if(file_exists(get_stylesheet_directory().'/wp-gotowebinar/shortcode-registration.php')) {
require(get_stylesheet_directory().'/wp-gotowebinar/shortcode-registration.php');      
} else {
require('inc/shortcode-registration.php');   
}

// Add calendar shortcode
if(file_exists(get_stylesheet_directory().'/wp-gotowebinar/shortcode-calendar.php')) {
require(get_stylesheet_directory().'/wp-gotowebinar/shortcode-calendar.php');      
} else {
require('inc/shortcode-calendar.php');   
}

// Add widget
require('inc/widget.php');


// Load front end style and scripts
function wp_gotowebinar_register_frontend() { 
    $options = get_option('gotowebinar_settings');
    wp_register_style( 'full-calendar-style', plugins_url('/inc/external/fullcalendar.min.css', __FILE__ ) );
    wp_register_style( 'font-awesome-icons', plugins_url('/inc/external/font-awesome.min.css', __FILE__ ) );
    wp_register_style( 'custom-style', plugins_url( '/inc/style.css', __FILE__ ));
    wp_register_script( 'moment', plugins_url('/inc/external/moment.js', __FILE__ ), array( 'jquery' )); 
    wp_register_script( 'moment-timezone', plugins_url('/inc/external/moment-timezone-with-data.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'full-calendar', plugins_url('/inc/external/fullcalendar.min.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'full-calendar-locale', plugins_url('/inc/external/locale-all.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js'); 
    wp_enqueue_script('jquery-ui', plugins_url('/inc/external/jquery-ui.min.js', __FILE__ ), array( 'jquery'), '1.12.1');
    wp_register_script( 'custom-script', plugins_url( '/inc/script.js', __FILE__ ), array( 'jquery' ),1.1,true);
    wp_localize_script( 'custom-script', 'registration_form_submit', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_localize_script( 'custom-script', 'integration_post', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    
    wp_enqueue_script( array('moment','moment-timezone','full-calendar','full-calendar-locale','custom-script','google-recaptcha'));
    wp_enqueue_style( array('font-awesome-icons','full-calendar-style','custom-style') );   
    if($options['gotowebinar_button_background_color'] == "#ffffff"){
    $spinnerColor = $options['gotowebinar_button_text_color'];
    } else {
     $spinnerColor = $options['gotowebinar_button_background_color'];   
    }
    $colour_options = "
    .tooltip {
	background-color: {$options['gotowebinar_tooltip_background_color']};
	color: {$options['gotowebinar_tooltip_text_color']};
    border-color: {$options['gotowebinar_tooltip_border_color']};
    }
    .webinar-registration input[type=\"submit\"] {
    background-color: {$options['gotowebinar_button_background_color']};
	color: {$options['gotowebinar_button_text_color']};
    border-color: {$options['gotowebinar_button_border_color']};
    }
    .webinar-registration .fa-spinner {
    color: {$spinnerColor};
    }
    .upcoming-webinars fa, .upcoming-webinars a, .upcoming-webinars-widget fa, .upcoming-webinars-widget a, .webinar-registration a {
    color: {$options['gotowebinar_icon_color']};
    } 
    ";
    wp_add_inline_style( 'custom-style', $colour_options );
    
    global $gotowebinar_is_pro;
    //only output this scrip if pro and if toolbar is activated
    if ($gotowebinar_is_pro == "YES" && isset($options['gotowebinar_toolbar_activate'])){ 
        
        wp_enqueue_script( 'flipclock', plugins_url( '/inc/external/flipclock.min.js', __FILE__ ), array('jquery'));
        wp_enqueue_style( 'flipclockstyle', plugins_url( '/inc/external/flipclock.css', __FILE__ ));
        wp_enqueue_script( 'jquerycookie', plugins_url( '/inc/external/jquery.cookie.js', __FILE__ ), array('jquery'));
        
        
        
        
    }
    
  
}
add_action( 'wp_enqueue_scripts', 'wp_gotowebinar_register_frontend' );



// Load admin style and scripts
function wp_gotowebinar_register_admin($hook)
{
    wp_enqueue_style( 'visual-composer-style', plugins_url( '/inc/vc-adminstyle.css', __FILE__ ));
    global $gotowebinar_wp_settings_page;
    
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'product' === $post->post_type ) {     
            wp_enqueue_script( 'custom-admin-script-pro', plugins_url( '/inc/pro/adminscriptpro.js', __FILE__ ), array( 'jquery'));
        }
    }
    
    if($hook != $gotowebinar_wp_settings_page)
        return;
    
    
    wp_enqueue_script('time-picker', plugins_url('/inc/external/jquery.timepicker.min.js', __FILE__ ), array('jquery'));
    
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'custom-admin-script', plugins_url( '/inc/adminscript.js', __FILE__ ), array( 'jquery','wp-color-picker' ));
    wp_enqueue_script('jquery-ui', plugins_url('/inc/external/jquery-ui.min.js', __FILE__ ), array( 'jquery'), '1.12.1');
    wp_enqueue_script('jquery-form');
    wp_enqueue_script('jquery-effects-shake');
    wp_enqueue_script('chart','https://www.gstatic.com/charts/loader.js');
    wp_enqueue_style( 'custom-admin-style', plugins_url( '/inc/adminstyle.css', __FILE__ ));
    wp_register_style( 'font-awesome-icons', plugins_url('/inc/external/font-awesome.min.css', __FILE__ ) );
    wp_register_style( 'time-picker-style', plugins_url('/inc/external/jquery.timepicker.min.css', __FILE__ ) );
    wp_enqueue_style( array('font-awesome-icons','time-picker-style') );
    
    wp_enqueue_script( 'moment', plugins_url('/inc/external/moment.js', __FILE__ ), array( 'jquery' )); 
    wp_enqueue_script( 'moment-timezone', plugins_url('/inc/external/moment-timezone-with-data.js', __FILE__ ), array( 'jquery' ));
    
}
add_action( 'admin_enqueue_scripts', 'wp_gotowebinar_register_admin' );


// Include pro functions
if ($gotowebinar_is_pro == "YES"){ 
include('inc/pro/pro.php');
include('inc/pro/options-output-pro.php');
} 
//clear cache and deactivation tasks
require('inc/clear-cache.php');
// add visual composer functionality
require('inc/visual-composer.php');
// add registration function
require('inc/registration.php');

//function to save dismiss welcome notice

function wp_gotowebinar_disable_welcome_message_callback() {

$gotowebinar_options = get_option('gotowebinar_settings');
$gotowebinar_options['gotowebinar_welcome_message'] = 1;   
     
update_option('gotowebinar_settings', $gotowebinar_options);        
wp_die(); 
    
}
add_action( 'wp_ajax_disable_welcome_message', 'wp_gotowebinar_disable_welcome_message_callback' );





//functions to register tinymce features
function wp_gotowebinar_register_plugin( $plugin_array ) {
    $plugin_array['wpgotowebinar_button'] = plugins_url('/inc/tinymce.js', __FILE__ );
    return $plugin_array;
}

function wp_gotowebinar_register_buttons($button) {
    array_push($button, 'wpgotowebinar_button'); 
    return $button;
}

function wp_gotowebinar_mce() {
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
        return;
    }
    
    if ('true' == get_user_option( 'rich_editing')){
        add_filter( "mce_external_plugins", "wp_gotowebinar_register_plugin" );
        add_filter( 'mce_buttons', 'wp_gotowebinar_register_buttons' );
    }   
}
add_action('init','wp_gotowebinar_mce');






//get timezones
function wp_gotowebinar_get_timezones() {
    global $time_zone_list;    
    
    $list = array();
    
    $list[] = array(
            'text' =>	'',
			'value'	=>	''
        );
    
    foreach($time_zone_list as $key => $value) {
        $list[] = array(
            'text' =>	$key,
			'value'	=>	$key
        );
    } 
    
    wp_send_json($list);
    wp_die();
}

function wp_gotowebinar_get_timezones_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_timezones();
}
add_action( 'wp_ajax_get_timezones_list', 'wp_gotowebinar_get_timezones_ajax' );



//get upcoming webinars
function wp_gotowebinar_get_webinars() {
    
    $options = get_option('gotowebinar_settings');
    
    list($jsondata,$json_response) = wp_gotowebinar_upcoming_webinars('gtw_key_vc', 604800);
    
    $list = array();
    
    $list[] = array(
            'text' =>	'Use most upcoming webinar',
			'value'	=>	'upcoming'
        );
    
    if($json_response == 200){  
        foreach ($jsondata as $data) {
            
            foreach($data['times'] as $mytimes) {
                $date = new DateTime($mytimes['startTime']); 
                $startTime = $date->format($options['gotowebinar_date_format']);    
            }
            
            $list[] = array(
            'text' =>	$data['subject'].' ('.$startTime.')',
			'value'	=>	$data['webinarKey']
            );    
        } 
    }
    
    wp_send_json($list);
    wp_die();
 
}

function wp_gotowebinar_get_webinars_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_webinars();
}
add_action( 'wp_ajax_get_webinars_list', 'wp_gotowebinar_get_webinars_ajax' );





//get mailchimp lists
function wp_gotowebinar_get_mailchimp() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_mailchimp_api'])){

        list($jsondata,$json_response) = wp_gotowebinar_mailchimp_list_hint(); 

        if (200 == $json_response) {
            
            $lists = array();
            
            foreach($jsondata['lists'] as $list){
                
                $lists[] = array(
                    'text' =>	$list['name'],
                    'value'	=>	$list['id']
                );
            }
            
            wp_send_json($lists);
            wp_die();

        }
    }
}

function wp_gotowebinar_get_mailchimp_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_mailchimp();
}
add_action( 'wp_ajax_get_mailchimp_list', 'wp_gotowebinar_get_mailchimp_ajax');





//get constantcontact lists
function wp_gotowebinar_get_constantcontact() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_constantcontact_token'])){

        list($jsondata,$json_response) = wp_gotowebinar_constantcontact_list_hint(); 

        if (200 == $json_response) {
            
            $lists = array();
            
            foreach($jsondata as $list){
                
                $lists[] = array(
                    'text' =>	$list['name'],
                    'value'	=>	$list['id']
                );
            }
            
            wp_send_json($lists);
            wp_die();

        }
    }
}

function wp_gotowebinar_get_constantcontact_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_constantcontact();
}
add_action( 'wp_ajax_get_constantcontact_list', 'wp_gotowebinar_get_constantcontact_ajax');





//get activecampaign lists
function wp_gotowebinar_get_activecampaign() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_activecampaign_account'])){

        list($jsondata,$json_response) = wp_gotowebinar_activecampaign_list_hint(); 

        if (200 == $json_response) {
            
            $lists = array();
            
            foreach($jsondata as $list){
                if (is_array($list) && isset($list['name'])) {
                    $lists[] = array(
                        'text' =>	$list['name'],
                        'value'	=>	$list['id']
                    );
                }
            }
            
            wp_send_json($lists);
            wp_die();

        }
    }
}

function wp_gotowebinar_get_activecampaign_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_activecampaign();
}
add_action( 'wp_ajax_get_activecampaign_list', 'wp_gotowebinar_get_activecampaign_ajax');







//get campaignmonitor lists
function wp_gotowebinar_get_campaignmonitor() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_campaignmonitor_client_id'])){

        list($jsondata,$json_response) = wp_gotowebinar_campaignmonitor_list_hint(); 

        if (200 == $json_response) {
            
            $lists = array();
            
            foreach($jsondata as $list){
                if (is_array($list) && isset($list['Name'])) {
                    $lists[] = array(
                        'text' =>	$list['Name'],
                        'value'	=>	$list['ListID']
                    );
                }
            }
            
            wp_send_json($lists);
            wp_die();

        }
    }
}

function wp_gotowebinar_get_campaignmonitor_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_campaignmonitor();
}
add_action( 'wp_ajax_get_campaignmonitor_list', 'wp_gotowebinar_get_campaignmonitor_ajax');









//get aweber lists
function wp_gotowebinar_get_aweber() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_aweber_token'])){

        list($jsondata,$json_response) = wp_gotowebinar_aweber_list_hint(); 

        if (200 == $json_response) {
            
            $lists = array();
            
            foreach($jsondata['entries'] as $key => $list){

                $lists[] = array(
                    'text' =>	$list['name'],
                    'value'	=>	$list['id']
                );

            }
            
            wp_send_json($lists);
            wp_die();

        }
    }
}

function wp_gotowebinar_get_aweber_ajax() {
	// check for nonce
	check_ajax_referer( 'wpgotowebinar-nonce', 'security' );
	return wp_gotowebinar_get_aweber();
}
add_action( 'wp_ajax_get_aweber_list', 'wp_gotowebinar_get_aweber_ajax');







function wp_gotowebinar_send_tinymce_data() {
	// create nonce
	global $pagenow;
	if( $pagenow != 'admin.php' ){
		$nonce = wp_create_nonce( 'wpgotowebinar-nonce' );
		?><script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				var data = {
					'action'	: 'get_timezones_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.timezoneList = response;
							}
						}
			  		}
			  	});
                var data = {
					'action'	: 'get_webinars_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.webinarList = response;
							}
						}
			  		}
			  	});
                var data = {
					'action'	: 'get_mailchimp_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.mailchimpList = response;
							}
						}
			  		}
			  	});
                var data = {
					'action'	: 'get_constantcontact_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.constantcontactList = response;
							}
						}
			  		}
			  	});
                var data = {
					'action'	: 'get_activecampaign_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.activecampaignList = response;
							}
						}
			  		}
			  	});
                var data = {
					'action'	: 'get_campaignmonitor_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.campaignmonitorList = response;
							}
						}
			  		}
			  	});
                var data = {
					'action'	: 'get_aweber_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post(ajaxurl, data, function(response) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.aweberList = response;
							}
						}
			  		}
			  	});
                
			});
		</script>
<?php 
	}
}
add_action('admin_footer','wp_gotowebinar_send_tinymce_data');











//get email service list hints for use in tinymce and visual composer shortcode builder and also in pro settings
function wp_gotowebinar_mailchimp_list_hint() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_mailchimp_api'])){
    
        $serverCenter = substr($options['gotowebinar_mailchimp_api'], strpos($options['gotowebinar_mailchimp_api'],'-')+1);
    
        $response = wp_remote_get( 'https://'.$serverCenter.'.api.mailchimp.com/3.0/lists', array(
            'headers' => array(
                'Authorization' => 'Basic '. base64_encode('anystring:'.$options['gotowebinar_mailchimp_api']),
            ),
        ));
        
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $response )), true);
        $json_response = wp_remote_retrieve_response_code($response);
        
        return array($jsondata,$json_response);
        
    }
}

function wp_gotowebinar_constantcontact_list_hint() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_constantcontact_token'])){
        
        $response = wp_remote_get('https://api.constantcontact.com/v2/lists?api_key=me68vunsy43cw654ydm2tucf', array(
            'headers' => array(
                'Authorization' => 'Bearer '.$options['gotowebinar_constantcontact_token'],
            ),
        ));
        
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $response )), true);
        $json_response = wp_remote_retrieve_response_code($response);
        
        return array($jsondata,$json_response);
        
    }
}

function wp_gotowebinar_activecampaign_list_hint() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_activecampaign_account'])){
        
        $response = wp_remote_get($options['gotowebinar_activecampaign_account'].'/admin/api.php?api_action=list_list&api_key='.$options['gotowebinar_activecampaign_api'].'&ids=all&api_output=json', array(
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Content-Type' => 'application/json; charset=utf-8',
                    )
                ));
        
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $response )), true);
        $json_response = wp_remote_retrieve_response_code($response);
        
        return array($jsondata,$json_response);
        
    }
}


function wp_gotowebinar_campaignmonitor_list_hint() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_campaignmonitor_client_id'])){
        
        $response = wp_remote_get('https://api.createsend.com/api/v3.1/clients/'.$options['gotowebinar_campaignmonitor_client_id'].'/lists.json?pretty=true', array(
            'headers' => array(
                'Authorization' => 'Basic '. base64_encode($options['gotowebinar_campaignmonitor_api']),
            ),
        ));
        
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $response )), true);
        $json_response = wp_remote_retrieve_response_code($response);
        
        return array($jsondata,$json_response);
        
    }
}









function wp_gotowebinar_aweber_account_hint() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_aweber_token']) && strlen($options['gotowebinar_aweber_token'])>0){
        
        //lets first get the account id
        
        //get authorization code from settings
        $authorizationCode = $options['gotowebinar_aweber_authorization_code'];
        $separateData = explode("|",$authorizationCode);
        $applicationKey = $separateData[0];
        $applicationSecret = $separateData[1];
        $nonce = wp_create_nonce('aweber');
        $unixTimestamp = time();
        
        $url = 'https://api.aweber.com/1.0/accounts';
        
        //start building a string which will be encoded and turned into the oauth1 signature
        $signatureBaseString = 'oauth_consumer_key=';
        $signatureBaseString .= $applicationKey;
        $signatureBaseString .= '&oauth_nonce=';
        $signatureBaseString .= $nonce;
        $signatureBaseString .= '&oauth_signature_method=HMAC-SHA1&oauth_timestamp=';
        $signatureBaseString .= $unixTimestamp;
        $signatureBaseString .= '&oauth_token=';
        $signatureBaseString .= $options['gotowebinar_aweber_token'];
        $signatureBaseString .= '&oauth_version=1.0';

        //encode the signature
        $signatureBaseString = 'GET&'.urlencode($url).'&'.urlencode($signatureBaseString);
        
        //the key of the signature
        $sigKey = $applicationSecret.'&'.$options['gotowebinar_aweber_token_secret'];

        //the final signature woohoo!
        $signature = base64_encode(hash_hmac('sha1', $signatureBaseString, $sigKey, true));

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'OAuth oauth_consumer_key="'.$applicationKey.'", oauth_nonce="'.$nonce.'", oauth_signature="'.$signature.'", oauth_signature_method="HMAC-SHA1", oauth_timestamp="'.$unixTimestamp.'", oauth_token="'.$options['gotowebinar_aweber_token'].'", oauth_version="1.0"',
            ),
        ));
         
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $response )), true);
        $json_response = wp_remote_retrieve_response_code($response);
        
        return array($jsondata,$json_response);
        
    }
}




















function wp_gotowebinar_aweber_list_hint() {
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_aweber_accounts']) && strlen($options['gotowebinar_aweber_token'])>0){
        
        //get authorization code from settings
        $authorizationCode = $options['gotowebinar_aweber_authorization_code'];
        $separateData = explode("|",$authorizationCode);
        $applicationKey = $separateData[0];
        $applicationSecret = $separateData[1];
        $nonce = wp_create_nonce('aweber');
        $unixTimestamp = time();
        
        //start building a string which will be encoded and turned into the oauth1 signature
        $signatureBaseString = 'oauth_consumer_key=';
        $signatureBaseString .= $applicationKey;
        $signatureBaseString .= '&oauth_nonce=';
        $signatureBaseString .= $nonce;
        $signatureBaseString .= '&oauth_signature_method=HMAC-SHA1&oauth_timestamp=';
        $signatureBaseString .= $unixTimestamp;
        $signatureBaseString .= '&oauth_token=';
        $signatureBaseString .= $options['gotowebinar_aweber_token'];
        $signatureBaseString .= '&oauth_version=1.0';
        
        //get the new url for lists with the accountid placed inside
        $url = 'https://api.aweber.com/1.0/accounts/'.$options['gotowebinar_aweber_accounts'].'/lists';
        
        //encode the signature
        $signatureBaseString = 'GET&'.urlencode($url).'&'.urlencode($signatureBaseString);
        
        //the key of the signature
        $sigKey = $applicationSecret.'&'.$options['gotowebinar_aweber_token_secret'];

        //the final signature woohoo!
        $signature = base64_encode(hash_hmac('sha1', $signatureBaseString, $sigKey, true));

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'OAuth oauth_consumer_key="'.$applicationKey.'", oauth_nonce="'.$nonce.'", oauth_signature="'.$signature.'", oauth_signature_method="HMAC-SHA1", oauth_timestamp="'.$unixTimestamp.'", oauth_token="'.$options['gotowebinar_aweber_token'].'", oauth_version="1.0"',
            ),
        ));
         
        $jsondata = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $response )), true);
        $json_response = wp_remote_retrieve_response_code($response);
        
        return array($jsondata,$json_response);
        
    }
}




//add item to log
function wp_gotowebinar_add_log_item($type,$message,$user) {
    
    include(ABSPATH . "wp-includes/pluggable.php"); 
    $options = get_option('gotowebinar_settings');
    
    
    if(get_option('gotowebinar_log')===false){
        add_option( 'gotowebinar_log',array(), '', 'yes' );
    }
    
    $currentOption = get_option('gotowebinar_log');
    
    //get the current user name
    $current_user = wp_get_current_user();
    $userFullName = $current_user->user_firstname.' '.$current_user->user_lastname;
        
    if(strlen($userFullName)<2) {
        $name = $current_user->user_login;  
    } else {
        $name = $userFullName;     
    }
    
    //get the time
    $currentDate = date_i18n($options['gotowebinar_date_format'], current_time('timestamp'),true);
    $currentTime = date_i18n(str_replace(" T","",$options['gotowebinar_time_format']), current_time('timestamp'),true);
    $fullDateTime = $currentDate.' '.$currentTime;
    
    if($user == true){
        $newLogItem = array($type,$fullDateTime,$message,$name);    
    } else {
        $newLogItem = array($type,$fullDateTime,$message,'');    
    }
    
    
    
    //if there are more than 200 log entries lets start removing the earlier log entrie
    if(count($currentOption)>=200){
        array_shift($currentOption);
    }
    
    //add the new item to the array
    array_push($currentOption,$newLogItem);
    
    //update the option
    update_option('gotowebinar_log',$currentOption,'yes');

}


function wp_gotowebinar_add_create_webinar_log_item(){
    
    $type = $_POST['type'];
    $message = $_POST['message'];
    $user = true;
    
    wp_gotowebinar_add_log_item($type,$message,$user);
    wp_die();
}
add_action( 'wp_ajax_create_product_log', 'wp_gotowebinar_add_create_webinar_log_item' );


//Function to run upon ajax request to delete log
function wp_gotowebinar_delete_log_callback() {
    delete_option('gotowebinar_log');  
    wp_die();     
}
add_action( 'wp_ajax_delete_log', 'wp_gotowebinar_delete_log_callback' );








?>