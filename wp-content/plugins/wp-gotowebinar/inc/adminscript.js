jQuery(document).ready(function ($) {
    
    
    //make tabs tabs
    $( "#tabs" ).tabs();
    
    
    //make links go to particular tabs
    $('.wrap').on("click",".open-tab", function(){
        var tab = $(this).attr('href');
        var index = $(tab).index()-1;        
        $('#tabs').tabs({active: index});
        $('#gotowebinar_tab_memory').val(tab);
    });
    
    
    //add link to hidden link setting when a tab is clicked
    $('.wrap').on("click", ".nav-tab", function () {
        var tab = $(this).attr('href');
        $('#gotowebinar_tab_memory').val(tab);
    });
    
    
    //load previous tab when opening settings page
    if($('#gotowebinar_tab_memory').val().length > 1) {
    var tab = $('#gotowebinar_tab_memory').val();    
    var index = $(tab).index() - 1;
    $('#tabs').tabs({
        active: index
    });
    }


    //when clear cache button is clicked clear the cache by running a function
     $('#gotowebinar_clear_cache').click(function (event) {
        event.preventDefault();
        var data = {
            'action': 'clear_cache',
        };
        jQuery.post(ajaxurl, data, function (response) {
            $('<div class="notice notice-success"><p>The cache was cleared successfully.</p></div>').insertAfter('#gotowebinar_clear_cache');
        });
    });
    
    //deletes log when delete log button is pressed
     $('#gotowebinar_delete_log').click(function (event) {

        event.preventDefault();
        
        var confirmation = confirm("Are you sure you want to delete the log?"); 
         
        if(confirmation == true){
         
            var data = {
                'action': 'delete_log',
            };


            jQuery.post(ajaxurl, data, function (response) {

                $('#log .notice').slideUp();

                $('<div class="notice notice-success"><p>The log was deleted successfully.</p></div>').insertAfter('#gotowebinar_delete_log');
            });

        }
    });
    
    
    //hides colours options when disable tooltip is checked
    if ($('#gotowebinar_disable_tooltip').is(':checked')) {
        $(".tooltipcolors").hide();
    } else {
        $(".tooltipcolors").show();
    }
    $('#gotowebinar_disable_tooltip').click(function () {
        if ($(this).is(':checked')) {
            $(".tooltipcolors").hide();
        } else {
            $(".tooltipcolors").show();
        }
    });
    
    
    //hides timezone error message option when checkbox is unchecked
    if ($('#gotowebinar_enable_timezone_conversion').is(':checked')) {
        $(".timezoneerrormessage").show();
    } else {
        $(".timezoneerrormessage").hide();
    }
    $('#gotowebinar_enable_timezone_conversion').click(function () {
        if ($(this).is(':checked')) {
            $(".timezoneerrormessage").show();
        } else {
            $(".timezoneerrormessage").hide();
        }
    });
    
    //hides confirmation message option when checkbox is unchecked
    if ($('#gotowebinar_registration_confirmation').is(':checked')) {
        $(".confirmation-message").show();
    } else {
        $(".confirmation-message").hide();
    }
    $('#gotowebinar_registration_confirmation').click(function () {
        if ($(this).is(':checked')) {
            $(".confirmation-message").show();
        } else {
            $(".confirmation-message").hide();
        }
    });
    
    
    //hides timezone options when timezone activation is unchecked
    if ($('#gotowebinar_toolbar_activate').is(':checked')) {
        $(".toolbarspecificsetting").show();
    } else {
        $(".toolbarspecificsetting").hide();
    }
    $('#gotowebinar_toolbar_activate').click(function () {
        if ($(this).is(':checked')) {
            $(".toolbarspecificsetting").show();
        } else {
            $(".toolbarspecificsetting").hide();
        }
    });



    
    
    //shows mailchimp/constantcontact options when MailChimp/Constant/activecampaign Contact is selected
    $('#gotowebinar_email_service :selected').each(function () {
        var selectValue = $(this).val();
        $(".emailspecificsetting").hide();
        $("."+selectValue).show();
        
    });
    
    
    $('#gotowebinar_email_service').change(function () {
        var selectValue = $(this).val();
        $(".emailspecificsetting").hide();
        $("."+selectValue).show();
    });
    
    
    
    
    
    //hides button colours when registration page is on the default option i.e the GoToWebinar website registration page
    if ($('#gotowebinar_custom_registration_page').val() == 'default') {
        $(".buttoncolors").hide();
    } else {
        $(".buttoncolors").show();
    }
    $('#gotowebinar_custom_registration_page').change(function () {
        if ($(this).val() == 'default') {
            $(".buttoncolors").hide();
        } else {
            $(".buttoncolors").show();
        }
    });


    //hides and then shows on click help tooltips
    $(".hidden").hide();
    $(".information-icon").click(function (event) {
        event.preventDefault();
        $(this).next(".hidden").slideToggle();
    });

    //instantiates the Wordpress colour picker
    $('.my-color-field').wpColorPicker();



    //get the current url of the page the user is on
    var currentUrl = $(location).attr('href');
    //encode the url so it can be sent as a query string
    var currentUrlEncoded = encodeURIComponent(currentUrl);
    //create a link for our button
    var buttonLink = "https://api.getgo.com/oauth/authorize?client_id=mXgdAmzVs9lGVbECGrUT2ieZePoVmh4z&state=" + currentUrlEncoded;
    //add the button after the authorization field and add the above link to it
    $('<a class="button-secondary" href="' + buttonLink + '">Click here to get Auth and Key</a>').insertAfter('#gotowebinar_authorization');

    
    
    
    //this function can find a parameter in a query string
    function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    var code = getParameterByName('code');
    
    
    
    if ($('#gotowebinar_authorization').val().length == 0 && code != null) {
        
        
        //sets the consumer key of my GoToWebinar app
        var consumerKey = "mXgdAmzVs9lGVbECGrUT2ieZePoVmh4z";

        //performs ajax request to Citrix server to get the users organizer key and access token utilising the key taken from the state query string above 
        jQuery.ajax({
                url: "https://api.getgo.com/oauth/access_token",
                type: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Accept": "application/json",
                },
                contentType: "application/x-www-form-urlencoded",
                data: {
                    "client_id": consumerKey,
                    "grant_type": "authorization_code",
                    "code": code,
                },
            })
            .done(function (data, textStatus, jqXHR) {
                console.log("HTTP Request Succeeded: " + jqXHR.status);
                var organizer_key = data.organizer_key;
                var access_token = data.access_token;
                $('#gotowebinar_authorization').val(access_token);
                $('#gotowebinar_organizer_key').val(organizer_key);
                $('<div class="notice notice-info is-dismissible"><p>Please press Save Settings to finish authentication.</p></div>').insertBefore('#save');
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.log("HTTP Request Failed");

            });
    }

    
    
    
    
    
    
    //turns the faq div at the bottom into an accordion
    $("#accordion").accordion({
        collapsible: true,
        autoHeight: false,
        heightStyle: "content",
        active: false,
        speed: "fast"
    });
    
    
    
    
    //add message when user is editing mailchimp api to save settings to then choose default list

    $('#gotowebinar_mailchimp_api').on('input', function (event) {

        $('.gotowebinar_mailchimp_api').remove();

        $('<div class="notice notice-info is-dismissible gotowebinar_mailchimp_api"><p>Please press Save All Settings to choose a default MailChimp List.</p></div>').insertAfter('#gotowebinar_mailchimp_api');

    });
    
    
    //add button after aweber authorization code to then get additional details
    $('#gotowebinar_aweber_authorization_code').on('input', function (event) {

        $('.gotowebinar_aweber_api').remove();

        $('<a target="_blank" id="aweber-token-generate" class="button-secondary gotowebinar_aweber_api">Click here to get Token</a>').insertAfter('#gotowebinar_aweber_authorization_code');

    });
    
    
    
    $('#gotowebinar_settings_form').on("click","#aweber-token-generate", function(){
        //hide buttonto prevent a double click which would be no fun for the authentication process
        $('#aweber-token-generate').slideUp();
        
        //get the existing authorization code
        var authorizationCode = $('#gotowebinar_aweber_authorization_code').val();
         
        //do ajax call to get the token
        var data = {
            'action': 'aweber_token',
            'authorizationCode': authorizationCode, 
        };
        
        jQuery.post(ajaxurl, data, function (response) {
            
            var positionOfFirstEquals = response.indexOf('=');
            var positionOfFirstAnd = response.indexOf('&');
            var lengthOfResponse = response.length;
            var positionOfAuthToken = response.indexOf('oauth_token=');

            //some temporary data
            var aweberTokenSecret = response.substr(positionOfFirstEquals+1,positionOfFirstAnd-positionOfFirstEquals-1);
            var aweberToken = response.substr(positionOfAuthToken+12);
            
            //lets fill in the token fields
            $('#gotowebinar_aweber_token_secret').val(aweberTokenSecret);
            $('#gotowebinar_aweber_token').val(aweberToken);
                
        });
        
    });
    
    
    
    
    
    
    //add message when user is editing activecampaign api to save settings to then choose default list

    $('#gotowebinar_activecampaign_account').on('input', function (event) {

        $('.gotowebinar_activecampaign_api').remove();

        $('<div class="notice notice-info is-dismissible gotowebinar_activecampaign_api"><p>Please press Save All Settings to choose a default ActiveCampaign List.</p></div>').insertAfter('#gotowebinar_activecampaign_account');

    });
    
    


    //start variable declarations to do Constant Contact Authorization
    
    //get the current url and strip out any existing query strings, if the string doesn't contain any query strings then just use the existing current url
    
    if (currentUrl.indexOf("&") == -1) {
      var currentUrlCleaned =  currentUrl;
        
    } else {
        
      var currentUrlCleaned = currentUrl.slice(0, (currentUrl.length - currentUrl.indexOf("&"))*-1);   
    }
    

    
    var redirectUri = encodeURIComponent('https://northernbeacheswebsites.com.au/redirectconstantcontact?redirect=' + currentUrlCleaned);

    var myApi = "me68vunsy43cw654ydm2tucf";
    //create a link for the button    
    var constantContactLink = "https://oauth2.constantcontact.com/oauth2/oauth/siteowner/authorize?response_type=code&client_id=" + myApi + "&redirect_uri=" + redirectUri;

    //add a button after constant contact field to authorize
    $('<a id="accessToken" class="button-secondary" href="' + constantContactLink + '">Click here to get Token</a>').insertAfter('#gotowebinar_constantcontact_token');
    
    
    var valueCCToken = $('#gotowebinar_constantcontact_token').val();
    
    
    //make requst to CC for access token
        if (valueCCToken != null && valueCCToken.length == 0) {
             
        //get query string paramter called codeCC
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
        //create a variable that gets the code query string from the response from my server
        var codeCC = getParameterByName('codeCC');
                    
            var data = {
            'action': 'constant_contact_token',
            'codeCC': codeCC,
            'redirectUri': redirectUri,  
        };
        jQuery.post(ajaxurl, data, function (response) {
            
            $('#gotowebinar_constantcontact_token').val(response);
            
            
            if($('#gotowebinar_constantcontact_token').length >0){
            
            $('<div class="notice notice-info is-dismissible"><p>Please press Save All Settings to choose a default Constant Contact List.</p></div>').insertAfter('#accessToken');
            }
                
        });
        } //end if
    
    
    
    
    
    
    
    
    //adds button text to text area for success, error and already registered setting - used for translation options
    
    
    $('.gotowebinar_append_buttons').click(function() { 
    $(this).parent().next().children().val($(this).parent().next().children().val() + $(this).attr("value")); 
    $(this).parent().next().children().focus();      
    });
    

    
    
    //hide loading spinners
    $('.create-gotowebinar-product').parent().find('.fa-spinner').hide();
    $('.edit-gotowebinar-product').parent().find('.fa-spinner').hide();
    
    //creates product
    $('.create-gotowebinar-product').click(function(event) { 
       event.preventDefault();
    
    var webinarId = this.id;    
    var webinarPrice = $(this).parent().prev().children().val();  
        
    console.log(webinarId);
    console.log(webinarPrice);        
    
    if(webinarPrice.length <1 || $.isNumeric(webinarPrice) == false ){ 
     console.log('You need to enter a price'); 
    
    $(this).parent().prev().children().effect("shake", { times:3 }, 300);    
        
    $(this).parent().prev().append('<p class="price-warning">Please enter a price like 50</p>');
        
    setTimeout(function() {
    $('.price-warning').slideUp();
    }, 3000);    
        
        
    } else {
           
        
        $('.spinner-'+webinarId).show();
        
        var data = {
			'action': 'create_product',
			'webinarPrice': webinarPrice,
            'webinarId': webinarId,
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
            
            $('.spinner-'+webinarId).hide();
            
            var createButtonFind = '.create-gotowebinar-product-'+webinarId;     
            
            
            $(createButtonFind).after('<p class="product-created-message">Product created!</p>');
            
            setTimeout(function() {
            $('.product-created-message').slideUp();
            }, 3000);
            
            $(createButtonFind).after('<a title="Delete Product" style="margin-right: 10px; margin-top:5px; margin-bottom:5px;" class="button-secondary delete-gotowebinar-product" data='+webinarId+' id="'+response+'"><i class="fa fa-trash" aria-hidden="true"></i> Product</a>');
                        
            $(createButtonFind).after('<a title="Edit Product" style="margin-right: 10px; margin-top:5px; margin-bottom:5px;" href="post.php?post='+response+'&amp;action=edit" class="button-secondary edit-gotowebinar-product"><i class="fa fa-pencil" aria-hidden="true"></i> Product</a>');
            
            $(createButtonFind).parent().prev().children().prop('readonly',true);
            
            
            $(createButtonFind).remove();
            


            
		});
        
        
    }   
        
    });
    
    
    
    
    
    
    
    
    
    
    
    
    //deletes product

    $('td').on("click",".delete-gotowebinar-product", function(){
      
        var webinarId = $(this).attr('data'); 
        var postId = this.id;  
        
        
        var webinarTitle = $(this).parent().prev().prev().prev().text();
        
        var confirmationOfDelete = confirm('Are you sure you want to delete the webinar product: '+webinarTitle+'? Note: this won\'t delete the webinar from GoToWebinar it will just delete the WooCommerce product.');
        
        if (confirmationOfDelete == true) {
            
        $('.spinner-'+webinarId).show(); 
            
           var data = {
			'action': 'delete_product',
			'postId': postId,
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) { 
         
            $('.spinner-'+webinarId).hide();
        
            
             $('#row-'+webinarId).slideUp();
            
            var data = {
                'action': 'create_product_log',
                'type': 'success',
                'message': 'Webinar Product Deleted - '+webinarTitle,
            };

            jQuery.post(ajaxurl, data, function (response) {});

            
        });   
            
            
        } //end if condition of confirmation dialog
        
    }); // end delete product
    

    





    //deletes webinar

    $('td').on("click",".delete-gotowebinar-webinar", function(){
      
        var webinarId = $(this).attr('id'); 
                
        var webinarTitle = $(this).parent().prev().prev().prev().text();
        
        var confirmationOfDelete = confirm('Are you sure you want to delete the webinar: '+webinarTitle+'? Important: this will delete the webinar from GoToWebinar. Note: Upon page refresh the webinar may still appear in your listing due to caching.');
        
        if (confirmationOfDelete == true) {
            
        $('.spinner-'+webinarId).show(); 
            
           var data = {
			'action': 'delete_webinar',
			'webinarId': webinarId,
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) { 
         
            $('.spinner-'+webinarId).hide();

            $('#row-'+webinarId).slideUp();
            
            var data = {
                    'action': 'create_product_log',
                    'type': 'success',
                    'message': 'Webinar Deleted - '+webinarTitle,
                };

            jQuery.post(ajaxurl, data, function (response) {});
            
        });   
            
            
        } //end if condition of confirmation dialog
        
    }); // end delete product
    

    



    
    
    //add a new line on plus button for webinar series option when creating a webinar
    $('body').on('click','.add_timing',function() {
       
        $('.datepicker').datepicker('destroy');  
        
        
    $('.remove_timing').show();    
    var timing = $(this).closest('.gotowebinar_create_webinar_single');
    var clone = timing.clone();
    clone.find(':text').val('');
    clone.find('.gotowebinar_create_webinar_timings_startDate').attr('id',Math.floor((Math.random() * 9999999) + 1));
    clone.find('.gotowebinar_create_webinar_timings_endDate').attr('id',Math.floor((Math.random() * 9999999) + 1));    
    timing.after(clone);
    $('.remove_timing').first().hide();
        
        

        
    $( '.gotowebinar_create_webinar_timings_startDate' ).on( 'change paste keyup' , function () {
    $( this ).parent().find('.gotowebinar_create_webinar_timings_endDate').val( $( this ).val() );
} );      
     
    $( '.gotowebinar_create_webinar_timings_startTime' ).on( 'change paste keyup' , function () {
    $( this ).parent().find('.gotowebinar_create_webinar_timings_endTime').val( $( this ).val() );
} ); 
        

        
        
        
        
        
        
    
    //fire date picker
    $('.datepicker').datepicker({  
dateFormat:"yy-mm-dd",    
}); 
        
    $('.timepicker').timepicker({
    timeFormat: 'HH:mm',
    dropdown: false,
});    
        
        
        
    }); //end add timing function
    
    
    

    //remove a new line on plus button for webinar series option when creating a webinar
    $('.remove_timing').live('click', function() {
    var timing = $(this).closest('.gotowebinar_create_webinar_single');
    
    
    var webinarTimingLength = $('.remove_timing').length;
            
    if (webinarTimingLength > 1){
       timing.remove(); 
        
    }
        
        
    }); //end add timing function
    
    
    $('#gotowebinar_create_webinar_type').on('change', function() {
    if($(this).val() == "series") {
        $('.gotowebinar_create_webinar_single i').show();
        $('.remove_timing').show();
        $('.remove_timing').first().hide();
        $('.gotowebinar_create_webinar_sequence').hide();
        $('.gotowebinar_create_webinar_single').show();
    }
    });
    
    
    $('#gotowebinar_create_webinar_type').on('change', function() {
    if($(this).val() == "single_session") {
        $('.remove_timing').show();
        $('.gotowebinar_create_webinar_single i').hide();
        $('.gotowebinar_create_webinar_sequence').hide();
        $('.gotowebinar_create_webinar_single').not(':first').remove(); 
        $('.gotowebinar_create_webinar_single').show();
//        $('.gotowebinar_create_webinar_single').first().show(); 
    }
    });
    
    
    $('#gotowebinar_create_webinar_type').on('change', function() {
    if($(this).val() == "sequence") {
        $('.gotowebinar_create_webinar_sequence').show();
        $('.gotowebinar_create_webinar_single').hide();
    }
    });
    
    

