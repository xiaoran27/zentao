<?php


$config->bug->create->requiredFields  = 'title,openedBuild,purchaser';
$config->bug->edit->requiredFields    = $config->bug->create->requiredFields;
$config->bug->list->defaultFields = 'id,severity,pri,title,purchaser,openedBy,assignedTo,resolvedBy,resolution';

$config->bug->list->allFields = 'id, module, execution, story, task,
    title, purchaser, keywords, severity, pri, type, os, browser, hardware,
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
    title, purchaser, keywords, severity, pri, type, os, browser,
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


$config->bug->list->customCreateFields      = 'execution,noticefeedbackBy,story,task,pri,purchaser,severity,os,browser,deadline,mailto,keywords';
$config->bug->list->customBatchEditFields   = 'type,severity,pri,purchaser,productplan,assignedTo,deadline,resolvedBy,resolution,os,browser,keywords';
if($config->systemMode == 'new')
{
    $config->bug->list->customBatchCreateFields = 'project,execution,steps,type,pri,purchaser,deadline,severity,os,browser,keywords';
}
else
{
    $config->bug->list->customBatchCreateFields = 'execution,steps,type,pri,purchaser,deadline,severity,os,browser,keywords';
}

$config->bug->search['fields']['feedbackBy']  = $lang->bug->feedbackBy;
$config->bug->search['fields']['purchaser']  = $lang->bug->purchaser;


$config->bug->search['params']['feedbackBy']  = array('operator' => '=',       'control' => 'input',  'values' => '');
$config->bug->search['params']['purchaser']  = array('operator' => '=',       'control' => 'input',  'values' => '');

$config->bug->datatable->defaultField = array('id', 'severity', 'pri', 'confirmed', 'title', 'purchaser', 'status', 'openedBy', 'openedDate', 'assignedTo', 'resolution', 'actions');


$config->bug->datatable->fieldList['purchaser']['title']    = 'purchaser';
$config->bug->datatable->fieldList['purchaser']['fixed']    = 'left';
$config->bug->datatable->fieldList['purchaser']['width']    = 'auto';
$config->bug->datatable->fieldList['purchaser']['required'] = 'yes';
