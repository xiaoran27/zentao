<?php

    /**
     * Print cell data
     *
     * @param  object $col
     * @param  object $story
     * @param  array  $users
     * @param  array  $branches
     * @param  array  $storyStages
     * @param  array  $modulePairs
     * @param  array  $storyTasks
     * @param  array  $storyBugs
     * @param  array  $storyCases
     * @access public
     * @return void
     */
    public function printCell($col, $story, $users, $branches, $storyStages, $modulePairs = array(), $storyTasks = array(), $storyBugs = array(), $storyCases = array(), $mode = 'datatable', $storyType = 'story', $execution = '', $isShowBranch = '')
    {
        $tab         = $this->app->tab;
        $executionID = empty($execution) ? $this->session->execution : $execution->id;
        $account     = $this->app->user->account;
        $storyLink   = helper::createLink('story', 'view', "storyID=$story->id&version=0&param=0&storyType=$story->type");
        $canView     = common::hasPriv($story->type, 'view', null, "storyType=$story->type");

        if($tab == 'project')
        {
            $storyLink = helper::createLink('projectstory', 'view', "storyID=$story->id");
            $canView   = common::hasPriv('projectstory', 'view');
        }
        elseif($tab == 'execution')
        {
            $storyLink = helper::createLink('execution', 'storyView', "storyID=$story->id");
            $canView   = common::hasPriv('execution', 'storyView');
        }

        /* Check the product is closed. */
        $canBeChanged = common::canBeChanged('story', $story);
        $canOrder     = common::hasPriv('execution', 'storySort');

        $canBatchEdit         = common::hasPriv('story',        'batchEdit');
        $canBatchClose        = common::hasPriv($story->type,   'batchClose');
        $canBatchReview       = common::hasPriv('story',        'batchReview');
        $canBatchChangeStage  = common::hasPriv('story',        'batchChangeStage');
        $canBatchChangeBranch = common::hasPriv($story->type,   'batchChangeBranch');
        $canBatchChangeModule = common::hasPriv($story->type,   'batchChangeModule');
        $canBatchChangePlan   = common::hasPriv('story',        'batchChangePlan');
        $canBatchAssignTo     = common::hasPriv($story->type,   'batchAssignTo');
        $canBatchUnlinkStory  = common::hasPriv('projectstory', 'batchUnlinkStory');
        $canBatchUnlink       = common::hasPriv('execution',    'batchUnlinkStory');

        if($tab == 'execution')
        {
            $checkObject = new stdclass();
            $checkObject->execution = $executionID;

            $canBatchToTask = common::hasPriv('story', 'batchToTask', $checkObject);
        }

        if($tab == 'execution')
        {
            $canBatchAction = ($canBeChanged and ($canBatchEdit or $canBatchClose or $canBatchChangeStage or $canBatchUnlink or $canBatchToTask));
        }
        elseif($tab == 'project')
        {
            $canBatchAction = ($canBatchEdit or $canBatchClose or $canBatchReview or $canBatchChangeStage or $canBatchChangeBranch or $canBatchChangeModule or $canBatchChangePlan or $canBatchAssignTo or $canBatchUnlinkStory);
        }
        else
        {
            $canBatchAction = ($canBatchEdit or $canBatchClose or $canBatchReview or $canBatchChangeStage or $canBatchChangeBranch or $canBatchChangeModule or $canBatchChangePlan or $canBatchAssignTo);
        }

        $id = $col->id;
        if($col->show)
        {
            $class = "c-{$id}";
            $title = '';
            $style = '';

            if($id == 'assignedTo')
            {
                $title = zget($users, $story->assignedTo, $story->assignedTo);
                if($story->assignedTo == $account) $class .= ' red';
            }
            elseif($id == 'openedBy')
            {
                $title = zget($users, $story->openedBy, $story->openedBy);
            }
            elseif($id == 'title')
            {
                $title  = $story->title;
                $class .= ' text-ellipsis';
                if(!empty($story->children)) $class .= ' has-child';
            }
            elseif($id == 'plan')
            {
                $title  = isset($story->planTitle) ? $story->planTitle : '';
                $class .= ' text-ellipsis';
            }
            elseif($id == 'sourceNote')
            {
                $title  = $story->sourceNote;
                $class .= ' text-ellipsis';
            }
            elseif($id == 'category')
            {
                $title  = zget($this->lang->story->categoryList, $story->category);
            }
            elseif($id == 'estimate')
            {
                $title = $story->estimate . ' ' . $this->lang->hourCommon;
            }
            elseif($id == 'reviewedBy')
            {
                $reviewedBy = '';
                foreach(explode(',', $story->reviewedBy) as $user) $reviewedBy .= zget($users, $user) . ' ';
                $story->reviewedBy = trim($reviewedBy);

                $title  = $reviewedBy;
                $class .= ' text-ellipsis';
            }
            elseif($id == 'stage')
            {
                $style .= 'overflow: visible;';
                if(isset($storyStages[$story->id]))
                {
                    foreach($storyStages[$story->id] as $storyBranch => $storyStage)
                    {
                        if(isset($branches[$storyBranch])) $title .= $branches[$storyBranch] . ": " . $this->lang->story->stageList[$storyStage->stage] . "\n";
                    }
                }
            }
            elseif($id == 'feedbackBy')
            {
                $title = $story->feedbackBy;
            }
            elseif($id =='version')
            {
                $title = $story->version;
                $class = 'text-center';
            }
            elseif($id == 'notifyEmail')
            {
                $title = $story->notifyEmail;
            }
            elseif($id == 'actions')
            {
                $class .= ' text-left';
            }
            elseif($id == 'order')
            {
                $class = 'sort-handler c-sort';
            }

            echo "<td class='" . $class . "' title='$title' style='$style'>";
            if($this->config->edition != 'open') $this->loadModel('flow')->printFlowCell('story', $story, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction and ($storyType == 'story' or ($storyType == 'requirement' and $story->type == 'requirement'))) echo html::checkbox('storyIdList', array($story->id => ''));
                if($canBatchAction and $storyType == 'requirement' and $story->type == 'story') echo "<span class='c-span'></span>";
                echo $canView ? html::a($storyLink, sprintf('%03d', $story->id), '', "data-app='$tab'") : sprintf('%03d', $story->id);
                break;
            case 'order':
                echo "<i class='icon-move'>";
                break;
            case 'pri':
                echo "<span class='label-pri label-pri-" . $story->pri . "' title='" . zget($this->lang->story->priList, $story->pri, $story->pri) . "'>";
                echo zget($this->lang->story->priList, $story->pri, $story->pri);
                echo "</span>";
                break;
            case 'title':
                if($tab == 'project')
                {
                    $showBranch = isset($this->config->projectstory->story->showBranch) ? $this->config->projectstory->story->showBranch : 1;
                }
                elseif($tab == 'execution')
                {
                    $showBranch = 0;
                    if($isShowBranch) $showBranch = isset($this->config->execution->story->showBranch) ? $this->config->execution->story->showBranch : 1;
                }
                else
                {
                    $showBranch = isset($this->config->product->browse->showBranch) ? $this->config->product->browse->showBranch : 1;
                }
                if($storyType == 'requirement' and $story->type == 'story') echo '<span class="label label-badge label-light">SR</span> ';
                if($story->parent > 0 and isset($story->parentName)) $story->title = "{$story->parentName} / {$story->title}";
                if(isset($branches[$story->branch]) and $showBranch and $this->config->vision == 'rnd') echo "<span class='label label-outline label-badge' title={$branches[$story->branch]}>{$branches[$story->branch]}</span> ";
                if($story->module and isset($modulePairs[$story->module])) echo "<span class='label label-gray label-badge'>{$modulePairs[$story->module]}</span> ";
                if($story->parent > 0) echo '<span class="label label-badge label-light" title="' . $this->lang->story->children . '">' . $this->lang->story->childrenAB . '</span> ';
                echo $canView ? html::a($storyLink, $story->title, '', "style='color: $story->color' data-app='$tab'") : "<span style='color: $story->color'>{$story->title}</span>";
                if(!empty($story->children)) echo '<a class="story-toggle" data-id="' . $story->id . '"><i class="icon icon-angle-double-right"></i></a>';
                break;
            case 'plan':
                echo isset($story->planTitle) ? $story->planTitle : '';
                break;
            case 'branch':
                echo zget($branches, $story->branch, '');
                break;
            case 'keywords':
                echo $story->keywords;
                break;
            case 'bzCategory':
                echo zget($this->lang->story->bzCategoryList, $story->bzCategory, $story->bzCategory);
                break;
            case 'prCategory':
                echo zget($this->lang->story->prCategoryList, $story->prCategory, $story->prCategory);
                break;
            case 'responseResult':
                echo zget($this->lang->story->responseResultList, $story->responseResult, $story->responseResult);
                break;
            case 'uatDate':
                echo helper::isZeroDate($story->uatDate) ? '' : $story->uatDate;
                break;
            case 'purchaser':
                // echo $story->purchaser;
                $purchaserList = $this->loadModel('common')->getPurchaserList();
                echo zget($purchaserList, $story->purchaser, $story->purchaser);
                break;
            case 'source':
                echo zget($this->lang->story->sourceList, $story->source, $story->source);
                break;
            case 'sourceNote':
                echo $story->sourceNote;
                break;
            case 'category':
                echo zget($this->lang->story->categoryList, $story->category);
                break;
            case 'status':
                if($story->URChanged)
                {
                    print("<span class='status-story status-changed'>{$this->lang->story->URChanged}</span>");
                    break;
                }
                echo "<span class='status-{$story->status}'>";
                echo $this->processStatus('story', $story);
                echo '</span>';
                break;
            case 'estimate':
                echo $story->estimate . $this->config->hourUnit;
                break;
            case 'stage':
                if(isset($storyStages[$story->id]) and !empty($branches))
                {
                    echo "<div class='dropdown dropdown-hover'>";
                    echo $this->lang->story->stageList[$story->stage];
                    echo "<span class='caret'></span>";
                    echo "<ul class='dropdown-menu pull-right'>";
                    foreach($storyStages[$story->id] as $storyBranch => $storyStage)
                    {
                        if(isset($branches[$storyBranch])) echo '<li class="text-ellipsis">' . $branches[$storyBranch] . ": " . $this->lang->story->stageList[$storyStage->stage] . '</li>';
                    }
                    echo "</ul>";
                    echo '</div>';
                }
                else
                {
                    echo $this->lang->story->stageList[$story->stage];
                }
                break;
            case 'taskCount':
                $tasksLink = helper::createLink('story', 'tasks', "storyID=$story->id", '', 'class="iframe"');
                $storyTasks[$story->id] > 0 ? print(html::a($tasksLink, $storyTasks[$story->id], '', 'class="iframe"')) : print(0);
                break;
            case 'bugCount':
                $bugsLink = helper::createLink('story', 'bugs', "storyID=$story->id");
                $storyBugs[$story->id] > 0 ? print(html::a($bugsLink, $storyBugs[$story->id], '', 'class="iframe"')) : print(0);
                break;
            case 'caseCount':
                $casesLink = helper::createLink('story', 'cases', "storyID=$story->id");
                $storyCases[$story->id] > 0 ? print(html::a($casesLink, $storyCases[$story->id], '', 'class="iframe"')) : print(0);
                break;
            case 'openedBy':
                echo zget($users, $story->openedBy, $story->openedBy);
                break;
            case 'openedDate':
                echo helper::isZeroDate($story->openedDate) ? '' : substr($story->openedDate, 5, 11);
                break;
            case 'assignedTo':
                $this->printAssignedHtml($story, $users);
                break;
            case 'assignedDate':
                echo helper::isZeroDate($story->assignedDate) ? '' : substr($story->assignedDate, 5, 11);
                break;
            case 'reviewedBy':
                echo $story->reviewedBy;
                break;
            case 'reviewedDate':
                echo helper::isZeroDate($story->reviewedDate) ? '' : substr($story->reviewedDate, 5, 11);
                break;
            case 'closedBy':
                echo zget($users, $story->closedBy, $story->closedBy);
                break;
            case 'closedDate':
                echo helper::isZeroDate($story->closedDate) ? '' : substr($story->closedDate, 5, 11);
                break;
            case 'closedReason':
                echo zget($this->lang->story->reasonList, $story->closedReason, $story->closedReason);
                break;
            case 'lastEditedBy':
                echo zget($users, $story->lastEditedBy, $story->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo helper::isZeroDate($story->lastEditedDate) ? '' : substr($story->lastEditedDate, 5, 11);
                break;
            case 'feedbackBy':
                echo $story->feedbackBy;
                break;
            case 'notifyEmail':
                echo $story->notifyEmail;
                break;
            case 'mailto':
                $mailto = explode(',', $story->mailto);
                foreach($mailto as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    echo zget($users, $account) . ' &nbsp;';
                }
                break;
            case 'version':
                echo $story->version;
                break;
            case 'actions':
                if($tab == 'execution')
                {
                    $menuType = 'execution';
                }
                else
                {
                    $menuType = 'browse';
                    $execution = '';
                }
                echo $this->buildOperateMenu($story, $menuType, $execution);
                break;
            }
            echo '</td>';
        }
    }