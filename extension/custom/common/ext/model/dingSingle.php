<?php

/**
 * Send single message
 *
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
function dingSingle($message, $userList="", $accessToken=null, $robotCode=null)
{

    if (empty($userList) ){
        $userList = "";
    }

    $type='ding';
    if (empty($accessToken) ){
        $accessToken = $this->getToken($type);
    }
    if ( $accessToken === false ) return false;

    
    if (!isset($this->config->$type)) return false;
    $appname = $this->config->$type->default->app;
    
    if (empty($robotCode) ){
        $robotCode = $this->config->$type->apps[$appname]['robotCode'];
    }
    if (empty($robotCode) ){
        $robotCode = $this->config->$type->apps[$appname]['appKey'];
    }

    return $this->sendSignle($accessToken, $robotCode, $userList, $message);

}
