<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

//define all the settings in the plugin
function wp_gotowebinar_settings_init() { 
    
    //start authorisation section
	register_setting( 'general_options', 'gotowebinar_settings' );
    
    //general options
	add_settings_section(
		'wp_gotowebinar_general_options','', 
		'wp_gotowebinar_general_options_callback', 
		'general_options'
	);

	add_settings_field( 
		'gotowebinar_authorization','', 
		'gotowebinar_authorization_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_organizer_key','', 
		'gotowebinar_organizer_key_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_date_format','', 
		'gotowebinar_date_format_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_time_format','', 
		'gotowebinar_time_format_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    
    add_settings_field( 
		'gotowebinar_disable_tooltip','', 
		'gotowebinar_disable_tooltip_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_tooltip_text_color','', 
		'gotowebinar_tooltip_text_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_tooltip_background_color','', 
		'gotowebinar_tooltip_background_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_tooltip_border_color','', 
		'gotowebinar_tooltip_border_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_icon_color','', 
		'gotowebinar_icon_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_custom_registration_page','', 
		'gotowebinar_custom_registration_page_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_custom_thankyou_page','', 
		'gotowebinar_custom_thankyou_page_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_button_text_color','', 
		'gotowebinar_button_text_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_button_background_color','', 
		'gotowebinar_button_background_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_button_border_color','', 
		'gotowebinar_button_border_color_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_disable_cache','', 
		'gotowebinar_disable_cache_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_enable_timezone_conversion','', 
		'gotowebinar_enable_timezone_conversion_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_timezone_error_message','', 
		'gotowebinar_timezone_error_message_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_recaptcha_site_key','', 
		'gotowebinar_recaptcha_site_key_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_registration_confirmation','', 
		'gotowebinar_registration_confirmation_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_welcome_message','', 
		'gotowebinar_welcome_message_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    add_settings_field( 
		'gotowebinar_tab_memory','', 
		'gotowebinar_tab_memory_render', 
		'general_options', 
		'wp_gotowebinar_general_options' 
	);
    
    //translation
    register_setting( 'translation', 'gotowebinar_settings' );
    
	add_settings_section(
		'wp_gotowebinar_translation','', 
		'wp_gotowebinar_translation_callback', 
		'translation'
	);

	add_settings_field( 
		'gotowebinar_translate_firstName','', 
		'gotowebinar_translate_firstName_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_lastName','', 
		'gotowebinar_translate_lastName_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_email','', 
		'gotowebinar_translate_email_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_address','', 
		'gotowebinar_translate_address_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_city','', 
		'gotowebinar_translate_city_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_state','', 
		'gotowebinar_translate_state_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_firstName','', 
		'gotowebinar_translate_firstName_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_zipCode','', 
		'gotowebinar_translate_zipCode_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_country','', 
		'gotowebinar_translate_country_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_phone','', 
		'gotowebinar_translate_phone_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_organization','', 
		'gotowebinar_translate_organization_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_jobTitle','', 
		'gotowebinar_translate_jobTitle_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_questionsAndComments','', 
		'gotowebinar_translate_questionsAndComments_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_industry','', 
		'gotowebinar_translate_industry_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_numberOfEmployees','', 
		'gotowebinar_translate_numberOfEmployees_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_purchasingTimeFrame','', 
		'gotowebinar_translate_purchasingTimeFrame_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_purchasingRole','', 
		'gotowebinar_translate_purchasingRole_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_submitButton','', 
		'gotowebinar_translate_submitButton_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_successMessage','', 
		'gotowebinar_translate_successMessage_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_alreadyRegisteredMessage','', 
		'gotowebinar_translate_alreadyRegisteredMessage_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_errorMessage','', 
		'gotowebinar_translate_errorMessage_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_attendeeLimit','', 
		'gotowebinar_translate_attendeeLimit_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_cancelledWebinar','', 
		'gotowebinar_translate_cancelledWebinar_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_required','', 
		'gotowebinar_translate_required_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    add_settings_field( 
		'gotowebinar_translate_registration_confirmation_message','', 
		'gotowebinar_translate_registration_confirmation_message_render', 
		'translation', 
		'wp_gotowebinar_translation' 
	);
    
    
    
    //clear cache
    register_setting( 'clear_cache', 'gotowebinar_settings' );
    
	add_settings_section(
		'wp_gotowebinar_clear_cache','', 
		'wp_gotowebinar_clear_cache_setting_callback', 
		'clear_cache'
	);
    
    //faq
    register_setting( 'faq', 'gotowebinar_settings' );
    
	add_settings_section(
		'wp_gotowebinar_faq','', 
		'wp_gotowebinar_faq_callback', 
		'faq'
	);
    
    //support
    register_setting( 'support', 'gotowebinar_settings' );
    
	add_settings_section(
		'wp_gotowebinar_support','', 
		'wp_gotowebinar_support_callback', 
		'support'
	);
    
    //log
    register_setting( 'log', 'gotowebinar_settings' );
    
	add_settings_section(
		'wp_gotowebinar_log','', 
		'wp_gotowebinar_log_callback', 
		'log'
	);
    
    //locked
    register_setting( 'locked', 'gotowebinar_settings' );
    
	add_settings_section(
		'wp_gotowebinar_locked','', 
		'wp_gotowebinar_locked_callback', 
		'locked'
	);
    
    
    
    
}

/**
* 
*
*
* The following functions output the callback of the sections
*/
function wp_gotowebinar_general_options_callback(){}
function wp_gotowebinar_translation_callback(){}
function wp_gotowebinar_clear_cache_setting_callback(){ 
    ?>
    <tr class="gotowebinar_settings_row" valign="top">
        <td scope="row">
            <label for="gotowebinar_clear_cache">Clear Cache <i class="fa fa-info-circle information-icon" aria-hidden="true"></i>
                <p class="hidden"><em>Clearing the cache removes all webinar information stored in your local database. The cache is automatically cleared after 24 hours or when a day finished. If it is important that a webinar's details need to be updated please clear the cache.</em></p>
            </label>
        </td>
        <td>
            <a class="button-primary" id="gotowebinar_clear_cache" name="gotowebinar_clear_cache">Clear Webinar Cache</a>
        </td>
    </tr>

<?php 
}

function wp_gotowebinar_log_callback(){
    
    ?>
    <tr class="gotowebinar_settings_row" valign="top">
        <td scope="row">
            
            <em style="margin-right: 10px; vertical-align: -webkit-baseline-middle;">Note: only the last 200 actions will be logged</em><a class="button-secondary" id="gotowebinar_delete_log" name="gotowebinar_delete_log">Delete Log</a>
            <br></br>
            <?php
                
            if(get_option('gotowebinar_log') !== false) {   
        
                $currentOption = array_reverse(get_option('gotowebinar_log'));
                
                
                foreach($currentOption as $logItem){

                    if(strlen($logItem[3])>0){
                        $actionBy = 'by '.$logItem[3];
                    } else {
                        $actionBy = '';         
                    } 

                    echo '<div class="notice notice-'.$logItem[0].' inline"><p style="margin: 5px 0px !important;"><span style="color: #b6b6b6;">'.$logItem[1].'</span> <strong>'.$logItem[2].'</strong> '.$actionBy.'</p></div>';
                }
            } else {
                echo 'It looks like nothing has happened yet...how boring!';  
            }
            ?>

        </td>
    </tr>

<?php 
}

function wp_gotowebinar_faq_callback(){ 
    global $time_zone_list;
    list($jsondata,$json_response) = wp_gotowebinar_upcoming_webinars('gtw_key', 600);
    $options = get_option('gotowebinar_settings');
    ?>
    <tr class="gotowebinar_settings_row" valign="top">
        <td scope="row" colspan="2">
            <div class="inside">
                            
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/7lLOk14OpfA?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                            <br></br>
            
            
                            <h3><span style="color: #CC0000;">New in v10.0</span> Having issues understanding the shortcodes and not using the Visual Composer plugin? Now you can use the 'WP GoToWebinar' button in the standard WordPress content editor to easily build shortcodes!</h3>
            
                            <img style="width: 100%; max-width: 800px;" src="<?php echo plugins_url( '/images/tinymce.png', __FILE__ )?> ">
            
                            <br></br>
                            <br></br>
                            <div id="accordion">
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I get my GoToWebinar Authorization and Organizer Key?</h3>
                                <div>
                                    Please click the "Click here to get Auth and Key" button next to the Authorization field which can be found in the <a class="open-tab" href="#general_options">General Options</a> tab. On the Citrix dialog box that appears please press Allow; if you're not signed in you will be prompted to sign in first. You will then be redirected to this settings page where you can then save the settings.
                                </div>
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> The 'GoToWebinar Authorization and Organizer Key' button isn't working?</h3>
                                <div>
                                    Please ensure you are using a javascript enabled browser. Most browsers these days enable javascript, however to double check this please visit <a target="_blank" href="https://www.whatismybrowser.com/detect/is-javascript-enabled">this page</a>. It's also important that before you click on the "Click here to get Auth and Key" button above that the "Authorization" and "Organizer Key" fields are blank. If they aren't blank please remove any existing text, save the settings, and then click the button.
                                </div>
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I add an Upcoming GoToWebinars Table to my page or post?</h3>
                                <div>
                                    Just use this shortcode: <code>[gotowebinar]</code>. This shortcode will display all of your upcoming GoToWebinars. However you can put options into the shortcode like: <code>[gotowebinar include="" exclude="" hide="" timezone="" days=""]</code>. Just place a word or phrase in the include option to only show webinars that contain that word or phrase in the webinars title - the same goes for the exclude option. So to show all webinars that have a title that contains <strong>Training</strong> but excludes webinars that have the word <strong>Introduction</strong> you would use this shortcode: <code>[gotowebinar include="Training" exclude="Introduction"]</code>. Please note that what you type in these filters is case sensitive. You can also use the <strong>hide=""</strong> parameter to hide words/phrases from the title of the webinar. This can be a handy feature if you want to want to hide the <strong>include</strong> word or phrase from actually showing in the table. Using the <strong>days=""</strong> parameter you can get webinars from a certain amount of days in the future. For example <code>[gotowebinar days="10"]</code> will only show webinars that are coming up in the next 10 days. This can be handy if you have a large volume of webinars. You can also show webinars that are from a particular timezone, this can be handy if you have an American website but you only want to show webinars from your American office in New York. This can be done using <code>[gotowebinar timezone="America/New_York"]</code>. A list of these timezone filters can be found below:
                                    <ul id="quad">
                                        <?php foreach($time_zone_list as $key => $value) {    
    echo '<li>'.$key.'</li>';
}
?>
                                    </ul>
                                </div>
                                
                                
                                
                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I add an Upcoming GoToWebinars Calendar to my page or post? <span style="color: #CC0000;">New in v9.0</span></h3>
                                <div>
                                    Just use this shortcode: <code>[gotowebinar-calendar]</code>. This shortcode will display all of your upcoming GoToWebinars in a calendar display. You can also use the same shortcode parameters that are available in the main table view so you create shortcodes like: <code>[gotowebinar-calendar include="" exclude="" hide="" timezone="" days=""]</code>. The calendars language is taken from your WordPress language setup. So if your WordPress language is French, Monday will appear as 'Lundi' and so on. All the times displayed in the calendar should be the local time of the user. However note, other parts of this plugin use the users IP address to detect their location and time, however, the calendar gets this from the users computer. This has been done to improve loading time. You can also use the new Visual Composer element 'WP GoToWebinar - Calendar' to insert a calendar onto a page. Please note that only 1 calendar can be placed on a page at any given time. You can have multiple calendars but they just must be on different pages or posts.   
                                </div>
                                
                                
                                
                                
                                
                                
                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I show a registration form for a single webinar?</h3>
                                <div>
                                    <ol>
                                        <li>Navigate to the post or page you would like to add the webinars to</li>
                                        <li>Enter in the shortcode <code>[gotowebinar-reg key="YOUR WEBINAR KEY"]</code></li>
                                        <li>You can also add a hide parameter to the shortcode to hide parts of the title showing like: <code>[gotowebinar-reg key="YOUR WEBINAR KEY" hide="Training"]</code></li>
                                    </ol>
                                    <p>You might now be asking how do I get my webinar key? You can get this from the GoToWebinar website: <a target="_blank" href="https://global.gotowebinar.com/webinars.tmpl">https://global.gotowebinar.com/webinars.tmpl</a> (it's the number at the end of your webinars URL). If you have already authenticated this plugin you can use the quick table below to get your webinars key:</p>

                                    <table id="gotowebinar_key_display_table" style="width:100%; table-layout: fixed; margin-top: 10px;">
                                        <tr>
                                            <th style="text-align: left;">Webinar Title</th>
                                            <th style="text-align: left;">Webinar Date</th>
                                            <th style="text-align: left;">Webinar Key</th>
                                        </tr>
                                        <?php   
   foreach ($jsondata as $data) {
    echo '<tr><td>'; 
       
    if(isset($data['subject'])){   
       
    echo $data['subject'];   
    }
    echo '</td>';
       
    if(isset($data['times'])){   
     echo '<td>';  
    foreach($data['times'] as $mytimes) {
    
    $date = new DateTime($mytimes['startTime']); 
    echo $date->format($options['gotowebinar_date_format']).'</br>';    
        
    }
     echo '</td>';   
   }
    echo '<td><strong style="color:#CC0000">';
       
    if(isset($data['webinarKey'])){
       
    echo $data['webinarKey'];
    }
       
    echo '<strong></td></tr>';  
   }
    ?>
                                    </table>
                                </div>
                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I show a registration form for my very next webinar?</h3>
                                <div>Lucky you, we created a shortcode just for you! Just put this on any post or page: <code>[gotowebinar-reg key="upcoming"]</code>. You can also use additional shortcode parameters: 'include','exclude' and 'timezone' to show a registration form for your next upcoming webinar that meets a certain condition. For example to show a registration form of the next upcoming webinar that has a subject that includes the word 'Training' you can use a shortcode like this: <code>[gotowebinar-reg key="upcoming" include="Training"]</code>.
                                </div>
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I change the fields on my registration form?</h3>
                                <div>The registratiom form fields shown by WP GoToWebinar mirrors the registration form fields you setup when creating the webinar on the GoToWebinar website. So you can include fields, make them required or not, or even add your own questions by going to the GoToWebinar website. Just remember if you make changes to your questions to clear the cache by pressing the "Clear Webinar Cache" button in the <a class="open-tab" href="#clear_cache">Clear Cache</a> tab. The cache is cleared automatically every 24 hours or when the next day starts but you may want to see the changes immediately so that's why we included the button!
                                </div>
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I enable users to register for a webinar on my website from the Upcoming Webinars table or widget?</h3>
                                <div>
                                    <ol>
                                        <li>Add the shortcode <code>[gotowebinar-reg]</code> on your newly created or existing registration page</li>
                                        <li>On the <a class="open-tab" href="#general_options">General Options</a> tab select the registration page from the Custom Registration Page dropdown setting</li>
                                        <li>That's it! Now when people click register from the Upcoming Webinars Shortcode or Widget instead of going to the GoToWebinar website they are taken to your registration page</li>
                                    </ol>
                                </div>
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I add the GoToWebinars widget?</h3>
                                <div>
                                    You will find the GoToWebinars widget on your <a href="widgets.php">widget page</a>. You can add multiple widgets with different filter criteria as well as setting a maximum number of webinars to display.
                                </div>
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How do I add upcoming webinars or a registration form using Visual Composer?</h3>
                                <div>
                                    <ol>
                                        <li>Firstly if you don't have Visual Composer yet you can purchase it from <a target="_blank" href="http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431">here.</a></li>
                                        <li>Go to your page or post and click on the Backend Editor button if you aren't in the Visual Composer mode yet.</li>
                                        <li>Under "Content" you will find WP GoToWebinar content elements, some of which contain additional settings.</li>
                                    </ol>
                                </div>


                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> Can I add multiple keywords separated by commas in the widget or shortcode?</h3>
                                <div>
                                    At this point of time unfortunately you can't do something like: <code>[gotowebinar include="Training, Introduction, New Features"]</code>. Doing this will only include webinars that contain the full phrase <strong>"Training, Introduction, New Features"</strong>. Of course you can use the <strong>include</strong> and <strong>exclude</strong> parameters together.
                                </div>

                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> I added a new webinar or changed the fields of an existing webinar but the changes aren't showing?</h3>
                                <div>So that you don't make too many API calls to GoToWebinar so they don't shut you off from the API and to make your upcoming webinars and registration forms load super fast we have implemented caching. The caching will store your GoToWebinar data for 24 hours or when the next day starts. So if you have added a new webinar or you have changed the registration fields of a webinar this is why your changes aren't showing. It is generally recommended to keep the cache on, but you can turn it off by checking the "Turn Caching Off" checkbox on the <a class="open-tab" href="#general_options">General Options</a> tab. Or you can keep the cache on and just press the "Clear Webinar Cache" button found in the <a class="open-tab" href="#clear_cache">Clear Cache</a> tab.
                                </div>
                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> Does this plugin work with GoToTraining and other Citrix products?</h3>
                                <div>No, just GoToWebinar.
                                </div>
                                
                                
                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> How can I change the translation of the table headings and other elements?</h3>
                                <div><p>WP GoToWebinar has already made headings, hours/minutes and the days of the week translatable. I have already completed the following translations: Arabic, German, Spanish, French, Italian, Chinese Tradition and Simplified and Hindi. So the WP GoToWebinar plugin will output the appropriate language  based on your main Wordpress language setting. If I have made an error in these translations please advise and I will update the plugin.</p>
                                    
                                <p>If there's no translation for your language please use the <a target="_blank" href="https://poedit.net/">Poedit</a> program which is free to create a translation in your language and share it with me and I will put it in the next release. You need to place your .mo and .po file in the <code>inc/lang</code> folder.</p>
                                    
                                <p>You can easily change the field names of the registration form fields and custom messages from the <a class="open-tab" href="#translation">Translation</a> tab.</p> 
                                </div>

                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> Can I customise the look of the widget, upcoming webinar table or registration page?</h3>
                                <div>
                                    <p>I have included a number of CSS classes and ID's so if you're familar with CSS you can make a lot of cosmetic changes without having to go too crazy. There are many CSS plugins you can use to implement custom CSS on your site. Why not try out <a target="_blank" href="https://en-au.wordpress.org/plugins/simple-custom-css/">this one</a> I use. If you want to get more technical you can also use jQuery to make changes.</p>  
                                        
                                    <p>I have also included a way you can customise the shortcodes and widget if you want to make some more full on structural changes to how things are displayed. You do need to know what you're doing to do this i.e. you need to know a bit about PHP and HTML. All you need to do is create a folder in your current theme called <strong>wp-gotowebinar</strong> and copy and paste either the <strong>shortcode.php</strong> (upcoming webinar table), <strong>shortcode-registration.php</strong> (registration form) or <strong>widget-output.php</strong> (widget) files found in the WP GoToWebinar plugin folder into this new folder you created in your theme. You can then customise these files as you please and still receive plugin updates without your changes being overridden. I can't guarantee the compatibility of these custom files so you may need to update these custom files as the plugin changes.</p>   
                                    
                                    <p>I do provide a paid service to do this customisations for you at very reasonable rate! So <a href="https://northernbeacheswebsites.com.au/contact/">contact me</a> to get a quote.</p>  
                                    
                                </div>
                                
                                
                                <h3><i class="fa fa-info-circle" aria-hidden="true"></i> Somethings not working or I would like to customise something?</h3>
                                <div>
                                    <p>Please use the Wordpress forum for this plugin <a target="_blank" href="https://wordpress.org/support/plugin/wp-gotowebinar">here</a> to report any bugs.</p>
                                    
                                    <p>For support on how to use or setup this plugin please upgrade to <a target="_blank" href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/">pro</a> to receive timely support. When you upgrade to pro you can also request features you would like to see in the plugin. I also provide a customisation service if you need to change anything. Please complete this <a target="_blank" href="https://northernbeacheswebsites.com.au/contact/">form</a> for customisations.</p>
                                </div>
                            </div>  <!--end accordion-->
                        </div> <!--end inside-->        
            
        </td>
    </tr>


<?php 
}



function wp_gotowebinar_support_callback(){ 
    ?>
    <tr class="gotowebinar_settings_row" valign="top">
        <td scope="row" colspan="2">
            <h3><?php esc_attr_e('Free Support', 'wp-gotowebinar'); ?></h3>
            <div class="inside">

                <p>Before making a support request please read over the <a class="open-tab" href="#faq">FAQ</a> tab thoroughly as it is likely your request is already answered there. On this page you will also see a walkthrough of all the main features of the plugin. Also make sure you are using the latest version of WordPress and this plugin.</p>
                
                <p><strong style="color:#CC0000; font-size: 16px; font-weight:900;">Before you create a support request on the WordPress Forum, please include the following information otherwise your request may not be answered.</strong></p>

                <p><code><?php echo 'PHP Version: <strong>'.phpversion().'</strong>'; ?></br>
                <?php echo 'Wordpress Version: <strong>'.get_bloginfo('version').'</strong>'; ?></br>
                Plugin Version: <strong><?php echo wpgotowebinar_plugin_get_version(); ?></strong></br>
                Current Theme: <strong><?php 
                $user_theme = wp_get_theme();    
                echo esc_html( $user_theme->get( 'Name' ) );
                ?></strong></br>

                Active Plugins:</br> 
                <?php 
                $active_plugins=get_option('active_plugins');
                $plugins=get_plugins();
                $activated_plugins=array();
                foreach ($active_plugins as $plugin){           
                array_push($activated_plugins, $plugins[$plugin]);     
                } 

                foreach ($activated_plugins as $key){  
                echo '<strong>'.$key['Name'].'</strong></br>';
                }
            
                ?></code></p>
                
                <p>URL's and Screenshots of issues can also be extremely helpful in diagnosing things so please include this where possible.</p>

                <a class="button-secondary" target="_blank" href="https://wordpress.org/support/plugin/wp-gotowebinar" >Create a support request on the forum</a>

                <p>For priority support please <a target="_blank" href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/">upgrade to the pro version of the plugin</a>.</p>

            </div>
        </td>
    </tr>


<?php 
}


function wp_gotowebinar_locked_callback(){ 
    ?>
    <tr class="gotowebinar_settings_row" valign="top">
        <td scope="row" colspan="2">
            <h3><?php esc_attr_e('Please upgrade to PRO to use this feature', 'wp-gotowebinar'); ?></h3>
                        
            <div class="inside"> 
                <p>Sorry this feature is only available to pro users. There's heaps of good reasons to upgrade to pro including being able to connect registration forms with ActiveCampaign, Agile CRM, Campaign Monitor, Constant Contact, Highrise CRM, Hubspot CRM, Insightly CRM, MailChimp, Pipedrive CRM, Salesforce CRM and Zoho CRM. WP GoToWebinar Pro offers the most awesome experience to sell and manage webinars from Wordpress.</p>
                
                <p>You can also monitor webinar performance with our advanced reporting tools from within WordPress. Increase webinar participations and conversions with our webinar countdown toolbar!</p>
                
                <a class="button-primary" target="_blank" href="https://northernbeacheswebsites.com.au/wp-gotowebinar-pro/">LEARN MORE</a>
            </div>   
            
         </td>
    </tr>

<?php 
}           



function gotowebinar_authorization_render(){  wp_gotowebinar_settings_row('gotowebinar_authorization','Authorization','','text','','','','');  
}

function gotowebinar_organizer_key_render() { 
    wp_gotowebinar_settings_row('gotowebinar_organizer_key','Organizer Key','','text','','','','');  
}

function gotowebinar_date_format_render() {
    $values = array("j/n/Y"=>date('j/n/Y'), "n/j/Y"=>date('n/j/Y'), "j M, Y"=>date('j M, Y'), "M j, Y"=>date('M j, Y'));
    
    wp_gotowebinar_settings_row('gotowebinar_date_format','Date Format','','select','',$values,'','');   
}

function gotowebinar_time_format_render() {
    $values = array("g:ia T"=>"12 Hour Time", "H:i T"=>"24 Hour Time");
    
    wp_gotowebinar_settings_row('gotowebinar_time_format','Time Format','','select','',$values,'','');   
}

function gotowebinar_disable_tooltip_render() {                                     wp_gotowebinar_settings_row('gotowebinar_disable_tooltip','Disable GMT Tooltips','(by default a tooltip displays when a user hovers over the start date and start time of your webinar)','checkbox','','','','');   
}

function gotowebinar_tooltip_text_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_tooltip_text_color','Tooltip Text Color','','color','','','','tooltipcolors');   
}

