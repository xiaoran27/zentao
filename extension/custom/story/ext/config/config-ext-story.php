<?php

// $config->story->bzCategories   = array('T10', 'B100', 'KA', 'SMB');
// $config->story->prCategories   = array('project', 'product', 'myself', 'tech');
$config->story->bzCategories   = array('B100', 'B101-500', 'LKA');
$config->story->prCategories   = array('project', 'product', 'new', 'myself', 'platform', 'tech', 'other');
$config->story->responseResultes   = array(0,1,2,3);


$config->story->create->requiredFields = 'title,bzCategory,prCategory';
$config->story->edit->requiredFields = 'title,bzCategory,prCategory';
$config->story->change->requiredFields = 'title,bzCategory,prCategory';


$config->story->exportFields = '
    id, product, branch, module, purchaser, bzCategory, prCategory, uatDate, responseResult, plan, source, sourceNote, title, spec, verify, keywords,responseResult,
    pri, estimate, status, stage, category, taskCountAB, bugCountAB, caseCountAB,
    openedBy, openedDate, assignedTo, assignedDate, mailto,
    reviewedBy, reviewedDate,
    closedBy, closedDate, closedReason,
    lastEditedBy, lastEditedDate,
    childStories, linkStories, duplicateStory, files';

$config->story->list->customCreateFields      = 'purchaser,bzCategory,prCategory,uatDate,source,verify,pri,estimate,mailto,keywords';
$config->story->list->customBatchCreateFields = 'plan,purchaser,bzCategory,prCategory,uatDate,spec,source,verify,pri,estimate,review,keywords';
$config->story->list->customBatchEditFields   = 'branch,plan,purchaser,bzCategory,prCategory,uatDate, estimate,pri,assignedTo,source,stage,closedBy,closedReason,keywords';

$config->story->custom->batchCreateFields = 'module,plan,purchaser,bzCategory,prCategory,uatDate,spec,pri,estimate,review,%s';
$config->story->custom->batchEditFields   = 'branch,module,plan,purchaser,bzCategory,prCategory,uatDate,estimate,pri,source,stage,closedBy,closedReason';


if($app->tab == 'execution')
{
    $config->story->datatable->defaultField = array('id','order', 'pri', 'title', 'purchaser', 'bzCategory', 'prCategory', 'uatDate', 'plan', 'openedBy', 'assignedTo', 'estimate', 'status', 'stage', 'taskCount', 'actions');
}
else
{
    $config->story->datatable->defaultField = array('id', 'pri', 'title', 'purchaser', 'bzCategory','prCategory', 'uatDate', 'plan', 'openedBy', 'assignedTo', 'estimate', 'status', 'stage', 'taskCount', 'actions');
}


$config->story->datatable->fieldList['bzCategory']['title']    = 'bzCategory';
$config->story->datatable->fieldList['bzCategory']['fixed']    = 'left';
$config->story->datatable->fieldList['bzCategory']['width']    = '90';
$config->story->datatable->fieldList['bzCategory']['required'] = 'yes';
$config->story->datatable->fieldList['bzCategory']['control']    = 'select';
$config->story->datatable->fieldList['bzCategory']['dataSource'] = $config->story->bzCategories;

$config->story->datatable->fieldList['prCategory']['title']    = 'prCategory';
$config->story->datatable->fieldList['prCategory']['fixed']    = 'left';
$config->story->datatable->fieldList['prCategory']['width']    = '90';
$config->story->datatable->fieldList['prCategory']['required'] = 'yes';
$config->story->datatable->fieldList['prCategory']['control']    = 'select';
$config->story->datatable->fieldList['prCategory']['dataSource'] = $config->story->prCategories;

$config->story->datatable->fieldList['uatDate']['title']    = 'uatDate';
$config->story->datatable->fieldList['uatDate']['fixed']    = 'left';
$config->story->datatable->fieldList['uatDate']['width']    = '90';
$config->story->datatable->fieldList['uatDate']['required'] = 'no';

$config->story->datatable->fieldList['purchaser']['title']    = 'purchaser';
$config->story->datatable->fieldList['purchaser']['fixed']    = 'left';
$config->story->datatable->fieldList['purchaser']['width']    = '90';
$config->story->datatable->fieldList['purchaser']['required'] = 'yes';

$config->story->datatable->fieldList['responseResult']['title']    = 'responseResult';
$config->story->datatable->fieldList['responseResult']['fixed']    = 'left';
$config->story->datatable->fieldList['responseResult']['width']    = '90';
$config->story->datatable->fieldList['responseResult']['required'] = 'no';
$config->story->datatable->fieldList['responseResult']['control']    = 'select';
$config->story->datatable->fieldList['responseResult']['dataSource'] = $config->story->responseResultes;
