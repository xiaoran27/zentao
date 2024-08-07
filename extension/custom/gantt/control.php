<?php
/**
 * The control file of program module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     gantt
 * @version     $Id
 * @link        http://www.zentao.net
 */
class gantt extends control
{
    /**
     * Construct
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('common');
        $this->loadModel('user');
        $this->loadModel('dept');
        $this->loadModel('program');
        $this->loadModel('project');
        $this->loadModel('product');
    }

    /**
     * 分类统计工时
     *
     * @param  array $taskList  // id => stdclass
     * @param  string $statfields
     * @param  bool $hasSum
     * @access public
     * @return array  filed => array( name => num )
     */
    private function statTask($taskList, $statfields = 'deptname,realname,productname,story,worktype',$hasSum=true)
    {
        $TASK_PRE = 'T';
        $DEPTSUM = '小计';

        $statList = array();
        $statfields = explode(',',$statfields);
        
        // 计算工时
        foreach($taskList as $key => $_) {
            if ( substr($_->id,0,1) !== $TASK_PRE  or $_->children > 0 or $_->status == 'cancel'  or $_->closedReason == 'cancel'  ) continue;
            
            foreach($statfields as $field ) {
                if ( !isset($statList[$field]) ) $statList[$field] = array();

                $statkey = empty($_->$field)?'未知':$_->$field; 
                if ( !in_array($statkey, array_keys($statList[$field]))){
                    $$statList[$field][$statkey]->estimate = 0;
                    $$statList[$field][$statkey]->consumed = 0;
                    $$statList[$field][$statkey]->left = 0;
                }
                $statList[$field][$statkey]->estimate += round($_->estimate,1);
                $statList[$field][$statkey]->consumed += round($_->consumed,1);
                $statList[$field][$statkey]->left += round($_->left,1);
                $statList[$field][$DEPTSUM]->estimate += round($_->estimate,1);
                $statList[$field][$DEPTSUM]->consumed += round($_->consumed,1);
                $statList[$field][$DEPTSUM]->left += round($_->left,1);
            }
            
        }
        return $statList;
    }    
    
