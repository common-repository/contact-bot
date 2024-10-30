<?php

/**
 * Admin stuff
 */

/**
 * Enqueue admin scripts
 */
function mcb_load_admin_scripts()
{

    // Scripts
    wp_enqueue_script('mcb-angular-core', MCB_PLUGIN_URL . '/lib/angular/angular.min.js', array('jquery'));
    wp_enqueue_script('mcb-admin-main', MCB_PLUGIN_URL . '/admin/js/scripts.js');
    wp_enqueue_script('mcb-sweetalert', MCB_PLUGIN_URL . '/lib/sweetalert/dist/sweetalert.min.js');
    wp_enqueue_script('mcb-sweetalert-angular', MCB_PLUGIN_URL . '/lib/ngSweetAlert/SweetAlert.min.js', ['mcb-sweetalert']);

    // Styles
    wp_enqueue_style('mcb-admin-fa', MCB_PLUGIN_URL . '/lib/font-awesome/css/font-awesome.min.css');
    wp_enqueue_style('mcb-admin-main', MCB_PLUGIN_URL . '/admin/css/styles.css');
    wp_enqueue_style('mcb-sweetalert-style', MCB_PLUGIN_URL . '/lib/sweetalert/dist/sweetalert.css');

    // Localize the script with the admin JS translation strings
    wp_localize_script( 'mcb-admin-main', 'MCB_JS_STRINGS', mcb_js_localization_strings() );
}

add_action('admin_enqueue_scripts', 'mcb_load_admin_scripts');

/**
 * Displays a formatted message after options page submission.
 *
 * @param string $message : should already be internationlized.
 * @param string $type : error, warning, or updated.
 */
function mcb_options_message($message, $type = 'updated')
{
    ?>
    <div id="mcb-options-message">
        <div class="<?php echo $type; ?>">
            <p><?php echo $message; ?></p>
        </div>
    </div>
    <?php
}

/**
 * Gets the standard mcb settings from the database and return as an array.
 */
function mcb_load_settings()
{
    return MCBUtils::load_settings();
}

/**
 * Saves the settings array
 *
 * @param array $settings : 'option_name' => 'value'
 */
function mcb_save_settings($settings)
{
    MCBUtils::save_settings($settings);
}

/**
 * Markup for main academy admin page
 */
function mcb_settings_page_callback()
{

    $selected_client = MCBUtils::get_settings_item('selected_client', 'fb-messenger');

    ?>

    <div ng-app="mcbAdminApp">
        <div ng-controller="AdminCtrl">

            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="mcbGeneralSettingsForm">
                <input type="hidden" value="<?php echo $selected_client ? $selected_client : '';?>" ng-model="selectedClient" id="selected-client-input" />

                <div class="wrap">
                    <h2>
                        <?php _e('Contact Bot Configuration', 'mcb'); ?>
                    </h2>
                    <br/>

                    <div style="margin-bottom:5px;"><b><?php _e('Select your messaging client:', 'mcb'); ?></label></b>
                    </div>
                    <select name="messagingClient" ng-model="data.messagingClient"
                            ng-disabled="loading.settingClient"
                            ng-change="clientSelected(data.messagingClient)">
                        <option value="telegram" ng-selected="data.messagingClient == 'telegram'"><?php _e('Telegram', 'mcb'); ?></option>
                        <option value="fb-messenger" ng-selected="data.messagingClient == 'fb-messenger'"><?php _e('Facebook Messenger', 'mcb'); ?></option>
                    </select> <i class="fa fa-circle-o-notch fa-spin" ng-if="loading.settingClient"></i>

                    <br>

                    <!-- Telegram -->
                    <?php require_once(MCB_PLUGIN_DIR . '/admin/integrations/telegram.php'); ?>

                    <!-- Messenger -->
                    <?php /*require_once(MCB_PLUGIN_DIR . '/admin/integrations/facebook_messenger.php'); */?>

                    <h3><?php _e("Facebook messenger integration is no longer supported", "mcb"); ?></h3>
                    <p><?php _e("Please use Telegram instead.", "mcb"); ?></p>

                    <hr/>


                </div>
            </form>

        </div>
        <!-- End of ng-controller -->
    </div> <!-- End of ng-app -->

    <?php
}

function mcb_documentation_page_callback()
{
    ?>

    <div ng-app="mcbAdminApp">
        <div ng-controller="HowToUseCtrl">

            <h2><?php _e('How to use Contact Bot', 'mcb'); ?></h2>

            <div class="mcb-docs">
                <h4>
                    <?php _e("It's actually quite simple...", 'mcb'); ?>
                </h4>

                <p><?php _e("Once you configure and connect your Contact Bot to your messaging client (Telegram or Facebook Messenger), place the <code>[contactbot]</code> shortcode wherever you want the chat window to appear (in a page or a post), like so:", 'mcb'); ?></p>

                <p class="img-wrapper">
                    <img src="<?php echo MCB_PLUGIN_URL . '/images/mcb-doc-1.png'; ?>"
                         style="max-width: 400px;"
                         alt="Documentation screenshot 1"/>
                </p>

                <p>
                    <?php _e("If you visit the page where you placed the shortcode now, you should see something like this (looks may vary depending on your theme):", 'mcb'); ?>
                </p>

                <p class="img-wrapper">
                    <img src="<?php echo MCB_PLUGIN_URL . '/images/mcb-doc-2.png'; ?>"
                         style="max-width: 400px;"
                         alt="Documentation screenshot 2"/>
                </p>

                <p>
                    <?php _e("If you send a message now, you should be getting the notification on your messaging client (see screenshot below).", 'mcb'); ?>
                </p>

                <p>
                    <?php _e("A few <b>IMPORTANT</b> things to point out:", 'mcb'); ?>
                <ol>
                    <li><?php _e("Since the user may not be logged in (and therefore anonymous), the bot will assign a random username to that user to ease communication. Keep in mind that this is NOT the real user name. If the user IS logged in to your site, the actual username will be used.", 'mcb'); ?></li>
                    <li>
                        <?php _e("In order to reply to a user, you <b><span style='color: red;'>MUST</b> prefix the message with the username followed by a colon, e.g. <code>leonard:</code> as highlighted in the screenshot below. The reason for this is that you may be conversing with more than one user simultaneously and you have to tell the bot at which user your message is aimed.", 'mcb'); ?>
                    </li>
                </ol>
                </p>
                <p class="img-wrapper">
                    <img src="<?php echo MCB_PLUGIN_URL . '/images/mcb-doc-3.png'; ?>"
                         style="max-width: 400px;"
                         alt="Documentation screenshot 3"/>
                </p>

                <h4><?php _e("That's it! Enjoy Contact Bot and feel free to provide feedback or ask any question at <a href='mailto:mycontactbot@gmail.com'>mycontactbot@gmail.com</a></h4>", 'mcb'); ?>
            </div>

        </div>
    </div>

    <?php
}


?>