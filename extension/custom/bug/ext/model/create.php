<?php


    /**
     * Create a bug.
     *
     * @param  string $from   object that is transfered to bug.
     * @param  string $extras.
     * @access public
     * @return array|bool
     */
    public function create($from = '', $extras = '')
    {
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $output);

        $now = helper::now();
        $bug = fixer::input('post')
            ->setDefault('openedBy', $this->app->user->account)
            ->setDefault('openedDate', $now)
            ->setDefault('project,execution,story,task', 0)
            ->setDefault('openedBuild', '')
            ->setDefault('notifyEmail', '')
            ->setDefault('deadline', '0000-00-00')
            ->setIF($this->config->systemMode == 'new' && $this->lang->navGroup->bug != 'qa', 'project', $this->session->project)
            ->setIF(strpos($this->config->bug->create->requiredFields, 'deadline') !== false, 'deadline', $this->post->deadline)
            ->setIF($this->post->assignedTo != '', 'assignedDate', $now)
            ->setIF($this->post->story != false, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))
            ->setIF(strpos($this->config->bug->create->requiredFields, 'execution') !== false, 'execution', $this->post->execution)
            ->stripTags($this->config->bug->editor->create['id'], $this->config->allowedTags)
            ->cleanInt('product,execution,module,severity')
            ->trim('title')
            ->join('openedBuild', ',')
            ->join('mailto', ',')
            ->join('os', ',')
            ->join('browser', ',')
            ->join('occursEnv', ',')
            ->remove('files,labels,uid,oldTaskID,contactListMenu,region,lane')
            ->get();

        if($bug->execution != 0) $bug->project = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($bug->execution)->fetch('project');

        /* Check repeat bug. */
        $result = $this->loadModel('common')->removeDuplicate('bug', $bug, "product={$bug->product}");
        if($result and $result['stop']) return array('status' => 'exists', 'id' => $result['duplicate']);

        $bug = $this->loadModel('file')->processImgURL($bug, $this->config->bug->editor->create['id'], $this->post->uid);

        /* Use classic mode to replace required project. */
        if($this->config->systemMode == 'classic' and strpos($this->config->bug->create->requiredFields, 'project') !== false) $this->config->bug->create->requiredFields = str_replace('project', 'execution', $this->config->bug->create->requiredFields);

        // $this->loadModel('common')->log(json_encode($bug,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

        $this->dao->insert(TABLE_BUG)->data($bug)
            ->autoCheck()
            ->checkIF($bug->notifyEmail, 'notifyEmail', 'email')
            ->batchCheck($this->config->bug->create->requiredFields, 'notempty')
            ->checkFlow()
            ->exec();

        if(!dao::isError())
        {
            $bugID = $this->dao->lastInsertID();

            $this->file->updateObjectID($this->post->uid, $bugID, 'bug');
            $this->file->saveUpload('bug', $bugID);
            empty($bug->case) ? $this->loadModel('score')->create('bug', 'create', $bugID) : $this->loadModel('score')->create('bug', 'createFormCase', $bug->case);

            if($bug->execution)
            {
                $this->loadModel('kanban');

                $laneID = isset($output['laneID']) ? $output['laneID'] : 0;
                if(!empty($_POST['lane'])) $laneID = $_POST['lane'];

                $columnID = $this->kanban->getColumnIDByLaneID($laneID, 'unconfirmed');
                if(empty($columnID)) $columnID = isset($output['columnID']) ? $output['columnID'] : 0;

                if(!empty($laneID) and !empty($columnID)) $this->kanban->addKanbanCell($bug->execution, $laneID, $columnID, 'bug', $bugID);
                if(empty($laneID) or empty($columnID)) $this->kanban->updateLane($bug->execution, 'bug');
            }

            /* Callback the callable method to process the related data for object that is transfered to bug. */
            if($from && is_callable(array($this, $this->config->bug->fromObjects[$from]['callback']))) call_user_func(array($this, $this->config->bug->fromObjects[$from]['callback']), $bugID);

            return array('status' => 'created', 'id' => $bugID);
        }
        return false;
    }