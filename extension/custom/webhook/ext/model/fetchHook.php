<?php


public function fetchHook($webhook, $sendData, $actionID = 0)
{
    return $this->loadExtension('bytenew')->fetchHook($webhook, $sendData, $actionID);
}


