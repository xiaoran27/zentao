<?php


    public function updateStoryRspAcceptTimeByTask($days=1, $taskID=0, $createAction = true)
    {
        return $this->loadExtension('bytenew')->updateStoryRspAcceptTimeByTask($days, $taskID, $createAction );
    }


