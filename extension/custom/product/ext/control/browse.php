<?php

class myProduct extends product
{


    /**
     * Browse a product.
     *
     * @param  int         $productID
     * @param  int|stirng  $branch
     * @param  string      $browseType
     * @param  int         $param
     * @param  string      $storyType requirement|story
     * @param  string      $orderBy
     * @param  int         $recTotal
     * @param  int         $recPerPage
     * @param  int         $pageID
     * @param  int         $projectID
     * @access public
     * @return void
     */
    public function browse($productID = 0, $branch = '', $browseType = '', $param = 0, $storyType = 'story', $orderBy = '', $recTotal = 0, $recPerPage = 20, $pageID = 1, $projectID = 0)
    {
        $productID = $this->app->tab != 'project' ? $this->product->saveState($productID, $this->products) : $productID;
        $product   = $this->product->getById($productID);

        if($product and $product->type != 'normal')
        {
            $branchPairs = $this->loadModel('branch')->getPairs($productID, 'all');
            $branch      = ($this->cookie->preBranch !== '' and $branch === '' and isset($branchPairs[$this->cookie->preBranch])) ? $this->cookie->preBranch : $branch;
            $branchID    = $branch;
        }
        else
        {
            $branchID = $branch = 'all';
        }

        /* Set menu. */
        if($this->app->tab == 'project')
        {
            $this->session->set('storyList', $this->app->getURI(true), 'project');
            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->session->set('storyList',   $this->app->getURI(true), 'product');
            $this->session->set('productList', $this->app->getURI(true), 'product');

            $this->product->setMenu($productID, $branch, 0, '', "storyType=$storyType");
        }

        /* Lower browse type. */
        $browseType = strtolower($browseType);

        /* Load datatable and execution. */
        $this->loadModel('datatable');
        $this->loadModel('execution');

        /* Set product, module and query. */
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
        setcookie('preBranch', $branch, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $productID or $this->cookie->preBranch != $branch or $browseType == 'bybranch')
        {
            $_COOKIE['storyModule'] = 0;
            setcookie('storyModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }

        if($browseType == 'bymodule' or $browseType == '')
        {
            setcookie('storyModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            if($this->app->tab == 'project') setcookie('storyModuleParam', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $_COOKIE['storyBranch'] = 'all';
            setcookie('storyBranch', 'all', 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            if($browseType == '') setcookie('treeBranch', $branch, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bybranch') setcookie('storyBranch', $branch, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);

        $cookieModule = $this->app->tab == 'project' ? $this->cookie->storyModuleParam : $this->cookie->storyModule;
        $moduleID = ($browseType == 'bymodule') ? (int)$param : (($browseType == 'bysearch' or $browseType == 'bybranch') ? 0 : ($cookieModule ? $cookieModule : 0));
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Set moduleTree. */
        $createModuleLink = $storyType == 'story' ? 'createStoryLink' : 'createRequirementLink';
        if($browseType == '')
        {
            setcookie('treeBranch', $branch, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $browseType = 'unclosed';
        }
        else
        {
            $branch = $this->cookie->treeBranch;
        }

        /* If in project story and not chose product, get project story mdoules. */
        if($this->app->rawModule == 'projectstory' and empty($productID))
        {
            $moduleTree = $this->tree->getProjectStoryTreeMenu($projectID, 0, array('treeModel', $createModuleLink));
        }
        else
        {
            $moduleTree = $this->tree->getTreeMenu($productID, 'story', $startModuleID = 0, array('treeModel', $createModuleLink), array('projectID' => $projectID, 'productID' => $productID), $branch, "&param=$param&storyType=$storyType");
        }

        if($browseType != 'bymodule' and $browseType != 'bybranch') $this->session->set('storyBrowseType', $browseType);
        if(($browseType == 'bymodule' or $browseType == 'bybranch') and $this->session->storyBrowseType == 'bysearch') $this->session->set('storyBrowseType', 'unclosed');

        /* Process the order by field. */
        if(!$orderBy) $orderBy = $this->cookie->productStoryOrder ? $this->cookie->productStoryOrder : 'id_desc';
        setcookie('productStoryOrder', $orderBy, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);

        /* Append id for secend sort. */
        $sort = common::appendOrder($orderBy);
        if(strpos($sort, 'pri_') !== false) $sort = str_replace('pri_', 'priOrder_', $sort);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'xhtml') $recPerPage = 10;
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Display of branch label. */
        $showBranch = $this->loadModel('branch')->showBranch($productID);

        /* Get stories. */
        if($this->app->rawModule == 'projectstory')
        {
            $showBranch = $this->loadModel('branch')->showBranch($productID, 0, $projectID);

            if(!empty($product)) $this->session->set('currentProductType', $product->type);

            $this->products  = $this->product->getProducts($projectID, 'all', '', false);
            $projectProducts = $this->product->getProducts($projectID);
            $productPlans    = $this->execution->getPlans($projectProducts, 'skipParent');

            if($browseType == 'bybranch') $param = $branchID;
            $stories = $this->story->getExecutionStories($projectID, $productID, $branchID, $sort, $browseType, $param, 'story', '', $pager);
        }
        else
        {
            $stories = $this->product->getStories($productID, $branchID, $browseType, $queryID, $moduleID, $storyType, $sort, $pager);
        }
        $queryCondition = $this->dao->get();

        /* Display status of branch. */
        $branchOption    = array();
        $branchTagOption = array();
        if($product and $product->type != 'normal')
        {
            $branches = $this->loadModel('branch')->getList($productID, $projectID, 'all');
            foreach($branches as $branchInfo)
            {
                $branchOption[$branchInfo->id]    = $branchInfo->name;
                $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
            }
        }

        /* Process the sql, get the conditon partion, save it to session. */
        $this->loadModel('common')->saveQueryCondition($queryCondition, 'story', ($browseType != 'bysearch' and $browseType != 'reviewbyme' and $this->app->rawModule != 'projectstory'));

        if(!empty($stories)) $stories = $this->story->mergeReviewer($stories);

        /* Get related tasks, bugs, cases count of each story. */
        $storyIdList = array();
        foreach($stories as $story)
        {
            $storyIdList[$story->id] = $story->id;
            if(!empty($story->children))
            {
                foreach($story->children as $child) $storyIdList[$child->id] = $child->id;
            }
        }
        $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storyIdList);
        $storyBugs  = $this->loadModel('bug')->getStoryBugCounts($storyIdList);
        $storyCases = $this->loadModel('testcase')->getStoryCaseCounts($storyIdList);

        /* Change for requirement story title. */
        if($storyType == 'requirement')
        {
            $this->lang->story->title  = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->title);
            $this->lang->story->create = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->create);
            $this->config->product->search['fields']['title'] = $this->lang->story->title ;
            unset($this->config->product->search['fields']['plan']);
            unset($this->config->product->search['fields']['stage']);
        }else{
            unset($this->config->product->search['fields']['responseResult']);
            unset($this->config->product->search['params']['responseResult']);

            unset($this->config->story->datatable->fieldList['responseResult']);
        }

        /* Build search form. */
        $rawModule = $this->app->rawModule;
        $rawMethod = $this->app->rawMethod;

        $params    = $rawModule == 'projectstory' ? "projectID=$projectID&" : '';
        $actionURL = $this->createLink($rawModule, $rawMethod, $params . "productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID&storyType=$storyType");

        $this->config->product->search['onMenuBar'] = 'yes';
        $this->product->buildSearchForm($productID, $this->products, $queryID, $actionURL, $branch, $projectID);

        $showModule = !empty($this->config->datatable->productBrowse->showModule) ? $this->config->datatable->productBrowse->showModule : '';

        $productName = ($this->app->rawModule == 'projectstory' and empty($productID)) ? $this->lang->product->all : $this->products[$productID];

        /* Assign. */
        $this->view->title           = $productName . $this->lang->colon . $this->lang->product->browse;
        $this->view->position[]      = $productName;
        $this->view->position[]      = $this->lang->product->browse;
        $this->view->productID       = $productID;
        $this->view->product         = $product;
        $this->view->productName     = $productName;
        $this->view->moduleID        = $moduleID;
        $this->view->stories         = $stories;
        $this->view->plans           = $this->loadModel('productplan')->getPairs($productID, $branch === 'all' ? '' : $branch, '', true);
        $this->view->productPlans    = isset($productPlans) ? array(0 => '') + $productPlans : array();
        $this->view->summary         = $this->product->summary($stories, $storyType);
        $this->view->moduleTree      = $moduleTree;
        $this->view->parentModules   = $this->tree->getParents($moduleID);
        $this->view->pager           = $pager;
        $this->view->users           = $this->user->getPairs('noletter|pofirst|nodeleted');
        $this->view->orderBy         = $orderBy;
        $this->view->browseType      = $browseType;
        $this->view->modules         = $this->tree->getOptionMenu($productID, 'story', 0, $branchID);
        $this->view->moduleID        = $moduleID;
        $this->view->moduleName      = ($moduleID and $moduleID !== 'all') ? $this->tree->getById($moduleID)->name : $this->lang->tree->all;
        $this->view->branch          = $branch;
        $this->view->branchID        = $branchID;
        $this->view->branchOption    = $branchOption;
        $this->view->branchTagOption = $branchTagOption;
        $this->view->showBranch      = $showBranch;
        $this->view->storyStages     = $this->product->batchGetStoryStage($stories);
        $this->view->setModule       = true;
        $this->view->storyTasks      = $storyTasks;
        $this->view->storyBugs       = $storyBugs;
        $this->view->storyCases      = $storyCases;
        $this->view->param           = $param;
        $this->view->projectID       = $projectID;
        $this->view->products        = $this->products;
        $this->view->projectProducts = isset($projectProducts) ? $projectProducts : array();
        $this->view->storyType       = $storyType;
        $this->view->from            = $this->app->tab;
        $this->view->modulePairs     = $showModule ? $this->tree->getModulePairs($productID, 'story', $showModule) : array();
        $this->display();
    }

}