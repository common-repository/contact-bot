<?php
/*
Plugin Name: Contact bot
Plugin URI: http://contactbot.github.io/
Description: A simple and friendly contact bot
Author: Amitai Gat
Text Domain: mcb
Domain Path: /languages/
Version: 1.5
*/

define('MCB_VERSION', '1.5');

define('MCB_REQUIRED_WP_VERSION', '4.3');

define('MCB_PLUGIN', __FILE__);

define('MCB_PLUGIN_BASENAME', plugin_basename(MCB_PLUGIN));

define('MCB_PLUGIN_NAME', trim(dirname(MCB_PLUGIN_BASENAME), '/'));

define('MCB_PLUGIN_DIR', untrailingslashit(dirname(MCB_PLUGIN)));

define('MCB_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));

define('MCB_PLUGIN_MODULES_DIR', MCB_PLUGIN_DIR . '/modules');

// Deprecated, not used in the plugin core. Use MCB_plugin_url() instead.
define('MCB_PLUGIN_URL', untrailingslashit(plugins_url('', MCB_PLUGIN)));

if (!defined('MCB_ADMIN_READ_CAPABILITY')) {
    define('MCB_ADMIN_READ_CAPABILITY', 'edit_posts');
}

if (!defined('MCB_ADMIN_READ_WRITE_CAPABILITY')) {
    define('MCB_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages');
}

if (!defined('MCB_VERIFY_NONCE')) {
    define('MCB_VERIFY_NONCE', true);
}

// Load session library
require_once MCB_PLUGIN_DIR . '/php-lib/Aura.Session/autoload.php';

// Load everything else
require_once MCB_PLUGIN_DIR . '/loader.php';

/**
 * Loads plugin text domain
 */
function mcb_load_textdomain()
{
    load_plugin_textdomain('mcb');
}

add_action('init', 'mcb_load_textdomain');

/**
 * Register admin pages
 */
function mcb_register_admin_pages()
{
    // Schedule page
    $page = add_menu_page(__('Contact Bot Configuration', 'mcb'),
        __('Contact Bot', 'mcb'),
        MCB_ADMIN_READ_WRITE_CAPABILITY,
        'mcb-settings',
        'mcb_settings_page_callback',
        'dashicons-smiley',
        40);

    // Standard settings page
    add_submenu_page('mcb-settings',
        __('How to use', 'mcb'),
        __('How to use', 'mcb'),
        MCB_ADMIN_READ_WRITE_CAPABILITY,
        'mcb-documentation',
        'mcb_documentation_page_callback');

    // add_action( "admin_print_styles-{$page}", 'mcb_plugin_admin_styles' );

}

add_action('admin_menu', 'mcb_register_admin_pages');

/**
* Register activation hook
*/
function mcb_register_activation() {
    do_action( 'mcb_activate_action' );
}
register_activation_hook( __FILE__, 'mcb_register_activation' );

/**
 * Activation
 */
function mcb_activate() {
    $data = get_plugin_data(__FILE__);

    if ($data && is_array($data) && isset($data['Version'])) {
        $installed_version = floatval($data['Version']);
        $new_version = floatval(MCB_VERSION);

        if ($installed_version && $new_version) {
            mcb_execute_update_scripts($installed_version, $new_version);
        }
    }
}
add_action( 'mcb_activate_action', 'mcb_activate' );

function mcb_execute_update_scripts($installed_version, $new_version) {
    if ($installed_version < 1.4) {
        // Verify we have a selected client
        $selected_client = MCBUtils::get_settings_item('selected_client');
        if ($selected_client) {
            // We don't have a selected client, let's see if telegram is
            // already connected.
            $telegram_connected = MCBUtils::get_settings_item('telegram_connected');
            $client = $telegram_connected ? MCBMessenger::TYPE_TELEGRAM : MCBMessenger::TYPE_FB_MESSENGER;
            MCBUtils::save_settings_item('selected_client', $client);
        }
    }
}

/**
 *
 *
 *
 *        AJAX HANDLERS
 *
 *
 *
 *
 */

/**
 * Generate (and store) Facebook Messenger (fbm) token
 */
add_action('wp_ajax_mcb_select_client', 'mcb_select_client_callback');

function mcb_select_client_callback()
{
    if (current_user_can('administrator')) {
        // Generate token
        if (isset($_POST['data']) && isset($_POST['data']['client'])) {
            $client = trim($_POST['data']['client']);

            $valid_clients = [
                'fb-messenger',
                'telegram'
            ];

            if (!in_array($client, $valid_clients)) {
                status_header(404);
                wp_send_json_error(['error' => "invalid client: {$client}"]);
            } else {
                // Store token
                MCBUtils::save_settings_item('selected_client', $client);

                // Return result
                wp_die(json_encode(['client' => $client]));
            }
        } else {
            status_header(404);
            wp_send_json_error(['error' => 'client param missing']);
        }


    }
    status_header(401);
    wp_die(); // this is required to terminate immediately and return a proper response*/
}

/**
 * Handle incoming messages from client (user)
 */
add_action('wp_ajax_mcb_handle_message', 'prefix_ajax_mcb_handle_message');
add_action('wp_ajax_nopriv_mcb_handle_message', 'prefix_ajax_mcb_handle_message');

function prefix_ajax_mcb_handle_message()
{

    // Handle request then generate response using WP_Ajax_Response
    $data = MCBUtils::post('data');
    if (!$data) {
        die("missing 'data' param");
    }

    $s = MCBSession::instance();

    $user = wp_get_current_user();
    $session_user = $s->getUser();
    if (!$session_user || ($user && $user->ID !== 0 && $session_user->getUsername() != $user->user_login)) {
        // No user in this session
        $u = new MCBUser();
        if ($user && $user->ID !== 0) {
            // User is logged in
            $u->setType(MCBUser::TYPE_USER);
            $u->setUsername($user->user_login);
        } else {
            // User not logged in, let's provide an alias for now
            $u->setType(MCBUser::TYPE_ALIAS);
            $u->setUsername(MCBNames::getRandom());
        }

        $s->setUser($u);
        $session_user = $s->getUser();
    }

    if (!$session_user) {
        throw new Exception("Unable to get session for this user");
    }

    $r = MCBMessenger::handle_message($session_user, $data);
    if (isset($r->error)) {
        status_header(500);
        wp_send_json_error(['error' => $r->error]);
    }
    else {
        http_response_code(200);
    }
}

/**
 * Handle updates request from client (user)
 */
add_action('wp_ajax_mcb_get_updates', 'prefix_ajax_mcb_get_updates');
add_action('wp_ajax_nopriv_mcb_get_updates', 'prefix_ajax_mcb_get_updates');

function prefix_ajax_mcb_get_updates()
{
    $s = MCBSession::instance();
    $session_user = $s->getUser();

    if (!$session_user) {
        // No session for this user, do not return anything
        // Just wait for the user to write something first
        // and establish the identity
        wp_die(); // this is required to terminate immediately and return a proper response*/
    }

    // We have a session for this user
    // Let's get updates
    $data = MCBUtils::post('data');
    $updates = MCBMessenger::getUpdates($session_user, $data);
    if (isset($updates['error'])) {
        status_header(500);
        wp_send_json_error($updates);
    }

    // Handle request then generate response using WP_Ajax_Response
    wp_die(json_encode($updates)); // this is required to terminate immediately and return a proper response*/
}

/**
 * Store telegram token
 */
add_action('wp_ajax_mcb_store_telegram_token', 'mcb_store_telegram_token_callback');

function mcb_store_telegram_token_callback()
{
    if (current_user_can('administrator')) {
        // user is admin
        if (isset($_POST['data'])) {
            $token = trim($_POST['data']);

            MCBUtils::save_settings_item('telegram_token', $token);
        } else {
            status_header(404);
            wp_send_json_error(['error' => 'token is not provided']);
        }

    }
    wp_die(); // this is required to terminate immediately and return a proper response*/
}

/**
 * Verify telegram token
 */
add_action('wp_ajax_mcb_verify_telegram', 'mcb_verify_telegram_callback');

function mcb_verify_telegram_callback()
{
    if (current_user_can('administrator')) {
        // Telegram connected successfully
        MCBUtils::save_settings_item('telegram_connected', false);
        MCBUtils::save_settings_item('selected_client', 'telegram');

        // user is admin
        $result = MCBMessenger::verifyAdmin(MCBMessenger::TYPE_TELEGRAM);
        if (isset($result['error'])) {
            status_header(401);
            wp_send_json_error($result);
        }

        // Telegram connected successfully
        MCBUtils::save_settings_item('telegram_connected', true);

    }
    wp_die(); // this is required to terminate immediately and return a proper response*/
}

/**
 * Generate (and store) Facebook Messenger (fbm) token
 */
add_action('wp_ajax_mcb_generate_fbm_token', 'mcb_generate_fbm_callback');

function mcb_generate_fbm_callback()
{
    if (current_user_can('administrator')) {
        // Generate token
        $token = MCBUtils::gen_uuid();

        // Store token
        MCBUtils::save_settings_item('fbm_token', $token);

        // Return result
        wp_die(json_encode(['token' => $token]));

    }
    status_header(401);
    wp_die(); // this is required to terminate immediately and return a proper response*/
}

/**
 * Verify telegram token
 */
add_action('wp_ajax_mcb_verify_fbm', 'mcb_verify_fbm_callback');

function mcb_verify_fbm_callback()
{
    if (current_user_can('administrator')) {
        // Telegram connected successfully
        MCBUtils::save_settings_item('fbm_connected', false);
        MCBUtils::save_settings_item('selected_client', 'fb-messenger');

        // user is admin
        $result = MCBMessenger::verifyAdmin(MCBMessenger::TYPE_FB_MESSENGER);
        if (isset($result['error'])) {
            status_header(401);
            wp_send_json_error($result);
        }

        // Telegram connected successfully
        MCBUtils::save_settings_item('fbm_connected', true);

    }
    wp_die(); // this is required to terminate immediately and return a proper response*/
}