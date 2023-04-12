<?php


    public function updateRequirementStatusStageByStoryID($storyID, $createAction = true)
    {
        return $this->loadExtension('bytenew')->updateRequirementStatusStageByStoryID($storyID, $createAction);
    }