function gotowebinar_tooltip_background_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_tooltip_background_color','Tooltip Background Color','','color','','','','tooltipcolors');   
}

function gotowebinar_tooltip_border_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_tooltip_border_color','Tooltip Border Color','','color','','','','tooltipcolors');   
}

function gotowebinar_icon_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_icon_color','Icon Link and Link Color','','color','','','','');   
}

function gotowebinar_custom_registration_page_render() {                                     wp_gotowebinar_settings_row('gotowebinar_custom_registration_page','Custom Registration Page (Optional)','Select a custom page from the list and when a user clicks on a "Register" link they will be taken to your selected registration page instead of the default GoToWebinar registration page.','page','--Use GoToWebinar Website--','','<strong>Important:</strong> add the shortcode <strong><code>[gotowebinar-reg]</code></strong> on the selected page. If you don\'t do this the registration form won\'t appear.','');   
}

function gotowebinar_custom_thankyou_page_render() {                                     wp_gotowebinar_settings_row('gotowebinar_custom_thankyou_page','Custom Thank You Page (Optional)','Select a custom page from the list and when a user registers successfully for a webinar they will be redirected to this page. Please note that if this field is left blank a default success message will appear. You can customise this default message in the <a class="open-tab" href="#translation">translation tab</a>.','page','--Use Default Message--','','','');   
}

