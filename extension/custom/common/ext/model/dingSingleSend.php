<?php

/**
 * Send message
 *
 * @param string $userList
 * @param string $message
 * @access public
 * @return bool|string
 */
public
function dingSingleSend($accessToken, $robotCode, $userList, $message)
{
    return $this->sendSignle($accessToken, $robotCode, $userList, $message);

}
