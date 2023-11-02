<?php


    public function printChanges($objectType, $histories, $canChangeTag = true, $onlyReturn = false)
    {
        return $this->loadExtension('bytenew')->printChanges($objectType, $histories, $canChangeTag , $onlyReturn);
    }