function gotowebinar_button_text_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_button_text_color','Button Text Color','','color','','','','buttoncolors');   
}

function gotowebinar_button_background_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_button_background_color','Button Background Color','','color','','','','buttoncolors');   
}

function gotowebinar_button_border_color_render() {                                     wp_gotowebinar_settings_row('gotowebinar_button_border_color','Button Border Color','','color','','','','buttoncolors');   
}

function gotowebinar_disable_cache_render() {                                     wp_gotowebinar_settings_row('gotowebinar_disable_cache','Turn Caching Off','By default WP GoToWebinar keeps a cache of your upcoming webinars and webinar details to both improve page loading speed and to reduce API calls made to GoToWebinar. This means that old webinars can appear on your site for up to one day. It is recommended to keep the cache on, however for smaller sites it\'s probably ok to turn off.','checkbox','','','','');   
}

function gotowebinar_enable_timezone_conversion_render() {                                     wp_gotowebinar_settings_row('gotowebinar_enable_timezone_conversion','Enable Timezone Conversion Link','By checking this option a timezone conversion link will appear just above the main table display of upcoming webinars and just below the time and date on the registration page which will allow users to convert the webinar time and date into their local time.','checkbox','','','','');   
}

function gotowebinar_timezone_error_message_render() {             wp_gotowebinar_settings_row('gotowebinar_timezone_error_message','Custom Error Message for Failed Location Detection','If the location of the user can\'t be determined please enter a custom error message to display. If left blank the error message displayed will be "Sorry, your location could not be determined.','textarea','','','','timezoneerrormessage');  
}

