<?php
/**
 * Displays the first step of the Connect-EZ Setup Wizard.
 */

defined('ABSPATH') || exit;

//function connect_ez_setup_wizard_step1() {
    // Calculate the progress (25% for the first step)
   $progress = 0;
    
    // Generate the URL for the next step
 $next_url = esc_url(admin_url('admin.php?page=connect-ez-setup-wizard&step=2')); 

    ?>
    <div class="wrap">
        <div class="setup-wizard-container"> <!-- Add this container -->
		<!-- Progress Indicator -->
        <div class="setup-wizard-progress">
            <div class="progress-bar" style="width: <?php echo esc_attr($progress); ?>%;"></div>
        </div>

		<h1 class="h4 card-header bg-white border-bottom-0 pt-4 pb-1">
            <?php esc_html_e('Welcome to the Connect-EZ Setup Wizard!', 'connect-ez-wp'); ?>
        </h1>

        <div class="card-body text-muted">
            <p><?php esc_html_e('Thank you for choosing Connect-EZ - The Calling Plugin For WordPress', 'connect-ez-wp'); ?></p>
            <hr class="mt-4 pt-3 pb-0" />
            <p class="small"><?php echo wp_kses_post(__('This quick setup wizard will help you configure the basic settings. It’s completely optional and shouldn’t take longer than ten minutes. You will need to have two email addresses, one is to receive calls (the one you usually share). Another email address to actually initiate calls from the site. You can receive calls on your phone, laptop or PBX.', 'connect-ez-wp')); ?></p>
        </div>

        <div class="card-footer mb-0 bg-white gp-setup-actions step border-top-0">
           <button class="custom-button button-primary" onclick="window.location.href='<?php echo esc_url($next_url); ?>'"><?php esc_html_e("Let's get started!", 'connect-ez-wp'); ?></button>
            <button class="custom-button btn-secondary d-block mt-2" onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=connect-ez-settings-page')); ?>'"><?php esc_html_e('Cancel', 'connect-ez-wp'); ?></button>

        </div>
    </div>
    <?php
//}

// Hook this step to your setup wizard
add_action('admin_notices', 'connect_ez_setup_wizard_step1');