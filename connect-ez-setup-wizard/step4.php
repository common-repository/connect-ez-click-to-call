<?php
/**
 * Displays the fourth step of the Connect-EZ Setup Wizard.
 */

defined('ABSPATH') || exit;
?>

<div class="wrap">
    <div class="setup-wizard-container"> <!-- Add this container -->
        <h1 class="h4 card-header bg-white border-bottom-0 pt-4 pb-1">
            <?php esc_html_e('Connect-EZ Setup Wizard - Thank You!', 'connect-ez-wp'); ?>
        </h1>

        <!-- Progress Indicator -->
        <div class="setup-wizard-progress">
            <div class="progress-bar" style="width: 100%;"></div>
        </div>

        <div class="card-body text-muted">
            <p><?php esc_html_e('Congratulations! You have successfully completed the Connect-EZ Setup Wizard. Please add the code [connect-ez-wp-form] in an HTML block wherever you want the "Call now" button to appear. You can now close this wizard and choose the kind of calling button displayed, or if you&#39;d like the user to enter an email address or phone number before they can call from your site from the settings page. Click on "Save" once you are done.', 'connect-ez-wp'); ?></p>
 
        </div>

        <div class="card-footer mb-0 bg-white gp-setup-actions step border-top-0">
  <button class="custom-button btn-secondary d-block mt-2" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=connect-ez-settings-page')); ?>'"><?php esc_html_e('Close', 'connect-ez-wp'); ?></button>

        </div>
    </div>
</div>