    /**
     * project/execution/task list.
     *
     * @param  int  $programId=223  
     * @param  string  $projectId='' 多个用','分隔
     * @param  string  $projectEnd=null yyyy-mm-dd 
     * @param  int $dept_id=0  
     * @param  string  $task_assignTo=''  多个用','分隔
     * @param  string  $projectPM=''  多个用','分隔
     * @param  string  $projectStatus='unclosed'  多个用','分隔 wait|doing|suspended|closed|unclosed 
     * @param  string  $rowtype=''  多个用','分隔 project|execution|task
     * @param  string  $projectPM=''  多个用','分隔
     * @param  string  $excutionId=''  多个用','分隔
     * @param  string  $storyId=''  多个用','分隔
     * @param  string  $workType=''  多个用','分隔 saas|self|outer
     * @param  int  $productId=0 
     * @param  string  $task_finishedBy=''  多个用','分隔
     * @param  string  $task_estStarted=''  yyyy-mm-dd 
     * @param  int     $limit = 500
     * @access public
     * @return void
     */
    public function frappe(
        $programId = 223 //
        , $projectId=''  // 1,2,3
        , $projectEnd=null // yyyy-mm-dd
        , $dept_id=0  
        , $task_assignTo=''  //a,b,c
        , $projectPM=''  //a,b,c
        , $projectStatus = 'unclosed'  // wait|doing|suspended|closed|unclosed
        , $rowtype = 'project'  //project|execution|task
        , $excutionId=null //1,2,3
        , $storyId=null //1,2,3
        , $workType='self' //saas,self,outer
        , $productId=0
        , $task_finishedBy=null  // 1,2,3
        , $task_estStarted=null // yyyy-mm-dd
        , $limit = 500 )
    {

        $projectEnd = str_replace(',','-',$projectEnd);
        $task_estStarted = str_replace(',','-',$task_estStarted);

        $this->view->programId = $programId ;
        $this->view->projectId =  $projectId; 
        $this->view->projectEnd =  $projectEnd;
        $this->view->dept_id =  $dept_id;
        $this->view->task_assignTo =  $task_assignTo;
        $this->view->projectPM =  $projectPM;
        $this->view->projectStatus =  $projectStatus;  // wait|doing|suspended|closed|unclosed
        $this->view->rowtype =  $rowtype;
        $this->view->excutionId =  $excutionId;
        $this->view->storyId =  $storyId;
        $this->view->workType =  $workType;
        $this->view->productId =  $productId;
        $this->view->task_finishedBy =  $task_finishedBy;
        $this->view->task_estStarted =  $task_estStarted;

        $projectStatus = empty($projectStatus)? 'unclosed' : $projectStatus;
        $projectStatus = $projectStatus == 'unclosed' ? 'doing,suspended,wait' : $projectStatus;


        $result = $this->gantt->getTaskList($programId
            , $projectId  // 1,2,3
            , $projectEnd // yyyy,mm,dd
            , $dept_id
            , $task_assignTo  //a,b,c
            , $projectPM  //a,b,c
            , $projectStatus   // wait|doing|suspended|closed|unclosed
            , $rowtype  //project|execution|task
            , $excutionId //1,2,3
            , $storyId //1,2,3
            , $workType //saas,self,outer
            , $productId
            , $task_finishedBy  // 1,2,3
            , $task_estStarted // yyyy,mm,dd
            , $limit );

        $users   = $this->user->getPairs('noletter|noclosed');
        $depts   = $this->dept->getDeptPairs();
       
        $taskKeys = array();
        $taskList = array();
        if(!empty($result))
        {

            $PROJECT_PRE = 'P';
            $EXECUTION_PRE = 'E';
            $TASK_PRE = 'T';
            $MAX_DAYS = 93;

            $taskList = array();    
            foreach($result as $key => $value)
            {
                if (helper::isZeroDate($value->myBegin) or helper::isZeroDate($value->myEnd)) continue;

                $_ = new stdclass();
                $_->raw = clone $value  ;

                $value->myBegin__ = $value->myBegin;
                $value->myEnd__ = $value->myEnd;
                // 起止日期的处理： 超过93天，则限制在当前的-31、+62天内呈现
                $days = helper::diffDate($value->myBegin, helper::today());
                if ($days>$MAX_DAYS or $days<-$MAX_DAYS) {
                    $value->myBegin__ = $value->myBegin;
                    $date = new DateTime(helper::today()); // 创建一个DateTime对象，表示2023年4月1日
                    $date->modify(($days>0?"+":"-")."$MAX_DAYS days");
                    $value->myBegin = $date->format('Y-m-d');
                }
                $days = helper::diffDate($value->myEnd, helper::today());
                if ($days>$MAX_DAYS or $days<-$MAX_DAYS) {
                    $value->myEnd__ = $value->myEnd;
                    $date = new DateTime(helper::today()); // 创建一个DateTime对象，表示2023年4月1日
                    $date->modify(($days>0?"+":"-")."$MAX_DAYS days");
                    $value->myEnd = $date->format('Y-m-d');
                }
                $days = helper::diffDate($value->myEnd, $value->myBegin);
                if ($days>$MAX_DAYS or $days<-$MAX_DAYS) {
                    $date = new DateTime(helper::today()); 
                    $date->modify("-31 days");
                    $value->myBegin = $date->format('Y-m-d');
                    $date->modify("+$MAX_DAYS days");
                    $value->myEnd = $date->format('Y-m-d');
                }                
                $value->myEnd = ($value->myBegin > $value->myEnd)?$value->myBegin:$value->myEnd;
                
                
                $ids = array_filter(explode(',',$value->fullpath));
                $selfPre = '';
                $parentPre = '';
                switch($value->tocLevel)
                {
                    case 1:  //project 忽略父节点
                        $selfPre = $PROJECT_PRE;
                        $parentPre = '';
                        $value->parent = '';
                        break;
                    case 2:   //execution 有project&有父才显示父节点
                        $selfPre = $EXECUTION_PRE;
                        if ( (empty($rowtype) or strpos(','.$rowtype.',',',project,') !== false) and in_array("{$PROJECT_PRE}{$ids[count($ids)-1]}", $taskKeys) ){
                            $parentPre = $PROJECT_PRE;
                            $value->parent = $ids[count($ids)-1];
                        }else{
                            $parentPre = '';
                            $value->parent = '';
                        }
                        break;
                    case 3:   //task 有(execution或project)&有父才显示父节点
                        $selfPre = $TASK_PRE;
                        if ( (empty($rowtype) or strpos(','.$rowtype.',',',execution,') !== false) and in_array("{$EXECUTION_PRE}{$ids[count($ids)-1]}", $taskKeys) ){
                            $parentPre = $EXECUTION_PRE;
                            $value->parent = $ids[count($ids)-1];
                        }elseif ( (empty($rowtype) or strpos(','.$rowtype.',',',project,') !== false) and in_array("{$PROJECT_PRE}{$ids[count($ids)-2]}", $taskKeys) ){
                            $parentPre = $PROJECT_PRE;
                            $value->parent = $ids[count($ids)-2];
                        }else{
                            $parentPre = '';
                            $value->parent = '';
                        }
                        break;
                    default:    //task 有(task或execution或project)&有父才显示父节点
                        $selfPre = $TASK_PRE;
                        if ( (empty($rowtype) or strpos(','.$rowtype.',',',task,') !== false) and in_array("{$TASK_PRE}{$ids[count($ids)-1]}", $taskKeys) ){
                            $parentPre = $TASK_PRE;
                            $value->parent = $ids[count($ids)-1];
                        }elseif ( (empty($rowtype) or strpos(','.$rowtype.',',',execution,') !== false) and in_array("{$EXECUTION_PRE}{$ids[count($ids)-2]}", $taskKeys) ){
                            $parentPre = $EXECUTION_PRE;
                            $value->parent = $ids[count($ids)-2];
                        }elseif ( (empty($rowtype) or strpos(','.$rowtype.',',',project,') !== false) and in_array("{$PROJECT_PRE}{$ids[count($ids)-3]}", $taskKeys) ){
                            $parentPre = $PROJECT_PRE;
                            $value->parent = $ids[count($ids)-3];
                        }else{
                            $parentPre = '';
                            $value->parent = '';
                        }
                        break;
                }
                $resetParent =  empty($value->parent) or empty($parentPre) or !in_array("{$parentPre}{$value->parent}", $taskKeys);
                if ($resetParent){
                    $value->parent = '';
                }


                $_->id = $selfPre.$value->id;
                $_->name = $value->name;
                $_->important = $value->milestone > 0;
                $_->start = substr($value->myBegin,0,10);
                $_->end = substr($value->myEnd,0,10);
                $_->start__ = $value->myBegin__;
                $_->end__ = $value->myEnd__;
                $_->estimate = round($value->estimate,1);
                $_->consumed = round($value->consumed,1);
                $_->left = round($value->left,1);
                $_->closedReason = $value->closedReason;
                $_->parent = empty($value->parent)?"":$parentPre.$value->parent;
                $_->dependencies = $_->parent;
                $_->duration__ = (helper::diffDate($_->end__, $_->start__)+1).'d';

                $_->owner = $value->pm;
                $_->realname = empty($value->realname)?$users[$value->pm]:$value->realname;
                $_->deptid = empty($value->dept_id)?'':$value->dept_id;
                $_->deptname = empty($value->dept_name)?'':$value->dept_name;
                $_->productid = empty($value->product_id)?'':$value->product_id;
                $_->productname = empty($value->product_name)?'':$value->product_name;

                $_->custom_class = ' bar-bytenew ';
                if ($value->tocLevel <= 2){  // project|execution
                    $_->custom_class .= " bar-$value->type ";
                }elseif ($value->tocLevel == 3){  // task
                    $_->custom_class .= " bar-task-primary ";
                }else{
                    $_->custom_class .= " bar-task ";
                }

                $_->type = $value->type;
                $_->status = $value->status;
                $_->stage = $value->stage;
                $_->story = $value->story;
                $_->workType = $value->workType;
                $_->url = $value->url;

                // $_->name = "$_->name ($_->status,$_->realname)";
                $_->progress = ( ( $_->status === 'closed' or $_->status === 'done' or $_->status === 'cancel'  ) and $_->progress < 100 ) ? 100: $_->progress;

                if (in_array($_->id, $taskKeys)) continue;  // 去除重复的task

                $_->milestone = $value->milestone;
                $_->children = 0;
                if (!empty($_->parent) and !empty($taskList[$_->parent]))  {
                    $taskList[$_->parent]->children += 1;
                }

                $taskList[$_->id] = $_;
                $taskKeys[$_->id] = $_->id;
            }
        }


        if( $this->app->getViewType() == 'json')
        {
            $this->send(array('status' => 'success', 'data' => $taskList));
            return;
        }

        $statList =  $this->statTask($taskList);
        $taskListKeys = array_keys($taskList);
        $taskList = array_values($taskList);
        
        
        
        $programPairs = $this->program->getpairs(true);
        $projectPairs = array();
        $programs = explode(',', trim($programId));
        if (empty($programId) or empty($programs) or count($programs)<1)
        {
            $projectPairs = $this->project->getPairsByProgram('', $status = 'all', $isQueryAll = true, $orderBy = 'order_asc', $excludedModel = '', $model = '', $param = '');
        }else{
            foreach($programs as $programId_)
            {
                $projectPairs_ = $this->project->getPairsByProgram($programId_, $status = 'all', $isQueryAll = true, $orderBy = 'order_asc', $excludedModel = '', $model = '', $param = '');
                $projectPairs = $projectPairs + $projectPairs_;
            }
        }
        $productPairs = $this->product->getpairs('noclosed');
        $DEPTSUM = '小计';

        $this->view->DEPTSUM     = $DEPTSUM;
        $this->view->statList     = $statList;
        $this->view->deptHours     = $statList['deptname'];
        $this->view->taskListKeys     = $taskListKeys;
        $this->view->taskList     = $taskList;
        $this->view->productPairs = $productPairs ;
        $this->view->programPairs = $programPairs ;
        $this->view->projectPairs = $projectPairs ;
        $this->view->depts     = $depts;
        $this->view->users     = $users;
        $this->view->userIdPairs  = $this->user->getPairs('noletter|showid');
        $this->view->usersAvatar  = $this->user->getAvatarPairs('');

        $this->view->title      = $this->lang->gantt->common;
        $this->view->position[] = $this->lang->gantt->common;
        $this->view->submenu    = 'gantt';
        $this->display();
   }

