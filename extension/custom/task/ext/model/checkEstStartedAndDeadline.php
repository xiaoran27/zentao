<?php


    public function checkEstStartedAndDeadline($executionID, $estStarted, $deadline, $pre = '')
    {
        return $this->loadExtension('bytenew')->checkEstStartedAndDeadline($executionID, $estStarted, $deadline, $pre);
    }