$( '.gotowebinar_create_webinar_timings_startDate' ).on( 'change paste keyup' , function () {
    $( this ).parent().find('.gotowebinar_create_webinar_timings_endDate').val( $( this ).val() );
} ); 
    
$( '.gotowebinar_create_webinar_timings_startTime' ).on( 'change paste keyup' , function () {
    $( this ).parent().find('.gotowebinar_create_webinar_timings_endTime').val( $( this ).val() );
} );     
    

$( '.gotowebinar_create_webinar_timings_endDate' ).on( 'change paste keyup' , function () {
    $( this ).parent().find('.gotowebinar_create_webinar_timings_recurrenceDate').val( $( this ).val() );
} );        
    
    

    //set timezone to users current timezone
    var usersTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    $( "#gotowebinar_create_webinar_timeZone" ).val(usersTimezone);
    

    

    
    //hide spinner
    $('.spinner-create-webinar').hide();
    
    
    
    
    
    
    
    
    //create a webinar
     $('#create-webinar').click(function (event) {
        event.preventDefault();
         
         
         
        //create a variable that counts errors
        var inputErrors = 0; 
        var inputErrorsNormal = 0;
        var inputErrorsSequence = 0;  
        
        //check main standard fields 
        if($('#gotowebinar_create_webinar_subject').val().length <1){
            $('#gotowebinar_create_webinar_subject').effect("shake", { times:3 }, 300);
            inputErrorsNormal++;
            inputErrorsSequence++; 
        } else if ($('#gotowebinar_create_webinar_description').val().length <1) {
            $('#gotowebinar_create_webinar_description').effect("shake", { times:3 }, 300);
            inputErrorsNormal++;
            inputErrorsSequence++; 
        } else if ($('#gotowebinar_authorization').val().length <1) {
            $('#gotowebinar_authorization').effect("shake", { times:3 }, 300);
            inputErrorsNormal++;
            inputErrorsSequence++; 
        }  else if ($('#gotowebinar_organizer_key').val().length <1) {
            $('#gotowebinar_organizer_key').effect("shake", { times:3 }, 300);
            inputErrorsNormal++;
            inputErrorsSequence++; 
        } 
         
         //check dynamic fields 
         $('.gotowebinar_create_webinar_single input').each(function(){
            if($(this).val().length <1 && ($('#gotowebinar_create_webinar_type').val() == 'series' || $('#gotowebinar_create_webinar_type').val() == 'single_session')){   
            $(this).parent().effect("shake", { times:3 }, 300);
            inputErrorsNormal++;    
            }
         });
         
         
         
         
         
         
         //check if start date and time is less than end date and time
         $('.gotowebinar_create_webinar_single').each(function(){
             
            if(($(this).find('.gotowebinar_create_webinar_timings_startDate').val() >= $(this).find('.gotowebinar_create_webinar_timings_endDate').val() &&  $(this).find('.gotowebinar_create_webinar_timings_startTime').val() >= $(this).find('.gotowebinar_create_webinar_timings_endTime').val() ) ||  $(this).find('.gotowebinar_create_webinar_timings_startDate').val() > $(this).find('.gotowebinar_create_webinar_timings_endDate').val()) { 
                
            $(this).effect("shake", { times:3 }, 300);
            inputErrorsNormal++;    
            }
         });
         
         
         
         $('.gotowebinar_create_webinar_sequence input').each(function(){
            if($(this).val().length <1 && $('#gotowebinar_create_webinar_type').val() == 'sequence'){   
            $(this).parent().effect("shake", { times:3 }, 300);
            inputErrorsSequence++;    
            }
         });
         
         
         //check if start date and time is less than end date and time

         $('.gotowebinar_create_webinar_sequence').each(function(){
            
            if($('#gotowebinar_create_webinar_type').val() == 'sequence'){ 
             
                if($(this).find('.gotowebinar_create_webinar_timings_startDate').val() > $(this).find('.gotowebinar_create_webinar_timings_endDate').val() || ($(this).find('.gotowebinar_create_webinar_timings_startDate').val() >= $(this).find('.gotowebinar_create_webinar_timings_endDate').val() &&  $(this).find('.gotowebinar_create_webinar_timings_startTime').val() >= $(this).find('.gotowebinar_create_webinar_timings_endTime').val() ) || $(this).find('.gotowebinar_create_webinar_timings_endDate').val() > $(this).find('.gotowebinar_create_webinar_timings_recurrenceDate').val()){  

                $(this).effect("shake", { times:3 }, 300);
                inputErrorsSequence++;    
                }
            }
         });
         
        
        //check if there are only errors with the webinar type, this prevents an error ocurring in a non-selected webinar type 
        if($('#gotowebinar_create_webinar_type').val() == "sequence"){
          inputErrors = inputErrorsSequence; 
        } else {
          inputErrors = inputErrorsNormal;  
        }
         
         
        //check if there are any errors, if there aren't any errors do api call 
        if(inputErrors == 0){
            
            
        //show spinner
        $('.spinner-create-webinar').show();    
        
        var timeZone = $('#gotowebinar_create_webinar_timeZone').val(); 
            
        //get input values for single or series webinar and store the object into an array    
        var webinarTimes = Array();    
            
        $('.gotowebinar_create_webinar_single').each(function(){
            
            //in the below code we use moment.js to convert the time into the selected timezone by changing the offset to the correct amount
            
            var startDate = $(this).find('.gotowebinar_create_webinar_timings_startDate').val();
            var startTime = $(this).find('.gotowebinar_create_webinar_timings_startTime').val();
            var startCombined = moment.tz(startDate+' '+startTime,timeZone);
            startCombined = startCombined.format();
   

            var endDate = $(this).find('.gotowebinar_create_webinar_timings_endDate').val();
            var endTime = $(this).find('.gotowebinar_create_webinar_timings_endTime').val();
            var endCombined = moment.tz(endDate+' '+endTime,timeZone);
            endCombined = endCombined.format();
            
            var webinarTime = {};
            webinarTime["endTime"] = endCombined;
            webinarTime["startTime"] = startCombined;
            
            webinarTimes.push(webinarTime);
            
        });
            
            
        var webinarSubject = $('#gotowebinar_create_webinar_subject').val();
        
        var data = {
                    "subject": $('#gotowebinar_create_webinar_subject').val(),
                    "description": $('#gotowebinar_create_webinar_description').val(),
                    "timeZone": timeZone,
                    "type": $('#gotowebinar_create_webinar_type').val(),
                    "isPasswordProtected": false,
                    };  
        
        if($('#gotowebinar_create_webinar_type').val() == "sequence"){
         

          var startDate = $('.gotowebinar_create_webinar_sequence').find('.gotowebinar_create_webinar_timings_startDate').val();
          var startTime = $('.gotowebinar_create_webinar_sequence').find('.gotowebinar_create_webinar_timings_startTime').val();
          var startCombined = moment.tz(startDate+' '+startTime,timeZone);
          startCombined = startCombined.format();    
            
          var endDate = $('.gotowebinar_create_webinar_sequence').find('.gotowebinar_create_webinar_timings_endDate').val();
          var endTime = $('.gotowebinar_create_webinar_sequence').find('.gotowebinar_create_webinar_timings_endTime').val();
          var endCombined = moment.tz(endDate+' '+endTime,timeZone);
          endCombined = endCombined.format(); 
            
          var webinarTimeSequence = {};
          webinarTimeSequence["startTime"] = startCombined;
          webinarTimeSequence["endTime"] = endCombined;   
            
            
            
            
            
         data["recurrenceStart"] = webinarTimeSequence;  
            
         var recurrenceEnd = $('.gotowebinar_create_webinar_timings_recurrenceDate').val();  
         var recurrencePattern = $('.gotowebinar_create_webinar_timings_pattern').val();
            
         data["recurrenceEnd"] = recurrenceEnd;
         data["recurrencePattern"] = recurrencePattern;    
 
            
        } else {
         data["times"] = webinarTimes;   
        }    
            
             
        var postJson = JSON.stringify(data); 
            
            

            jQuery.ajax({
                url: "https://api.getgo.com/G2W/rest/organizers/"+$('#gotowebinar_organizer_key').val()+"/webinars",
                type: "POST",
                headers: {
                    "Authorization": $('#gotowebinar_authorization').val(),
                    "Content-Type": "application/json; charset=utf-8",
                },
                contentType: "application/json",
                data: postJson
            })
            .done(function(data, textStatus, jqXHR) {
                $('.spinner-create-webinar').hide();
                
                $('#create-webinar').after('<p class="webinar-created-message">Webinar created!</p>');
                
                setTimeout(function() {
                $('.webinar-created-message').slideUp();
                }, 3000);
                
//                console.log("HTTP Request Succeeded: " + jqXHR.status);
//                console.log(data);
                
                var data = {
                    'action': 'create_product_log',
                    'type': 'success',
                    'message': 'Webinar Created - '+webinarSubject,
                };

                jQuery.post(ajaxurl, data, function (response) {});

                                
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                
                $('.spinner-create-webinar').hide();
                $('#create-webinar').after('<p class="webinar-created-message">Something went wrong.</p>');
                
                setTimeout(function() {
                $('.webinar-created-message').slideUp();
                }, 3000);
                
//                console.log(errorThrown);
//                console.log(textStatus);
//                console.log("HTTP Request Failed: " + jqXHR.status);
                
                var data = {
                    'action': 'create_product_log',
                    'type': 'error',
                    'message': 'Webinar Not Created - '+webinarSubject,
                };

                jQuery.post(ajaxurl, data, function (response) {});
                
            })
            .always(function() {
                /* ... */
            });
            
            
            
        } //end if input errors
         
    
     }); //end create webinar button click
    
 
    
    
    //make list sortable
    $( "#webinar-timings-list" ).sortable();
