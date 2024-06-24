<?php

if (!isset($lang->gantt)) $lang->gantt = new stdclass();
$lang->gantt->title = '甘特图';
$lang->gantt->common     = '甘特图';

$lang->gantt->featureBar = array();
$lang->gantt->featureBar['browser'] = array();
$lang->gantt->featureBar['browser']['all']       = '全部';
$lang->gantt->featureBar['browser']['unclosed']  = '未关闭';
$lang->gantt->featureBar['browser']['wait']      = '未开始';
$lang->gantt->featureBar['browser']['doing']     = '进行中';
$lang->gantt->featureBar['browser']['suspended'] = '已挂起';
$lang->gantt->featureBar['browser']['closed']    = '已关闭';


$lang->gantt->projectStatusList = array( 'unclosed' => '未关闭', 'wait' => '未开始', 'doing' => '进行中', 'suspended' => '已挂起', 'closed' => '已关闭');
$lang->gantt->typeList = array( 'project' => '项目', 'execution' => '迭代', 'task' => '任务');


/* Fields. */
$lang->gantt->id             = 'ID';
$lang->gantt->name           = '名称';
$lang->gantt->milestone        = '里程碑';
$lang->gantt->story       = '产品需求ID';
$lang->gantt->realname        = '姓名';
$lang->gantt->dept_id        = '部门ID';
$lang->gantt->dept_name        = '部门';
$lang->gantt->dept_path        = '部门路径';
$lang->gantt->estimate       = '预估';
$lang->gantt->consumed           = '消耗';
$lang->gantt->progress         = '进度';
$lang->gantt->myBegin         = '开始日期';
$lang->gantt->myEnd         = '结束日期';
$lang->gantt->PM             = '项目PM';
$lang->gantt->bd         = 'BD|新建人';
$lang->gantt->sa         = 'SA|取消人';
$lang->gantt->cs         = 'CS|完成人';

$lang->gantt->status         = '状态';
$lang->gantt->stage          = '阶段';

$lang->gantt->url     = 'URL';
$lang->gantt->begin          = '计划开始';
$lang->gantt->end            = '计划完成';
$lang->gantt->realBegin      = '实际开始';
$lang->gantt->realEnd        = '实际完成';

$lang->gantt->type           = '类型';
$lang->gantt->parent         = '父项';
$lang->gantt->children         = '子数';
$lang->gantt->path   = '父PATH';
$lang->gantt->fullpath       = '全路径';
$lang->gantt->tocLevel     = '层级';


/* query. */
$lang->gantt->query = new stdclass();
$lang->gantt->queryTips         = "多个id可用英文逗号分隔";
$lang->gantt->query->programId             = '项目集ID';
$lang->gantt->query->projectId             = '项目ID';
$lang->gantt->query->projectEnd             = '项目结束';
$lang->gantt->query->task_assignTo             = '任务指派给';
$lang->gantt->query->dept_id             = '指派部门';
$lang->gantt->query->projectPM             = 'PM';
$lang->gantt->query->projectStatus             = '项目状态';
$lang->gantt->query->rowtype             = $lang->gantt->type;
$lang->gantt->query->excutionId             = '迭代ID';
$lang->gantt->query->storyId             = $lang->gantt->story;
$lang->gantt->query->task_finishedBy             = '任务完成人';
$lang->gantt->query->task_estStarted             = '任务开始';


