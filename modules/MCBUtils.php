<?php

/**
 * Class MCBUtils
 *
 * A general purpose helper class
 */
class MCBUtils
{

    const MCB_SETTINGS = 'mcb_settings';

    /**
     * Safely gets a $_POST argument
     *
     * @param $arg
     * @return bool
     */
    public static function post($arg) {
        return isset($_POST[$arg]) ? $_POST[$arg] : false;
    }

    /**
     * Send a POST JSON request
     *
     * @param $url
     * @param array $payload
     * @return mixed
     */
    public static function postJson($url, $payload = []) {
        $payload = json_encode($payload);

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Gets the standard mcb settings from the database and return as an array.
     */
    public static function load_settings() {
        // mcb_set_default_settings();
        $settings = get_option( self::MCB_SETTINGS );
        return unserialize( $settings );
    }

    /**
     * Saves the settings array
     *
     * @param array $settings: 'option_name' => 'value'
     */
    public static function save_settings( $settings ) {
        $settings = serialize( $settings );
        update_option( self::MCB_SETTINGS, $settings );
    }

    /**
     * Stores a single item in the settings object
     *
     * @param $key
     * @param $value
     */
    public static function save_settings_item( $key, $value ) {
        $settings = get_option( self::MCB_SETTINGS );
        $settings = unserialize( $settings );
        $settings[$key] = $value;
        $settings = serialize( $settings );
        update_option( self::MCB_SETTINGS, $settings );
    }

    /**
     * Returns a single item from the settings
     *
     * @param $key
     * @param bool|false $default_value
     * @return bool
     */
    public static function get_settings_item( $key, $default_value = false ) {
        $settings = get_option( self::MCB_SETTINGS );
        $settings = unserialize( $settings );
        return isset($settings[$key]) ? $settings[$key] : $default_value;
    }

    public function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}