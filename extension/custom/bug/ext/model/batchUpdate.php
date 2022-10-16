<?php


    /**
     * Batch update bugs.
     *
     * @access public
     * @return array
     */
    public function batchUpdate()
    {
        $bugs        = array();
        $allChanges  = array();
        $now         = helper::now();
        $data        = fixer::input('post')->get();
        $bugIDList   = $this->post->bugIDList ? $this->post->bugIDList : array();
        $unlinkPlans = array();
        $link2Plans  = array();

        if(!empty($bugIDList))
        {
            /* Process the data if the value is 'ditto'. */
            foreach($bugIDList as $bugID)
            {
                if($data->types[$bugID]       == 'ditto') $data->types[$bugID]       = isset($prev['type'])       ? $prev['type']       : '';
                if($data->severities[$bugID]  == 'ditto') $data->severities[$bugID]  = isset($prev['severity'])   ? $prev['severity']   : 3;
                if($data->pris[$bugID]        == 'ditto') $data->pris[$bugID]        = isset($prev['pri'])        ? $prev['pri']        : 0;
                if($data->plans[$bugID]       == 'ditto') $data->plans[$bugID]       = isset($prev['plan'])       ? $prev['plan'] : '';
                if($data->assignedTos[$bugID] == 'ditto') $data->assignedTos[$bugID] = isset($prev['assignedTo']) ? $prev['assignedTo'] : '';
                if($data->resolvedBys[$bugID] == 'ditto') $data->resolvedBys[$bugID] = isset($prev['resolvedBy']) ? $prev['resolvedBy'] : '';
                if($data->resolutions[$bugID] == 'ditto') $data->resolutions[$bugID] = isset($prev['resolution']) ? $prev['resolution'] : '';
                if(isset($data->branches[$bugID]) and $data->branches[$bugID] == 'ditto') $data->branches[$bugID] = isset($prev['branch']) ? $prev['branch'] : 0;

                $prev['type']       = $data->types[$bugID];
                $prev['severity']   = $data->severities[$bugID];
                $prev['pri']        = $data->pris[$bugID];
                $prev['branch']     = isset($data->branches[$bugID]) ? $data->branches[$bugID] : '';
                $prev['plan']       = $data->plans[$bugID];
                $prev['assignedTo'] = $data->assignedTos[$bugID];
                $prev['resolvedBy'] = $data->resolvedBys[$bugID];
                $prev['resolution'] = $data->resolutions[$bugID];
            }

            /* Initialize bugs from the post data.*/
            $extendFields = $this->getFlowExtendFields();
            $oldBugs = $bugIDList ? $this->getByList($bugIDList) : array();
            foreach($bugIDList as $bugID)
            {
                $oldBug = $oldBugs[$bugID];

                $os       = array_filter($data->os[$bugID]);
                $browsers = array_filter($data->browsers[$bugID]);

                $bug = new stdclass();
                $bug->id             = $bugID;
                $bug->lastEditedBy   = $this->app->user->account;
                $bug->lastEditedDate = $now;
                $bug->type           = $data->types[$bugID];
                $bug->severity       = $data->severities[$bugID];
                $bug->pri            = $data->pris[$bugID];
                $bug->color          = $data->colors[$bugID];
                $bug->title          = $data->titles[$bugID];
                $bug->feedbackBy     = $data->feedbackBy[$bugID];
                $bug->purchaser      = $data->purchaser[$bugID];
                $bug->plan           = empty($data->plans[$bugID]) ? 0 : $data->plans[$bugID];
                $bug->branch         = empty($data->branches[$bugID]) ? 0 : $data->branches[$bugID];
                $bug->module         = $data->modules[$bugID];
                $bug->assignedTo     = $bug->status == 'closed' ? $oldBug->assignedTo : $data->assignedTos[$bugID];
                $bug->deadline       = $data->deadlines[$bugID];
                $bug->resolvedBy     = $data->resolvedBys[$bugID];
                $bug->keywords       = $data->keywords[$bugID];
                $bug->os             = implode(',', $os);
                $bug->browser        = implode(',', $browsers);
                $bug->resolution     = $data->resolutions[$bugID];
                $bug->duplicateBug   = $data->duplicateBugs[$bugID] ? $data->duplicateBugs[$bugID] : $oldBug->duplicateBug;

                if($bug->assignedTo != $oldBug->assignedTo) $bug->assignedDate = $now;
                if($bug->resolution != '') $bug->confirmed = 1;
                if(($bug->resolvedBy != '' or $bug->resolution != '') and $oldBug->status != 'closed')
                {
                    $bug->resolvedDate = $now;
                    $bug->status       = 'resolved';
                }
                if($bug->resolution != '' and $bug->resolvedBy == '') $bug->resolvedBy = $this->app->user->account;
                if($bug->resolution != '' and $bug->assignedTo == '')
                {
                    $bug->assignedTo   = $oldBug->openedBy;
                    $bug->assignedDate = $now;
                }

                foreach($extendFields as $extendField)
                {
                    $bug->{$extendField->field} = $this->post->{$extendField->field}[$bugID];
                    if(is_array($bug->{$extendField->field})) $bug->{$extendField->field} = join(',', $bug->{$extendField->field});

                    $bug->{$extendField->field} = htmlSpecialString($bug->{$extendField->field});
                }

                if($bug->plan != $oldBug->plan)
                {
                    if($bug->plan != $oldBug->plan and !empty($oldBug->plan)) $unlinkPlans[$oldBug->plan] = empty($unlinkPlans[$oldBug->plan]) ? $bugID : "{$unlinkPlans[$oldBug->plan]},$bugID";
                    if($bug->plan != $oldBug->plan and !empty($bug->plan))    $link2Plans[$bug->plan]  = empty($link2Plans[$bug->plan]) ? $bugID : "{$link2Plans[$bug->plan]},$bugID";
                }

                $bugs[$bugID] = $bug;
                unset($bug);
            }

            $isBiz = $this->config->edition == 'biz';
            $isMax = $this->config->edition == 'max';

            /* Update bugs. */
            foreach($bugs as $bugID => $bug)
            {
                $oldBug = $oldBugs[$bugID];

                $this->dao->update(TABLE_BUG)->data($bug)
                    ->autoCheck()
                    ->batchCheck($this->config->bug->edit->requiredFields, 'notempty')
                    ->checkIF($bug->resolvedBy, 'resolution', 'notempty')
                    ->checkIF($bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
                    ->checkFlow()
                    ->where('id')->eq((int)$bugID)
                    ->exec();

                if(!dao::isError())
                {
                    if(!empty($bug->resolvedBy)) $this->loadModel('score')->create('bug', 'resolve', $bugID);

                    $this->executeHooks($bugID);

                    $allChanges[$bugID] = common::createChanges($oldBug, $bug);

                    if(($isBiz || $isMax) && $oldBug->feedback && !isset($feedbacks[$oldBug->feedback]))
                    {
                        $feedbacks[$oldBug->feedback] = $oldBug->feedback;
                        $this->loadModel('feedback')->updateStatus('bug', $oldBug->feedback, $bug->status, $oldBug->status);
                    }
                }
                else
                {
                    return helper::end(js::error('bug#' . $bugID . dao::getError(true)));
                }
            }
        }
        if(!dao::isError())
        {
            $this->loadModel('score')->create('ajax', 'batchEdit');

            $this->loadModel('action');
            foreach($unlinkPlans as $planID => $bugs) $this->action->create('productplan', $planID, 'unlinkbug', '', $bugs);
            foreach($link2Plans as $planID => $bugs) $this->action->create('productplan', $planID, 'linkbug', '', $bugs);
        }
        return $allChanges;
    }