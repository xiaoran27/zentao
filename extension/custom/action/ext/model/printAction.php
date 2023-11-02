<?php


    public function printAction($action, $desc = '', $onlyReturn=false)
    {
        return $this->loadExtension('bytenew')->printAction($action, $desc , $onlyReturn);
    }