function gotowebinar_recaptcha_site_key_render() {wp_gotowebinar_settings_row('gotowebinar_recaptcha_site_key','reCAPTCHA Site Key','If you would like to enable Google reCAPTCHA on the registration form please enter in your reCAPTCHA Site Key in this setting. You can get your reCAPTCHA key from <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>. If left blank no reCAPTCHA will be used','text','','','','');  
}

function gotowebinar_registration_confirmation_render() {                                     wp_gotowebinar_settings_row('gotowebinar_registration_confirmation','Registration Confirmation Checkbox','Adds a confirmation checkbox at the end of the registration form. This checkbox needs to be checked before registration will go through.','checkbox','','','','');   
}

function gotowebinar_welcome_message_render() {                                     wp_gotowebinar_settings_row('gotowebinar_welcome_message','Hide Quickstart Guide','Hides the Quickstart Guide notice on this plugin page.','checkbox','','','','hidden-row');   
}

function gotowebinar_tab_memory_render() {                                     wp_gotowebinar_settings_row('gotowebinar_tab_memory','Tab Memory','Remembers the last settings tab','text','','','','hidden-row');   
}







//start translation
function gotowebinar_translate_firstName_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_firstName','First Name','','text','','','','');  
}

function gotowebinar_translate_lastName_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_lastName','Last Name','','text','','','','');  
}

