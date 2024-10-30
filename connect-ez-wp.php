<?php
/*
 * Plugin Name: Connect-EZ WP
 * Plugin URI: https://www.connect-ez.com/
 * Description: Connect-EZ Sip WordPress Plugin
 * Version: 1.0.4
 */

/**
 * Initialize the admin panel.
 */
add_action('admin_menu', 'connect_ez_add_admin_menu');

/**
 * Add the admin menu.
 */
function connect_ez_add_admin_menu() {
    add_dashboard_page(
        '',
        '',
        'manage_options',
        'connect-ez-setup-wizard',
        '',
        null
    );
}

// Add the admin page for full-screen.
add_action('current_screen', 'connect_ez_maybe_output_admin_page', 10);

/**
 * Output the dashboard admin page.
 */
function connect_ez_maybe_output_admin_page() {
    // Exit if not in admin.
    if (!is_admin()) {
        return;
    }

    // Make sure we're on the right screen.
    $screen = get_current_screen();
    if ('connect-ez-setup-wizard' !== $screen->id) {
        return;
    }
    connect_ez_get_admin_page_header();

    // Check for the step parameter and determine which step to show.
    $step = isset($_GET['step']) ? intval($_GET['step']) : 1;

// If we are on step 1, check if the settings are already saved
if ($step === 1) {
    $options = get_option('connect_ez_plugin_options');
    $settings_saved = !empty($options['private_identity']) && !empty($options['public_identity']) && !empty($options['password']);

    if ($settings_saved) {
        // If settings are already saved, redirect to the settings page
        wp_safe_redirect(admin_url('admin.php?page=connect-ez-settings-page'));
        exit;
    }
}

// Display the appropriate step content based on the step number.
if ($step === 1) {
    include 'connect-ez-setup-wizard/step1.php';
} elseif ($step === 2) {
    include 'connect-ez-setup-wizard/step2.php';
} elseif ($step === 3) {
    include 'connect-ez-setup-wizard/step3.php';
} elseif ($step === 4) {
    include 'connect-ez-setup-wizard/step4.php';
} else {
    echo 'Step not found'; // Handle invalid step
}
	connect_ez_get_admin_page_footer();
    exit;
}

/**
 * Output landing page header.
 */
function connect_ez_get_admin_page_header() {
    // Output header HTML.
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Connect-EZ Setup Wizard</title>
        <?php
		wp_register_style(
				'connect-ez-wizard-css',
				plugins_url( 'css/connect-ez-wizard.css', __FILE__ ),
				array(),
				'1.0.0'
			);
			wp_print_styles( 'connect-ez-wizard-css' );
        ?>
    </head>
    <body>
    <?php
}

/**
 * Output landing page footer.
 */
function connect_ez_get_admin_page_footer() {
    // Output footer HTML.
    ?>
    </body>
    </html>
    <?php
}


// Add an activation hook
register_activation_hook(__FILE__, 'connect_ez_plugin_activate');
add_action('admin_init', 'connect_ez_plugin_redirect');

function connect_ez_plugin_activate() {
    add_option('connect_ez_plugin_do_activation_redirect', true);
}

function connect_ez_plugin_redirect() {
    if (get_option('connect_ez_plugin_do_activation_redirect', false)) {
        delete_option('connect_ez_plugin_do_activation_redirect');
         if(!isset($_GET['activate-multi']))
		 {
	wp_safe_redirect(admin_url('admin.php?page=connect-ez-setup-wizard'));exit;
		 }
    }
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'connect_ez_plugin_settings_link' );
function connect_ez_plugin_settings_link( array $links ) {
    $url = get_admin_url() . "options-general.php?page=connect-ez-settings-page";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'textdomain') . '</a>';
      $links[] = $settings_link;
    return $links;
  }


function connect_ezHtml($params) 
{
    $options = get_option('connect_ez_plugin_options');
    if ($options['mode'] != 'caller') {
        $html = 'Plugin is not in caller mode. Please click save from the settings page!';
        return $html;
    }
    $timestamp = time();
    $callee = $options["callee"];
    $icon = $options["icon"];
    $username = $options['private_identity'];
    $password = $options['password'];
    $impu = $options['public_identity'];
    $formId = $callee . '-' . $timestamp;
    $display_type = $options['display_name'];
    $display_name = get_bloginfo('name');

    wp_enqueue_script('sipml-api', plugins_url('js/SIPml-api.min.js', __FILE__), array('jquery'));
    wp_enqueue_style('sip-css', plugins_url('css/style.css', __FILE__));
    $localVariables = "var realm = '{$options['realm']}';\n";
    $localVariables .= "var impi = '$username';\n";
    $localVariables .= "var impu = '$impu';\n";
    $localVariables .= "var passwd = '$password';\n";
    $localVariables .= "var display_type = '$display_type';\n";
    $localVariables .= "var display_name = '$display_name';\n";
    $localVariables .= "var websocket_proxy_url = '{$options['websocket_server_url']}';\n";
    $localVariables .= "var ice_servers = '{$options['ice_servers']}';\n";
    wp_enqueue_script('connect-ez', plugins_url('js/sip_script.js', __FILE__));
    wp_add_inline_script( 'connect-ez', $localVariables, 'before' );

    static $i = 1;
    $html = "";
    if ($i == 1) {
        $html .= '<!-- Audios -->
          <audio id="audio_remote" autoplay="autoplay"></audio>
          <audio id="ringtone" loop="" src="' . plugins_url("sounds/ringtone.wav", __FILE__) . '"> </audio>
          <audio id="ringbacktone" loop="" src="' . plugins_url("sounds/ringbacktone.wav", __FILE__) . '"> </audio>
          <audio id="dtmfTone" src="' . plugins_url("sounds/dtmf.wav", __FILE__) . '"> </audio>';
    }
    $html .= file_get_contents(__DIR__ . '/callee.html');
    $icon = plugins_url("img/$icon.png", __FILE__);
    $html = str_replace("<!--formId-->", $formId, $html);
    $html = str_replace("<!--username-->", $username, $html);
    $html = str_replace("<!--password-->", $password, $html);
    $html = str_replace("<!--callee-->", $callee, $html);
    $html = str_replace("<!--icon-->", $icon, $html);
    $i++;
    return $html;
}

