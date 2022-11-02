<?php

class myStory extends story
{
    

    /**
     * Create a batch stories.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $moduleID
     * @param  int    $storyID
     * @param  int    $executionID
     * @param  int    $plan
     * @param  string $storyType requirement|story
     * @param  string $extra for example feedbackID=0
     * @access public
     * @return void
     */
    public function batchCreate($productID = 0, $branch = 0, $moduleID = 0, $storyID = 0, $executionID = 0, $plan = 0, $storyType = 'story', $extra = '')
    {
        /* Set menu. */
        if($executionID)
        {
            $execution = $this->dao->findById((int)$executionID)->from(TABLE_EXECUTION)->fetch();
            if($execution->type == 'project')
            {
                $this->project->setMenu($executionID);
                $this->app->rawModule = 'projectstory';
                $this->lang->navGroup->story = 'project';
                $this->lang->product->menu = $this->lang->{$execution->model}->menu;
            }
            else
            {
                if($execution->type == 'kanban')
                {
                    $this->loadModel('kanban');
                    $extra = str_replace(array(',', ' '), array('&', ''), $extra);
                    parse_str($extra, $output);

                    $regionPairs = $this->kanban->getRegionPairs($executionID, 0, 'execution');
                    $regionID    = !empty($output['regionID']) ? $output['regionID'] : key($regionPairs);
                    $lanePairs   = $this->kanban->getLanePairsByRegion($regionID, 'story');
                    $laneID      = !empty($output['laneID']) ? $output['laneID'] : key($lanePairs);

                    $this->view->regionID    = $regionID;
                    $this->view->laneID      = $laneID;
                    $this->view->regionPairs = $regionPairs;
                    $this->view->lanePairs   = $lanePairs;
                }

                $this->execution->setMenu($executionID);
                $this->app->rawModule = 'execution';
                $this->lang->navGroup->story = 'execution';
            }
            $this->view->execution = $execution;
        }
        else
        {
            $this->product->setMenu($productID, $branch);
        }

        /* Clear title when switching products and set the session for the current product. */
        if($productID != $this->cookie->preProductID) unset($_SESSION['storyImagesFile']);
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        $this->story->replaceURLang($storyType);

        /* Check can subdivide or not. */
        if($storyID)
        {
            $story = $this->story->getById($storyID);
            if(($story->status != 'active' or $story->stage != 'wait' or $story->parent > 0) and $this->config->vision != 'lite') return print(js::alert($this->lang->story->errorNotSubdivide) . js::locate('back'));
        }

        if(!empty($_POST))
        {
            $mails = $this->story->batchCreate($productID, $branch, $storyType);
            if(dao::isError()) return print(js::error(dao::getError()));

            $stories = array();
            foreach($mails as $mail) $stories[] = $mail->storyID;

            $lanes = array();
            if(isset($_POST['lanes']))
            {
                foreach($mails as $i => $mail) $lanes[$mail->storyID] = $_POST['lanes'][$i];
            }

            /* Project or execution linked stories. */
            if($executionID)
            {
                $products = array();
                foreach($mails as $story) $products[$story->storyID] = $productID;
                if($executionID != $this->session->project) $this->execution->linkStory($this->session->project, $stories, $products);
                $this->execution->linkStory($executionID, $stories, $products, $extra, $lanes);
            }

            /* If storyID not equal zero, subdivide this story to child stories and close it. */
            if($storyID and !empty($mails))
            {
                $this->story->subdivide($storyID, $stories);
                if(dao::isError()) return print(js::error(dao::getError()));
            }

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'idList' => $stories));