function gotowebinar_translate_email_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_email','Email','','text','','','','');  
}

function gotowebinar_translate_address_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_address','Address','','text','','','','');  
}

function gotowebinar_translate_city_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_city','City','','text','','','','');  
}

function gotowebinar_translate_state_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_state','State','','text','','','','');  
}

function gotowebinar_translate_zipCode_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_zipCode','Zip Code','','text','','','','');  
}

function gotowebinar_translate_country_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_country','Country','','text','','','','');  
}

function gotowebinar_translate_phone_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_phone','Phone','','text','','','','');  
}

function gotowebinar_translate_organization_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_organization','Organisation','','text','','','','');  
}

function gotowebinar_translate_jobTitle_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_jobTitle','Job Title','','text','','','','');  
}

function gotowebinar_translate_questionsAndComments_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_questionsAndComments','Questions and Comments','','text','','','','');  
}

function gotowebinar_translate_industry_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_industry','Industry','','text','','','','');  
}

function gotowebinar_translate_numberOfEmployees_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_numberOfEmployees','Number of Employees','','text','','','','');  
}

function gotowebinar_translate_purchasingTimeFrame_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_purchasingTimeFrame','Purchasing Time Frame','','text','','','','');  
}

function gotowebinar_translate_purchasingRole_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_purchasingRole','Purchasing Role','','text','','','','');  
}

