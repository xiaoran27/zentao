<?php


    public function getTextForDing($ltdays = 93, $excludeUsers = 'admin,system')
    {
        return $this->loadExtension('bytenew')->getTextForDing($ltdays, $excludeUsers);
    }


