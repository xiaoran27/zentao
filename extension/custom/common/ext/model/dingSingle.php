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
function dingSingle($accessToken, $robotCode, $userList, $message)
{
    return $this->dingSignle($accessToken, $robotCode, $userList, $message);

}
