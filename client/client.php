<?php

/**
 * Client stuff
 */

/**
 * Enqueue client scripts
 */
function mcb_load_client_scripts() {
    wp_enqueue_script( 'mcb-angular-core', MCB_PLUGIN_URL . '/lib/angular/angular.min.js', array( 'jquery' ) );
    wp_enqueue_script( 'mcb-angular-animate', MCB_PLUGIN_URL . '/lib/angular/angular-animate.min.js', array( 'mcb-angular-core' ) );
    wp_enqueue_script( 'mcb-lodash', MCB_PLUGIN_URL . '/lib/lodash/lodash.core.min.js' );
    wp_enqueue_script( 'mcb-main', MCB_PLUGIN_URL . '/client/js/scripts.js' );

    wp_enqueue_style( 'mcb-client-main', MCB_PLUGIN_URL . '/client/css/styles.css');

    // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script( 'mcb-main', 'ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}
add_action( 'wp_enqueue_scripts', 'mcb_load_client_scripts' );

// [contactbot]
function mcb_shortcode_callback( $atts ) {
    $settings = MCBUtils::load_settings();

    if ((isset($settings['telegram_connected']) && $settings['telegram_connected'] == true)
        || (isset($settings['fbm_connected']) && $settings['fbm_connected'] == true)) {
        // We have a valid client
        // Output the HTML
        include(MCB_PLUGIN_DIR . '/client/output.php');
    }
    else {
        _e( 'No valid messaging client configured. Please contact site administrator.', 'mcb' );
    }

}
add_shortcode( 'contactbot', 'mcb_shortcode_callback' );