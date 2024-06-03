<?php


    public function dingSend($type = 'single,robotapi', $ltdays = 93, $webhook='', $autoCancel=true)
    {
        return $this->loadExtension('bytenew')->dingSend($type, $ltdays, $webhook, $autoCancel);
    }


