jQuery(document).ready(function ($) {
    $('#caller-mode').click(function () {
        $('#shortcode_area').html('Use this code to put an icon on your pages which enables your users to call you via SipMl5 protocol. <br/>\n' +
            '        <strong>[connect-ez-wp-form]</strong>')
    })
    
    $('#callee-mode').click(function () {
        $('#shortcode_area').html('Use this code to have a call center on your pages to answer incoming calls. <br/>\n' +
            '        <strong>[connect-ez-wp-call-center]</strong>')
    });

    jQuery('#sendEmail').click(function(){
        jQuery(this).attr('disabled', 'disabled');
        const email = jQuery("input[name='connect_ez_plugin_options[connect_email]']").val();
        const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        const API_URL = "https://www.purplex8.com/registration-api/wp-register.php";

        if( email == '' ){
            alert('Email field is required');
            jQuery('#sendEmail').removeAttr('disabled');
            return false;
        }

        if( !(email.match(emailRegex)) ){
            alert('Invalid email address');
            jQuery('#sendEmail').removeAttr('disabled');
            return false;
        }

        const settings = {
            "url": API_URL,
            "method": "POST",
            "timeout": 0,
            "crossDomain": true,
            "headers": {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            "data": {
              "email": email
            }
          };
          
          $.ajax(settings).done(function (response) {
            jQuery('#sendEmail').removeAttr('disabled');
            const data = JSON.parse(response);
            const messageWrapper = document.createElement('div');
            messageWrapper.setAttribute('class', 'notice notice-success settings-error is-dismissible');
            messageWrapper.setAttribute('id', 'setting-error-settings_updated');

            const message = document.createElement('p');
            message.innerText = data.message.replace("\n", " ");

            const closeBTN = document.createElement('button');
            closeBTN.setAttribute('type', 'button');
            closeBTN.setAttribute('class', 'notice-dismiss');

            // const closeBTNText = document.createElement('span');
            // closeBTNText.setAttribute('class', 'screen-reader-text');
            // closeBTNText.innerText = 'Dismiss this notice.';

            // closeBTN.append(closeBTNText);
            messageWrapper.append(message);
            // messageWrapper.append(closeBTN);

            document.getElementById('wpbody-content').prepend(messageWrapper);

            setTimeout(() => {
                messageWrapper.remove();
            }, 5000)

          });
    })


})