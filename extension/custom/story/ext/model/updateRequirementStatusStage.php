<?php


    public function updateRequirementStatusStage($days = 1,$reject = 3,$research = 30,$suspend = 30)
    {
        return $this->loadExtension('bytenew')->updateRequirementStatusStage($days,$reject,$research,$suspend);
    }


