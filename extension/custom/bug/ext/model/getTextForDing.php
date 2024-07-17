<?php


    public function getTextForDing($ltdays = 31, $excludeUsers = 'admin,system')
    {
        return $this->loadExtension('bytenew')->getTextForDing($ltdays, $excludeUsers);
    }


