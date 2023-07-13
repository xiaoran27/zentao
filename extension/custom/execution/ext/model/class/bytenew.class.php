<?php

class bytenewExecution extends executionModel
{

    /**
     * Link story.
     *
     * @param int    $executionID
     * @param array  $stories
     * @param array  $products
     * @param string $extra
     * @param array  $lanes
     *
     * @access public
     * @return bool
     */
    public function linkStory($executionID, $stories = array(), $products = array(), $extra = '', $lanes = array())
    {
        if(empty($executionID)) return false;
        if(empty($stories)) $stories = $this->post->stories;
        if(empty($stories)) return false;
        if(empty($products)) $products = $this->post->products;

        $this->loadModel('action');
        $this->loadModel('kanban');
        $versions      = $this->loadModel('story')->getVersions($stories);
        $linkedStories = $this->dao->select('*')->from(TABLE_PROJECTSTORY)->where('project')->eq($executionID)->orderBy('order_desc')->fetchPairs('story', 'order');
        $lastOrder     = reset($linkedStories);
        $storyList     = $this->dao->select('id, status, branch')->from(TABLE_STORY)->where('id')->in(array_values($stories))->fetchAll('id');
        $execution     = $this->getById($executionID);
        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);
        foreach($stories as $key => $storyID)
        {
            $notAllowedStatus = $this->app->rawMethod == 'batchcreate' ? 'closed' : 'draft,reviewing,closed';
            if(strpos($notAllowedStatus, $storyList[$storyID]->status) !== false) continue;
            if(isset($linkedStories[$storyID])) continue;

            $laneID = isset($output['laneID']) ? $output['laneID'] : 0;
            if(!empty($lanes[$storyID])) $laneID = $lanes[$storyID];

            $columnID = $this->kanban->getColumnIDByLaneID($laneID, 'backlog');
            if(empty($columnID)) $columnID = isset($output['columnID']) ? $output['columnID'] : 0;

            if(!empty($laneID) and !empty($columnID)) $this->kanban->addKanbanCell($executionID, $laneID, $columnID, 'story', $storyID);

            $data = new stdclass();
            $data->project = $executionID;
            $data->product = (int)$products[$storyID];
            $data->branch  = $storyList[$storyID]->branch;
            $data->story   = $storyID;
            $data->version = $versions[$storyID];
            $data->order   = ++$lastOrder;
            $this->dao->replace(TABLE_PROJECTSTORY)->data($data)->exec();
            $storyBizProjectId = $executionID;
            if ($execution->type!='project'){
                $storyBizProjectId = $execution->project;
            }
            $oldBizProject = $storyList[$storyID]->bizProject;
            $newBizProject = '';
            if (empty($storyList[$storyID]->bizProject)){
                $newBizProject = $storyBizProjectId;
                $this->dao->update(TABLE_STORY)->set('lastEditedBy')->eq($this->app->user->account)->set('lastEditedDate')->eq(date('Y-m-d H:i:s'))->set('bizProject')->eq($storyBizProjectId)->where('id')->eq($storyID)->exec();
            }

            $this->story->setStage($storyID);
            $this->linkCases($executionID, (int)$products[$storyID], $storyID);

            $action = $execution->type == 'project' ? 'linked2project' : 'linked2execution';
            if($action == 'linked2execution' and $execution->type == 'kanban') $action = 'linked2kanban';

            $msg = '';
            if (!empty($newBizProject)){
                $msg = '修改了项目名称，旧值"'.$oldBizProject.'"，新值"'.$newBizProject.'"';
            }
            if($execution->multiple or $execution->type == 'project') $this->action->create('story', $storyID, $action, $msg, $executionID);
        }

        if(!isset($output['laneID']) or !isset($output['columnID'])) $this->kanban->updateLane($executionID, 'story');
    }

}