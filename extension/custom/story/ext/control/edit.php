<?php
include '../../../../../module/story/control.php';

/**
 * The control file of story module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     story
 * @version     $Id: control.php 5145 2013-07-15 06:47:26Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class myStory extends story
{


    /**
     * Edit a story.
     *
     * @param  int    $storyID
     * @param  string $kanbanGroup
     * @param  string $storyType story|requirement
     * @access public
     * @return void
     */
    public function edit($storyID, $kanbanGroup = 'default', $storyType = 'story')
    {
        $this->loadModel('file');
        $this->app->loadLang('bug');
        $story = $this->story->getById($storyID, 0, true);
        $this->commonAction($storyID);

        if(!empty($_POST))
        {
            $this->story->update($storyID);
            if(dao::isError()) return print(js::error(dao::getError()));

            $this->executeHooks($storyID);

            if(isonlybody())
            {
                $execution = $this->execution->getByID($this->session->execution);
                if($this->app->tab == 'execution')
                {
                    $execLaneType = $this->session->execLaneType ? $this->session->execLaneType : 'all';
                    $execGroupBy  = $this->session->execGroupBy ? $this->session->execGroupBy : 'default';

                    if($execution->type == 'kanban')
                    {
                        $rdSearchValue = $this->session->rdSearchValue ? $this->session->rdSearchValue : '';
                        $kanbanData    = $this->loadModel('kanban')->getRDKanban($this->session->execution, $execLaneType, 'id_desc', 0, $kanbanGroup, $rdSearchValue);
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
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success', 'data' => $storyID));
            $params = $this->app->rawModule == 'story' ? "storyID=$storyID&version=0&param=0&storyType=$storyType" : "storyID=$storyID";
            return print(js::locate($this->createLink($this->app->rawModule, 'view', $params), 'parent'));
        }

        /* Sort products. */
        $myProducts     = array();
        $othersProducts = array();
        $products       = $this->loadModel('product')->getList();

        foreach($products as $product)
        {
            if($product->status == 'normal' and $product->PO == $this->app->user->account) $myProducts[$product->id] = $product->name;
            if($product->status == 'normal' and !($product->PO == $this->app->user->account)) $othersProducts[$product->id] = $product->name;
            if($product->status == 'closed') continue;
        }
        $products = $myProducts + $othersProducts;

        /* Assign. */
        $product = $this->product->getById($story->product);
        $stories = $this->story->getParentStoryPairs($story->product, $story->parent);
        if(isset($stories[$storyID])) unset($stories[$storyID]);

        /* Get users. */
        $users = $this->user->getPairs('pofirst|nodeleted|noclosed', "$story->assignedTo,$story->openedBy,$story->closedBy");

        if($this->app->tab == 'project' or $this->app->tab == 'execution')
        {
            $objectID  = $this->app->tab == 'project' ? $this->session->project : $this->session->execution;
            $products  = $this->product->getProductPairsByProject($objectID);
            $this->view->objectID = $objectID;
        }

        /* Hidden some fields of projects without products. */
        $this->view->hiddenProduct = false;
        $this->view->hiddenParent  = false;
        $this->view->hiddenPlan    = false;
        $this->view->hiddenURS     = false;
        $this->view->teamUsers     = array();

        if($product->shadow)
        {
            $project = $this->project->getByShadowProduct($product->id);
            $this->view->teamUsers     = $this->project->getTeamMemberPairs($project->id);
            $this->view->hiddenProduct = true;
            $this->view->hiddenParent  = true;

            if($project->model !== 'scrum')  $this->view->hiddenPlan = true;
            if(!$project->multiple)
            {
                $this->view->hiddenPlan = true;
                unset($this->lang->story->stageList[''], $this->lang->story->stageList['wait'], $this->lang->story->stageList['planned']);
            }
            if($project->model === 'kanban') $this->view->hiddenURS  = true;
        }

        /* Display status of branch. */
        $branches = $this->loadModel('branch')->getList($product->id, isset($objectID) ? $objectID : 0, 'all');
        $branchOption    = array();
        $branchTagOption = array();
        foreach($branches as $branchInfo)
        {
            $branchOption[$branchInfo->id]    = $branchInfo->name;
            $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
        }

        /* Get story reviewers. */
        $reviewedReviewer = array();
        $reviewerList     = $this->story->getReviewerPairs($story->id, $story->version);
        $reviewedBy       = explode(',', trim($story->reviewedBy, ','));
        foreach($reviewedBy as $reviewer) $reviewedReviewer[] = zget($users, $reviewer);

        /* Get product reviewers. */
        $productReviewers = $product->reviewer;
        if(!$productReviewers and $product->acl != 'open') $productReviewers = $this->loadModel('user')->getProductViewListUsers($product, '', '', '', '');

        /* Process the module when branch products are switched to normal products. */
        if($product->type == 'normal' and !empty($story->branch)) $this->view->moduleOptionMenu += $this->tree->getModulesName($story->module);

        $branch         = $product->type == 'branch' ? ($story->branch > 0 ? $story->branch : '0') : 'all';
        $productStories = $this->story->getProductStoryPairs($story->product, $branch, 0, 'all', 'id_desc', 0, '', $story->type);

        if($story->type == 'requirement') $this->lang->story->notice->reviewerNotEmpty = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->notice->reviewerNotEmpty);

        $this->view->title            = $this->lang->story->edit . "STORY" . $this->lang->colon . $this->view->story->title;
        $this->view->position[]       = $this->lang->story->edit;
        $this->view->story            = $story;
        $this->view->twins            = empty($story->twins) ? array() : $this->story->getByList($story->twins);
        $this->view->stories          = $stories;
        $this->view->purchaserList    = $this->loadModel('common')->getPurchaserList();
        $this->view->purchasers       = array_keys($this->view->purchaserList);
        $this->view->users            = $users;
        $this->view->product          = $product;
        $this->view->plans            = $this->loadModel('productplan')->getPairsForStory($story->product, $story->branch == 0 ? 'all' : $story->branch, 'skipParent');
        $this->view->products         = $products;
        $this->view->productStories   = $productStories;
        $this->view->branchOption     = $branchOption;
        $this->view->branchTagOption  = $branchTagOption;
        $this->view->branches         = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($product->id);
        $this->view->reviewers        = array_keys($reviewerList);
        $this->view->reviewedReviewer = $reviewedReviewer;
        $this->view->lastReviewer     = $this->story->getLastReviewer($story->id);
        $this->view->productReviewers = $this->user->getPairs('noclosed|nodeleted', array_keys($reviewerList), 0, $productReviewers);

        $this->display();
    }

}
