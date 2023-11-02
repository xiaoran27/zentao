<?php


    public function logHistory($actionID, $changes)
    {
        return $this->loadExtension('bytenew')->logHistory($actionID, $changes);
    }