//    $( "#webinar-timings-list" ).disableSelection();
    
 
    
    
    
    
    
    
    
    
    
    //get registrants
    $('#tabs').on("click",".get-registrants",function() { 
                    
    var webinarId = this.id;     
    var authorization = $('#gotowebinar_authorization').val();
    var organizerKey = $('#gotowebinar_organizer_key').val();
    var closestRegistrationInfoHeading = $(this).parent().parent().next();    
    var closestRegistrationInfoData = $(this).parent().parent().next().next(); 
         
    //check auth and organizer key exists
         
    if(authorization.length <1) {
        $(authorization).effect("shake", { times:3 }, 300);  
    } else if (organizerKey.length <1) {
        $(organizerKey).effect("shake", { times:3 }, 300);     
    } else {
     
    //show spinner    
    $('.spinner-'+webinarId).show();  
    $('.registrant-info-data-'+webinarId).remove();    
        
        
        
    //do request    
    var data = {
	'action': 'get_registrants',
	'webinarId': webinarId,
		};


	jQuery.post(ajaxurl, data, function(response) { 
         
    $('.spinner-'+webinarId).hide(); 
    $(closestRegistrationInfoHeading).show();
            
    $(closestRegistrationInfoHeading).after(response);    
        
            
    });   
               

    } //end else condition i.e. the request   
             
         
     }); //end get registrants
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    //delete registrant
    
    $('#tabs').on("click",".delete-registrant",function() {
    
        var registrantKey = this.id;   
        var webinarId = $(this).attr("data");
        var authorization = $('#gotowebinar_authorization').val();
        var organizerKey = $('#gotowebinar_organizer_key').val();
        var registrantRow = $(this).parent().parent();
        var registrantFirstName = $(this).parent().parent().find('.registrant-first-name').text();
        var registrantLastName = $(this).parent().parent().find('.registrant-last-name').text(); 
        
        
    if(authorization.length <1) {
        $(authorization).effect("shake", { times:3 }, 300);  
    } else if (organizerKey.length <1) {
        $(organizerKey).effect("shake", { times:3 }, 300);     
    } else {
     
        
        
    var confirmationOfDelete = confirm('Are you sure you want to remove '+registrantFirstName+' '+registrantLastName+' from the webinar?');
        
    if (confirmationOfDelete == true) {
        
        
        
    //show spinner    
    $('.spinner-'+webinarId).show();     
        
jQuery.ajax({
    url: "https://api.getgo.com/G2W/rest/organizers/"+organizerKey+"/webinars/"+webinarId+"/registrants/"+registrantKey,
    type: "DELETE",
    headers: {
        "Authorization": authorization,
    },
})
.done(function(data, textStatus, jqXHR) {
    
    //hide spinner    
    $('.spinner-'+webinarId).hide(); 
    
    $(registrantRow).slideUp();
    
    console.log("HTTP Request Succeeded: " + jqXHR.status);
    
    var data = {
                    'action': 'create_product_log',
                    'type': 'success',
                    'message': 'Registrant Deleted - '+webinarId+' - '+registrantFirstName+' '+registrantLastName,
                };

    jQuery.post(ajaxurl, data, function (response) {});
    
    
    
})
.fail(function(jqXHR, textStatus, errorThrown) {
    console.log("HTTP Request Failed");
    
    var data = {
                    'action': 'create_product_log',
                    'type': 'error',
                    'message': 'Registrant Not Deleted - '+webinarId+' - '+registrantFirstName+' '+registrantLastName,
                };

    jQuery.post(ajaxurl, data, function (response) {});
    
})
.always(function() {
    /* ... */
});  
 
 } //end confirmation of delete
 } //end else statement
        
        
        
}); //end delete registrant on click
    
    



