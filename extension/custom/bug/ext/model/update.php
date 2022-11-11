<?php

    public function update($bugID)
    {
        return $this->loadExtension('bytenew')->update($bugID);
    }

    /**
     * Update a bug.
     *
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function update0($bugID)
    {
        $oldBug = $this->getById($bugID);
        if(!empty($_POST['lastEditedDate']) and $oldBug->lastEditedDate != $this->post->lastEditedDate)
        {
            dao::$errors[] = $this->lang->error->editedByOther;
            return false;
        }
        $now = helper::now();
        $bug = fixer::input('post')
            ->add('id', $bugID)
            ->cleanInt('product,module,severity,project,execution,story,task,branch')
            ->stripTags($this->config->bug->editor->edit['id'], $this->config->allowedTags)
            ->setDefault('product,module,execution,story,task,duplicateBug,branch', 0)
            ->setDefault('openedBuild', '')
            ->setDefault('os', '')
            ->setDefault('browser', '')
            ->setDefault('plan', 0)
            ->setDefault('deadline', '0000-00-00')
            ->setDefault('resolvedDate', '')
            ->setDefault('lastEditedBy',   $this->app->user->account)
            ->setDefault('mailto', '')
            ->setDefault('deleteFiles', array())
            ->add('lastEditedDate', $now)
            ->setIF(strpos($this->config->bug->edit->requiredFields, 'deadline') !== false, 'deadline', $this->post->deadline)
            ->join('openedBuild', ',')
            ->join('mailto', ',')
            ->join('linkBug', ',')
            ->join('os', ',')
            ->join('browser', ',')
            ->join('occursEnv', ',')
            ->setIF($this->post->assignedTo  != $oldBug->assignedTo, 'assignedDate', $now)
            ->setIF($this->post->resolvedBy  != '' and $this->post->resolvedDate == '', 'resolvedDate', $now)
            ->setIF($this->post->resolution  != '' and $this->post->resolvedDate == '', 'resolvedDate', $now)
            ->setIF($this->post->resolution  != '' and $this->post->resolvedBy   == '', 'resolvedBy',   $this->app->user->account)
            ->setIF($this->post->closedBy    != '' and $this->post->closedDate   == '', 'closedDate',   $now)
            ->setIF($this->post->closedDate  != '' and $this->post->closedBy     == '', 'closedBy',     $this->app->user->account)
            ->setIF($this->post->closedBy    != '' or  $this->post->closedDate   != '', 'assignedTo',   'closed')
            ->setIF($this->post->closedBy    != '' or  $this->post->closedDate   != '', 'assignedDate', $now)
            ->setIF($this->post->resolution  != '' or  $this->post->resolvedDate != '', 'status',       'resolved')
            ->setIF($this->post->closedBy    != '' or  $this->post->closedDate   != '', 'status',       'closed')
            ->setIF(($this->post->resolution != '' or  $this->post->resolvedDate != '') and $this->post->assignedTo == '', 'assignedTo', $oldBug->openedBy)
            ->setIF(($this->post->resolution != '' or  $this->post->resolvedDate != '') and $this->post->assignedTo == '', 'assignedDate', $now)
            ->setIF($this->post->assignedTo  == '' and $oldBug->status           == 'closed', 'assignedTo', 'closed')
            ->setIF($this->post->resolution  == '' and $this->post->resolvedDate =='', 'status', 'active')
            ->setIF($this->post->resolution  != '', 'confirmed', 1)
            ->setIF($this->post->story != false and $this->post->story != $oldBug->story, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))
            ->setIF(!$this->post->linkBug, 'linkBug', '')
            ->setIF($this->post->case === '', 'case', 0)
            ->remove('comment,files,labels,uid,contactListMenu')
            ->get();

        $bug = $this->loadModel('file')->processImgURL($bug, $this->config->bug->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_BUG)->data($bug, 'deleteFiles')
            ->autoCheck()
            ->batchCheck($this->config->bug->edit->requiredFields, 'notempty')
            ->checkIF($bug->resolvedBy, 'resolution',  'notempty')
            ->checkIF($bug->closedBy,   'resolution',  'notempty')
            ->checkIF($bug->notifyEmail, 'notifyEmail', 'email')
            ->checkIF($bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
            ->checkIF($bug->resolution == 'fixed',     'resolvedBuild','notempty')
            ->checkFlow()
            ->where('id')->eq((int)$bugID)
            ->exec();

        if(!dao::isError())
        {
            /* Link bug to build and release. */
            if($bug->resolution == 'fixed' and !empty($bug->resolvedBuild) and $oldBug->resolvedBuild != $bug->resolvedBuild)
            {
                if(!empty($oldBug->resolvedBuild)) $this->loadModel('build')->unlinkBug($oldBug->resolvedBuild, (int)$bugID);
                $this->linkBugToBuild($bugID, $bug->resolvedBuild);
            }

            if($bug->plan != $oldBug->plan)
            {
                $this->loadModel('action');
                if(!empty($oldBug->plan)) $this->action->create('productplan', $oldBug->plan, 'unlinkbug', '', $bugID);
                if(!empty($bug->plan)) $this->action->create('productplan', $bug->plan, 'linkbug', '', $bugID);
            }

            $linkBugs    = explode(',', $bug->linkBug);
            $oldLinkBugs = explode(',', $oldBug->linkBug);
            $addBugs     = array_diff($linkBugs, $oldLinkBugs);
            $removeBugs  = array_diff($oldLinkBugs, $linkBugs);
            $changeBugs  = array_merge($addBugs, $removeBugs);
            $changeBugs  = $this->dao->select('id,linkbug')->from(TABLE_BUG)->where('id')->in(array_filter($changeBugs))->fetchPairs();
            foreach($changeBugs as $changeBugID => $changeBug)
            {
                if(in_array($changeBugID, $addBugs) and empty($changeBug))  $this->dao->update(TABLE_BUG)->set('linkBug')->eq($bugID)->where('id')->eq((int)$changeBugID)->exec();
                if(in_array($changeBugID, $addBugs) and !empty($changeBug)) $this->dao->update(TABLE_BUG)->set('linkBug')->eq("$changeBug,$bugID")->where('id')->eq((int)$changeBugID)->exec();
                if(in_array($changeBugID, $removeBugs))
                {
                    $linkBugs = explode(',', $changeBug);
                    unset($linkBugs[array_search($bugID, $linkBugs)]);
                    $this->dao->update(TABLE_BUG)->set('linkBug')->eq(implode(',', $linkBugs))->where('id')->eq((int)$changeBugID)->exec();
                }
            }

            if(!empty($bug->resolvedBy)) $this->loadModel('score')->create('bug', 'resolve', $bugID);

            if($bug->execution and $bug->status != $oldBug->status) $this->loadModel('kanban')->updateLane($bug->execution, 'bug');

            if(($this->config->edition == 'biz' || $this->config->edition == 'max') && $oldBug->feedback) $this->loadModel('feedback')->updateStatus('bug', $oldBug->feedback, $bug->status, $oldBug->status);

            $this->file->processFile4Object('bug', $oldBug, $bug);
            return common::createChanges($oldBug, $bug);
        }
    }