    /**
     * Program list.
     *
     * @param  string  $status
     * @param  string  $orderBy
     * @access public
     * @return void
     */
    public function browse($status = 'unclosed', $orderBy = 'order_asc', $recTotal = 0, $recPerPage = 10, $pageID = 1, $param = 0)
    {
        if(common::hasPriv('program', 'create')) $this->lang->pageActions = html::a($this->createLink('program', 'create'), "<i class='icon icon-plus'></i> " . $this->lang->program->create, '', "class='btn btn-primary create-program-btn'");

        $this->session->set('programList', $this->app->getURI(true), 'program');
        $this->session->set('projectList', $this->app->getURI(true), 'program');
        $this->session->set('createProjectLocate', $this->app->getURI(true), 'program');

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $programType = $this->cookie->programType ? $this->cookie->programType : 'bylist';

        if($programType === 'bygrid')
        {
            $programs = $this->program->getProgramStats($status, 20, $orderBy);
        }
        else
        {
            if(strtolower($status) == 'bysearch')
            {
                $queryID  = (int)$param;
                $programs = $this->program->getListBySearch($orderBy, $queryID);
            }
            else
            {
                /* Get top programs and projects. */
                $topObjects = $this->program->getList($status == 'unclosed' ? 'doing,suspended,wait' : $status, $orderBy, $pager, 'top');
                if(!$topObjects) $topObjects = array(0);
                $programs   = $this->program->getList($status, $orderBy, NULL, 'child', array_keys($topObjects));

                /* Get summary. */
                $topCount = $indCount = 0;
                foreach($programs as $program)
                {
                    if($program->type == 'program' and $program->parent == 0) $topCount ++;
                    if($program->type == 'project' and $program->parent == 0) $indCount ++;
                }
                $summary = sprintf($this->lang->program->summary, $topCount, $indCount);
            }
        }

        /* Get PM id list. */
        $accounts = array();
        $hasProject = false;
        foreach($programs as $program)
        {
            if(!empty($program->PM) and !in_array($program->PM, $accounts)) $accounts[] = $program->PM;
            if($hasProject === false and $program->type != 'program') $hasProject = true;
        }
        $PMList = $this->loadModel('user')->getListByAccounts($accounts, 'account');

        /* Build the search form. */
        $actionURL = $this->createLink('program', 'browse', "status=bySearch&orderBy={$orderBy}&recTotal={$recTotal}&recPerPage={$recPerPage}&pageID={$pageID}&param=myQueryID");
        $this->config->program->search['actionURL'] = $actionURL;
        $this->loadModel('search')->setSearchParams($this->config->program->search);

        $this->view->title      = $this->lang->program->browse;
        $this->view->position[] = $this->lang->program->browse;

        $this->view->programs     = $programs;
        $this->view->status       = $status;
        $this->view->orderBy      = $orderBy;
        $this->view->summary      = isset($summary) ? $summary : '';
        $this->view->pager        = $pager;
        $this->view->users        = $this->user->getPairs('noletter');
        $this->view->userIdPairs  = $this->user->getPairs('noletter|showid');
        $this->view->usersAvatar  = $this->user->getAvatarPairs('');
        $this->view->programType  = $programType;
        $this->view->PMList       = $PMList;
        $this->view->progressList = $this->program->getProgressList();
        $this->view->hasProject   = $hasProject;

        $this->display();
    }