//save dismiss notice
$('.wrap').on("click",".wpgotowebinar-welcome button",function() {
        
    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'disable_welcome_message'
        }
    })
  
});
    

// make datepicker and timepicker work on all browsers    
 
if($('.datepicker').length){    
$('.datepicker').datepicker({  
dateFormat:"yy-mm-dd",    
});   
}

if($('.timepicker').length){      
$('.timepicker').timepicker({
    timeFormat: 'HH:mm',
    dropdown: false,
});
}
    
    

//save settings using ajax    
$('#gotowebinar_settings_form').submit(function() {
        
    $('<div class="notice notice-warning is-dismissible settings-loading-message"><p><i class="fa fa-spinner" aria-hidden="true"></i> Please wait while we save the settings...</p></div>').insertAfter('.gotowebinar-save-all-settings-button');
    
    tinyMCE.triggerSave();

    $(this).ajaxSubmit({
        success: function(){

            $('.settings-loading-message').remove();

            $('<div class="notice notice-success is-dismissible settings-saved-message"><p>The settings have been saved.</p></div>').insertAfter('.gotowebinar-save-all-settings-button');
            
            setTimeout(function() {
                $('.settings-saved-message').slideUp();
            }, 3000);
            
            
            if($('.ui-tabs-active').attr('aria-controls')=='integration'){
                location.reload();
                $('<div class="notice notice-info is-dismissible reload-message"><p>Please wait as we reload the page.</p></div>').insertAfter('.gotowebinar-save-all-settings-button');
            }
            
            

        }
    });
    
    return false; 
    
    $('.settings-loading-message').remove();
    
});
    
    

