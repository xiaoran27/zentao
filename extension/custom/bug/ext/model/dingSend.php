<?php


    public function dingSend($type = 'single,robotapi', $ltdays = 31, $webhook='', $autoClosed=true)
    {
        return $this->loadExtension('bytenew')->dingSend($type, $ltdays, $webhook, $autoClosed);
    }


