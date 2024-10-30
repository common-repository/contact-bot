<?php

/**
 * Class MCBTelegramClient
 *
 * A Telegram client implementing the IClient interface
 */
final class MCBTelegramClient implements IClient
{

    // const TEMP_TOKEN = '213671504:AAGrCbBAWhGbEPwtZtm97Ny9-U8wE7fSfco';

    /**
     * Call this method to get singleton
     *
     * @return MCBTelegramClient
     */
    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {

            $inst = new MCBTelegramClient();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    private function __construct($token)
    {
        $this->token = MCBUtils::get_settings_item('telegram_token', '');
    }

    private function baseUrl() {
        return 'https://api.telegram.org/bot' . $this->token;
    }


    public function send($data) {
        $url = $this->baseUrl() . '/sendMessage';
        $result = MCBUtils::postJson($url, $data);

        if (!$result) {
            throw new Exception("Telegram API call failed");
        }

        $json = json_decode($result);
        if (!$json->ok) {
            // JSON is not OK
            throw new Exception("{$json->error_code}: $json->description");
        }

        return $json;
    }

    public function getHistory($offset = null) {
        $url = $this->baseUrl() . '/getUpdates';
        $data = $offset ? ['offset' => $offset] : [];
        $result = MCBUtils::postJson($url, $data);

        if (!$result) {
            throw new Exception("Telegram API call failed (base url: {$this->baseUrl()})");
        }

        $json = json_decode($result);
        if (!$json) {
            throw new Exception("Failed to get server response from {$this->baseUrl()}: " . print_r($result, true));
        }

        if (!$json->ok) {
            // JSON is not OK
            throw new Exception("{$json->error_code}: $json->description");
        }

        return $json;
    }

    public function findAdminVerificationMessage($messages) {
        $db_token = MCBUtils::get_settings_item('telegram_token');
        foreach ($messages as $m) {
            if (isset($m->message)) {
                $message = $m->message;
                $output_array = [];
                $input = $message->text;
                $match = preg_match("/i'm the admin, my email is (\[?[^\s]+\[?) and my bot token is (\[?[^\s]+\[?)/i",
                    $input, $output_array);
                if ($match) {
                    // We've got a match

                    $token = $output_array[2];
                    if (trim($token) == trim($db_token)) {
                        return [
                            'type' => MCBMessenger::TYPE_TELEGRAM,
                            'email' => $output_array[1],
                            'id' => $message->from->id,
                            'first_name' => isset($message->from->first_name) ? $message->from->first_name : null,
                            'last_name' => isset($message->from->last_name) ? $message->from->last_name : null,
                            'username' => isset($message->from->username) ? $message->from->username : null,
                        ];
                    }


                }
            }
        }

        return false;
    }
}