<?php

// $config->bug->occursEnvs   = array('dev', 'test', 'pre', 'online', 'loreal', 'elc');
$config->bug->occursEnvs   = array_keys($lang->bug->occursEnvList);
$config->bug->purchasers   = array_keys($lang->bug->purchaserList);

$config->bug->create->requiredFields  = 'title,openedBuild,purchaser,occursEnv';
$config->bug->edit->requiredFields    = $config->bug->create->requiredFields . ',feedbackTime,collectTime,comment';
$config->bug->resolve->requiredFields = 'resolution,comment';
// $config->bug->confirmbug->requiredFields = 'assignedTo,comment';
$config->bug->list->defaultFields = 'id,severity,pri,title,purchaser,occursEnv,openedBy,assignedTo,resolvedBy,resolution';

$config->bug->list->allFields = 'id, module, execution, story, task,
    title, keywords, severity, pri, type, os, browser, hardware,
    purchaser, occursEnv, feedbackTime, collectTime, feedbackBy,
    found, steps, status, deadline, activatedCount, confirmed, mailto,
    openedBy, openedDate, openedBuild,
    assignedTo, assignedDate,
    resolvedBy, resolution, resolvedBuild, resolvedDate,
    closedBy, closedDate,
    duplicateBug, linkBug,
    case,
    lastEditedBy,
    lastEditedDate';

$config->bug->exportFields = 'id, product, branch, module, project, execution, story, task,
    title, keywords, severity, pri, type, os, browser,
    purchaser, occursEnv, feedbackTime, collectTime,
    steps, status, deadline, activatedCount, confirmed, mailto,
    openedBy, openedDate, openedBuild,
    assignedTo, assignedDate,
    resolvedBy, resolution, resolvedBuild, resolvedDate,
    closedBy, closedDate,
    duplicateBug, linkBug,
    case,
    lastEditedBy,
    lastEditedDate, files ,feedbackBy, notifyEmail';
if($config->systemMode == 'classic') $config->bug->exportFields = str_replace(' project,', '', $config->bug->exportFields);


$config->bug->list->customCreateFields      = 'execution,noticefeedbackBy,story,task,pri,purchaser,occursEnv,feedbackTime,collectTime,severity,os,browser,deadline,mailto,keywords';
$config->bug->list->customBatchEditFields   = 'type,severity,pri,purchaser,occursEnv,feedbackTime,collectTime,productplan,assignedTo,deadline,resolvedBy,resolution,os,browser,keywords';
if($config->systemMode == 'new')
{
    $config->bug->list->customBatchCreateFields = 'project,execution,steps,type,pri,purchaser,occursEnv,feedbackTime,collectTime,deadline,severity';
}
else
{
    $config->bug->list->customBatchCreateFields = 'execution,steps,type,pri,purchaser,occursEnv,feedbackTime,collectTime,deadline,severity';
}

$config->bug->custom->createFields      = $config->bug->list->customCreateFields;
$config->bug->custom->batchCreateFields = 'project,execution,deadline,steps,type,severity,os,browser,%s';
$config->bug->custom->batchEditFields   = 'type,severity,pri,purchaser,occursEnv,feedbackTime,collectTime,assignedTo,deadline,status,resolvedBy,resolution';

$config->bug->search['fields']['feedbackBy']  = $lang->bug->feedbackBy;
$config->bug->search['fields']['purchaser']  = $lang->bug->purchaser;
$config->bug->search['fields']['occursEnv']  = $lang->bug->occursEnv;
$config->bug->search['fields']['feedbackTime']  = $lang->bug->feedbackTime;
$config->bug->search['fields']['collectTime']  = $lang->bug->collectTime;


$config->bug->search['params']['feedbackBy']  = array('operator' => '=',       'control' => 'input',  'values' => '');
// $config->bug->search['params']['purchaser']  = array('operator' => 'include',       'control' => 'select',  'values' => $lang->bug->purchaserList);
$config->bug->search['params']['purchaser']  = array('operator' => 'include',       'control' => 'select',  'values' => 'purchaserList');
$config->bug->search['params']['occursEnv']        = array('operator' => 'include',       'control' => 'select', 'values' => $lang->bug->occursEnvList);
$config->bug->search['params']['feedbackTime']  = array('operator' => '=',       'control' => 'input',  'values' => '');
$config->bug->search['params']['collectTime']  = array('operator' => '=',       'control' => 'input',  'values' => '');

$config->bug->datatable->defaultField = array('id', 'severity', 'pri', 'confirmed', 'title', 'purchaser', 'occursEnv', 'status', 'openedBy', 'openedDate', 'assignedTo', 'resolution', 'actions');


$config->bug->datatable->fieldList['purchaser']['title']    = 'purchaser';
$config->bug->datatable->fieldList['purchaser']['fixed']    = 'left';
$config->bug->datatable->fieldList['purchaser']['width']    = 'auto';
$config->bug->datatable->fieldList['purchaser']['required'] = 'yes';
$config->bug->datatable->fieldList['purchaser']['control']    = 'select';
$config->bug->datatable->fieldList['purchaser']['dataSource'] = $config->bug->purchasers;

$config->bug->datatable->fieldList['occursEnv']['title']    = 'occursEnv';
$config->bug->datatable->fieldList['occursEnv']['fixed']    = 'left';
$config->bug->datatable->fieldList['occursEnv']['width']    = 'auto';
$config->bug->datatable->fieldList['occursEnv']['required'] = 'no';
$config->bug->datatable->fieldList['occursEnv']['control']    = 'select';
$config->bug->datatable->fieldList['occursEnv']['dataSource'] = $config->bug->occursEnvs;

$config->bug->datatable->fieldList['feedbackBy']['title']    = 'feedbackBy';
$config->bug->datatable->fieldList['feedbackBy']['fixed']    = 'left';
$config->bug->datatable->fieldList['feedbackBy']['width']    = 'auto';
$config->bug->datatable->fieldList['feedbackBy']['required'] = 'no';

$config->bug->datatable->fieldList['feedbackTime']['title']    = 'feedbackTime';
$config->bug->datatable->fieldList['feedbackTime']['fixed']    = 'left';
$config->bug->datatable->fieldList['feedbackTime']['width']    = 'auto';
$config->bug->datatable->fieldList['feedbackTime']['required'] = 'no';

$config->bug->datatable->fieldList['collectTime']['title']    = 'collectTime';
$config->bug->datatable->fieldList['collectTime']['fixed']    = 'left';
$config->bug->datatable->fieldList['collectTime']['width']    = 'auto';
$config->bug->datatable->fieldList['collectTime']['required'] = 'no';

