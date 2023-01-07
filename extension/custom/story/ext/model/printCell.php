<?php

    public function printCell($col, $story, $users, $branches, $storyStages, $modulePairs = array(), $storyTasks = array(), $storyBugs = array(), $storyCases = array(), $mode = 'datatable', $storyType = 'story', $execution = '', $isShowBranch = '')
    {
        return $this->loadExtension('bytenew')->printCell($col, $story, $users, $branches, $storyStages, $modulePairs, $storyTasks, $storyBugs, $storyCases, $mode, $storyType, $execution, $isShowBranch);
    }