function gotowebinar_translate_submitButton_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_submitButton','<span style="padding: 6px 10px; border: 2px solid black; margin-right: 4px;"><strong>Submit</strong></span> Button','','text','','','','');   
}

function gotowebinar_translate_successMessage_render() {   
    
    
    $values = array('First Name','Last Name','Webinar Title','Webinar Time','Webinar Date','User Email','Registration URL');
    
    wp_gotowebinar_settings_row('gotowebinar_translate_successMessage','Success Message','Please click the below buttons or copy and paste the shortcode into the settings field to create your custom message. Please don\'t use single or double quotes in your custom message!','shortcode','',$values,'',''); 
                                                                                                          
}

function gotowebinar_translate_alreadyRegisteredMessage_render() {  
    
    $values = array('First Name','Last Name','Webinar Title','Webinar Time','Webinar Date','User Email','Registration URL');
    
    wp_gotowebinar_settings_row('gotowebinar_translate_alreadyRegisteredMessage','Already Registered Message','Please click the below buttons or copy and paste the shortcode into the settings field to create your custom message. Please don\'t use single or double quotes in your custom message!','shortcode','',$values,'','');  
}

function gotowebinar_translate_errorMessage_render() {    
    
    $values = array('First Name','Last Name','Webinar Title','Webinar Time','Webinar Date','User Email','Registration URL');
    
    wp_gotowebinar_settings_row('gotowebinar_translate_errorMessage','Error Message','Please click the below buttons or copy and paste the shortcode into the settings field to create your custom message. Please don\'t use single or double quotes in your custom message!','shortcode','',$values,'','');  
}