            if(isonlybody())
            {
                $executionID = $executionID ? $executionID : $this->session->execution;
                $execution   = $this->execution->getByID($executionID);
                if($this->app->tab == 'execution')
                {
                    $execLaneType = $this->session->execLaneType ? $this->session->execLaneType : 'all';
                    $execGroupBy  = $this->session->execGroupBy ? $this->session->execGroupBy : 'default';

                    if($execution->type == 'kanban')
                    {
                        $rdSearchValue = $this->session->rdSearchValue ? $this->session->rdSearchValue : '';
                        $kanbanData    = $this->loadModel('kanban')->getRDKanban($executionID, $execLaneType, 'id_desc', 0, $execGroupBy, $rdSearchValue);
                        $kanbanData    = json_encode($kanbanData);
                        return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban($kanbanData)"));
                    }
                    else
                    {
                        $taskSearchValue = $this->session->taskSearchValue ? $this->session->taskSearchValue : '';
                        $kanbanData      = $this->loadModel('kanban')->getExecutionKanban($execution->id, $execLaneType, $execGroupBy, $taskSearchValue);
                        $kanbanType      = $execLaneType == 'all' ? 'story' : key($kanbanData);
                        $kanbanData      = $kanbanData[$kanbanType];
                        $kanbanData      = json_encode($kanbanData);
                        return print(js::closeModal('parent.parent', '', "parent.parent.updateKanban(\"story\", $kanbanData)"));
                    }
                }
                else
                {
                    return print(js::reload('parent.parent'));
                }
            }

            if($storyID)
            {
                return print(js::locate(inlink('view', "storyID=$storyID&version=0&param=0&storyType=$storyType"), 'parent'));
            }
            elseif($executionID)
            {
                setcookie('storyModuleParam', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
                $moduleName = $execution->type == 'project' ? 'projectstory' : 'execution';
                $param      = $execution->type == 'project' ? "projectID=$executionID&productID=$productID" : "executionID=$executionID&orderBy=id_desc&browseType=unclosed";
                $link       = $this->createLink($moduleName, 'story', $param);
                return print(js::locate($link, 'parent'));
            }
            else
            {
                setcookie('storyModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
                $locateLink = $this->session->storyList ? $this->session->storyList : $this->createLink('product', 'browse', "productID=$productID&branch=$branch&browseType=unclosed&queryID=0&storyType=$storyType");
                return print(js::locate($locateLink, 'parent'));
            }
        }

        /* Set branch and module. */
        $product  = $this->product->getById($productID);
        $products = $this->product->getPairs();
        if($product) $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);

        if($executionID != 0)
        {
            $productBranches = $product->type != 'normal' ? $this->loadModel('execution')->getBranchByProduct($productID, $executionID, 'noclosed|withMain') : array();
            $branches        = isset($productBranches[$productID]) ? $productBranches[$productID] : array();
            $branch          = key($branches);
        }
        else
        {
            $branches = $product->type != 'normal' ? $this->loadModel('branch')->getPairs($productID, 'active') : array();
        }