function connect_ezCallCenter($params) {

    $options = get_option('connect_ez_plugin_options');
    if ($options['mode'] != 'callee') {
        $html = 'Plugin is not in callee mode. Please check your settings!';
        return $html;
    }
    $icon = $options["icon"];
    $username = $options['private_identity'];
    $password = $options['password'];
    $impu = $options['public_identity'];
    $display_name = $options['display_name'];

    wp_enqueue_script('sipml-api', plugins_url('js/SIPml-api.min.js', __FILE__), array('jquery'));
    $localVariables = "var myUsername = '$username';\n";
    $localVariables .= "var realm = '{$options['realm']}';\n";
    $localVariables .= "var impi = '$username';\n";
    $localVariables .= "var impu = '$impu';\n";
    $localVariables .= "var passwd = '$password';\n";
    $localVariables .= "var display_name = '$display_name';\n";
    $localVariables .= "var websocket_proxy_url = '{$options['websocket_server_url']}';\n";
    $localVariables .= "var ice_servers = '{$options['ice_servers']}';\n";
    wp_enqueue_script('connect-ez', plugins_url('js/connect_ez.js', __FILE__));
    wp_add_inline_script( 'connect-ez', $localVariables, 'before' );
    wp_enqueue_style('sip-css', plugins_url('css/style.css', __FILE__));
    $icon = plugins_url("img/$icon.png", __FILE__);
    $html = file_get_contents(__DIR__ . '/call_center.html');
    $html = str_replace("<!--username-->", $username, $html);
    $html = str_replace("<!--password-->", $password, $html);
    $html = str_replace("<!--pluginPath-->", plugins_url('', __FILE__), $html);
    $html = str_replace("<!--icon-->", $icon, $html);
    return $html;
}

add_shortcode('connect-ez-call-center', 'connect_ezCallCenter');
add_shortcode('connect-ez-wp-form', 'connect_ezHtml');

/* Settings Section */
function connect_ez_settings_page() {
    // Check if the user has the capability to access this page
    if (current_user_can('manage_options')) {
        add_options_page('Connect EZ Wp Settings', 'Connect EZ Wp', 'manage_options', 'connect-ez-settings-page', 'connect_ez_render_plugin_settings_page');
    }
}

add_action('admin_menu', 'connect_ez_settings_page');

function connect_ez_render_plugin_settings_page() {
    wp_enqueue_script('connect-ez', plugin_dir_url(__FILE__) . 'js/functions.js', array('jquery'));
    wp_enqueue_style('sip-css', plugins_url('css/style.css', __FILE__));
    ?>
    <h2>Connect-EZ WP Settings Page</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields('connect_ez_plugin_options');
        do_settings_sections('connect_ez_plugin');
        ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>"/>
    </form>
    <?php
}

