<?php



//Visual Composer Upcoming
add_action( 'vc_before_init', 'wp_gotowebinar_visual_composer_upcoming' );
function wp_gotowebinar_visual_composer_upcoming() {


//get timezones listing  
  
global $time_zone_list;    
$vcTimeZones = array();
foreach($time_zone_list as $key => $value) {
    $vcTimeZones[] = $key;
} 

array_unshift($vcTimeZones,""); 
isset($vcTimeZones);


    
vc_map( array(
   "name" => __("WP GoToWebinar - Upcoming Webinars"),
   "base" => "gotowebinar",
   "description" => "Use this element to show upcoming webinars.",
   "icon" => "gotowebinar-icon",
   "category" => __('Content'),
   "params" => array(
      array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Include webinars that contain the following word or phrase in the title:"),
         "param_name" => "include",
         "admin_label" => TRUE,
          ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Exclude webinars that contain the following word or phrase in the title:"),
         "param_name" => "exclude",
         "admin_label" => TRUE,
         ),
         array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Hide the following word or phrase from the webinar title:"),
         "param_name" => "hide",
         "admin_label" => TRUE,
         ),
         array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Show webinars from the following amount of days:"),
         "param_name" => "days",
         "admin_label" => TRUE,
         "description" => __("If left blank all upcoming webinars will be shown.")
         ),      
         array(
         "type" => "dropdown",
         "holder" => "div",
         "heading" => __("Show webinars from only this timezone:"),
         "param_name" => "timezone",
         "value" => $vcTimeZones,
         "admin_label" => TRUE,
         "description" => __("If left blank all timezones will be shown.")
         ),          
    )
    ) );
    }









//Visual Composer Calendar
add_action( 'vc_before_init', 'wp_gotowebinar_visual_composer_calendar' );
function wp_gotowebinar_visual_composer_calendar() {


//get timezones listing  
  
global $time_zone_list;    
$vcTimeZones = array();
foreach($time_zone_list as $key => $value) {
    $vcTimeZones[] = $key;
} 

array_unshift($vcTimeZones,""); 
isset($vcTimeZones);


    
vc_map( array(
   "name" => __("WP GoToWebinar - Calendar"),
   "base" => "gotowebinar-calendar",
   "description" => "Use this element to show upcoming webinars via a calendar display.",
   "icon" => "gotowebinar-icon",
   "category" => __('Content'),
   "params" => array(
      array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Include webinars that contain the following word or phrase in the title:"),
         "param_name" => "include",
         "admin_label" => TRUE,
          ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Exclude webinars that contain the following word or phrase in the title:"),
         "param_name" => "exclude",
         "admin_label" => TRUE,
         ),
         array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Hide the following word or phrase from the webinar title:"),
         "param_name" => "hide",
         "admin_label" => TRUE,
         ),
         array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Show webinars from the following amount of days:"),
         "param_name" => "days",
         "admin_label" => TRUE,
         "description" => __("If left blank all upcoming webinars will be shown.")
         ),      
         array(
         "type" => "dropdown",
         "holder" => "div",
         "heading" => __("Show webinars from only this timezone:"),
         "param_name" => "timezone",
         "value" => $vcTimeZones,
         "admin_label" => TRUE,
         "description" => __("If left blank all timezones will be shown.")
         ),          
    )
    ) );
    }








//Visual Composer Add in General Registration
add_action( 'vc_before_init', 'wp_gotowebinar_visual_general_registration' );
function wp_gotowebinar_visual_general_registration() {
vc_map( array(
   "name" => __("WP GoToWebinar - Registration"),
   "base" => "gotowebinar-reg-gen",
   "description" => "Put this element on your registration page.",
   "icon" => "gotowebinar-icon",
   "category" => __('Content'),
   "show_settings_on_create" => FALSE,
    ) );
}