        $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'story', 0, $branch === 'all' ? 0 : $branch);
        $moduleOptionMenu['ditto'] = $this->lang->story->ditto;

        /* Get reviewers. */
        $reviewers = $product->reviewer;
        if(!$reviewers and $product->acl != 'open') $reviewers = $this->loadModel('user')->getProductViewListUsers($product, '', '', '', '');

        /* Init vars. */
        $planID   = $plan;
        $pri      = 0;
        $estimate = '';
        $title    = '';
        $spec     = '';
        $uatDate    = '';
	    $purchaser   = '';

        /* Process upload images. */
        if($this->session->storyImagesFile)
        {
            $files = $this->session->storyImagesFile;
            foreach($files as $fileName => $file)
            {
                $title = $file['title'];
                $titles[$title] = $fileName;
            }
            $this->view->titles = $titles;
        }
        $plans          = $this->loadModel('productplan')->getPairsForStory($productID, ($branch === 'all' or !in_array($branch, array_keys($branches))) ? 0 : $branch, 'skipParent|unexpired|noclosed');
        $plans['ditto'] = $this->lang->story->ditto;

        $priList          = (array)$this->lang->story->priList;
        $priList['ditto'] = $this->lang->story->ditto;

        $bzCategoryList          = (array)$this->lang->story->bzCategoryList;
        $bzCategoryList['ditto'] = $this->lang->story->ditto;

        $prCategoryList          = $storyType == 'requirement'?$this->lang->story->prCategoryList0:$this->lang->story->prCategoryList;
        $prCategoryList['ditto'] = $this->lang->story->ditto;

        $responseResultList          = (array)$this->lang->story->responseResultList;
        $responseResultList['ditto'] = $this->lang->story->ditto;

        

        $sourceList          = (array)$this->lang->story->sourceList;
        $sourceList['ditto'] = $this->lang->story->ditto;

        /* Set Custom*/
        foreach(explode(',', $this->config->story->list->customBatchCreateFields) as $field)
        {
            if($product->type != 'normal') $customFields[$product->type] = $this->lang->product->branchName[$product->type];
            $customFields[$field] = $this->lang->story->$field;
        }

        if($product->type != 'normal')
        {
            $this->config->story->custom->batchCreateFields = sprintf($this->config->story->custom->batchCreateFields, $product->type);
        }
        else
        {
            $this->config->story->custom->batchCreateFields = trim(sprintf($this->config->story->custom->batchCreateFields, ''), ',');
        }

        $showFields = $this->config->story->custom->batchCreateFields;
        //error_log($showFields);
        
        if($product->type == 'normal')
        {
            $showFields = str_replace(array(0 => ",branch,", 1 => ",platform,"), '', ",$showFields,");
            $showFields = trim($showFields, ',');
        }
        if($storyType == 'requirement')
        {
            unset($customFields['plan']);
            $showFields = str_replace('plan', '', $showFields);
        }

        $this->view->customFields = $customFields;
        $this->view->showFields   = $showFields;

        $this->view->title            = $product->name . $this->lang->colon . ($storyID ? $this->lang->story->subdivide : $this->lang->story->batchCreate);
        $this->view->productName      = $product->name;
        $this->view->position[]       = html::a($this->createLink('product', 'browse', "product=$productID&branch=$branch"), $product->name);
        $this->view->position[]       = $this->lang->story->common;
        $this->view->position[]       = $storyID ? $this->lang->story->subdivide : $this->lang->story->batchCreate;
        $this->view->storyID          = $storyID;
        $this->view->products         = $products;
        $this->view->product          = $product;
        $this->view->moduleID         = $moduleID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->plans            = $plans;
        $this->view->reviewers        = $this->user->getPairs('noclosed|nodeleted', '', 0, $reviewers);
        $this->view->users            = $this->user->getPairs('pdfirst|noclosed|nodeleted');
        $this->view->priList          = $priList;
        $this->view->bzCategoryList       = $bzCategoryList;
        $this->view->prCategoryList       = $prCategoryList;
        $this->view->responseResultList       = $responseResultList;
        
        $this->view->uatDate       = $uatDate;
	    $this->view->purchaser           = $purchaser;
        $this->view->purchaserList       = array('' => '',  'ditto' => $this->lang->story->ditto) + $this->loadModel('common')->getPurchaserList();
        $this->view->sourceList       = $sourceList;
        $this->view->planID           = $planID;
        $this->view->pri              = $pri;
        $this->view->productID        = $productID;
        $this->view->estimate         = $estimate;
        $this->view->storyTitle       = isset($story->title) ? $story->title : '';
        $this->view->spec             = $spec;
        $this->view->type             = $storyType;
        $this->view->branch           = $branch;
        $this->view->branches         = $branches;
        /* When the user is product owner or add story in project or not set review, the default is not to review. */
        $this->view->needReview       = ($this->app->user->account == $product->PO || $executionID > 0 || $this->config->story->needReview == 0) ? 0 : 1;
        $this->view->forceReview      = $this->story->checkForceReview();
        $this->view->executionID      = $executionID;

        $this->display();
    }

}
