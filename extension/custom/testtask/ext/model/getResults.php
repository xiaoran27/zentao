<?php


    public function getResults($runID, $caseID = 0, $status = 'all')
    {
        return $this->loadExtension('bytenew')->getResults($runID, $caseID, $status );
    }