function connect_ez_register_settings()
{
    register_setting('connect_ez_plugin_options', 'connect_ez_plugin_options', 'connect_ez_plugin_options_validate');
    add_settings_section('sip_settings', 'WebRTC Server Settings', 'connect_ez_plugin_section_sip', 'connect_ez_plugin');

    add_settings_field('connect_ez_plugin_setting_realm', 'Realm', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'sip_settings', ['name' => 'realm', 'value' => 'purplex8.com']);
    add_settings_field('connect_ez_plugin_setting_websocket_server_url', 'WebSocket Server URL', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'sip_settings', ['name' => 'websocket_server_url', 'value' => 'wss://purplex8.com:44344']);
    add_settings_field('connect_ez_plugin_setting_ice_servers', 'ICE Servers', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'sip_settings', ['name' => 'ice_servers', 'value' => '[]', 'help' => 'https://www.connect-ez.com/click-to-call-service/']);
    add_settings_field('connect_ez_plugin_setting_create_account', 'Create account', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'sip_settings', ['name' => 'connect_email', 'value' => '']);

    add_settings_section('users_settings', 'Users Settings', 'connect_ez_plugin_section_users', 'connect_ez_plugin');

    add_settings_field('connect_ez_plugin_setting_private_identity', 'Private Identity', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'users_settings', ['name' => 'private_identity', 'value' => '']);
    add_settings_field('connect_ez_plugin_setting_public_identity', 'Public Identity', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'users_settings', ['name' => 'public_identity', 'value' => '']);
    add_settings_field('connect_ez_plugin_setting_password', 'Password', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'users_settings', ['name' => 'password', 'value' => '']);
    add_settings_field('connect_ez_plugin_setting_display_name', 'Caller-ID', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'users_settings', ['name' => 'display_name', 'value' => 'guest', 'type' => 'radio', 'radioOptions' => ['guest', 'email', 'phone']]);
    add_settings_field('connect_ez_plugin_setting_icon', 'Icon', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'users_settings', ['name' => 'icon', 'value' => 'callee1', 'type' => 'radio', 'radioOptions' => ['callee1', 'callee2', 'callee3']]);

    add_settings_section('caller_callee_settings', 'Call Destination Settings', 'connect_ez_plugin_section_caller_callee', 'connect_ez_plugin');
    add_settings_field('connect_ez_plugin_setting_mode', 'Mode', 'connect_ez_plugin_setting_form_mode', 'connect_ez_plugin', 'caller_callee_settings');
    add_settings_field('connect_ez_plugin_setting_callee', 'Caller Destination', 'connect_ez_plugin_setting_form_field', 'connect_ez_plugin', 'caller_callee_settings', ['name' => 'callee', 'value' => '']);
}

add_action('admin_init', 'connect_ez_register_settings');

function connect_ez_plugin_options_validate($input)
{
    $newInput = array();
    foreach ($input as $key => $value) {
        $newInput[$key] = trim($value);
    }
    return $newInput;
}

function connect_ez_plugin_section_sip()
{
    echo '<p>Here you can set your SIP settings. For simplicity, Connect-EZ default settings are added. Make sure to save the settings before using them.</p>';
}

function connect_ez_plugin_section_users()
{
    echo '<p>Here you can set your users</p>';
}

function connect_ez_plugin_section_caller_callee()
{
    echo '<p>Here you can set caller/callee configurations</p>';
}

function connect_ez_plugin_setting_form_field($args)
{
    $options = get_option('connect_ez_plugin_options');
    $name = $args["name"];
    $value = isset($options[$name]) ? $options[$name] : $args["value"];
    
    if (isset($args['type']) && $args['type'] == 'radio') {
        $description = '';
        foreach ($args['radioOptions'] as $radioValue) {
            if ($radioValue == 'caller') {
                $description = '(You should set the destination to call.)';
            }
            if ($radioValue == 'callee') {
                $description = '(Receiving calls on defined user earlier.)';
            }
            echo "<input name='connect_ez_plugin_options[" . esc_attr($name) . "]' type='radio' value='" . esc_attr($radioValue) . "' " . esc_attr(($radioValue == $value ? 'checked' : '')) . " />";
            if ($args['name'] == 'icon') {
                echo "<img src='" . esc_url(plugins_url('img/' . $radioValue . '.png', __FILE__)) . "' width='64' />";
            } else {
                echo esc_attr(ucfirst($radioValue) . $description) . '<br />';
            }
        }
    } else {
        echo "<input class='connect-ez-settings-field' id='connect_ez_plugin_setting_" . esc_attr($name) . "' name='connect_ez_plugin_options[" . esc_attr($name) . "]' type='text' value='" . esc_attr($value) . "' />";
    }

    if( $args['name'] == 'connect_email' ) {
        echo '<button type="button" class="wp-core-ui button-primary" id="sendEmail">Submit Email</button>';
    }
}

function connect_ez_plugin_setting_form_mode()
{
    $options = get_option('connect_ez_plugin_options');
    $value = isset($options['mode']) ? $options['mode'] : 'caller';
    ?>
    <div>
        <input id="caller-mode" name='connect_ez_plugin_options[mode]' type='radio' value='caller' <?= $value == 'caller' ? 'checked' : '' ?> />Caller (Destination to call - from your app.)
    </div>
<!--    <div style="margin-top: 10px">
        <input id="callee-mode" name='connect_ez_plugin_options[mode]' type='radio' value='callee' <?= $value == 'callee' ? 'checked' : '' ?> />Callee (Receiving calls on defined user earlier.)
    </div>-->
    <div id="shortcode_area" style="margin-top: 10px">
        <?php if ($value == 'caller'):?>
            Use this code to put an icon on your pages which enables your users to call you via SipMl5 protocol. <br/>
            <strong>[connect-ez-wp-form]</strong>
<!--        <?php else: ?>
            Use this code to have a call center on your pages to answer incoming calls. <br/>
            <strong>[connect-ez-call-center]</strong>
        <?php endif ?>-->
    </div>
    <?php
}