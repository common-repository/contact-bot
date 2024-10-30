<?php

/**
 * The MCB messenger
 *
 * This class provides an abstract interface to access
 * the different messaging clients (e.g. Telegram, Messenger, etc.)
 */

// Load interface
require_once MCB_PLUGIN_DIR . '/modules/providers/IClient.php';

// Load providers (FB Messenger coming soon)
require_once MCB_PLUGIN_DIR . '/modules/providers/MCBTelegramClient.php';
require_once MCB_PLUGIN_DIR . '/modules/providers/MCBFbmClient.php';

class MCBMessenger
{
    const TYPE_TELEGRAM = 'telegram';
    const TYPE_FB_MESSENGER = 'fb-messenger';

    // Where we keep a reference to the admin
    public static $admin = null;

    /**
     * @param $type: telegram, fb-messenger, etc.
     */
    public static function verifyAdmin($type) {
        if ($type == self::TYPE_TELEGRAM) {
            $client = MCBTelegramClient::instance();

            // Get updates
            try {
                $updates = $client->getHistory();
            }
            catch (Exception $e) {
                return ['error' => $e->getMessage(), 'errorCode' => 9999];
            }
            if ($updates->ok && count($updates->result) > 0) {

                // Admin is confirming registration
                // Let's find the admin message
                if ($admin = $client->findAdminVerificationMessage($updates->result)) {
                    // Store in database
                    MCBUtils::save_settings_item('admin', $admin);

                    // Store in instance so we don't have to fetch it
                    self::$admin = $admin;
                }
                else {
                    return ['error' => 'failed to verify admin', 'errorCode' => 700];
                }
            }
            else {
                return ['error' => 'failed to get new updates for admin verification', 'errorCode' => 704];
            }
        }
        else if ($type == self::TYPE_FB_MESSENGER) {
            $fbclient = MCBFbmClient::instance();

            $result = $fbclient->verifyAdmin();
            if (isset($result['error'])) {
                // We have a problem
                return $result;
            }
            else {
                // Success, store in database
                MCBUtils::save_settings_item('fb_admin', $result);
            }
        }

        return [];
    }

    /**
     * Handle an incoming message
     *
     * @param $from: the user id or alias
     * @param $message
     * @return mixed|null|string
     * @throws Exception
     */
    public static function handle_message(MCBUser $session_user, $message) {
        // Get selected client
        $type = MCBUtils::get_settings_item('selected_client', false);
        if (!$type || $type === false) {
            return ['error' => 'no selected clients'];
        }

        if ($type == self::TYPE_TELEGRAM) {
            // If selected client is Telegram
            $client = MCBTelegramClient::instance();

            $from = $session_user->getUsername();
            $data = [
                'chat_id' => self::getAdmin()['id'],
                'text' =>  "$from -> {$message['text']}",
                'src' => $message['src'],
                'offset' => 0
            ];

            // TODO: increment offset if needed
        }
        else {
            // FBM
            // If selected client is Telegram
            $host = isset($message['host']) ? $message['host'] : MCBFbmClient::HOST_REMOTE;
            $client = MCBFbmClient::instance($host);

            $from = $session_user->getUsername();
            $data = [
                'token' => $client->token,
                'from' => $from,
                'text' =>  $message['text'],
                'src' => $message['src'],
                'offset' => 0
            ];

        }

        $result = null;
        try {
            $result = $client->send($data);
        }
        catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;

    }

    /**
     * Get the latest bot updates
     *
     * @param MCBUser $session_user
     * @return array
     * @throws Exception
     */
    public static function getUpdates(MCBUser $session_user, $data = null) {
        $type = MCBUtils::get_settings_item('selected_client', false);
        if (!$type || $type === false) {
            return ['error' => 'no selected clients'];
        }

        if ($type == self::TYPE_TELEGRAM) {
            $client = MCBTelegramClient::instance();

            // Get updates
            $offset = $session_user->getTelegramOffset() ? $session_user->getTelegramOffset() : 0;
            $offset = $offset > 0 ? $offset + 1 : 0;
            $updates = $client->getHistory($offset);
            if ($updates->ok && count($updates->result) > 0) {
                // We have updates, let's filter out updates
                // that are not directed at this user
                $username = $session_user->getUsername() . ':';
                $results = array_filter($updates->result, function ($r) use ($username) {
                    if (stripos($r->message->text, $username) === 0) {
                        // We got a match
                        $r->src = 'admin';

                        // Remove session string
                        $r->message->text = trim(str_ireplace($username, '', $r->message->text));
                        return $r;
                    }
                });

                // Get offset
                $offset = max(array_map(function($r) {
                    return $r->update_id;
                }, $results));

                $s = MCBSession::instance();
                $s->setTelegramOffset($offset);

                return $results;
            }
        }
        else if ($type == self::TYPE_FB_MESSENGER) {
            $host = ($data && isset($data['host'])) ? $data['host'] : MCBFbmClient::HOST_REMOTE;
            $client = MCBFbmClient::instance($host);

            $offset = $session_user->getFbmOffset() ? $session_user->getFbmOffset() : 0;
            $updates = $client->getHistory($offset);

            $username = $session_user->getUsername() . ':';
            $results = array_filter($updates, function ($r) use ($username) {
                if (stripos($r->message->text, $username) === 0) {
                    // We got a match
                    $r->src = 'admin';

                    // Remove session string
                    $r->message->text = trim(str_ireplace($username, '', $r->message->text));
                    return $r;
                }
            });

            return $results;
        }
        else {
            return ['error' => "unknown client type -> {$type}"];
        }
    }

    /**
     * Returns the selected admin
     *
     * @return bool|null
     * @throws Exception
     */
    private static function getAdmin() {
        // Check if we have it in our static variable
        if (self::$admin) return self::$admin;

        // No, we don't. Let's fetch from our database
        $admin = MCBUtils::get_settings_item('admin');
        if ($admin) {
            self::$admin = $admin;
            return $admin;
        }
        else {
            throw new Exception("No contact bot admin");
        }
    }
}