    /**
     * Program kanban list.
     *
     * @access public
     * @return void
     */
    public function kanban()
    {
        $this->app->loadLang('release');

        $this->session->set('projectList', $this->app->getURI(true), 'project');
        $this->session->set('productPlanList', $this->app->getURI(true), 'product');
        $this->session->set('releaseList', $this->app->getURI(true), 'product');

        $this->view->title       = $this->lang->program->kanban->common;
        $this->view->kanbanGroup = array_filter($this->program->getKanbanGroup());
        $this->display();
    }

    /**
     * Program products list.
     *
     * @param  int     $programID
     * @param  string  $browseType
     * @param  string  $orderBy
     * @param  int     $recTotal
     * @param  int     $recPerPage
     * @param  int     $pageID
     * @access public
     * @return void
     */
    public function product($programID = 0, $browseType = 'noclosed', $orderBy = 'order_asc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $programPairs = $this->program->getPairs();

        if(defined('RUN_MODE') && RUN_MODE == 'api' && !isset($programPairs[$programID]))
        {
            return $this->send(array('status' => 'fail', 'code' => 404, 'message' => '404 Not found'));
        }

        $programID = $this->program->saveState($programID, $programPairs);

        setCookie("lastProgram", $programID, $this->config->cookieLife, $this->config->webRoot, '', false, true);

        $this->program->setMenu($programID);

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get the top programID. */
        if($programID)
        {
            $program   = $this->program->getByID($programID);
            $path      = explode(',', $program->path);
            $path      = array_filter($path);
            $programID = current($path);

            $this->view->program = $program;
        }

        $this->view->title         = $this->lang->program->product;
        $this->view->position[]    = $this->lang->program->product;
        $this->view->programID     = $programID;
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->pager         = $pager;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->products      = $this->loadModel('product')->getStats($orderBy, $pager, $browseType, '', 'story', $programID);
        $this->view->userIdPairs   = $this->user->getPairs('noletter|showid');
        $this->view->usersAvatar   = $this->user->getAvatarPairs('');
        $this->view->showBatchEdit = $this->cookie->showProductBatchEdit;

        $this->display();
    }

