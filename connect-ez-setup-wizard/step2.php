<?php
defined('ABSPATH') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit-callee-settings'])) {
        $callee = sanitize_text_field($_POST['callee']);
        if (empty($callee)) {
            echo '<p class="error-message">Callee field is required.</p>';
        } else {
            // Save the "callee" value to the options
            $updated_options = get_option('connect_ez_plugin_options', array());
            $updated_options['callee'] = $callee;
            update_option('connect_ez_plugin_options', $updated_options);

            echo '<p class="success-message">Callee settings saved successfully.</p>';
            // Redirect to step3.php
  	  wp_redirect(admin_url('admin.php?page=connect-ez-setup-wizard&step=3'));
   	 exit;
        }
    }
}
?>

<div class="wrap">
	<div class="setup-wizard-container"> <!-- Add this container -->
    <div class="setup-wizard-header">
        <h1 class="h4 card-header bg-white border-bottom-0 pt-4 pb-1">
            <?php esc_html_e('Connect-EZ Setup Wizard - Step 2', 'connect-ez-wp'); ?>
        </h1>

        <!-- Progress Indicator -->
        <div class="setup-wizard-progress">
            <div class="progress-bar" style="width: 50%;"></div>
        </div>
    </div>

    <div class="card shadow-sm my-5">
        <h1 class="h4 card-header bg-white border-bottom-0 pt-4 pb-1">
            <?php esc_html_e('Receiving Calls', 'connect-ez-wp'); ?>
        </h1>

        <div class="card-body">
            <p><?php echo wp_kses_post('You can receive calls on your phone without using a number by installing our app:<br /><a href="https://play.google.com/store/apps/details?id=com.sstech.ConnectEZ&utm_source=SetupWizard&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1" target="_blank"><img alt="Get it on Google Play" src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" style="height: 65px; width: 155px;"/></a> <br /> <a href="https://apps.apple.com/us/app/connect-ez/id1573713958?itsct=apps_box_badge&amp;itscg=30200" style="display: inline-block; overflow: hidden; border-radius: 13px; width: 250px; height: 83px;" target="_blank"><img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83&amp;releaseDate=1649116800" alt="Download on the App Store" style="border-radius: 13px; width: 125px; height: 41px;"></a><br /> <br />Get in touch if you want to receive calls on your laptop or PBX via our <a href="https://www.connect-ez.com/contact" target="_blank">contact form</a>. <br /> Once you download and install the app, please click on the hamburger icon on the top left, you will see a number in the following format 10000XXXXX. Please enter the full ten digits below:', 'connect-ez-wp'); ?></p>
		
            <form method="post" action="">
                <div class="form-group">
                    <label for="callee"><?php esc_html_e('My ten digit number', 'connect-ez-wp'); ?></label>
                    <input type="text" id="callee" name="callee" required>
                </div><br />
              <button type="submit" class="custom-button button-primary" name="submit-callee-settings">Save & Next</button>
</form>

<button type="button" class="custom-button" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=connect-ez-setup-wizard&step=2')); ?>'">Back</button>
<button type="button" class="custom-button btn-secondary d-block mt-2" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=connect-ez-settings-page')); ?>'"><?php esc_html_e('Cancel', 'connect-ez-wp'); ?></button>

        </div>
    </div>
</div>