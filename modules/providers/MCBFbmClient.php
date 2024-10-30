<?php

/**
 * Class MCBFbmClient
 *
 * A Facebook messenger client implementing the IClient interface
 */
final class MCBFbmClient implements IClient
{

    const HOST_REMOTE = 'remote';
    const HOST_LOCAL = 'localhost';

    /**
     * Call this method to get singleton
     *
     * @return MCBTelegramClient
     */
    public static function instance($host = null)
    {
        $host = $host == null ? self::HOST_REMOTE : $host;
        static $inst = null;
        if ($inst === null) {

            $inst = new MCBFbmClient($host);
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    private function __construct($host)
    {
        $this->token = MCBUtils::get_settings_item('fbm_token', false);
        $this->host = $host;
    }

    private function baseUrl() {
        // Set to true to debug locally
        $isLocalhost = $this->host == 'localhost' ? true : false;
        return $isLocalhost ? 'http://localhost:3000/api/client' : 'https://mycontactbot.com/api/client';
    }


    public function send($data) {
        $url = $this->baseUrl() . '/sendMessage';
        $result = MCBUtils::postJson($url, $data);

        if (!$result) {
            throw new Exception("FBM API call failed");
        }

        $json = json_decode($result);
        return $json;
    }

    public function getHistory($offset = null) {
        if (!$this->token) {
            throw new Exception("FBM get updates cannot be called without a token (base url: {$this->baseUrl()})");
        }

        $url = $this->baseUrl() . '/getUpdates';
        $data = ['token' => $this->token, 'offset' => $offset ? $offset : 0];
        $result = MCBUtils::postJson($url, $data);

        if (!$result) {
            throw new Exception("FBM API call failed (base url: {$this->baseUrl()})");
        }

        $json = json_decode($result);

        /*if (!$json) {
            throw new Exception("Failed to get server response from {$this->baseUrl()}: " . print_r($result, true));
        }*/

        return $json;
    }

    public function verifyAdmin() {
        $token = $this->token;
        if (!$token) {
            return ['error' => 'Failed to get token from instance', 'errorCode' => 820];
        }

        $email = wp_get_current_user()->user_email;
        $url = $this->baseUrl() . '/verifyRegistration';
        $data = [
            'email' => $email,
            'token' => $token
        ];

        $result = MCBUtils::postJson($url, $data);
        try {
            $result = json_decode($result);
        }
        catch (Exception $e) {
            return ['error' => 'Failed to parse response', 'debug' => $e->getMessage(), 'errorCode' => 830];
        }

        if ($result->status == 'success') {
            // Admin created successfully
            return $data;
        }
        else {
            return ['error' => $result->error, 'errorCode' => $result->errorCode];
        }

    }
}