    /**
     * Create a program.
     *
     * @param  int    $parentProgramID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function create($parentProgramID = 0, $extra = '')
    {
        $parentProgram = $this->program->getByID($parentProgramID);

        if($_POST)
        {
            $programID = $this->program->create();
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('program', $programID, 'opened');
            $locateLink = $this->session->programList ? $this->session->programList : $this->createLink('program', 'browse');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $programID, 'locate' => $locateLink));
        }

        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);

        $this->view->title      = $this->lang->program->create;
        $this->view->position[] = $this->lang->program->create;

        $this->view->gobackLink     = (isset($output['from']) and $output['from'] == 'global') ? $this->createLink('program', 'browse') : '';
        $this->view->pmUsers        = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst');
        $this->view->poUsers        = $this->user->getPairs('noclosed|nodeleted|pofirst');
        $this->view->users          = $this->user->getPairs('noclosed|nodeleted');
        $this->view->parentProgram  = $parentProgram;
        $this->view->parents        = $this->program->getParentPairs();
        $this->view->programList    = $this->program->getList();
        $this->view->budgetUnitList = $this->project->getBudgetUnitList();
        $this->view->budgetLeft     = $this->program->getBudgetLeft($parentProgram);

        $this->display();
    }

    /**
     * Edit a program.
     *
     * @param  int $programID
     * @access public
     * @return void
     */
    public function edit($programID = 0)
    {
        if($_POST)
        {
            $changes = $this->program->update($programID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('program', $programID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->session->programList ? $this->session->programList : inLink('browse')));
        }

        $program       = $this->program->getByID($programID);
        $parentProgram = $program->parent ? $this->program->getByID($program->parent) : '';
        $parents       = $this->program->getParentPairs();

        /* Remove children program from parents. */
        $children = $this->dao->select('*')->from(TABLE_PROGRAM)->where('path')->like("%,$programID,%")->fetchPairs('id', 'id');
        foreach($children as $childID) unset($parents[$childID]);

        $this->view->title      = $this->lang->program->edit;
        $this->view->position[] = $this->lang->program->edit;

        $this->view->pmUsers         = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst',  $program->PM);
        $this->view->poUsers         = $this->user->getPairs('noclosed|nodeleted|pofirst');
        $this->view->users           = $this->user->getPairs('noclosed|nodeleted');
        $this->view->program         = $program;
        $this->view->parents         = $parents;
        $this->view->programList     = $this->program->getList();
        $this->view->budgetUnitList  = $this->loadModel('project')->getBudgetUnitList();
        $this->view->parentProgram   = $parentProgram;
        $this->view->availableBudget = $this->program->getBudgetLeft($parentProgram) + (float)$program->budget;

        $this->display();
    }

