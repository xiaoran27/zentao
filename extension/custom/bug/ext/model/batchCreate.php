<?php


    /**
     * Batch create
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $extra
     * @access public
     * @return void
     */
    public function batchCreate($productID, $branch = 0, $extra = '')
    {
        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);

        /* Load module and init vars. */
        $this->loadModel('action');
        $this->loadModel('kanban');
        $branch    = (int)$branch;
        $productID = (int)$productID;
        $now       = helper::now();
        $actions   = array();
        $data      = fixer::input('post')->get();

        $result = $this->loadModel('common')->removeDuplicate('bug', $data, "product={$productID}");
        $data   = $result['data'];

        /* Get pairs(moduleID => moduleOwner) for bug. */
        $stmt         = $this->dbh->query($this->loadModel('tree')->buildMenuQuery($productID, 'bug', $startModuleID = 0, $branch));
        $moduleOwners = array();
        while($module = $stmt->fetch()) $moduleOwners[$module->id] = $module->owner;

        $module    = 0;
        $project   = 0;
        $execution = 0;
        $type      = '';
        $pri       = 0;
        $feedbackBy      = '';
        $purchaser      = '';
        $occursEnv      = '';
        $feedbackTime      = '';
        $collectTime      = '';
        foreach($data->title as $i => $title)
        {
            $oses     = array_filter($data->oses[$i]);
            $browsers = array_filter($data->browsers[$i]);
            $occursEnvs     = array_filter($data->occursEnvs[$i]);

            if($data->modules[$i]    != 'ditto') $module    = (int)$data->modules[$i];
            if($data->projects[$i]   != 'ditto') $project   = (int)$data->projects[$i];
            if($data->executions[$i] != 'ditto') $execution = (int)$data->executions[$i];
            if($data->types[$i]      != 'ditto') $type      = $data->types[$i];
            if($data->pris[$i]       != 'ditto') $pri       = $data->pris[$i];
            if($data->occursEnvs[$i]       != 'ditto') $occursEnvs       = $data->occursEnvs[$i];

            $data->modules[$i]    = (int)$module;
            $data->projects[$i]   = (int)$project;
            $data->executions[$i] = (int)$execution;
            $data->types[$i]      = $type;
            $data->pris[$i]       = $pri;
            $data->oses[$i]       = implode(',', $oses);
            $data->browsers[$i]   = implode(',', $browsers);
            $data->occursEnvs[$i]   = implode(',', $occursEnvs);
        }

        /* Get bug data. */
        if(isset($data->uploadImage)) $this->loadModel('file');
        $extendFields = $this->getFlowExtendFields();
        $bugs = array();
        foreach($data->title as $i => $title)
        {
            $title = trim($title);
            if(empty($title)) continue;

            $bug = new stdClass();
            $bug->openedBy    = $this->app->user->account;
            $bug->openedDate  = $now;
            $bug->product     = (int)$productID;
            $bug->branch      = isset($data->branches) ? (int)$data->branches[$i] : 0;
            $bug->module      = (int)$data->modules[$i];
            $bug->project     = (int)$data->projects[$i];
            $bug->execution   = (int)$data->executions[$i];
            $bug->openedBuild = implode(',', $data->openedBuilds[$i]);
            $bug->color       = $data->color[$i];
            $bug->title       = $title;
            $bug->feedbackBy       = $data->feedbackBy[$i];
            $bug->purchaser       = $data->purchaser[$i];
            $bug->occursEnv       = $data->occursEnvs[$i];
            $bug->feedbackTime       = $data->feedbackTime[$i];
            $bug->collectTime       = $data->collectTime[$i];
            $bug->deadline    = $data->deadlines[$i];
            $bug->steps       = nl2br($data->stepses[$i]);
            $bug->type        = $data->types[$i];
            $bug->pri         = $data->pris[$i];
            $bug->severity    = $data->severities[$i];
            $bug->os          = $data->oses[$i];
            $bug->browser     = $data->browsers[$i];
            $bug->occursEnv     = $data->occursEnvs[$i];
            $bug->keywords    = $data->keywords[$i];

            if(isset($data->lanes[$i])) $bug->laneID = $data->lanes[$i];

            if($bug->execution != 0) $bug->project = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($bug->execution)->fetch('project');

            /* Assign the bug to the person in charge of the module. */
            if(!empty($moduleOwners[$bug->module]))
            {
                $bug->assignedTo   = $moduleOwners[$bug->module];
                $bug->assignedDate = $now;
            }

            foreach($extendFields as $extendField)
            {
                $bug->{$extendField->field} = $this->post->{$extendField->field}[$i];
                if(is_array($bug->{$extendField->field})) $bug->{$extendField->field} = join(',', $bug->{$extendField->field});

                $bug->{$extendField->field} = htmlSpecialString($bug->{$extendField->field});
            }

            /* Required field check. */
            foreach(explode(',', $this->config->bug->create->requiredFields) as $field)
            {
                $field = trim($field);
                if($field and empty($bug->$field))
                {
                    dao::$errors['message'][] = sprintf($this->lang->error->notempty, $this->lang->bug->$field);
                    return false;
                }
            }

            $this->loadModel('common')->log(print_r($bug, true), __FILE__, __LINE__);
            $bugs[$i] = $bug;

        }

        /* When the bug is created by uploading an image, add the image to the step of the bug. */
        foreach($bugs as $i => $bug)
        {
            $laneID = isset($output['laneID']) ? $output['laneID'] : 0;
            if(isset($bug->laneID))
            {
                $laneID = $bug->laneID;
                unset($bug->laneID);
            }

            if(!empty($data->uploadImage[$i]))
            {
                $fileName = $data->uploadImage[$i];
                $file     = $this->session->bugImagesFile[$fileName];

                $realPath = $file['realpath'];
                unset($file['realpath']);
                if(rename($realPath, $this->file->savePath . $this->file->getSaveName($file['pathname'])))
                {
                    if(in_array($file['extension'], $this->config->file->imageExtensions))
                    {
                        $file['addedBy']    = $this->app->user->account;
                        $file['addedDate']  = $now;
                        $this->dao->insert(TABLE_FILE)->data($file)->exec();

                        $fileID = $this->dao->lastInsertID();
                        $bug->steps .= '<img src="{' . $fileID . '.' . $file['extension'] . '}" alt="" />';
                    }
                }
                else
                {
                    unset($file);
                }
            }

            if($this->config->systemMode == 'new' && $this->lang->navGroup->bug != 'qa') $bug->project = $this->session->project;
            $this->dao->insert(TABLE_BUG)->data($bug)
                ->autoCheck()
                ->batchCheck($this->config->bug->create->requiredFields, 'notempty')
                ->checkFlow()
                ->exec();
            if(dao::isError()) return false;

            $bugID = $this->dao->lastInsertID();

            $this->executeHooks($bugID);

            if($bug->execution)
            {
                $columnID = $this->kanban->getColumnIDByLaneID($laneID, 'unconfirmed');
                if(empty($columnID)) $columnID = isset($output['columnID']) ? $output['columnID'] : 0;

                if(!empty($laneID) and !empty($columnID)) $this->kanban->addKanbanCell($bug->execution, $laneID, $columnID, 'bug', $bugID);
                if(empty($laneID) or empty($columnID)) $this->kanban->updateLane($bug->execution, 'bug');

            }
            /* When the bug is created by uploading the image, add the image to the file of the bug. */
            $this->loadModel('score')->create('bug', 'create', $bugID);
            if(!empty($data->uploadImage[$i]) and !empty($file))
            {
                $file['objectType'] = 'bug';
                $file['objectID']   = $bugID;
                $file['addedBy']    = $this->app->user->account;
                $file['addedDate']  = $now;
                $this->dao->insert(TABLE_FILE)->data($file)->exec();
                unset($file);
            }

            if(dao::isError())
            {
                dao::$errors['message'][] = 'bug#' . ($i) . dao::getError(true);
                return false;
            }
            $actions[$bugID] = $this->action->create('bug', $bugID, 'Opened');
        }

        /* Remove upload image file and session. */
        if(!empty($data->uploadImage) and $this->session->bugImagesFile)
        {
            $classFile = $this->app->loadClass('zfile');
            $file = current($_SESSION['bugImagesFile']);
            $realPath = dirname($file['realpath']);
            if(is_dir($realPath)) $classFile->removeDir($realPath);
            unset($_SESSION['bugImagesFile']);
        }
        if(!dao::isError()) $this->loadModel('score')->create('ajax', 'batchCreate');
        return $actions;
    }
