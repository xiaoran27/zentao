<?php

/**
 * Send single message
 *
 * @param string $message // {"title":"myTitle","text":"mytext"}
 * @param string $userList='' 多个手机号或dingID用','分隔
 * @param string $accessToken=null  默认$config->ding->apps[$config->ding->default->app]的授权值
 * @param string $robotCode=null  默认$config->ding->apps[$config->ding->default->app]的robotCode或appKey
 * 
 * @access public
 * @return bool|string
 */
public
function dingSingleSend($message, $userList="", $accessToken=null, $robotCode=null)
{
    return $this->dingSingle($message, $userList, $accessToken, $robotCode);

}
