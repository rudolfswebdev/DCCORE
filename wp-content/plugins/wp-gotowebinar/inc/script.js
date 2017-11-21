jQuery(document).ready(function ($) {
    

    //if the calendar div exists create the calendar
    if ($('#calendar').length){
        var calendarData = $('#calendar-data').attr('data');
        
        var calendarDataDecodedAndParsed = JSON.parse(atob(calendarData));
        
        var wordpressLocale = document.documentElement.lang;
        
//        console.log(calendarDataDecodedAndParsed);        
        
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
            },
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            height: 'auto',
            locale: wordpressLocale.substr(0,2),
            timezone: 'local',
            timeFormat: 'h(:mm)a',
            events: calendarDataDecodedAndParsed

        })       
    }
    
    
    

//prefills registration form if logged in
    if ($('#gotowebinar_current_user_email').length){
        
        var loggedInUserEmail = $('#gotowebinar_current_user_email').val();
        var loggedInUserFirstName = $('#gotowebinar_current_user_first_name').val();
        var loggedInUserLastName = $('#gotowebinar_current_user_last_name').val();
        
        $('#firstName').val(loggedInUserFirstName);
        $('#lastName').val(loggedInUserLastName);
        $('#email').val(loggedInUserEmail);
  
    }
    
    

//    gets the users timezone and prints it after the convert to my timezone link
    $(".timezone-convert-link").each(function () {
        $(this).click(function () {
            
            $(this).next('#timezone-answer').remove();

            jQuery.ajax({
            url: "https://freegeoip.net/json/?callback=",
            type: "GET",
            context: this,    
            })
            .done(function(data, textStatus, jqXHR) {
            var usersTimezone = data['time_zone'];
            
            //check if users timezone can't be determined and if so display custom or default error message    
            if(usersTimezone.length<1){
                var timezoneErrorMessage = $('#timezone_error_message').text();
                $('<span id="timezone-answer">: ' + timezoneErrorMessage + '</span>').insertAfter(this);   
            } else {
                $('<span id="timezone-answer">: ' + usersTimezone + '</span>').insertAfter(this);     
            }    
                 
            $(".webinar-time").each(function () {

                var webinarMoment = moment($(this).next('#webinars-moment').text());
                var timeFormat = $(this).next().next().text();
                var timeFormatTranslation = timeFormat.replace(/i/g, 'mm').replace(/T/g, 'z').replace(/g/g, 'h');
                var convertedWebinarMoment = webinarMoment.tz(usersTimezone).format(timeFormatTranslation);
                var convertedWebinarMomentTooltip = 'GMT ' + webinarMoment.tz(usersTimezone).format('Z');
                $(this).html(convertedWebinarMoment);
                $(this).attr("title", convertedWebinarMomentTooltip);
            });
                
            $(".webinar-date").each(function () {
                var webinarMoment = moment($(this).parent().next().find('#webinars-moment').text());
                var dateFormat = $(this).next('#webinar-date-format').text();
                var dateFormatTranslation = dateFormat.replace(/j/g, 'D').replace(/Y/g, 'YYYY').replace(/M/g, 'MMM').replace(/n/g, 'M');
                var convertedWebinarMoment = webinarMoment.tz(usersTimezone).format(dateFormatTranslation);
                var convertedWebinarMomentTooltip = webinarMoment.tz(usersTimezone).format('dddd');
                $(this).html(convertedWebinarMoment);
                $(this).attr("title", convertedWebinarMomentTooltip);
            });    
                
                
            }); //end api call

            
        }); //end click function
    }); //end timezone each function
    
    
    
    $(".timezone-convert-link-registration").each(function () {
        $(this).click(function () {
            $(this).next('#timezone-answer').remove();
            
            jQuery.ajax({
            url: "https://freegeoip.net/json/?callback=",
            type: "GET",
            context: this,    
            })
            .done(function(data, textStatus, jqXHR) {
            var usersTimezone = data['time_zone'];   
            
            //check if users timezone can't be determined and if so display custom or default error message    
            if(usersTimezone.length<1){
                var timezoneErrorMessage = $('#timezone_error_message').text();
                $('<span id="timezone-answer">: ' + timezoneErrorMessage + '</span>').insertAfter(this);   
            } else {
                $('<span id="timezone-answer">: ' + usersTimezone + '</span>').insertAfter(this);     
            }    
                     
            $(".webinar-time").each(function () {
                var webinarMoment = moment($(this).next('#webinars-moment').text());
                var timeFormat = $(this).next().next().text();
                var timeFormatTranslation = timeFormat.replace(/i/g, 'mm').replace(/T/g, 'z').replace(/g/g, 'h');
                var convertedWebinarMoment = webinarMoment.tz(usersTimezone).format(timeFormatTranslation);
                var convertedWebinarMomentTooltip = 'GMT ' + webinarMoment.tz(usersTimezone).format('Z');
                $(this).html(convertedWebinarMoment);
                $(this).parent().attr("title", convertedWebinarMomentTooltip);
            });
            
            $(".webinar-date").each(function () {
                var webinarMoment = moment($(this).parent().next().find('#webinars-moment').text());
                var dateFormat = $(this).next('#webinar-date-format').text();
                var dateFormatTranslation = dateFormat.replace(/j/g, 'D').replace(/Y/g, 'YYYY').replace(/M/g, 'MMM').replace(/n/g, 'M');
                var convertedWebinarMoment = webinarMoment.tz(usersTimezone).format(dateFormatTranslation);
                var convertedWebinarMomentTooltip = webinarMoment.tz(usersTimezone).format('dddd');
                $(this).html(convertedWebinarMoment);
                $(this).parent().attr("title", convertedWebinarMomentTooltip);
            });
                
            }); //end api call    
                
        });
    });
    
    

    
    //when clicking the more info icon toggle the display of the more information description
    $(".upcoming-webinars em").hide();
    
    
    $(".information-icon").click(function (event) {
        event.preventDefault();
        $(this).next("em").slideToggle();
    });

    //this is the code to get the mouse hover working
    $('.masterTooltip').hover(function () {
        // Hover over code
        var title = $(this).attr('title');
        $(this).data('tipText', title).removeAttr('title');
        $('<p class="tooltip"></p>')
            .text(title)
            .appendTo('body')
            .fadeIn('slow');
    }, function () {
        // Hover out code
        $(this).attr('title', $(this).data('tipText'));
        $('.tooltip').remove();
    }).mousemove(function (e) {
        var mousex = e.pageX + 20; //Get X coordinates
        var mousey = e.pageY + 10; //Get Y coordinates
        $('.tooltip')
            .css({
                top: mousey,
                left: mousex
            })
    });


    //if no td elements are displayed in the upcoming webinar table just hide the whole table
    $(".upcoming-webinars").not($('td').parent().parent().parent()).hide();


    
    
    
    
    //send form inputs to GoToWebinar
    $('.webinar-registration-form').submit(function (event) {
        event.preventDefault();
    
    
    //lets check the 2nd opt in condition 
    if ($('input[name="gotowebinar_opt_in_second"]').prop('checked') == true || $('input[name="gotowebinar_opt_in_second"]').length == 0) {    
        
        
    //when ajax activity is starting show the spinner and when stopped hide it
    $('.webinar-registration .fa-spinner').ajaxStart(function () {
        $(this).slideDown(1);
    }).ajaxComplete(function () {
        $(this).hide();
    });
        
        
        
        //check if there's a recaptcha on the page   
        if($('.g-recaptcha').length){
        //get the response value  
        var response = grecaptcha.getResponse();   
        
        if(response.length == 0){
           
        $('.g-recaptcha').effect("shake", { times:3 }, 300);      
            
        } else {
           var recaptchaResponse = true; 
        }    
        //if no recaptcha set the response to true also
        } else {
           var recaptchaResponse = true;   
        }
        
        if(recaptchaResponse == true){
          
        
          
        //remove previous error messages
        $('.error-message').remove();
        $('.success-message').remove();



        //variables
        var firstName = $(this).find('input[name="firstName"]').val();
        var lastName = $(this).find('input[name="lastName"]').val();
        var email = $(this).find('input[name="email"]').val();
        var phone = $(this).find('input[name="phone"]').val();
        var organization = $(this).find('input[name="organization"]').val();
        var webinarId = $(this).find('input[name="gotowebinar_registration_webinar_key"]').val();
        var webinarTitle = $(this).find('input[name="gotowebinar_registration_webinar_title"]').val();
        var webinarTime = $(this).find('input[name="gotowebinar_registration_webinar_time"]').val();
        var webinarDate = $(this).find('input[name="gotowebinar_registration_webinar_date"]').val();
        var registrationUrl = $(this).find('input[name="gotowebinar_registration_url"]').val();
        var customSuccessMessage = $(this).find('input[name="gotowebinar_translate_successMessage"]').val();
        var customAlreadyRegisteredMessage = $(this).find('input[name="gotowebinar_translate_alreadyRegisteredMessage"]').val();
        var customAttendeeLimitMessage = $(this).find('input[name="gotowebinar_translate_attendeeLimitMessage"]').val();
        var customErrorMessage = $(this).find('input[name="gotowebinar_translate_errorMessage"]').val();
        var customThankYouPage = $(this).find('input[name="gotowebinar_custom_thankyou_page"]').val();
        var mailChimpList = $(this).find('input[name="gotowebinar_mailchimp_default_list"]').val();
        var constantContactList = $(this).find('input[name="gotowebinar_constantcontact_default_list"]').val();
        var activeCampaignList = $(this).find('input[name="gotowebinar_activecampaign_default_list"]').val();
        var campaignMonitorList = $(this).find('input[name="gotowebinar_campaignmonitor_default_list"]').val();
        var aweberList = $(this).find('input[name="gotowebinar_aweber_default_list"]').val();
        var mailChimpSubscribeIf = $(this).find('input[name="gotowebinar_mailchimp_subscribe_if"]').val();
        
//        console.log('hey');
//        console.log(mailChimpSubscribeIf);    
            
        
        //replace custom messages to include variables
        String.prototype.replaceAll = function (find, replace) {
        var str = this;
        return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
        };
        
    
        customSuccessMessage = customSuccessMessage.replaceAll('[First Name]', firstName);
        customSuccessMessage = customSuccessMessage.replaceAll('[Last Name]', lastName);
        customSuccessMessage = customSuccessMessage.replaceAll('[Webinar Title]', webinarTitle);
        customSuccessMessage = customSuccessMessage.replaceAll('[Webinar Time]', webinarTime);
        customSuccessMessage = customSuccessMessage.replaceAll('[Webinar Date]', webinarDate);
        customSuccessMessage = customSuccessMessage.replaceAll('[User Email]', email);
        customSuccessMessage = customSuccessMessage.replaceAll('[Registration URL]', registrationUrl);
        
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[First Name]', firstName);
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[Last Name]', lastName);
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[Webinar Title]', webinarTitle);
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[Webinar Time]', webinarTime);
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[Webinar Date]', webinarDate);
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[User Email]', email);
        customAlreadyRegisteredMessage = customAlreadyRegisteredMessage.replaceAll('[Registration URL]', registrationUrl);
        
        customErrorMessage = customErrorMessage.replaceAll('[First Name]', firstName);
        customErrorMessage = customErrorMessage.replaceAll('[Last Name]', lastName);
        customErrorMessage = customErrorMessage.replaceAll('[Webinar Title]', webinarTitle);
        customErrorMessage = customErrorMessage.replaceAll('[Webinar Time]', webinarTime);
        customErrorMessage = customErrorMessage.replaceAll('[Webinar Date]', webinarDate);
        customErrorMessage = customErrorMessage.replaceAll('[User Email]', email);
        customErrorMessage = customErrorMessage.replaceAll('[Registration URL]', registrationUrl);
          
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[First Name]', firstName);
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[Last Name]', lastName);
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[Webinar Title]', webinarTitle);
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[Webinar Time]', webinarTime);
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[Webinar Date]', webinarDate);
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[User Email]', email);
        customAttendeeLimitMessage = customAttendeeLimitMessage.replaceAll('[Registration URL]', registrationUrl);    

        //this gets the responses in the normal fields
        var data = {};
        $(this).find('.gotowebinar-field').each(function () {
            data[this.name] = this.value;
        });


        //this gets the the responses in the unknown fields
        data.responses = $('.gotowebinar-question').map(function () {
            if ($(this).hasClass('gotowebinar-select')) {
                return {
                    questionKey: this.id,
                    responseText: $(this).text(),
                    answerKey: this.value
                };
            } else {
                return {
                    questionKey: this.id,
                    responseText: this.value
                };
            }
        }).get();


            
        //call to php function to add user to webinar
        var data = {
            'action': 'registration_form_submit',
            'webinarId': webinarId,
            'name': firstName+' '+lastName, 
            'webinarTitle': webinarTitle,
            'data': data, 
        }; 

        jQuery.ajax({
        url: registration_form_submit.ajaxurl,
        type: "POST",
        data: data,
        context: this,    
        })
        .done(function(data, textStatus, jqXHR) {
            
            if(data == "409") {
                
                if(customAlreadyRegisteredMessage.length > 0) {

                $(this).find('#gotowebinar_registration_submit').after('<div class="success-message already-registered"><i style="color:green;" class="fa fa-check" aria-hidden="true"></i> <span>'+ customAlreadyRegisteredMessage +'</span></div>'); 
                } else {
                $(this).find('#gotowebinar_registration_submit').after('<div class="success-message already-registered"><i style="color:green;" class="fa fa-check" aria-hidden="true"></i> <span>Thanks ' + firstName + ' you have already registered for <strong>' + webinarTitle + '</strong></span></div>');
                }
            
                
            } else if (data == "403") {   
            
                
                if(customAttendeeLimitMessage.length > 0) {

                $(this).find('#gotowebinar_registration_submit').after('<div class="success-message attendee-limit"><i style="color:red;" class="fa fa-times" aria-hidden="true"></i> <span>'+ customAttendeeLimitMessage +'</span></div>'); 
                } else {
                $(this).find('#gotowebinar_registration_submit').after('<div class="success-message attendee-limit"><i style="color:red;" class="fa fa-times" aria-hidden="true"></i> <span>Unfortunately the attendee limit has been reached for this webinar so we were unable to register you for ' + webinarTitle + '.</span></div>');
                }    
                
                
            //do error response    
            } else if(data == "ERROR") {
                
                
                if(customErrorMessage.length > 0) { 
                $(this).find('#gotowebinar_registration_submit').after('<div class="error-message registration-unsuccessful"><i style="color:red;" class="fa fa-times" aria-hidden="true"></i> <span>'+ customErrorMessage +'</span></div>'); 
                } else {
                $(this).find('#gotowebinar_registration_submit').after('<div class="error-message registration-unsuccessful"><i style="color:red;" class="fa fa-times" aria-hidden="true"></i> <span>Something has gone wrong and your registration hasn\'t been processed. Please try again. If the issue persists please try registering <a target="_blank" href="' + registrationUrl + '">here.</a></span></div>');
                }

            //do success response    
            } else {
                
                
                if(customThankYouPage.length > 0) { 

                    window.location.href = customThankYouPage;

                } else {


                if(customSuccessMessage.length > 0) {

                $(this).find('#gotowebinar_registration_submit').after('<div class="success-message registration-successful"><i style="color:green;" class="fa fa-check" aria-hidden="true"></i> <span>'+ customSuccessMessage +'</span></div>'); 

                } else {


                $(this).find('#gotowebinar_registration_submit').after('<div class="success-message registration-successful"><i style="color:green;" class="fa fa-check" aria-hidden="true"></i> <span>Thanks ' + firstName + '. You have successfully registered for: <strong>' + webinarTitle + '</strong>. At <strong>' + webinarTime + '</strong> on the <strong>' + webinarDate + '</strong> you can use this link: <a target="_blank" href="' + data + '">' + data + '</a> to join the webinar. An email has been sent to: ' + email + ' to confirm your registration.</span></div>');

                }
                } 
                 
            } //end success response 
            

        })
        .fail(function(jqXHR, textStatus, errorThrown) {
        })
        .always(function() {
            /* ... */
        });
        
    
    

        
    if (typeof mailChimpSubscribeIf !== 'undefined') {
    // variable is undefined
   
        //do newsletter subscribe action - here we are checking if an exclusion has been setup based on the webinar title   
        if(webinarTitle.indexOf(mailChimpSubscribeIf) !== -1 || mailChimpSubscribeIf.length < 1 ) {        

            //check if checkbox is checked or if the element doesn't exist which means the user has elected to not have an opt in condition        
            if ($('input[name="gotowebinar_opt_in"]').prop('checked') == true || $('input[name="gotowebinar_opt_in"]').length == 0) {

                var standardFields = {};
                $(this).find('.gotowebinar-field').each(function () {
                    standardFields[this.name] = this.value;
                });

                //make single request to send data to all setup integration services
                var data = {
                    'action': 'integration_post',
                    'standardFields': standardFields,
                    'name': firstName+' '+lastName,
                    'mailChimpList': mailChimpList,
                    'constantContactList': constantContactList,
                    'activeCampaignList': activeCampaignList,
                    'campaignMonitorList': campaignMonitorList,
                    'aweberList': aweberList,
                }; 

                jQuery.ajax({
                url: integration_post.ajaxurl,
                type: "POST",
                data: data,
                })
                .done(function(data, textStatus, jqXHR) {
                    console.log(data);    
                });

            } //end if condition of email subscription
        } //end check if user text is in webinar title and if the setting is filled out 
    
    } //end type of undefined
            
            
            
            
            
            
            
            
            
} //end recapctha check 


} //end 2nd optin condition 
else {
    $('.second-opt-in').effect("shake", { times:3 }, 300);
    
}        
        
 
        
}); //end submit click function on registration form
    
  
    
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
// when clicking the copy from billing button copy data from billing details over to webinar fields    
 $("#copy-from-billing").click(function (event) {
        event.preventDefault();    

     
    
// these field replacements are just occuring on the first item of each new webinar   
$('.1-firstName input').each(function() {
$(this).val($('#billing_first_name').val());    
}); 

$('.1-lastName input').each(function() {
$(this).val($('#billing_last_name').val());    
}); 
     
$('.1-phone input').each(function() {
$(this).val($('#billing_phone').val());    
});

$('.1-email input').each(function() {
$(this).val($('#billing_email').val());    
});       
     
// these field replacements are occuring on all webinar fields
$('.organization input').each(function() {
$(this).val($('#billing_company').val());    
});   


var billingCountry = $('#billing_country option:selected').text();
$(".country select option").filter(function() {
    return $(this).text() == billingCountry; 
}).prop('selected', true);     

     
var billingState = $('#billing_state option:selected').text();
$(".state select option").filter(function() {
    return $(this).text() == billingState; 
}).prop('selected', true);      
         
     
$('.address input').each(function() {
$(this).val($('#billing_address_1').val()+' '+$('#billing_address_2').val());    
});      
     
$('.city input').each(function() {
$(this).val($('#billing_city').val());    
});        
    
 

$('.zipCode input').each(function() {
$(this).val($('#billing_postcode').val());    
});      
     
     
 }); //end copy from billing button click
    
    
if ($('.gotowebinar-top-toolbar').length){    
    
    var countDownTime = $('.webinar-countdown-clock').attr('data');    
    var autoStart = $('.webinar-countdown-clock').attr('auto-start');
    
    var autoStart = (autoStart.toLowerCase() === 'true');
    
    
    //do webinar countdown clock    
    var clock = $('.webinar-countdown-clock').FlipClock(countDownTime, {
            countdown: true,
            autoStart: autoStart
        });    




    //close toolbar on close click
    if ($.cookie('hide-webinar-toolbar')!="true") { 

        $('.gotowebinar-top-toolbar').show();

        $("#close-gotowebinar-toolbar").click(function (event) {
            event.preventDefault();
            $('.gotowebinar-top-toolbar').slideUp();


            var expiryHours = $('.webinar-countdown-clock').attr('data-expiry');

            var date = new Date();
            date.setTime(date.getTime() + expiryHours * 60 * 60 * 1000); 
            $.cookie('hide-webinar-toolbar', "true", { expires: date });

        });
    }

}
    
    
}); //end documentreadyfunction