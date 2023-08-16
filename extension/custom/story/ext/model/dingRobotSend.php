<?php


public function dingRobotSend($url=null, $type='all', $product=-1, $sla=0, $program = 223, $responseResult = 'todo' )
{
    return $this->loadExtension('bytenew')->dingRobotSend($url, $type, $product, $sla, $program , $responseResult );
}