//Visual Composer Add in Single Webinar Registration
add_action( 'vc_before_init', 'wp_gotowebinar_visual_composer_registration' );
function wp_gotowebinar_visual_composer_registration() {
    
    global $gotowebinar_is_pro;
    
    //this gets the users webinar keys for quick reference
    function webinar_key_hint(){
    $options = get_option('gotowebinar_settings');

    //call upcoming webinars function and store responses as variables    
    list($jsondata,$json_response) = wp_gotowebinar_upcoming_webinars('gtw_key_vc', 604800);     

    $html = '<table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
            <tr>
                <th style="text-align: left;">Webinar Title</th>
                <th style="text-align: left;">Webinar Date</th>
                <th style="text-align: left;">Webinar Key</th>
            </tr>';
        
    
        
    if($json_response == 200){     
        foreach ($jsondata as $data) {
            $html .= '<tr><td>'; 
            if(isset($data['subject'])){    
            $html .= $data['subject'];  
            }
            $html .= '</td>';
            if(isset($data['times'])) {   

             $html .= '<td>';   
            foreach($data['times'] as $mytimes) {

            $date = new DateTime($mytimes['startTime']); 
            $html .= $date->format($options['gotowebinar_date_format']).'</br>';    

            }
            $html .= '</td>'; 
            }
            $html .= '<td><strong style="color:#CC0000">';
                if(isset($data['webinarKey'])){
                    $html .= $data['webinarKey'];
                }
            $html .= '<strong></td></tr>';  
       }
    }
        
        
        
    $html .= '</table>';
    return $html;
}
    
    
    
    
function mailchimp_list_hint(){    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_mailchimp_api'])){

        list($jsondata,$json_response) = wp_gotowebinar_mailchimp_list_hint(); 

        if ( 200 == $json_response) {

        $html = '<table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
                    <tr>
                        <th style="text-align: left;">List Name</th>
                        <th style="text-align: left;">List ID</th>
                    </tr>';        

        foreach($jsondata['lists'] as $list){
        $html .= '<tr>';     
        $html .= '<td>'.$list['name'].'</td>';
        $html .= '<td><strong style="color:#CC0000">'.$list['id'].'</strong></td>';              
        $html .= '</tr>'; 
        }
        $html .= '</table>';    
        return $html;  
        }
    }
} //end mailchimp list help function
    
    
    
    
function constant_contact_list_hint(){    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_constantcontact_token'])){
    
        list($jsondata,$json_response) = wp_gotowebinar_constantcontact_list_hint(); 


        if ( 200 == $json_response) {
           
            $html = '<table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
                        <tr>
                            <th style="text-align: left;">List Name</th>
                            <th style="text-align: left;">List ID</th>
                        </tr>';     



            foreach($jsondata as $list){
            $html .= '<tr>';     
            $html .= '<td>'.$list['name'].'</td>';
            $html .= '<td><strong style="color:#CC0000">'.$list['id'].'</strong></td>';              
            $html .= '</tr>'; 
            }
            $html .= '</table>';    
            return $html; 
        }
      
    
    }
    
} //end constant contact list hint function
    
    
    
    
  
function active_campaign_list_hint(){    
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_activecampaign_account'])){

        list($jsondata,$json_response) = wp_gotowebinar_activecampaign_list_hint(); 

        if (200 == $json_response) {

            $html = '<table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
            <tr>
                <th style="text-align: left;">List Name</th>
                <th style="text-align: left;">List ID</th>
            </tr>';     

            foreach($jsondata as $list){

                if (is_array($list) && isset($list['name'])) {

                    $html .= '<tr>';     
                    $html .= '<td>'.$list['name'].'</td>';
                    $html .= '<td><strong style="color:#CC0000">'.$list['id'].'</strong></td>'; 
                    $html .= '</tr>';

                }

            }

            $html .= '</table>';    
            return $html; 

        }
        
    }

}
    
    
    
    

    
    
    
function camapign_monitor_list_hint(){    
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_campaignmonitor_client_id'])){

        list($jsondata,$json_response) = wp_gotowebinar_campaignmonitor_list_hint();

        if (200 == $json_response) {

                $html = '<table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
                <tr>
                    <th style="text-align: left;">List Name</th>
                    <th style="text-align: left;">List ID</th>
                </tr>';     



                foreach($jsondata as $list){

                    if (is_array($list) && isset($list['Name'])) {

                        $html .= '<tr>';     
                        $html .= '<td>'.$list['Name'].'</td>';
                        $html .= '<td><strong style="color:#CC0000">'.$list['ListID'].'</strong></td>'; 
                        $html .= '</tr>';
                    }
                }

                $html .= '</table>';    
                return $html; 

        }
    }
}
    
    
function aweber_list_hint(){    
    
    $options = get_option('gotowebinar_settings');
    
    if(isset($options['gotowebinar_aweber_token'])){

        list($jsondata,$json_response) = wp_gotowebinar_aweber_list_hint();

        if (200 == $json_response) {

                $html = '<table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
                <tr>
                    <th style="text-align: left;">List Name</th>
                    <th style="text-align: left;">List ID</th>
                </tr>';     



                foreach($jsondata['entries'] as $key => $list){
                        $html .= '<tr>';     
                        $html .= '<td>'.$list['name'].'</td>';
                        $html .= '<td><strong style="color:#CC0000">'.$list['id'].'</strong></td>'; 
                        $html .= '</tr>';
                }

                $html .= '</table>';    
                return $html; 

        }
    }
}     
    
    
    



global $time_zone_list;    
$vcTimeZones = array();
foreach($time_zone_list as $key => $value) {
    $vcTimeZones[] = $key;
} 