//get chart data
    
$('.wrap').on("click","#refresh-performance-data",function(event) {
    event.preventDefault();
    
    $('<div class="notice notice-warning is-dismissible refresh-performance-data-loading-message"><p><i class="fa fa-spinner" aria-hidden="true"></i> Please wait while we fetch this data. This may take several minutes! On the positive side the next time you fetch this data it will be loaded more quickly :)</p></div>').insertAfter('#refresh-performance-data');
    
    //remove existing data
    $("#performanceOverview").empty();
    $("#percentageAttendance").empty();
    $("#registrantCount").empty();
    $("#averageInterestRating").empty();
    $("#averageAttendanceTimeMinutes").empty();
    $("#averageAttentiveness").empty();
    $("#allData").empty();
    $(".data-performance-heading").hide();
    
    
    

    
    var startDate = $('#gotowebinar_performance_startDate').val();
    var endDate = $('#gotowebinar_performance_endDate').val();
    
    var startDateSplit = startDate.split('-');
    var startDateObject = new Date(startDateSplit[0],startDateSplit[1]-1,startDateSplit[2]);
    
    var endDateSplit = endDate.split('-');
    var endDateObject = new Date(endDateSplit[0],endDateSplit[1]-1,endDateSplit[2]);
    
    var monthsDifference = endDateObject.getMonth() - startDateObject.getMonth()
       + (12 * (endDateObject.getFullYear() - startDateObject.getFullYear()));
    
    var todaysDate = new Date();
    
    if(monthsDifference<4){
        var gridLineCount = 4;
    } else {
        var gridLineCount = monthsDifference; 
    }
    
    
    if(startDate.length < 1 || endDate.length < 1 || startDateObject >= endDateObject || endDateObject > todaysDate){
        
        $('.refresh-performance-data-loading-message').remove();
        
        $('#gotowebinar_performance_startDate').effect("shake", { times:3 }, 300);
        $('#gotowebinar_performance_endDate').effect("shake", { times:3 }, 300);
        
        $('<div class="notice notice-error is-dismissible refresh-performance-data-error-message"><p>Make sure there\'s both a start and end date specified. Make sure the start date is earlier than the end date. Make sure the end date is not in the future.</p></div>').insertAfter('#refresh-performance-data');

            setTimeout(function() {
                $('.refresh-performance-data-error-message').slideUp();
            }, 8000);
        
    } else {
        
        
        //do request    
        var data = {
        'action': 'performance',
        'startDate': startDate,
        'endDate': endDate,    
        };

        jQuery.post(ajaxurl, data, function(response) { 

            //remove loading message
            $('.refresh-performance-data-loading-message').remove();

            $('<div class="notice notice-success is-dismissible refresh-performance-data-success-message"><p>The data has successfully loaded.</p></div>').insertAfter('#refresh-performance-data');

            setTimeout(function() {
                $('.refresh-performance-data-success-message').slideUp();
            }, 3000);
            
            $(".data-performance-heading").show();


            //parse the response
            var requestedData = JSON.parse(atob(response));
    //        console.log(requestedData);

            //get the length of the data for looping purposes
            var requestedDataLength = requestedData.length;

            //load google charts
            google.charts.load('current', {'packages':['corechart','table']});




            //create array which will contain processed data
            var percentageAttendanceArrayProcessed = [];
            var registrantCountArrayProcessed = [];
            var averageInterestRatingArrayProcessed = [];
            var averageAttendanceTimeMinutesArrayProcessed = [];
            var averageAttentivenessArrayProcessed = [];
            var allDataArrayProcessed = [];
            
            //create arrays for averages
            var percentageAttendanceAverageData = [];
            var registrantCountAverageData = [];
            var attendeesCountAverageData = [];
            var averageInterestRatingAverageData = [];
            var averageAttendanceTimeMinutesAverageData = [];
            var averageAttentivenessAverageData = [];
            
            
            
            
            //loop through each item in the array
            for (var i = 0; i < requestedDataLength; i++) {
                //declare common variables
                var date = new Date(requestedData[i][0]);
                var webinarName = requestedData[i][1];
                var niceDate = date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear();
                
                //declare specific data variables
                var dataPercentageAttendance = Math.round(requestedData[i][2]);
                var dataRegistrants = Math.round(requestedData[i][3]);
                var dataAttendees = Math.round(requestedData[i][4]);
                var dataAverageInterestRating = Math.round(requestedData[i][5]);
                var dataAverageAttendanceTimeMinutes = Math.round(requestedData[i][6]/60);
                var dataAverageAttentiveness = Math.round(requestedData[i][7]);
                
                //push data to arrays for averages
                percentageAttendanceAverageData.push(dataPercentageAttendance);
                registrantCountAverageData.push(dataRegistrants);
                attendeesCountAverageData.push(dataAttendees);
                averageInterestRatingAverageData.push(dataAverageInterestRating);
                averageAttendanceTimeMinutesAverageData.push(dataAverageAttendanceTimeMinutes);
                averageAttentivenessAverageData.push(dataAverageAttentiveness);

                
                //declare tooltips via tooltip builder function
                var tooltipPercentageAttendance = customToolTipHtml(dataPercentageAttendance+'%',niceDate,webinarName,'Percentage Attendance');
                var tooltipAttendees = customToolTipHtml(dataAttendees,niceDate,webinarName,'Attendees Count');
                var tooltipRegistrants = customToolTipHtml(dataRegistrants,niceDate,webinarName,'Registrant Count');    
                var tooltipAverageInterestRating = customToolTipHtml(dataAverageInterestRating,niceDate,webinarName,'Average Interest Rating');
                var tooltipAverageAttendanceTimeMinutes = customToolTipHtml(dataAverageAttendanceTimeMinutes+' minutes',niceDate,webinarName,'Average Attendance Time in Minutes');
                var tooltipAverageAttentiveness = customToolTipHtml(dataAverageAttentiveness+'%',niceDate,webinarName,'Average Attentiveness');
                
                //put data into a temporary array
                var percentageAttendanceTempArray = [date,dataPercentageAttendance,tooltipPercentageAttendance];
                var registrantCountTempArray = [date,dataRegistrants,tooltipRegistrants,dataAttendees,tooltipAttendees];
                var averageInterestRatingTempArray = [date,dataAverageInterestRating,tooltipAverageInterestRating];
                var averageAttendanceTimeMinutesTempArray = [date,dataAverageAttendanceTimeMinutes,tooltipAverageAttendanceTimeMinutes];
                var averageAttentivenessTempArray = [date,dataAverageAttentiveness,tooltipAverageAttentiveness];
                var allDataTempArray = [{v:date, f:niceDate},webinarName,{v:dataPercentageAttendance, f:dataPercentageAttendance+'%'}, dataRegistrants, dataAttendees, dataAverageInterestRating,{v:dataAverageAttendanceTimeMinutes, f:dataAverageAttendanceTimeMinutes+' minutes'},{v:dataAverageAttentiveness, f:dataAverageAttentiveness+'%'}];
                
                //push data to our processsed array
                percentageAttendanceArrayProcessed.push(percentageAttendanceTempArray);
                registrantCountArrayProcessed.push(registrantCountTempArray);
                averageInterestRatingArrayProcessed.push(averageInterestRatingTempArray);
                averageAttendanceTimeMinutesArrayProcessed.push(averageAttendanceTimeMinutesTempArray);
                averageAttentivenessArrayProcessed.push(averageAttentivenessTempArray);
                allDataArrayProcessed.push(allDataTempArray);
                
            }
            
            //function to get average
            function getAverage(grades) {
              return Math.round(grades.reduce(function (p, c) {
              return p + c;
            }) / grades.length);
            }
            
            //create variables for the average which calls upon the average function
            var percentageAttendanceAverage = getAverage(percentageAttendanceAverageData);
            var registrantCountAverage = getAverage(registrantCountAverageData);
            var attendeesCountAverage = getAverage(attendeesCountAverageData);
            var averageInterestRatingAverage = getAverage(averageInterestRatingAverageData);
            var averageAttendanceTimeMinutesAverage = getAverage(averageAttendanceTimeMinutesAverageData);
            var averageAttentivenessAverage = getAverage(averageAttentivenessAverageData);
            
            
            var performanceOverview = '';
            
            performanceOverview += performanceOverviewHtml(percentageAttendanceAverage,'Average Attendance Percent');
            
            performanceOverview += performanceOverviewHtml(registrantCountAverage,'Average Registrant Count');
            
            performanceOverview += performanceOverviewHtml(attendeesCountAverage,'Average Attendee Count');
            
            performanceOverview += performanceOverviewHtml(averageInterestRatingAverage,'Average Interest Rating');
            
            performanceOverview += performanceOverviewHtml(averageAttendanceTimeMinutesAverage,'Average Attendance Time Minutes');
            
            performanceOverview += performanceOverviewHtml(averageAttentivenessAverage,'Average Attentiveness Percent');
  
            //put variable into html
            $( "#performanceOverview" ).append(performanceOverview);
            
            
            


            google.charts.setOnLoadCallback(percentageAttendance);
            function percentageAttendance() {

            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Percentage Attendance');
            data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});

            data.addRows(percentageAttendanceArrayProcessed);


            var options = {
        //      title: 'Percentage Attendance',
              tooltip: {isHtml: true},    
              curveType: 'function', 
              pointSize: 8,
              lineWidth: 2,      
              height: 500,
              hAxis: {
                format: 'MMMM yyyy',
                gridlines: {count: gridLineCount}
              },
              vAxis: {
                gridlines: {color: 'none'},
                minValue: 0,
                maxValue: 100
              }
            };

            var percentageAttendance = new google.visualization.LineChart(document.getElementById('percentageAttendance'));

            percentageAttendance.draw(data, options);

            }




            google.charts.setOnLoadCallback(registrantCount);
            function registrantCount() {

            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Registrant Count');
            data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});    
            data.addColumn('number', 'Attendees Count');
            data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});

            data.addRows(registrantCountArrayProcessed);

            var options = {
        //      title: 'Percentage Attendance',
              tooltip: {isHtml: true},     
              height: 500,
              curveType: 'function',
              pointSize: 8,
              lineWidth: 2,      
              hAxis: {
                format: 'MMMM yyyy',
                gridlines: {count: gridLineCount}
              },
              vAxis: {
                gridlines: {color: 'none'},
                minValue: 0
              }
            };

            var registrantCount = new google.visualization.LineChart(document.getElementById('registrantCount'));

            registrantCount.draw(data, options);

            }



            

            google.charts.setOnLoadCallback(averageInterestRating);
            function averageInterestRating() {

            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Average Interest Rating');
            data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});

            data.addRows(averageInterestRatingArrayProcessed);

            var options = {
        //      title: 'Percentage Attendance',
              tooltip: {isHtml: true},     
              curveType: 'function', 
              pointSize: 8,
              lineWidth: 2,    
              height: 500,
              hAxis: {
                format: 'MMMM yyyy',
                gridlines: {count: gridLineCount}
              },
              vAxis: {
                gridlines: {color: 'none'},
                minValue: 0,
                maxValue: 100  
              }
            };

            var averageInterestRating = new google.visualization.LineChart(document.getElementById('averageInterestRating'));

            averageInterestRating.draw(data, options);

            }




            google.charts.setOnLoadCallback(averageAttendanceTimeMinutes);
            function averageAttendanceTimeMinutes() {

            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Average Attendance Time in Minutes');
            data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});

            data.addRows(averageAttendanceTimeMinutesArrayProcessed);

            var options = {
//              title: 'Percentage Attendance',
              tooltip: {isHtml: true},     
              curveType: 'function',
              pointSize: 8,
              lineWidth: 2,     
              height: 500,
              hAxis: {
                format: 'MMMM yyyy',
                gridlines: {count: gridLineCount}
              },
              vAxis: {
                gridlines: {color: 'none'},
                minValue: 0
              }
            };

            var averageAttendanceTimeMinutes = new google.visualization.LineChart(document.getElementById('averageAttendanceTimeMinutes'));

            averageAttendanceTimeMinutes.draw(data, options);

            }




            google.charts.setOnLoadCallback(averageAttentiveness);
            function averageAttentiveness() {

            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Average Attentiveness');
            data.addColumn({'type': 'string', 'role': 'tooltip', 'p': {'html': true}});

            data.addRows(averageAttentivenessArrayProcessed);

            var options = {
        //      title: 'Percentage Attendance',
              tooltip: {isHtml: true}, 
              curveType: 'function',
              pointSize: 8,
              lineWidth: 2,     
              height: 500,
              hAxis: {
                format: 'MMMM yyyy',
                gridlines: {count: gridLineCount}
              },
              vAxis: {
                gridlines: {color: 'none'},
                minValue: 0,
                maxValue: 100 
              }
            };


                var averageAttentiveness = new google.visualization.LineChart(document.getElementById('averageAttentiveness'));

                averageAttentiveness.draw(data, options); 


            }


            google.charts.setOnLoadCallback(allData);

              function allData() {
                var data = new google.visualization.DataTable();
                data.addColumn('date', 'Date');  
                data.addColumn('string', 'Webinar Name');
                data.addColumn('number', 'Percentage Attendance');
                data.addColumn('number', 'Registrant Count');
                data.addColumn('number', 'Attendee Count');
                data.addColumn('number', 'Average Interest Rating');  
                data.addColumn('number', 'Average Attendance Time');  
                data.addColumn('number', 'Average Attentiveness');    

                data.addRows(allDataArrayProcessed);

                var allData = new google.visualization.Table(document.getElementById('allData'));

                allData.draw(data, {showRowNumber: false, width: '100%', height: '100%'});
                  
                
                var startDateNoDash = startDate.replace(/-/g,'');
                var endDateNoDash = endDate.replace(/-/g,''); 
                
                  
                  
                  
                  
                  
                //function to create csv string which includes column labels  
                function dataTableToCSV(dataTable_arg) {
                    var dt_cols = dataTable_arg.getNumberOfColumns();
                    var dt_rows = dataTable_arg.getNumberOfRows();

                    var csv_cols = [];
                    var csv_out;

                    // Iterate columns
                    for (var i=0; i<dt_cols; i++) {
                        // Replace any commas in column labels
                        csv_cols.push(dataTable_arg.getColumnLabel(i).replace(/,/g,""));
                    }

                    // Create column row of CSV
                    csv_out = csv_cols.join(",")+"\r\n";

                    // Iterate rows
                    for (i=0; i<dt_rows; i++) {
                        var raw_col = [];
                        for (var j=0; j<dt_cols; j++) {
                            // Replace any commas in row values
                            raw_col.push(dataTable_arg.getFormattedValue(i, j, 'label').replace(/,/g,""));
                        }
                        // Add row to CSV text
                        csv_out += raw_col.join(",")+"\r\n";
                    }

                    return csv_out;
                }  

                  
                  
                //add download csv functionality
                $('#download-csv').click(function () {
                    var csvFormattedDataTable = dataTableToCSV(data);
                    var encodedUri = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csvFormattedDataTable);
                    this.href = encodedUri;
                    this.download = 'table-data-'+startDateNoDash+'-'+endDateNoDash+'.csv';
                    this.target = '_blank';
                });  

                  
              }
                
            
            //add a counter to number
            $('.performance-data').each(function () {
                
                var $this = $(this);
                jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function () {
                        $this.text(Math.ceil(this.Counter));
                    }
                });
            
            }); 
            
            
            
            
            
            

        }); //end response
        
        
    } //end error check
    
    

}); //end on click   
    
//creates a tooltip
function customToolTipHtml(data,date,webinarName,dataName){   
    return '<div style="padding:10px;">' + webinarName + ' ('+ date + ')</br>'+dataName+': <strong>'+data+'</strong></div>';
}
    
//create performance data overview
function performanceOverviewHtml(data,dataName){   
    return '<div class="performance-data-item">'+
                '<span class="performance-data">'+data+'</span>'+
                '<span class="performance-data-name">'+dataName+'</span>'+
            '</div>';
}   


    

    
});