    /**
     * Close a program.
     *
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function close($programID)
    {
        $this->loadModel('action');
        $program = $this->project->getByID($programID, 'program');

        if(!empty($_POST))
        {
            /* Only when all subprograms and subprojects are closed can the program be closed. */
            $hasUnfinished = $this->program->hasUnfinished($program);
            if($hasUnfinished) return print(js::error($this->lang->program->closeErrorMessage));

            $changes = $this->project->close($programID);
            if(dao::isError()) return print(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('program', $programID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($programID);
            return print(js::reload('parent.parent'));
        }

        $this->view->title      = $this->lang->program->close;
        $this->view->position[] = $this->lang->program->close;
        $this->view->project    = $program;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('program', $programID);

        $this->display('project', 'close');
    }

    /**
     * Start program.
     *
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function start($programID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->project->start($programID, 'program');
            if(dao::isError()) return print(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('program', $programID, 'Started', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->loadModel('common')->syncPPEStatus($programID);
            $this->executeHooks($programID);
            return print(js::reload('parent.parent'));
        }

        $this->view->title      = $this->lang->program->start;
        $this->view->position[] = $this->lang->program->start;
        $this->view->project    = $this->project->getByID($programID, 'program');
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('program', $programID);
        $this->display('project', 'start');
    }

    /**
     * Activate a program.
     *
     * @param  int     $programID
     * @access public
     * @return void
     */
    public function activate($programID = 0)
    {
        $this->loadModel('action');
        $program = $this->project->getByID($programID, 'program');

        if(!empty($_POST))
        {
            $changes = $this->project->activate($programID);
            if(dao::isError()) return print(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('program', $programID, 'Activated', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            return print(js::reload('parent.parent'));
        }

        $newBegin = date('Y-m-d');
        $dateDiff = helper::diffDate($newBegin, $program->begin);
        $newEnd   = date('Y-m-d', strtotime($program->end) + $dateDiff * 24 * 3600);

        $this->view->title      = $this->lang->program->activate;
        $this->view->position[] = $this->lang->program->activate;
        $this->view->project    = $program;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('program', $programID);
        $this->view->newBegin   = $newBegin;
        $this->view->newEnd     = $newEnd;
        $this->display('project', 'activate');
    }

    /**
     * Suspend a program.
     *
     * @param  int     $programID
     * @access public
     * @return void
     */
    public function suspend($programID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->project->suspend($programID);
            if(dao::isError()) return print(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('program', $programID, 'Suspended', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($programID);
            return print(js::reload('parent.parent'));
        }

        $this->view->title      = $this->lang->program->suspend;
        $this->view->position[] = $this->lang->program->suspend;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->action->getList('program', $programID);
        $this->view->project    = $this->project->getByID($programID, 'program');

        $this->display('project', 'suspend');
    }

    /**
     * Delete a program.
     *
     * @param  int    $programID
     * @param  string $confirm  yes|no
     * @access public
     * @return void
     */
    public function delete($programID, $confirm = 'no')
    {
        $childrenCount = $this->dao->select('count(*) as count')->from(TABLE_PROGRAM)->where('parent')->eq($programID)->andWhere('deleted')->eq(0)->fetch('count');
        if($childrenCount)
        {
            if($this->viewType == 'json' or (defined('RUN_MODE') && RUN_MODE == 'api')) return $this->send(array('result' => 'fail', 'message' => 'Cannot delete the program has children'));
            return print(js::alert($this->lang->program->hasChildren));
        }

        $productCount = $this->dao->select('count(*) as count')->from(TABLE_PRODUCT)->where('program')->eq($programID)->andWhere('deleted')->eq(0)->fetch('count');
        if($productCount) return print(js::alert($this->lang->program->hasProduct));

        $program = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($programID)->fetch();
        if($confirm == 'no') return print(js::confirm(sprintf($this->lang->program->confirmDelete, $program->name), $this->createLink('program', 'delete', "programID=$programID&confirm=yes")));

        $this->dao->update(TABLE_PROGRAM)->set('deleted')->eq(1)->where('id')->eq($programID)->exec();
        $this->loadModel('action')->create('program', $programID, 'deleted', '', ACTIONMODEL::CAN_UNDELETED);

        echo js::reload('parent');
    }

    /**
     * Program project list.
     *
     * @param  int    $programID
     * @param  string $browseType
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function project($programID = 0, $browseType = 'doing', $orderBy = 'order_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $programID = $this->program->saveState($programID, $this->program->getPairs());
        setCookie("lastProgram", $programID, $this->config->cookieLife, $this->config->webRoot, '', false, true);

        $this->program->setMenu($programID);

        $uri = $this->app->getURI(true);
        $this->app->session->set('programProject', $uri, 'program');
        $this->app->session->set('projectList', $uri, 'program');
        $this->app->session->set('createProjectLocate', $uri, 'program');

        $this->loadModel('datatable');

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $programTitle = $this->loadModel('setting')->getItem('owner=' . $this->app->user->account . '&module=program&key=programTitle');
        $order        = explode('_', $orderBy);
        $sortField    = zget($this->config->program->sortFields, $order[0], 'id') . '_' . $order[1];
        $projectStats = $this->program->getProjectStats($programID, $browseType, 0, $sortField, $pager, $programTitle);

        $allProjectsNum = $this->program->getProjectStats($programID, 'all');
        $this->view->allProjectsNum = $allProjectsNum;

        $this->view->title      = $this->lang->program->project;
        $this->view->position[] = $this->lang->program->project;

        $this->view->projectStats  = $projectStats;
        $this->view->pager         = $pager;
        $this->view->programID     = $programID;
        $this->view->users         = $this->loadModel('user')->getPairs('noletter|pofirst|nodeleted');
        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->showBatchEdit = $this->cookie->showProjectBatchEdit;

        $this->display();
    }

    /**
     * Program stakeholder list.
     *
     * @param  int    $programID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function stakeholder($programID = 0, $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->app->loadLang('stakeholder');
        $this->program->setMenu($programID);

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager(0, $recPerPage, $pageID);

        $this->view->title      = $this->lang->program->stakeholder;
        $this->view->position[] = $this->lang->program->stakeholder;

        $this->view->stakeholders = $this->program->getStakeholders($programID, $orderBy, $pager);
        $this->view->pager        = $pager;
        $this->view->programID    = $programID;
        $this->view->program      = $this->program->getByID($programID);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|pofirst|nodeleted');
        $this->view->orderBy      = $orderBy;

        $this->display();
    }

    /**
     * Create program stakeholder.
     *
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function createStakeholder($programID = 0)
    {
        return print($this->fetch('stakeholder', 'create', "objectID=$programID"));
    }

    /**
     * Unlink program stakeholder.
     *
     * @param  int    $stakeholderID
     * @param  int    $programID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function unlinkStakeholder($stakeholderID, $programID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->program->confirmUnlink, $this->inlink('unlinkStakeholder', "stakeholderID=$stakeholderID&programID=$programID&confirm=yes")));
        }
        else
        {
            $account = $this->dao->select('user')->from(TABLE_STAKEHOLDER)->where('id')->eq($stakeholderID)->fetch('user');
            $this->dao->delete()->from(TABLE_STAKEHOLDER)->where('id')->eq($stakeholderID)->exec();

            $this->loadModel('user')->updateUserView($programID, 'program', array($account));
            $this->updateChildUserView($programID, $account);

            return print(js::reload('parent'));
         }
    }

    /**
     * Batch unlink program stakeholders.
     *
     * @param  int    $programID
     * @param  string $stakeholderIDList
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function batchUnlinkStakeholders($programID = 0, $stakeholderIDList = '', $confirm = 'no')
    {
        $stakeholderIDList = $stakeholderIDList ? $stakeholderIDList : implode(',', $this->post->stakeholderIDList);

        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->program->confirmBatchUnlink, $this->inlink('batchUnlinkStakeholders', "programID=$programID&stakeholderIDList=$stakeholderIDList&confirm=yes")));
        }
        else
        {
            $account = $this->dao->select('user')->from(TABLE_STAKEHOLDER)->where('id')->in($stakeholderIDList)->fetchPairs('user');
            $this->dao->delete()->from(TABLE_STAKEHOLDER)->where('id')->in($stakeholderIDList)->exec();

            $this->loadModel('user')->updateUserView($programID, 'program', $account);
            $this->updateChildUserView($programID, $account);

            return print(js::reload('parent'));
        }
    }

    /**
     * Update children user view.
     *
     * @param  int    $programID
     * @param  array  $account
     * @access protected
     * @return void
     */
    protected function updateChildUserView($programID = 0, $account = array())
    {
        $childPGMList  = $this->dao->select('id')->from(TABLE_PROJECT)->where('path')->like("%,$programID,%")->andWhere('type')->eq('program')->fetchPairs();
        $childPRJList  = $this->dao->select('id')->from(TABLE_PROJECT)->where('path')->like("%,$programID,%")->andWhere('type')->eq('project')->fetchPairs();
        $childProducts = $this->dao->select('id')->from(TABLE_PRODUCT)->where('program')->eq($programID)->fetchPairs();

        if(!empty($childPGMList))  $this->user->updateUserView($childPGMList, 'program',  array($account));
        if(!empty($childPRJList))  $this->user->updateUserView($childPRJList, 'project',  array($account));
        if(!empty($childProducts)) $this->user->updateUserView($childProducts, 'product', array($account));
    }

    /**
     * Export program.
     *
     * @param  string $status
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function export($status, $orderBy)
    {
        if($_POST)
        {
            $programLang   = $this->lang->program;
            $programConfig = $this->config->program;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $programConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = zget($programLang, $fieldName);
                unset($fields[$key]);
            }

            $programs = $this->program->getList($status, $orderBy, null);
            $users    = $this->loadModel('user')->getPairs('noletter');
            foreach($programs as $i => $program)
            {
                $program->PM       = zget($users, $program->PM);
                $program->status   = $this->processStatus('project', $program);
                $program->model    = zget($programLang->modelList, $program->model);
                $program->product  = zget($programLang->productList, $program->product);
                $program->budget   = $program->budget . zget($programLang->unitList, $program->budgetUnit);

                if($this->post->exportType == 'selected')
                {
                    $checkedItem = $this->cookie->checkedItem;
                    if(strpos(",$checkedItem,", ",{$program->id},") === false) unset($programs[$i]);
                }
            }

            if($this->config->edition != 'open') list($fields, $projectStats) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $projectStats);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $programs);
            $this->post->set('kind', 'program');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Ajax get program drop menu.
     *
     * @param  int    $programID
     * @param  string $module
     * @param  string $method
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($programID, $module, $method)
    {
        $this->view->programID = $programID;
        $this->view->module    = $module;
        $this->view->method    = $method;
        $this->view->programs  = $this->program->getList('all');
        $this->display();
    }

    /**
     * Ajax get projects.
     *
     * @access public
     * @return void
     */
    public function ajaxGetCopyProjects()
    {
        $data = fixer::input('post')->get();
        $projects = $this->dao->select('id, name')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->projects)->fi()
            ->beginIF(trim($data->name))->andWhere('name')->like("%$data->name%")->fi()
            ->fetchPairs();

        $html = empty($projects) ? "<div class='text-center'>{$this->lang->noData}</div>" : '';
        foreach($projects as $id => $name)
        {
            $active = $data->cpoyProjectID == $id ? 'active' : '';
            $html .= "<div class='col-md-4 col-sm-6'><a href='javascript:;' data-id=$id class='nobr $active'>" . html::icon($this->lang->icons['project'], 'text-muted') . $name . "</a></div>";
        }
        echo $html;
    }

    /**
     * Update program order.
     *
     * @access public
     * @return string
     */
    public function updateOrder()
    {
        $programs = $this->post->programs;
        foreach($programs as $id => $order)
        {
            $this->dao->update(TABLE_PROJECT)
                ->set('`order`')->eq($order)
                ->where('id')->eq($id)
                ->exec();
        }

        $this->send(array('result' => 'success'));
    }

    /*
     * Removing users from the white list.
     *
     * @param  int     $id
     * @param  string  $confirm
     * @access public
     * @return void
     */
    public function unbindWhitelist($id = 0, $confirm = 'no')
    {
        echo $this->fetch('personnel', 'unbindWhitelist', "id=$id&confirm=$confirm");
    }

    /**
     * View a program.
     *
     * @param int $programID
     * @access public
     * @return void
     */
    public function view($programID)
    {
        $programID = (int)$programID;
        $program   = $this->program->getByID($programID);
        if(!$program) return print(js::error($this->lang->notFound) . js::locate('back'));

        echo $this->fetch('program', 'product', "programID=$programID");
    }

    /**
     * Ajax set show setting.
     *
     * @access public
     * @return void
     */
    public function ajaxSetShowSetting()
    {
        $data = fixer::input('post')->get();
        $this->loadModel('setting')->updateItem("{$this->app->user->account}.program.showAllProjects", $data->showAllProjects);
    }
}
