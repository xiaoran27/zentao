<?php

public function getToken($type='ding',$appname=null, $appKey=null, $appSecret=null, $agentId=null)
{

    if (!isset($this->config->$type)) return false;
    

    if (empty($appname) ){
        $appname = $this->config->$type->default->app;
    }
    if (empty($appKey) ){
        $appKey = $this->config->$type->apps[$appname]['appKey'];
    }
    if (empty($appSecret) ){
        $appSecret = $this->config->$type->apps[$appname]['appSecret'];
    }
    if (empty($agentId) ){
        $agentId = $this->config->$type->apps[$appname]['agentId'];
    }

    $this->app->loadClass('dingapi', true);
    $dingapi = new dingapi($appKey, $appSecret, $agentId);
    $accessToken=$dingapi->getToken();
    
    return $accessToken;
}
