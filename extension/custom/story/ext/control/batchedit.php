<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


    /**
     * Batch edit story.
     *
     * @param  int    $productID
     * @param  int    $executionID
     * @param  int    $branch
     * @param  string $storyType
     * @param  string $from
     * @access public
     * @return void
     */
    public function batchEdit($productID = 0, $executionID = 0, $branch = 0, $storyType = 'story', $from = '')
    {
        $this->story->replaceURLang($storyType);

        $this->view->hiddenPlan = false;
        if($this->app->tab == 'product')
        {
            $this->product->setMenu($productID);
        }
        else if($this->app->tab == 'project')
        {
            $project = $this->dao->findByID($executionID)->from(TABLE_PROJECT)->fetch();
            if($project->type == 'project')
            {
                if(!($project->model == 'scrum' and !$project->hasProduct and $project->multiple)) $this->view->hiddenPlan = true;
                $this->project->setMenu($executionID);
            }
            else
            {
                if(!$project->hasProduct and !$project->multiple) $this->view->hiddenPlan = true;
                $this->execution->setMenu($executionID);
            }
        }
        else if($this->app->tab == 'execution')
        {
            $this->execution->setMenu($executionID);
        }
        else if($this->app->tab == 'qa')
        {
            $this->loadModel('qa')->setMenu('', $productID);
        }
        else if($this->app->tab == 'my')
        {
            $this->loadModel('my');
            if($from == 'work')       $this->lang->my->menu->work['subModule']       = 'story';
            if($from == 'contribute') $this->lang->my->menu->contribute['subModule'] = 'story';
        }

        /* Load model. */
        $this->loadModel('productplan');

        if($this->post->titles)
        {
            $allChanges = $this->story->batchUpdate();

            if($allChanges)
            {
                foreach($allChanges as $storyID => $changes)
                {
                    if(empty($changes)) continue;

                    $actionID = $this->action->create('story', $storyID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }
            return print(js::locate($this->session->storyList, 'parent'));
        }

        if(!$this->post->storyIdList) return print(js::locate($this->session->storyList, 'parent'));
        $storyIdList = $this->post->storyIdList;
        $storyIdList = array_unique($storyIdList);

        /* Get edited stories. */
        $stories = $this->story->getByList($storyIdList);

        /* Filter twins. */
        $twins = '';
        foreach($stories as $id => $story)
        {
            if(empty($story->twins)) continue;
            $twins .= "#$id ";
            unset($stories[$id]);
        }
        if(!empty($twins)) echo js::alert(sprintf($this->lang->story->batchEditTip, $twins));
        if(empty($stories)) return print(js::locate($this->session->storyList));

        $this->loadModel('branch');
        if($productID and !$executionID)
        {
            $product       = $this->product->getByID($productID);
            $branchProduct = $product->type == 'normal' ? false : true;

            $branches        = 0;
            $branchTagOption = array();
            if($branchProduct)
            {
                $branches = $this->branch->getList($productID, $executionID, 'all');
                foreach($branches as $branchInfo) $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
                $branches = array_keys($branches);
            }

            $modulePairs = $this->tree->getOptionMenu($productID, 'story', 0, $branches);
            $moduleList  = $branchProduct ? $modulePairs : array(0 => $modulePairs);

            $modules         = array($productID => $moduleList);
            $plans           = array($productID => $this->productplan->getBranchPlanPairs($productID, '', 'unexpired', true));
            $products        = array($productID => $product);
            $branchTagOption = array($productID => $branchTagOption);
        }
        else
        {
            $branchProduct   = false;
            $modules         = array();
            $branchTagOption = array();
            $products        = array();
            $plans           = array();

            /* Get product id list by the stories. */
            $productIdList = array();
            foreach($stories as $story) $productIdList[$story->product] = $story->product;
            $products = $this->product->getByIdList($productIdList);

            foreach($products as $storyProduct)
            {
                $branches = 0;
                if($storyProduct->type != 'normal')
                {
                    $branches = $this->branch->getList($storyProduct->id, $executionID, 'all');
                    foreach($branches as $branchInfo) $branches[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
                    $branchTagOption[$storyProduct->id] = array(BRANCH_MAIN => $this->lang->branch->main) + $branches;

                    $branches = array_keys($branches);
                }

                $modulePairs = $this->tree->getOptionMenu($storyProduct->id, 'story', 0, $branches);
                $modules[$storyProduct->id] = $storyProduct->type != 'normal' ? $modulePairs : array(0 => $modulePairs);

                $plans[$storyProduct->id] = $this->productplan->getBranchPlanPairs($storyProduct->id, $branches, 'unexpired', true);
                if(empty($plans[$storyProduct->id])) $plans[$storyProduct->id][0] = $plans[$storyProduct->id];

                if($storyProduct->type != 'normal') $branchProduct = true;
            }
        }

        /* Set ditto option for users. */
        $users = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        $users = array('' => '', 'ditto' => $this->lang->story->ditto) + $users;

        /* Set Custom*/
        foreach(explode(',', $this->config->story->list->customBatchEditFields) as $field) $customFields[$field] = $this->lang->story->$field;
        $showFields = $this->config->story->custom->batchEditFields;
        if($storyType == 'requirement')
        {
            unset($customFields['plan']);
            unset($customFields['stage']);
            $showFields = str_replace('plan',  '', $showFields);
            $showFields = str_replace('stage', '', $showFields);
        }
        $this->view->customFields = $customFields;
        $this->view->showFields   = $showFields;

        /* Judge whether the editedStories is too large and set session. */
        $countInputVars  = count($stories) * (count(explode(',', $this->config->story->custom->batchEditFields)) + 3);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        /* Append module when change product type. */
        $moduleList       = array(0 => '/');
        $productStoryList = array();
        foreach($stories as $story)
        {
            if(isset($modules[$story->product][$story->branch]))
            {
                $moduleList[$story->id] = $modules[$story->product][$story->branch];
            }
            else
            {
                $moduleList[$story->id] = $modules[$story->product][0] + $this->tree->getModulesName($story->module);
            }

            if($story->status == 'closed')
            {
                $storyProduct = $products[$story->product];
                $branch       = $storyProduct->type == 'branch' ? ($story->branch > 0 ? $story->branch : '0') : 'all';
                if(!isset($productStoryList[$story->product][$story->branch])) $productStoryList[$story->product][$story->branch] = $this->story->getProductStoryPairs($story->product, $branch, 0, 'all', 'id_desc', 0, '', $story->type);
            }

            if(!empty($story->plan) and !isset($plans[$story->product][$story->branch][$story->plan]))
            {
                $plan = $this->dao->select('id,title,begin,end')->from(TABLE_PRODUCTPLAN)->where('id')->eq($story->plan)->fetch();
                $plans[$story->product][$story->branch][$story->plan] = $plan->title . ' [' . $plan->begin . '~' . $plan->end . ']';
            }
        }

        $this->view->title             = $this->lang->story->batchEdit;
        $this->view->bzCategoryList    = array('' => '') + $this->lang->story->bzCategoryList;
        $this->view->prCategoryList    = array('' => '') + ($storyType == 'requirement'?$this->lang->story->prCategoryList0:$this->lang->story->prCategoryList);
        $this->view->responseResultList  = array('' => '') + ($storyType == 'requirement'?$this->lang->story->responseResultList:$this->lang->story->responseResultList0);
        $this->view->purchaserList       = array('' => '') + $this->loadModel('common')->getPurchaserList();
        $this->view->users             = $users;
        $this->view->priList           = array('0' => '', 'ditto' => $this->lang->story->ditto) + $this->lang->story->priList;
        $this->view->sourceList        = array('' => '',  'ditto' => $this->lang->story->ditto) + $this->lang->story->sourceList;
        $this->view->reasonList        = array('' => '',  'ditto' => $this->lang->story->ditto) + $this->lang->story->reasonList;
        $this->view->stageList         = array('' => '',  'ditto' => $this->lang->story->ditto) + $this->lang->story->stageList;
        $this->view->productID         = $productID;
        $this->view->products          = $products;
        $this->view->branchProduct     = $branchProduct;
        $this->view->storyIdList       = $storyIdList;
        $this->view->branch            = $branch;
        $this->view->plans             = $plans;
        $this->view->storyType         = $storyType;
        $this->view->stories           = $stories;
        $this->view->executionID       = $executionID;
        $this->view->branchTagOption   = $branchTagOption;
        $this->view->moduleList        = $moduleList;
        $this->view->productStoryList  = $productStoryList;
        $this->display();
    }

}