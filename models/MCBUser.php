<?php

/**
 * Class MCBUser
 *
 * Out custom user class
 */

class MCBUser {

    const TYPE_USER = 'type:user';
    const TYPE_ALIAS = 'type:alias';

    private $username;
    private $type;
    private $updated;
    private $telegram_offset = false;
    private $fbm_offset = false;

    /**
     * @return int
     */
    public function getFbmOffset()
    {
        return $this->fbm_offset;
    }

    /**
     * @param int $fbm_offset
     */
    public function setFbmOffset($fbm_offset)
    {
        $this->fbm_offset = $fbm_offset;
    }

    /**
     * @return mixed
     */
    public function getTelegramOffset()
    {
        return $this->telegram_offset;
    }

    /**
     * @param mixed $telegram_offset
     */
    public function setTelegramOffset($telegram_offset)
    {
        $this->telegram_offset = $telegram_offset;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }



}