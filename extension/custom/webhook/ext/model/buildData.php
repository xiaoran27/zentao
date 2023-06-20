<?php


public function buildData($objectType, $objectID, $actionType, $actionID, $webhook)
{
    return $this->loadExtension('bytenew')->buildData($objectType, $objectID, $actionType, $actionID, $webhook);
}


