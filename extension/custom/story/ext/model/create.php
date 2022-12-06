<?php


    public function create($executionID = 0, $bugID = 0, $from = '', $extra = '')
    {
        return $this->loadExtension('bytenew')->create($executionID , $bugID , $from , $extra );
    }


