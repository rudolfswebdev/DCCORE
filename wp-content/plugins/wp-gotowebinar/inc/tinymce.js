tinymce.PluginManager.add('wpgotowebinar_button', function(editor, url) {
        
    editor.addButton('wpgotowebinar_button', {
        
            title: 'WP GoToWebinar',
            type: 'menubutton',
            text: 'WP GoToWebinar',
            menu: [
                {
                    text: 'Registration Page Shortcode',
                    onclick: function() {
                        editor.execCommand('mceInsertContent', false, '[gotowebinar-reg]');
                    }
                },

                {
                    text: 'Upcoming Webinar Table/Calendar',
                    onclick: function() {

                        editor.windowManager.open({
                                title: 'Insert Upcoming Webinar Table/Calendar',
                                body: [

                                    {
                                        type: 'listbox',
                                        name: 'displaytype',
                                        label: 'Display upcoming webinars in a table or calendar format?',
                                        values: [{text:'Table',value:'table'},{text:'Calendar',value:'calendar'}]
                                    },

                                    {
                                        type: 'textbox',
                                        name: 'include',
                                        label: 'Include webinars that contain the following word or phrase in the title:',
                                        value: ''
                                    },

                                    {
                                        type: 'textbox',
                                        name: 'exclude',
                                        label: 'Exclude webinars that contain the following word or phrase in the title:',
                                        value: ''
                                    }, 

                                    {
                                        type: 'textbox',
                                        name: 'hide',
                                        label: 'Hide the following word or phrase from the webinar title:',
                                        value: ''
                                    }, 

                                    {
                                        type: 'textbox',
                                        name: 'days',
                                        label: 'Show webinars from the following amount of days:',
                                        value: ''
                                    }, 

                                    {
                                        type: 'listbox',
                                        name: 'timezone',
                                        label: 'Show webinars from only this timezone:',
                                        values: editor.settings.timezoneList
                                    },
                                    
                                ],
                                onsubmit: function(e) {   
                                    
                                    
                                    var displayType = e.data.displaytype;
                                    var include = e.data.include;
                                    var exclude = e.data.exclude;
                                    var hide = e.data.hide;
                                    var days = e.data.days;
                                    var timezone = e.data.timezone;
                                    
                                    
                                    var content = '[gotowebinar';
                                    
                                    if(displayType != null && displayType == 'calendar'){
                                        content += '-calendar';    
                                    }
                                    
                                    if(include != null && include.length > 0){
                                        content += ' include="'+include+'"';     
                                    }
                                    
                                    if(exclude != null && exclude.length > 0){
                                        content += ' exclude="'+exclude+'"';     
                                    }
                                    
                                    if(hide != null && hide.length > 0){
                                        content += ' hide="'+hide+'"';     
                                    }
                                    
                                    if(days != null && days.length > 0){
                                        content += ' days="'+days+'"';     
                                    }
                                    
                                    if(timezone != null && timezone.length > 0){
                                        content += ' timezone="'+timezone+'"';     
                                    }
  
                                    content += ']';
                                    
                                    editor.execCommand('mceInsertContent', false, content);
 
                                }
                        });



                }
            },

            {
                text: 'Single Registration Form',
                onclick: function() {
                    editor.windowManager.open({
                                title: 'Insert Single Webinar Registration Form',
                                body: [

                                    {
                                        type: 'listbox',
                                        name: 'key',
                                        label: 'Select webinar:',
                                        values: editor.settings.webinarList
                                    },	
                                    
                                    {
                                        type: 'textbox',
                                        name: 'hide',
                                        label: 'Hide the following word or phrase from the webinar title:',
                                        value: ''
                                    },
                                    
                                    {
                                        type: 'textbox',
                                        name: 'include',
                                        label: 'If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar subject includes:',
                                        value: ''
                                    },
                                    
                                    {
                                        type: 'textbox',
                                        name: 'exclude',
                                        label: 'If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar subject excludes:',
                                        value: ''
                                    },
                                    
                                    {
                                        type: 'listbox',
                                        name: 'timezone',
                                        label: 'If you have entered to show the registration form for the most upcoming webinar optionally enter a value here to only show the most upcoming webinar if the webinar timezone is:',
                                        values: editor.settings.timezoneList
                                    },
                                    
                                    {
                                        type: 'listbox',
                                        name: 'mailchimp',
                                        label: 'Custom MailChimp List ID (Optional):',
                                        values: editor.settings.mailchimpList
                                    }, 
                                    
                                    {
                                        type: 'listbox',
                                        name: 'constantcontact',
                                        label: 'Custom Constant Contact List ID (Optional):',
                                        values: editor.settings.constantcontactList
                                    }, 
                                    
                                    {
                                        type: 'listbox',
                                        name: 'campaignmonitor',
                                        label: 'Custom Campaign Monitor List ID (Optional):',
                                        values: editor.settings.campaignmonitorList
                                    }, 
                                    
                                    {
                                        type: 'listbox',
                                        name: 'activecampaign',
                                        label: 'Active Campaign List ID (Optional):',
                                        values: editor.settings.activecampaignList
                                    }, 
                                    {
                                        type: 'listbox',
                                        name: 'aweber',
                                        label: 'AWeber List ID (Optional):',
                                        values: editor.settings.aweberList
                                    },
                                ],
                                onsubmit: function(e) {   
                                                                        
                                    var key = e.data.key;
                                    var include = e.data.include;
                                    var exclude = e.data.exclude;
                                    var hide = e.data.hide;
                                    var timezone = e.data.timezone;
                                    var mailchimp = e.data.mailchimp;
                                    var constantcontact = e.data.constantcontact;
                                    var campaignmonitor = e.data.campaignmonitor;
                                    var activecampaign = e.data.activecampaign;
                                    var aweber = e.data.aweber;
                                                                        
                                    var content = '[gotowebinar-reg';
                                    
                                    if(key != null && key.length > 0){
                                        content += ' key="'+key+'"';     
                                    }
                                    
                                    if(include != null && include.length > 0){
                                        content += ' include="'+include+'"';     
                                    }
                                    
                                    if(exclude != null && exclude.length > 0){
                                        content += ' exclude="'+exclude+'"';     
                                    }
                                    
                                    if(hide != null && hide.length > 0){
                                        content += ' hide="'+hide+'"';     
                                    }
                                    
                                    if(timezone != null && timezone.length > 0){
                                        content += ' timezone="'+timezone+'"';     
                                    }
                                    
                                    if(mailchimp != null && mailchimp.length > 0){
                                        content += ' mailchimp="'+mailchimp+'"';     
                                    }
                                    
                                    if(constantcontact != null && constantcontact.length > 0){
                                        content += ' constantcontact="'+constantcontact+'"';     
                                    }
                                    
                                    if(campaignmonitor != null && campaignmonitor.length > 0){
                                        content += ' campaignmonitor="'+campaignmonitor+'"';     
                                    }
                                    
                                    if(activecampaign != null && activecampaign.length > 0){
                                        content += ' activecampaign="'+activecampaign+'"';     
                                    }
                                    
                                    if(aweber != null && aweber.length > 0){
                                        content += ' aweber="'+aweber+'"';     
                                    }
                                    
                                    content += ']';
                                    
                                    editor.execCommand('mceInsertContent', false, content);
                                    
                                }
                        });
                }
            },
       ]            
    });
});


