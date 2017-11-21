<?php
//function to register registrant for webinar



function wp_gotowebinar_webinar_registration($body,$webinarId,$webinarTitle,$name){
    $options = get_option('gotowebinar_settings');
    
    $response = wp_remote_post( 'https://api.getgo.com/G2W/rest/organizers/'.$options['gotowebinar_organizer_key'].'/webinars/'.$webinarId.'/registrants', array(
	'headers' => array(
		'Content-Type' => 'application/json',
		'Authorization' => $options['gotowebinar_authorization'],
		'Content-Type' => 'application/json; charset=utf-8',
	),
	'body' => $body,
    ));

    if (! is_wp_error($response)) {

        if ( 201 == wp_remote_retrieve_response_code( $response ) ) {

            wp_gotowebinar_add_log_item('success','Webinar Registration Successful - '.$webinarTitle.' - '.$name,false); 

            $jsondata = json_decode($response['body'],true); 
            echo $jsondata['joinUrl'];

        } elseif ( 409 == wp_remote_retrieve_response_code( $response ) ) {
            wp_gotowebinar_add_log_item('warning','Webinar Registration Unsuccessful (already registered) - '.$webinarTitle.' - '.$name,false);
            echo "409";
        } elseif ( 403 == wp_remote_retrieve_response_code( $response ) ) {
            wp_gotowebinar_add_log_item('warning','Webinar Registration Unsuccessful (attendee limit reached) - '.$webinarTitle.' - '.$name,false);
            echo "403";
        } else { 
            wp_gotowebinar_add_log_item('error','Webinar Registration Unsuccessful - '.$webinarTitle.' - '.$name,false);
            echo "ERROR";   
        }

    } else {
        // There was an error making the request
        wp_gotowebinar_add_log_item('error','Webinar Registration Unsuccessful - '.$webinarTitle.' - '.$name,false);
            echo "ERROR"; 
    }  
}



function wpgotowebinar_registration_form_submit(){
    $options = get_option('gotowebinar_settings');

    $webinarId = $_POST['webinarId'];
    $name = $_POST['name']; //used for logging only
    $webinarTitle = $_POST['webinarTitle']; //used for logging only
    $body = json_encode($_POST['data']);      

    wp_gotowebinar_webinar_registration($body,$webinarId,$webinarTitle,$name);    

    wp_die(); 

} //end function
add_action( 'wp_ajax_registration_form_submit', 'wpgotowebinar_registration_form_submit' );
add_action( 'wp_ajax_nopriv_registration_form_submit', 'wpgotowebinar_registration_form_submit' );

?>