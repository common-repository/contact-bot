<?php

/**
 * Interface IClient
 *
 * Our base messaging interface
 */
interface IClient
{
    public function send($message);

    public function getHistory($offset = null);
}