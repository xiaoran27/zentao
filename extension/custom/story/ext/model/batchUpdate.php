<?php

    /**
     * Batch update stories.
     *
     * @access public
     * @return array.
     */
    public function batchUpdate()
    {
        /* Init vars. */
        $stories     = array();
        $allChanges  = array();
        $now         = helper::now();
        $data        = fixer::input('post')->get();
        $storyIdList = $this->post->storyIdList ? $this->post->storyIdList : array();
        $unlinkPlans = array();
        $link2Plans  = array();

        //error_log($data);

        /* Init $stories. */
        if(!empty($storyIdList))
        {
            $oldStories = $this->getByList($storyIdList);

            /* Process the data if the value is 'ditto'. */
            foreach($storyIdList as $storyID)
            {
                if($data->pris[$storyID]     == 'ditto') $data->pris[$storyID]     = isset($prev['pri'])    ? $prev['pri']    : 0;
                if(isset($data->branches) and $data->branches[$storyID] == 'ditto') $data->branches[$storyID] = isset($prev['branch']) ? $prev['branch'] : 0;
                if($data->modules[$storyID]  == 'ditto') $data->modules[$storyID]  = isset($prev['module']) ? $prev['module'] : 0;
                if($data->plans[$storyID]    == 'ditto') $data->plans[$storyID]    = isset($prev['plan'])   ? $prev['plan']   : '';
                if($data->sources[$storyID]  == 'ditto') $data->sources[$storyID]  = isset($prev['source']) ? $prev['source'] : '';
                if($data->bzCategories[$storyID]  == 'ditto') $data->bzCategories[$storyID]  = isset($prev['bzCategory']) ? $prev['bzCategory'] : '';
                if($data->prCategories[$storyID]  == 'ditto') $data->prCategories[$storyID]  = isset($prev['prCategory']) ? $prev['prCategory'] : '';
                if($data->responseResultes[$storyID]  == 'ditto') $data->responseResultes[$storyID]  = isset($prev['responseResult']) ? $prev['responseResult'] : '';
                if(isset($data->stages[$storyID])        and ($data->stages[$storyID]        == 'ditto')) $data->stages[$storyID]        = isset($prev['stage'])        ? $prev['stage']        : '';
                if(isset($data->closedBys[$storyID])     and ($data->closedBys[$storyID]     == 'ditto')) $data->closedBys[$storyID]     = isset($prev['closedBy'])     ? $prev['closedBy']     : '';
                if(isset($data->closedReasons[$storyID]) and ($data->closedReasons[$storyID] == 'ditto')) $data->closedReasons[$storyID] = isset($prev['closedReason']) ? $prev['closedReason'] : '';

                $prev['pri']    = $data->pris[$storyID];
                $prev['branch'] = isset($data->branches[$storyID]) ? $data->branches[$storyID] : 0;
                $prev['module'] = $data->modules[$storyID];
                $prev['plan']   = $data->plans[$storyID];
                $prev['source'] = $data->sources[$storyID];
                $prev['bzCategory'] = $data->bzCategories[$storyID];
                $prev['prCategory'] = $data->prCategories[$storyID];
                $prev['responseResult'] = $data->responseResultes[$storyID];
                if(isset($data->stages[$storyID]))        $prev['stage']        = $data->stages[$storyID];
                if(isset($data->closedBys[$storyID]))     $prev['closedBy']     = $data->closedBys[$storyID];
                if(isset($data->closedReasons[$storyID])) $prev['closedReason'] = $data->closedReasons[$storyID];
            }

            $extendFields = $this->getFlowExtendFields();
            foreach($storyIdList as $storyID)
            {
                $oldStory = $oldStories[$storyID];

                $story                 = new stdclass();
                $story->id             = $storyID;
                $story->lastEditedBy   = $this->app->user->account;
                $story->lastEditedDate = $now;
                $story->status         = $oldStory->status;
                $story->color          = $data->colors[$storyID];
                $story->title          = $data->titles[$storyID];
                $story->estimate       = $data->estimates[$storyID];
                $story->category       = $data->category[$storyID];
                $story->pri            = $data->pris[$storyID];
                $story->assignedTo     = $data->assignedTo[$storyID];
                $story->assignedDate   = $oldStory == $data->assignedTo[$storyID] ? $oldStory->assignedDate : $now;
                $story->branch         = isset($data->branches[$storyID]) ? $data->branches[$storyID] : 0;
                $story->module         = $data->modules[$storyID];
                $story->plan           = $oldStories[$storyID]->parent < 0 ? '' : $data->plans[$storyID];
                $story->bzCategory         = $data->bzCategories[$storyID];
                $story->prCategory         = $data->prCategories[$storyID];
                $story->responseResult         = $data->responseResultes[$storyID];
                $story->uatDate         = $data->uatDate[$storyID];
		        $story->purchaser         = $data->purchaser[$storyID];
                $story->source         = $data->sources[$storyID];
                $story->sourceNote     = $data->sourceNote[$storyID];
                $story->keywords       = $data->keywords[$storyID];
                $story->stage          = isset($data->stages[$storyID])             ? $data->stages[$storyID]             : $oldStory->stage;
                $story->closedBy       = isset($data->closedBys[$storyID])          ? $data->closedBys[$storyID]          : $oldStory->closedBy;
                $story->closedReason   = isset($data->closedReasons[$storyID])      ? $data->closedReasons[$storyID]      : $oldStory->closedReason;
                $story->duplicateStory = isset($data->duplicateStories[$storyID])   ? $data->duplicateStories[$storyID]   : $oldStory->duplicateStory;
                $story->childStories   = isset($data->childStoriesIDList[$storyID]) ? $data->childStoriesIDList[$storyID] : $oldStory->childStories;
                $story->version        = $story->title == $oldStory->title ? $oldStory->version : $oldStory->version + 1;
                if($story->stage != $oldStory->stage) $story->stagedBy = (strpos('tested|verified|released|closed', $story->stage) !== false) ? $this->app->user->account : '';

                if($story->title != $oldStory->title and $story->status != 'draft')  $story->status     = 'changing';
                if($story->closedBy     != false  and $oldStory->closedDate == '')   $story->closedDate = $now;
                if($story->closedReason != false  and $oldStory->closedDate == '')   $story->closedDate = $now;
                if($story->closedBy     != false  or  $story->closedReason != false) $story->status     = 'closed';
                if($story->closedReason != false  and $story->closedBy     == false) $story->closedBy   = $this->app->user->account;

                if($story->plan != $oldStory->plan)
                {
                    if($story->plan != $oldStory->plan and !empty($oldStory->plan)) $unlinkPlans[$oldStory->plan] = empty($unlinkPlans[$oldStory->plan]) ? $storyID : "{$unlinkPlans[$oldStory->plan]},$storyID";
                    if($story->plan != $oldStory->plan and !empty($story->plan))    $link2Plans[$story->plan]  = empty($link2Plans[$story->plan]) ? $storyID : "{$link2Plans[$story->plan]},$storyID";
                }


                foreach($extendFields as $extendField)
                {
                    $story->{$extendField->field} = $this->post->{$extendField->field}[$storyID];
                    if(is_array($story->{$extendField->field})) $story->{$extendField->field} = join(',', $story->{$extendField->field});

                    $story->{$extendField->field} = htmlSpecialString($story->{$extendField->field});
                }

                $stories[$storyID] = $story;
            }

            foreach($stories as $storyID => $story)
            {
                $oldStory = $oldStories[$storyID];

                $this->dao->update(TABLE_STORY)->data($story)
                    ->autoCheck()
                    ->checkIF($story->closedBy, 'closedReason', 'notempty')
                    ->checkIF($story->closedReason == 'done', 'stage', 'notempty')
                    ->checkIF($story->closedReason == 'duplicate',  'duplicateStory', 'notempty')
                    ->checkFlow()
                    ->where('id')->eq((int)$storyID)
                    ->exec();
                if($story->title != $oldStory->title)
                {
                    $data          = new stdclass();
                    $data->story   = $storyID;
                    $data->version = $story->version;
                    $data->title   = $story->title;
                    $data->spec    = $oldStory->spec;
                    $data->verify  = $oldStory->verify;
                    $this->dao->insert(TABLE_STORYSPEC)->data($data)->exec();
                }

                if(!dao::isError())
                {
                    /* Update story sort of plan when story plan has changed. */
                    if($oldStory->plan != $story->plan) $this->updateStoryOrderOfPlan($storyID, $story->plan, $oldStory->plan);

                    $this->executeHooks($storyID);
                    if($story->type == 'story') $this->batchChangeStage(array($storyID), $story->stage);
                    if($story->closedReason == 'done') $this->loadModel('score')->create('story', 'close');
                    $allChanges[$storyID] = common::createChanges($oldStory, $story);

                    if(($this->config->edition == 'biz' || $this->config->edition == 'max') && $oldStory->feedback && !isset($feedbacks[$oldStory->feedback]))
                    {
                        $feedbacks[$oldStory->feedback] = $oldStory->feedback;
                        $this->loadModel('feedback')->updateStatus('story', $oldStory->feedback, $story->status, $oldStory->status);
                    }
                }
                else
                {
                    return print(js::error('story#' . $storyID . dao::getError(true)));
                }
            }
        }
        if(!dao::isError())
        {
            $this->loadModel('score')->create('ajax', 'batchEdit');

            $this->loadModel('action');
            foreach($unlinkPlans as $planID => $stories) $this->action->create('productplan', $planID, 'unlinkstory', '', $stories);
            foreach($link2Plans as $planID => $stories) $this->action->create('productplan', $planID, 'linkstory', '', $stories);

        }
        return $allChanges;
    }