<?php


    public function getById($caseID, $version = 0)
    {
        return $this->loadExtension('bytenew')->getById($caseID, $version);
    }


