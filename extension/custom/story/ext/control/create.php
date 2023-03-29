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
     * Create a story.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $moduleID
     * @param  int    $storyID
     * @param  int    $objectID  projectID|executionID
     * @param  int    $bugID
     * @param  int    $planID
     * @param  int    $todoID
     * @param  string $extra for example feedbackID=0
     * @param  string $storyType requirement|story
     * @access public
     * @return void
     */
    public function create($productID = 0, $branch = 0, $moduleID = 0, $storyID = 0, $objectID = 0, $bugID = 0, $planID = 0, $todoID = 0, $extra = '', $storyType = 'story')
    {
        $originProduct = $productID;    // Log the origin product id and use it to create the redirect url.

        /* Whether there is a object to transfer story, for example feedback. */
        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);

        if($productID == 0 and $objectID == 0) $this->locate($this->createLink('product', 'create'));

        /* Get product id according to the project id when lite vision todo transfer story */
        if($this->config->vision == 'lite' and $productID == 0)
        {
            $product = $this->loadModel('product')->getProductPairsByProject($objectID);
            if(!empty($product)) $productID = key($product);
        }

        $this->story->replaceURLang($storyType);
        if($this->app->tab == 'product')
        {
            $this->product->setMenu($productID);
        }
        elseif($this->app->tab == 'project')
        {
            $objects = $this->project->getPairsByProgram();

            if(empty($objectID)) $objectID = $this->session->project;

            $projectID = $objectID;
            if(!$this->session->multiple)
            {
                $projectID = $this->session->project;
                $objectID  = $this->execution->getNoMultipleID($projectID);
            }
            $projectID = isset($objects[$projectID]) ? $projectID : $this->session->project;
            $projectID = $this->project->saveState($projectID, $objects);
            $this->project->setMenu($projectID);
        }
        else if($this->app->tab == 'execution')
        {
            $objectID = empty($objectID) ? $this->session->execution : $objectID;
            $this->execution->setMenu($objectID);
            $execution = $this->dao->findById((int)$objectID)->from(TABLE_EXECUTION)->fetch();
            if($execution->type == 'kanban')
            {
                $this->loadModel('kanban');
                $regionPairs = $this->kanban->getRegionPairs($execution->id, 0, 'execution');
                $regionID    = !empty($output['regionID']) ? $output['regionID'] : key($regionPairs);
                $lanePairs   = $this->kanban->getLanePairsByRegion($regionID, 'story');
                $laneID      = !empty($output['laneID']) ? $output['laneID'] : key($lanePairs);

                $this->view->executionType = $execution->type;
                $this->view->regionID      = $regionID;
                $this->view->laneID        = $laneID;
                $this->view->regionPairs   = $regionPairs;
                $this->view->lanePairs     = $lanePairs;
            }
        }

        foreach($output as $paramKey => $paramValue)
        {
            if(isset($this->config->story->fromObjects[$paramKey]))
            {
                $fromObjectIDKey  = $paramKey;
                $fromObjectID     = $paramValue;
                $fromObjectName   = $this->config->story->fromObjects[$fromObjectIDKey]['name'];
                $fromObjectAction = $this->config->story->fromObjects[$fromObjectIDKey]['action'];
                break;
            }
        }

        /* If there is a object to transfer story, get it by getById function and set objectID,object in views. */
        if(isset($fromObjectID))
        {
            $fromObject = $this->loadModel($fromObjectName)->getById($fromObjectID);
            if(!$fromObject) return print(js::error($this->lang->notFound) . js::locate('back', 'parent'));

            $this->view->$fromObjectIDKey = $fromObjectID;
            $this->view->$fromObjectName  = $fromObject;
        }

        $copyStoryID = $storyID;

        if(!empty($_POST))
        {
            $response['result'] = 'success';

            setcookie('lastStoryModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);

            $storyResult = $this->story->create($objectID, $bugID, $from = isset($fromObjectIDKey) ? $fromObjectIDKey : '', $extra);
            if(!$storyResult or dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            $storyID   = $storyResult['id'];
            $productID = $this->post->product ? $this->post->product : $productID;

            if($storyResult['status'] == 'exists')
            {
                $response['message'] = sprintf($this->lang->duplicate, $this->lang->story->common);
                if($objectID == 0)
                {
                    $response['locate'] = $this->createLink('story', 'view', "storyID={$storyID}&version=0&param=0&storyType=$storyType");
                }
                else
                {
                    $execution          = $this->dao->findById((int)$objectID)->from(TABLE_EXECUTION)->fetch();
                    $moduleName         = $execution->type == 'project' ? 'projectstory' : 'execution';
                    $param              = $execution->type == 'project' ? "projectID=$objectID&productID=$originProduct" : "executionID=$objectID";
                    $response['locate'] = $this->createLink($moduleName, 'story', $param);
                }
                return $this->send($response);
            }

            $action = $bugID == 0 ? 'Opened' : 'Frombug';
            $extra  = $bugID == 0 ? '' : $bugID;
            /* Record related action, for example FromFeedback. */
            if(isset($fromObjectID))
            {
                $action = $fromObjectAction;
                $extra  = $fromObjectID;
            }
            /* Create actions. */
            $storyIds = $storyResult['ids'];
            foreach($storyIds as $idItem) $actionID = $this->action->create('story', $idItem, $action, '', $extra);

            /* Record submit review action. */
            $story = $this->dao->findById((int)$storyID)->from(TABLE_STORY)->fetch();
            if($story->status == 'reviewing')
            {
                foreach($storyIds as $idItem) $this->action->create('story', $idItem, 'submitReview');
            }

            if($objectID != 0)
            {
                $object = $this->dao->findById((int)$objectID)->from(TABLE_PROJECT)->fetch();
                if($object->type != 'project')
                {
                    foreach($storyIds as $idItem)
                    {
                        $this->action->create('story', $idItem, 'linked2project', '', $object->project);

                        $actionType = $object->type == 'kanban' ? 'linked2kanban' : 'linked2execution';
                        if($object->multiple) $this->action->create('story', $idItem, $actionType, '', $objectID);
                    }
                }
                else
                {
                    foreach($storyIds as $idItem) $this->action->create('story', $idItem, 'linked2project', '', $objectID);
                }
            }

            if($todoID > 0)
            {
                $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($todoID)->exec();
                $this->action->create('todo', $todoID, 'finished', '', "STORY:$storyID");

                if($this->config->edition == 'biz' || $this->config->edition == 'max')
                {
                    $todo = $this->dao->select('type, idvalue')->from(TABLE_TODO)->where('id')->eq($todoID)->fetch();
                    if($todo->type == 'feedback' && $todo->idvalue) $this->loadModel('feedback')->updateStatus('todo', $todo->idvalue, 'done');
                }
            }

            $message = $this->executeHooks($storyID);
            if($message) $this->lang->saveSuccess = $message;
            $response['message'] = $this->post->status == 'draft' ? $this->lang->story->saveDraftSuccess : $this->lang->saveSuccess;

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $storyID));

            /* If link from no head then reload. */
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
                        $kanbanData    = $this->loadModel('kanban')->getRDKanban($this->session->execution, $execLaneType, 'id_desc', 0, $execGroupBy, $rdSearchValue);
                        $kanbanData    = json_encode($kanbanData);

                        return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.updateKanban($kanbanData, 0)"));
                    }
                    else
                    {
                        $taskSearchValue = $this->session->taskSearchValue ? $this->session->taskSearchValue : '';
                        $kanbanData      = $this->loadModel('kanban')->getExecutionKanban($execution->id, $execLaneType, $execGroupBy, $taskSearchValue);
                        $kanbanType      = $execLaneType == 'all' ? 'story' : key($kanbanData);
                        $kanbanData      = $kanbanData[$kanbanType];
                        $kanbanData      = json_encode($kanbanData);
                        return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.updateKanban(\"story\", $kanbanData)"));
                    }
                }
                else
                {
                    return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
                }
            }

            if($this->post->newStory)
            {
                $response['message'] = $this->lang->story->successSaved . $this->lang->story->newStory;
                $response['locate']  = $this->createLink('story', 'create', "productID=$productID&branch=$branch&moduleID=$moduleID&story=0&objectID=$objectID&bugID=$bugID");
                return $this->send($response);
            }

            $moduleID = $this->post->module ? $this->post->module : 0;
            if($objectID == 0)
            {
                setcookie('storyModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
                $branchID  = $this->post->branch  ? $this->post->branch  : $branch;
                $response['locate'] = $this->createLink('product', 'browse', "productID=$productID&branch=$branchID&browseType=&param=0&storyType=$storyType&orderBy=id_desc");
                if($this->session->storyList)
                {
                    /* When copying story in the product plan, return to different pages for story#32949. */
                    if($copyStoryID and strpos($this->session->storyList, 'productplan') !== false)
                    {
                        $storyInfo = $this->story->getByList(array($storyID, $copyStoryID));
                        if($storyInfo[$storyID]->plan == $storyInfo[$copyStoryID]->plan or $storyInfo[$storyID]->product != $storyInfo[$copyStoryID]->product) $response['locate'] = $this->session->storyList;
                    }
                    else
                    {
                        $sessionStoryList = $this->session->storyList;
                        if(!empty($_POST['branches']) and count($_POST['branches']) > 1) $sessionStoryList = preg_replace('/branch=(\d+|[A-Za-z]+)/', 'branch=all', $this->session->storyList);
                        $response['locate'] = $sessionStoryList;
                    }
                }
            }
            else
            {
                setcookie('storyModuleParam', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
                $response['locate'] = $this->session->storyList;
            }
            if($this->app->getViewType() == 'xhtml') $response['locate'] = $this->createLink('story', 'view', "storyID=$storyID", 'html');
            return $this->send($response);
        }

        /* Set products, users and module. */
        if($objectID != 0)
        {
            $onlyNoClosed    = empty($this->config->CRProduct) ? 'noclosed' : '';
            $products        = $this->product->getProductPairsByProject($objectID, $onlyNoClosed);
            $productID       = empty($productID) ? key($products) : $productID;
            $product         = $this->product->getById(($productID and array_key_exists($productID, $products)) ? $productID : key($products));
            $productBranches = $product->type != 'normal' ? $this->loadModel('execution')->getBranchByProduct($productID, $objectID, 'noclosed|withMain') : array();
            $branches        = isset($productBranches[$productID]) ? $productBranches[$productID] : array();
            $branch          = key($branches);
        }
        else
        {
            $products = array();
            $productList = $this->product->getOrderedProducts('noclosed');
            foreach($productList as $product) $products[$product->id] = $product->name;
            $product = $this->product->getById($productID ? $productID : key($products));
            if(!isset($products[$product->id])) $products[$product->id] = $product->name;
            $branches = $product->type != 'normal' ? $this->loadModel('branch')->getPairs($productID, 'active') : array();
        }

        $users = $this->user->getPairs('pdfirst|noclosed|nodeleted');

        $branchData = explode(',', $branch);
        $branch     = current($branchData);
        $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'story', 0, $branch === 'all' ? 0 : $branch);

        if(empty($moduleOptionMenu)) return print(js::locate(helper::createLink('tree', 'browse', "productID=$productID&view=story")));

        /* Init vars. */
        $bzCategory     = '';
        $prCategory     = '';
        $responseResult     = 0;
        $uatDate     = '';
	    $purchaser   = '';
        $source     = '';
        $sourceNote = '';
        $pri        = 3;
        $estimate   = '';
        $title      = '';
        $spec       = '';
        $verify     = '';
        $keywords   = '';
        $mailto     = '';
        $color      = '';

        if($storyID > 0)
        {
            $story      = $this->story->getByID($storyID);
            $planID     = $story->plan;
            $bzCategory     = $story->bzCategory;
            $prCategory     = $story->prCategory;
            $responseResult     = $story->responseResult;
            
            $uatDate     = $story->uatDate;
	        $purchaser     = $story->purchaser;
            $source     = $story->source;
            $sourceNote = $story->sourceNote;
            $color      = $story->color;
            $pri        = $story->pri;
            $productID  = $story->product;
            $moduleID   = $story->module;
            $estimate   = $story->estimate;
            $title      = $story->title;
            $spec       = htmlSpecialString($story->spec);
            $verify     = htmlSpecialString($story->verify);
            $keywords   = $story->keywords;
            $mailto     = $story->mailto;
            $category   = $story->category;
        }

        if($bugID > 0)
        {
            $oldBug    = $this->loadModel('bug')->getById($bugID);
            $productID = $oldBug->product;
            $source    = 'bug';
            $title     = $oldBug->title;
            $keywords  = $oldBug->keywords;
            $spec      = $oldBug->steps;
            $pri       = !empty($oldBug->pri) ? $oldBug->pri : '3';
            if($oldBug->mailto and strpos($oldBug->mailto, $oldBug->openedBy) === false)
            {
                $mailto = $oldBug->mailto . $oldBug->openedBy . ',';
            }
            else
            {
                $mailto = $oldBug->mailto;
            }
        }

        if($todoID > 0)
        {
            $todo   = $this->loadModel('todo')->getById($todoID);
            $source = 'todo';
            $title  = $todo->name;
            $spec   = $todo->desc;
            $pri    = $todo->pri;
        }

        /* Replace the value of story that needs to be replaced with the value of the object that is transferred to story. */
        if(isset($fromObject))
        {
            if(isset($this->config->story->fromObjects[$fromObjectIDKey]['source']))
            {
                $sourceField = $this->config->story->fromObjects[$fromObjectIDKey]['source'];
                $sourceUser  = $this->loadModel('user')->getById($fromObject->{$sourceField});
                $source      = $sourceUser->role;
                $sourceNote  = $sourceUser->realname;
            }
            else
            {
                $source      = $fromObjectName;
                $sourceNote  = $fromObjectID;
            }

            foreach($this->config->story->fromObjects[$fromObjectIDKey]['fields'] as $storyField => $fromObjectField)
            {
                $storyField = $fromObject->{$fromObjectField};
            }
        }

        /* Get block id of assinge to me. */
        $blockID = 0;
        if(isonlybody())
        {
            $blockID = $this->dao->select('id')->from(TABLE_BLOCK)
                ->where('block')->eq('assingtome')
                ->andWhere('module')->eq('my')
                ->andWhere('account')->eq($this->app->user->account)
                ->orderBy('order_desc')
                ->fetch('id');
        }

        /* Get reviewers. */
        $reviewers = $product->reviewer;
        if(!$reviewers and $product->acl != 'open') $reviewers = $this->loadModel('user')->getProductViewListUsers($product, '', '', '', '');

        /* Hidden some fields of projects without products. */
        $this->view->hiddenProduct = false;
        $this->view->hiddenParent  = false;
        $this->view->hiddenPlan    = false;
        $this->view->hiddenURS     = false;
        $this->view->teamUsers     = array();

        if($this->app->tab === 'project' || $this->app->tab === 'execution')
        {
            $project = $this->dao->findById((int)$objectID)->from(TABLE_PROJECT)->fetch();
            if(!empty($project->project)) $project = $this->dao->findById((int)$project->project)->from(TABLE_PROJECT)->fetch();

            if(empty($project->hasProduct))
            {
                $this->view->teamUsers     = $this->project->getTeamMemberPairs($project->id);
                $this->view->hiddenProduct = true;
                $this->view->hiddenParent  = true;

                if($project->model !== 'scrum')  $this->view->hiddenPlan = true;
                if(!$project->multiple)          $this->view->hiddenPlan = true;
                if($project->model === 'kanban') $this->view->hiddenURS  = true;
            }
        }

        /* Get the module's children id list. */
        $moduleID     = $moduleID ? $moduleID : (int)$this->cookie->lastStoryModule;
        $moduleID     = isset($moduleOptionMenu[$moduleID]) ? $moduleID : 0;
        $moduleIdList = $this->tree->getAllChildId($moduleID);

        /* Set Custom. */
        foreach(explode(',', $this->config->story->list->customCreateFields) as $field) $customFields[$field] = $this->lang->story->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->story->custom->createFields;

        $this->view->title            = $product->name . $this->lang->colon . $this->lang->story->create;
        $this->view->position[]       = html::a($this->createLink('product', 'browse', "product=$productID&branch=$branch"), $product->name);
        $this->view->position[]       = $this->lang->story->common;
        $this->view->position[]       = $this->lang->story->create;
        $this->view->gobackLink       = (isset($output['from']) and $output['from'] == 'global') ? $this->createLink('product', 'browse', "productID=$productID") : '';
        $this->view->products         = $products;
        $this->view->users            = $users;
        $this->view->moduleID         = $moduleID;
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->plans            = str_replace('2030-01-01', $this->lang->story->undetermined, $this->loadModel('productplan')->getPairsForStory($productID, $branch == 0 ? '' : $branch, 'skipParent|unexpired|noclosed'));
        $this->view->planID           = $planID;
        $this->view->source           = $source;
        $this->view->sourceNote       = $sourceNote;
        $this->view->color            = $color;
        $this->view->pri              = $pri;
        $this->view->branch           = $branch;
        $this->view->branches         = $branches;
        $this->view->stories          = $this->story->getParentStoryPairs($productID);
        $this->view->productID        = $productID;
        $this->view->product          = $product;
        $this->view->reviewers        = $this->user->getPairs('noclosed|nodeleted', '', 0, $reviewers);
        $this->view->objectID         = $objectID;
        $this->view->estimate         = $estimate;
        $this->view->storyTitle       = $title;
        $this->view->spec             = $spec;
        $this->view->verify           = $verify;
        $this->view->keywords         = $keywords;
        $this->view->mailto           = $mailto;
        $this->view->blockID          = $blockID;
        $this->view->URS              = $storyType == 'story' ? $this->story->getProductStoryPairs($productID, $branch, $moduleIdList, 'changing,active,reviewing', 'id_desc', 0, '', 'requirement') : '';
        $this->view->needReview       = ($this->app->user->account == $product->PO or $objectID > 0 or $this->config->story->needReview == 0 or !$this->story->checkForceReview()) ? "checked='checked'" : "";
        $this->view->type             = $storyType;
        $this->view->category         = !empty($category) ? $category : 'feature';

        $this->display();
    }

}