array_unshift($vcTimeZones,""); 
isset($vcTimeZones);

    
    
    
    
    
if ($gotowebinar_is_pro == "YES"){ 
vc_map( array(
   "name" => __("WP GoToWebinar - Single Registration Form"),
   "base" => "gotowebinar-reg",
   "icon" => "gotowebinar-icon",
   "description" => "Use this element to show a registration form.",    
   "category" => __('Content'),
   "params" => array(
      array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("The webinar key of the webinar you want to show a registration form of:"),
         "param_name" => "key",
         "admin_label" => TRUE,
         "description" => __("If you want to show a registration form for your next webinar you can just type in <code><strong>upcoming</strong></code>. You can get your webinar key from the GoToWebinar website. We have also included a quick reference below:".webinar_key_hint())
          ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Hide the following word or phrase from the webinar title:"),
         "param_name" => "hide",
         "admin_label" => TRUE,
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar subject includes:"),
         "param_name" => "include",
         "admin_label" => TRUE,
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar subject excludes:"),
         "param_name" => "exclude",
         "admin_label" => TRUE,
         ),
       array(
         "type" => "dropdown",
         "holder" => "div",
         "heading" => __("If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar timezone is:"),
         "param_name" => "timezone",
         "value" => $vcTimeZones,
         "admin_label" => TRUE,
         "description" => __("If left blank all timezones will be shown.")
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "class" => "",
         "heading" => __("Custom MailChimp List ID (Optional):"),
         "param_name" => "mailchimp",
         "value" => __(""),
         "admin_label" => TRUE,
         "description" => __("By default we will use the list selected in the WP GoToWebinar Pro settings. However you can over-ride this selection by entering in a custom list ID above. Please use the below table to see your existing MailChimp List IDs:".mailchimp_list_hint())
         ),  
       array(
         "type" => "textfield",
         "holder" => "div",
         "class" => "",
         "heading" => __("Custom Constant Contact List ID (Optional):"),
         "param_name" => "constantcontact",
         "value" => __(""),
         "admin_label" => TRUE,
         "description" => __("By default we will use the list selected in the WP GoToWebinar Pro settings. However you can over-ride this selection by entering in a custom list ID above. Please use the below table to see your existing Constant Contact List IDs:".constant_contact_list_hint())
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "class" => "",
         "heading" => __("Custom Campaign Monitor List ID (Optional):"),
         "param_name" => "campaignmonitor",
         "value" => __(""),
         "admin_label" => TRUE,
         "description" => __("By default we will use the list selected in the WP GoToWebinar Pro settings. However you can over-ride this selection by entering in a custom list ID above. Please use the below table to see your existing Campaign Monitor List IDs:".camapign_monitor_list_hint())
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "class" => "",
         "heading" => __("Active Campaign List ID (Optional):"),
         "param_name" => "activecampaign",
         "value" => __(""),
         "admin_label" => TRUE,
         "description" => __("By default we will use the list selected in the WP GoToWebinar Pro settings. However you can over-ride this selection by entering in a custom list ID above. Please use the below table to see your existing ActiveCampaign List IDs:".active_campaign_list_hint())
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "class" => "",
         "heading" => __("AWeber List ID (Optional):"),
         "param_name" => "aweber",
         "value" => __(""),
         "admin_label" => TRUE,
         "description" => __("By default we will use the list selected in the WP GoToWebinar Pro settings. However you can over-ride this selection by entering in a custom list ID above. Please use the below table to see your existing AWeber List IDs:".aweber_list_hint())
         ) 
    )
    ) );
    } else {
    vc_map( array(
   "name" => __("WP GoToWebinar - Single Registration Form"),
   "base" => "gotowebinar-reg",
   "icon" => "gotowebinar-icon",
   "description" => "Use this element to show a registration form.",    
   "category" => __('Content'),
   "params" => array(
      array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("The webinar key of the webinar you want to show a registration form of:"),
         "param_name" => "key",
         "admin_label" => TRUE,
         "description" => __("If you want to show a registration form for your next webinar you can just type in <code><strong>upcoming</strong></code>. You can get your webinar key from the GoToWebinar website. We have also included a quick reference below:".webinar_key_hint())
          ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("Hide the following word or phrase from the webinar title:"),
         "param_name" => "hide",
         "admin_label" => TRUE,
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar subject includes:"),
         "param_name" => "include",
         "admin_label" => TRUE,
         ),
       array(
         "type" => "textfield",
         "holder" => "div",
         "heading" => __("If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar subject excludes:"),
         "param_name" => "exclude",
         "admin_label" => TRUE,
         ),
       array(
         "type" => "dropdown",
         "holder" => "div",
         "heading" => __("If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar timezone is:"),
         "param_name" => "timezone",
         "value" => $vcTimeZones,
         "admin_label" => TRUE,
         "description" => __("If left blank all timezones will be shown.")
         )
    )
    ) );
}
    }
    ?>