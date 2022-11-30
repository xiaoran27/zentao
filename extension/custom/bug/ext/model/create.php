<?php


    public function create($from = '', $extras = '')
    {
        return $this->loadExtension('bytenew')->create($from , $extras);
    }
