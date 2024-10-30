<?php
/**
 * Displays the third step of the Connect-EZ Setup Wizard.
 */

defined('ABSPATH') || exit;

$api_message = ''; // Initialize the API message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email is submitted
    if (isset($_POST['submit-email'])) {
        $email = sanitize_email($_POST['email']);

        if (empty($email)) {
            $api_message = '<p class="error-message">Email field is required.</p>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $api_message = '<p class="error-message">Invalid email address.</p>';
        } else {
            // Calling the API logic
            $API_URL = 'https://www.purplex8.com/registration-api/wp-register.php';
            $data = array('email' => $email);
            $response = wp_remote_post($API_URL, array('body' => $data));

            if (is_wp_error($response)) {
                $api_message = '<p class="error-message">An error occurred while submitting your email.</p>';
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                if ($data && isset($data['message'])) {
                    $api_message = '<p class="success-message">' . $data['message'] . '</p>';
                } else {
                    $api_message = '<p class="error-message">An error occurred while processing the response.</p>';
                }
            }
        }
    }
    // Check if SIP settings are submitted
    if (isset($_POST['submit-sip'])) {
        $private_identity = sanitize_text_field($_POST['private_identity']);
        $public_identity = sanitize_text_field($_POST['public_identity']);
        $password = sanitize_text_field($_POST['password']);

        if (empty($private_identity) || empty($public_identity) || empty($password)) {
            echo '<p class="error-message">All SIP settings fields are required.</p>';
        } else {
            // Save the SIP settings
            $updated_options = get_option('connect_ez_plugin_options', array());
            $updated_options['private_identity'] = $private_identity;
            $updated_options['public_identity'] = $public_identity;
            $updated_options['password'] = $password;
            update_option('connect_ez_plugin_options', $updated_options);
            
            echo '<p class="success-message">SIP settings saved successfully.</p>';
            
            // Redirect to Step 4
            wp_redirect(admin_url('admin.php?page=connect-ez-setup-wizard&step=4'));
            exit;
        }
    }
}
?>

<div class="wrap">
    <div class="setup-wizard-container">
        <div class="setup-wizard-header">
            <!-- ... your existing header code ... -->
        </div>

        <div class="card shadow-sm my-5">
            <h1 class="h4 card-header bg-white border-bottom-0 pt-4 pb-1">
                <?php esc_html_e('Configure Site Calling Settings', 'connect-ez-wp'); ?>
            </h1>

            <div class="card-body">
                <p><?php esc_html_e('In the box below, please submit a valid email address you have access to. (Please do NOT use the email you registered with in the last step):', 'connect-ez-wp'); ?></p>

                <form method="post" action="">
                    <!-- Email Input Box -->
                    <div class="form-group">
                        <label for="email"><?php esc_html_e('Email Address', 'connect-ez-wp'); ?></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <br>
                    <button type="submit" class="custom-button button-primary" name="submit-email">Submit</button>
                </form>
                
                <?php echo $api_message; // Display the API message here ?>

				<hr class="my-4">

                <div class="card-body">
                    <p><?php esc_html_e('You would have received an email. Please click verify, and you would have received your user credentials. Please add them exactly as received in the boxes below:', 'connect-ez-wp'); ?></p>

                    <form method="post" action="">
                        <!-- SIP Settings Input Boxes -->
                        <div class="form-group">
                            <label for="private-identity"><?php esc_html_e('Private Identity', 'connect-ez-wp'); ?></label>
                            <input type="text" id="private-identity" name="private_identity" required>
                        </div>

                        <div class="form-group">
                            <label for="public-identity"><?php esc_html_e('Public Identity', 'connect-ez-wp'); ?></label>
                            <input type="text" id="public-identity" name="public_identity" required>
                        </div>

                        <div class="form-group">
                            <label for="password"><?php esc_html_e('Password', 'connect-ez-wp'); ?></label>
                             <div class="password-input">
					        <input type="password" id="password" name="password" required>
        					<span class="show-password" id="showPassword" onclick="togglePasswordVisibility()">&#x1F441;</span>
    </div>

						</div>
						 				
						
                        <br>
                        <button type="submit" class="custom-button button-primary" name="submit-sip">Save & Next</button>
                    </form>

                    <button type="button" class="custom-button" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=connect-ez-setup-wizard&step=2')); ?>'">Back</button>
                    <button type="button" class="custom-button btn-secondary d-block mt-2" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=connect-ez-settings-page')); ?>'">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	  function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const showPasswordIcon = document.getElementById('showPassword');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showPasswordIcon.innerHTML = '&#x1F440;'; // Change to a crossed-eye icon
        } else {
            passwordInput.type = 'password';
            showPasswordIcon.innerHTML = '&#x1F441;'; // Change back to the eye icon
        }
    }
	
    document.addEventListener('DOMContentLoaded', function () {
        const privateIdentityInput = document.getElementById('private-identity');
        const publicIdentityInput = document.getElementById('public-identity');
        const passwordInput = document.getElementById('password');
        const saveButton = document.querySelector('button[name="submit-sip"]');

        function toggleSaveButtonState() {
            saveButton.disabled = !(
                privateIdentityInput.value.trim() !== '' &&
                publicIdentityInput.value.trim() !== '' &&
                passwordInput.value.trim() !== ''
            );
        }

        privateIdentityInput.addEventListener('input', toggleSaveButtonState);
        publicIdentityInput.addEventListener('input', toggleSaveButtonState);
        passwordInput.addEventListener('input', toggleSaveButtonState);

        // Initially, disable the "Save SIP Settings" button
        toggleSaveButtonState();
    });
</script>