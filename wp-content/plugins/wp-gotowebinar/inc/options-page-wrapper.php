<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
global $time_zone_list; 
global $gotowebinar_is_pro;
$options = get_option('gotowebinar_settings');

//call upcoming webinars function and store responses as variables    
list($jsondata,$json_response) = wp_gotowebinar_upcoming_webinars('gtw_key', 600);

?>

    <!-- start wrap -->
    <div class="wrap">
    <div id="poststuff">
        
    <!-- pro ad -->
    <?php if ($gotowebinar_is_pro != "YES"){ ?> 

    <div id="wp-gotowebinar-pro-ad">
        <div>

            <iframe width="560" height="315" src="https://www.youtube.com/embed/M3rty3sV9lU?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>

            <div>

                <h2>UPGRADE TO <span style="color:white;">WP GOTOWEBINAR PRO</span></h2>

                <p>THE EASIEST WAY TO SELL WEBINARS&trade; <em>VIA <a href="https://woocommerce.com/" target="_blank">WOOCOMMERCE</a></em></p>

                <p>PLUS ADD REGISTRANTS TO:</br></p><p><em>
                <a href="http://www.activecampaign.com/" target="_blank">ACTIVECAMPAIGN</a>,
                <a href="https://www.agilecrm.com" target="_blank">AGILE CRM</a>,
<!--                <a href="https://www.aweber.com/welcome.htm" target="_blank">AWEBER</a>,-->
                <a href="https://www.campaignmonitor.com/" target="_blank">CAMPAIGN MONITOR</a>, </br> 
                <a href="https://www.constantcontact.com/" target="_blank">CONSTANT CONTACT</a>,
                <a href="https://highrisehq.com" target="_blank">HIGHRISE CRM</a>, 
                <a href="https://www.hubspot.com/" target="_blank">HUBSPOT CRM</a>, 
                <a href="https://www.insightly.com/" target="_blank">INSIGHTLY CRM</a>, </br> 
                <a href="https://mailchimp.com/" target="_blank">MAILCHIMP</a>,
                <a href="https://www.pipedrive.com" target="_blank">PIPEDRIVE CRM</a>,
                <a href="https://www.salesforce.com" target="_blank">SALESFORCE CRM</a> &amp; 
                <a href="https://www.zoho.com/crm/" target="_blank">ZOHO CRM</a></em></p>
                
                <p>INCREASE WEBINAR PARTICIPATION AND CONVERSIONS WITH THE NEW WEBINAR COUNTDOWN TOOLBAR
                </p>

                <a href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/" target="_blank">LEARN MORE</a>


            </div>


        </div>
    </div>
    <?php } ?> 
        
    <!-- heading -->
    <?php if ($gotowebinar_is_pro == "YES"){ ?>  
        <h1><i class="fa fa-video-camera" aria-hidden="true"></i> <?php esc_attr_e( 'WP GoToWebinar Pro Options', 'wp_admin_style' ); ?></h1>
        <?php } else { ?>
        <h1><i class="fa fa-video-camera" aria-hidden="true"></i> <?php esc_attr_e( 'WP GoToWebinar Options', 'wp_admin_style' ); ?></h1>    
    <?php } ?>

    <!-- authentication note -->    
    <?php
    if(strlen($options['gotowebinar_organizer_key'])>0 && strlen($options['gotowebinar_authorization'])>0) {
        
        $authenticationStatusCode = wp_gotowebinar_authentication_check();
        //for development only to reduce calls
//        $authenticationStatusCode = 200;

        if($authenticationStatusCode == 200) {
            //plugin is authenticated message
            ?>
            <div class="notice notice-success gotowebinar-authentication-notice inline">
                  <p>The connection to your GoToWebinar account is working :)</p> 
            </div>    
            <?php
            
        } else {
            //plugin isn't authenticated
            ?>
            <div class="notice notice-error gotowebinar-authentication-notice inline">
                
                <table>
                <tr>
                <td><i style="margin-right: 10px; color: #dc3232; font-size: 64px;" class="fa fa-exclamation-triangle" aria-hidden="true"></i></td>  
                <td><p>The connection to your GoToWebinar account hasn't been successful. Even though the Authorization and Organizer Key have been successfully generated the API calls to your GoToWebinar account are failing. This means that the plugin won't work! This can be because of a few reasons, including you could be on a trial account, your GoToWebinar account has expired or you are using login details for another Citrix product like GoToMeeting. If this message is showing please contact Citrix first by clicking <a href="https://care.citrixonline.com/gotowebinar/contactus" target="_blank"><strong>here</strong></a> as I can't assist you with this particular issue. The issue may also just be a temporary issue, so perhaps try refreshing the page. The issue can also arise if you have made too many API calls in a given period. To minimise API calls we strongly recommend you enable caching by ensuring the setting is turned off in the <a class="open-tab" href="#general_options">General Options</a> tab.</p></td>    
                </tr>
                
                </table>
     
            </div>    
            <?php
        }
        
        
    }        
        
    ?>    
        
        
    <!-- welcome note -->         
    <?php    
    if(!isset($options['gotowebinar_welcome_message'])) {
    ?>
    <div class="notice notice-warning is-dismissible wpgotowebinar-welcome inline">
        <h3>Quickstart Guide</h3>
        <p>Thanks for using WP GoToWebinar! The first thing you need to do to get started is to get your Authorization and Organizer Key which can be found in the <a class="open-tab" href="#general_options">General Options</a> tab. Once this is done you can now utilise the various plugin features. This includes adding an upcoming webinar table to a post or page by adding the <code>[gotowebinar]</code> shortcode to your page/post content (or if you want to show this in a sidebar use the included widget). To make the register links in the upcoming webinar table go to a registration page on your website create a new page and add the <code>[gotowebinar-reg]</code> shortcode to it and select this page in the settings on the <a class="open-tab" href="#general_options">General Options</a> tab.</p>
        <p>To learn more about all the great stuff you can do with WP GoToWebinar check out the <a class="open-tab" href="#faq">FAQ</a> tab.</p>
        
    </div>
    <?php } ?>    
        
                
        

    <?php
        
        //function to transform titles
        
        function wpgotowebinar_change_title($name){
            
            $nameToLowerCase = strtolower($name);
            $replaceSpaces = str_replace(' ', '_', $nameToLowerCase);    
            
            return $replaceSpaces;
            
        }
        
        
        //function to output tab titles
        function wpgotowebinar_output_tab_titles($name,$proFeature) {
            
            global $gotowebinar_is_pro;
            
            if ($gotowebinar_is_pro == "YES" && $proFeature == "YES"){ 
                $iconOutput = '<i class="fa fa-unlock" aria-hidden="true"></i>';    
            } elseif ($proFeature == "YES") {
                $iconOutput = '<i class="fa fa-lock" aria-hidden="true"></i>'; 
            } else {
                $iconOutput = '';   
            }
         
            
            echo '<li><a class="nav-tab" href="#'.wpgotowebinar_change_title($name).'">'.$name.' '.$iconOutput.'</a></li>'; 
        }
        
        
        
        
        //function to output tab content
        function wpgotowebinar_tab_content ($tabName) {
            
            $transformedTitle = wpgotowebinar_change_title($tabName);
            
            ?>
            <div class="tab-content" id="<?php echo $transformedTitle; ?>">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                                <table class="form-table">
                                    <?php
                                    global $gotowebinar_is_pro;
                                    global $gotowebinar_pro_features;
            
                                    if($gotowebinar_is_pro != "YES" && $gotowebinar_pro_features[$tabName][0] == "YES") {
                                        
                                        settings_fields('locked');
                                        do_settings_sections('locked');     
                                        
                                    } else {
                                        
                                        if($transformedTitle == "support" && $gotowebinar_is_pro == "YES"){
                                            settings_fields('support_pro');
                                            do_settings_sections('support_pro');       
                                        } else {
                                            settings_fields($transformedTitle);
                                            do_settings_sections($transformedTitle);  
                                        }
                                            
                                        if($gotowebinar_pro_features[$tabName][1] == "YES"){
                                        ?>
                                        
                                        <table>
                                            <tr class="gotowebinar_settings_row">
                                                <td>
                                                    <button type="submit" name="submit" id="submit" class="button button-primary gotowebinar-save-all-settings-button"><?php _e('Save All Settings', 'wp-gotowebinar' ); ?></button>
                                                </td>
                                            </tr>    
                                        </table>    
                                        <?php    
                                        }
      
                                    }
                                    ?>
                                </table>
                             </div> <!-- .inside -->
                    </div> <!-- .postbox -->                      
                </div> <!-- .meta-box-sortables --> 
            </div> <!-- .tab-content -->  
            <?php
            
            
        }
    ?>    
    
 
        
        
        

    <!--start form-->    
    <form id="gotowebinar_settings_form" action="options.php" method="post">
       
        <div id="tabs" class="nav-tab-wrapper"> 
            <ul class="tab-titles">
                <?php 

                //declare pro and non pro options into an associative array
                global $gotowebinar_pro_features;

                foreach($gotowebinar_pro_features as $item => $value){

                    wpgotowebinar_output_tab_titles($item,$value[0]);
                }

                ?>

            </ul>

            <!--add tab content pages-->
            <?php

            global $gotowebinar_pro_features;

            foreach($gotowebinar_pro_features as $item => $value){
                wpgotowebinar_tab_content($item);     
            }
            ?>

        </div> <!--end tabs div-->         
    </form>
        
        


        
        
    
        
    <?php if ($gotowebinar_is_pro != "YES"){ ?>
        <p> <?php esc_attr_e( 'Thank you for using WP GoToWebinar. Your donation contributes to the development of this plugin. Any donation amount is much appreciated.'); ?></p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="VGVE97KF74FVN">
        <input type="image" src="https://northernbeacheswebsites.com.au/root-nbw/wp-content/uploads/2016/11/donate-button.png" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
        </form>

    <?php } ?>
        
        
    </div> <!--end post stuff-->    
        
    </div> <!-- .wrap -->