<?php

/**
 * Class MCBSession
 *
 * Session management class
 */

final class MCBSession {

    /**
     * Call this method to get the singleton instance
     *
     * @return MCBSession
     */
    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {

            $inst = new MCBSession();
        }
        return $inst;
    }

    private $segment;

    /**
     * Private ctor so nobody else can instance it
     *
     */
    private function __construct()
    {
        // Session
        $session_factory = new \Aura\Session\SessionFactory;
        $session = $session_factory->newInstance($_COOKIE);

        // get a _Segment_ object
        $this->segment = $session->getSegment('MCB\Session\MCBSession');
    }

    /**
     * Get a value from the session
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    private function get($key, $default = null) {
        return $this->segment->get($key, $default);
    }

    /**
     * Set a value in the session
     *
     * @param $key
     * @param $value
     */
    private function set($key, $value) {
        $this->segment->set($key, $value);
    }

    /**
     * Store the user object in the session
     *
     * @param $type: MCBUser::TYPE_USER || MCBUser::TYPE_ALIAS
     * @param $username: random alias or WP login_name if available
     */
    public function setUser(MCBUser $user) {
        $timestamp = (new DateTime())->getTimestamp();
        $user->setUpdated($timestamp);

        $this->set('user', $user);
    }

    /**
     * Returns the session user
     *
     * @return mixed
     */
    public function getUser() {
        return $this->get('user');
    }

    /*
     * Sets the telegram offset for the session user (Telegram only)
     */
    public function setTelegramOffset($offset) {
        if ($user = $this->getUser()) {
            $user->setTelegramOffset($offset);
            $this->set('user', $user);

            return $user;
        }

        return false;
    }

    /**
     * Get the telegram message from the session
     */
    public function getTelegramOffset() {
        if ($user = $this->getUser()) {
            return $user->getTelegramOffset();
        }

        return false;
    }

}