function gotowebinar_translate_attendeeLimit_render() { 
    
    $values = array('First Name','Last Name','Webinar Title','Webinar Time','Webinar Date','User Email','Registration URL');
    
    wp_gotowebinar_settings_row('gotowebinar_translate_attendeeLimit','Attendee Limit Reached Error Message','Please click the below buttons or copy and paste the shortcode into the settings field to create your custom message. Please don\'t use single or double quotes in your custom message!','shortcode','',$values,'','');  
}

function gotowebinar_translate_cancelledWebinar_render() {             wp_gotowebinar_settings_row('gotowebinar_translate_cancelledWebinar','Content to display on the registration page if a webinar has been cancelled or if there\'s a general error.','','editor','','','','');  
}

function gotowebinar_translate_required_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_required','Required Field Message','This is the error message that appears if a field isn\'t completed on the registration form.','text','','','','');  
}

function gotowebinar_translate_registration_confirmation_message_render(){  wp_gotowebinar_settings_row('gotowebinar_translate_registration_confirmation_message','Registration Confirmation Checkbox Field Title','This translates the text shown on the required confirmation message checkbox field title.','text','','','','confirmation-message');  
}


//function to generate settings rows
function wp_gotowebinar_settings_row($id,$label,$description,$type,$default,$parameter,$importantNote,$rowClass) {
    $options = get_option('gotowebinar_settings');
    
    //value
    if(isset($options[$id])){  
        $value = $options[$id];    
    } elseif(strlen($default)>0) {
        $value = $default;   
    } else {
        $value = '';
    }
    
    
    //the label
    echo '<tr class="gotowebinar_settings_row '.$rowClass.'" valign="top">';
    echo '<td scope="row">';
    echo '<label for="'.$id.'">'.$label;
    if(strlen($description)>0){
        echo ' <i class="fa fa-info-circle information-icon" aria-hidden="true"></i>';
        echo '<p class="hidden"><em>'.$description.'</em></p>';
    }
    if(strlen($importantNote)>0){
        echo '</br><span style="color: #CC0000;">';
        echo $importantNote;
        echo '</span>';
    } 
    echo '</label>';
    
    
    
    if($type == 'shortcode') {
        echo '</br>';
        
        foreach($parameter as $shortcodevalue){
            echo '<a value="['.$shortcodevalue.']" class="button-secondary gotowebinar_append_buttons">['.$shortcodevalue.']</a>';
        }       
    }
    
    

    //the setting    
    echo '</td><td>';
    
    //text
    if($type == "text"){
        echo '<input type="text" class="regular-text" name="gotowebinar_settings['.$id.']" id="'.$id.'" value="'.$value.'">';     
    }
    
    //select
    if($type == "select"){
        echo '<select name="gotowebinar_settings['.$id.']" id="'.$id.'">';
        
        foreach($parameter as $x => $xvalue){
            echo '<option value="'.$x.'" ';
            if($x == $value) {
                echo 'selected="selected"';    
            }
            echo '>'.$xvalue.'</option>';
        }
        echo '</select>';
    }
    
    
    //checkbox
    if($type == "checkbox"){
        echo '<label class="switch">';
        echo '<input type="checkbox" id="'.$id.'" name="gotowebinar_settings['.$id.']" ';
        echo checked($value,1,false);
        echo 'value="1">';
        echo '<span class="slider"></span></label>';
    }
        
    //color
    if($type == "color"){ 
        echo '<input name="gotowebinar_settings['.$id.']" id="'.$id.'" type="text" value="'.$value.'" class="my-color-field" data-default-color="'.$default.'"/>';    
    }
    
    //page
    if($type == "page"){
        $args = array(
            'echo' => 0,
            'selected' => $value,
            'name' => 'gotowebinar_settings['.$id.']',
            'id' => $id,
            'show_option_none' => $default,
            'option_none_value' => "default",
            'sort_column'  => 'post_title',
            );
        
            echo wp_dropdown_pages($args);     
    }
    
    //textarea
    if($type == "textarea" || $type == "shortcode"){
        echo '<textarea cols="46" rows="3" name="gotowebinar_settings['.$id.']" id="'.$id.'">'.$value.'</textarea>';
    }
    
    //editor
    if($type == "editor"){
        wp_editor(html_entity_decode(stripslashes($value)), $id, $settings = array(
            'textarea_name' => 'gotowebinar_settings['.$id.']',
            'drag_drop_upload' => true,
            'textarea_rows' => 7,  
            )
        );
    }
    
    
    //number
    if($type == "number"){
        echo '<input type="number" class="regular-text" name="gotowebinar_settings['.$id.']" id="'.$id.'" value="'.$value.'">';     
    }

    echo '</td></tr>';

}

?>