<?php

global $lang, $app;

# 钉钉通知过滤的人员，支持钉钉手机号、禅道account、禅道realname
$config->story->excludeUsers = $config->excludeUsers;

// 配置ding群配置的Robot的webhook
$config->story->url['dingRobotSend'] = 'https://oapi.dingtalk.com/robot/send?access_token=342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27';
$config->story->url['dingRobotSendPD'] = 'https://oapi.dingtalk.com/robot/send?access_token=342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27';
$config->story->url['dingRobotSendSA'] = 'https://oapi.dingtalk.com/robot/send?access_token=342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27';

// $config->story->bzCategories   = array('T10', 'B100', 'KA', 'SMB');
// $config->story->prCategories   = array('project', 'product', 'myself', 'tech');
// $config->story->bzCategories   = array('B100', 'B101-500', 'LKA');
// $config->story->prCategories   = array('project', 'product', 'new', 'myself', 'platform', 'tech', 'other');
// $config->story->responseResultes   = array(0,1,2,3);
// $config->story->purchasers = array();

$config->story->bzCategories = array_keys($lang->story->bzCategoryList);
$config->story->prCategories = array_keys($lang->story->prCategoryList);
$config->story->responseResultes = array_keys($lang->story->responseResultList);
$config->story->purchasers = array_keys($lang->story->purchaserList);
$config->story->prLevels = array_keys($lang->story->prLevelList);


$config->story->create->requiredFields = 'product,title,prCategory,mailto';
$config->story->edit->requiredFields = 'product,title,prCategory';
$config->story->change->requiredFields = 'product,title,prCategory';


$config->story->exportFields = '
    id, product, branch, module, purchaser, bzCategory, prCategory, uatDate, responseResult, plan, source, sourceNote, title, spec, verify, keywords,responseResult,
    pri, estimate, status, stage, category, taskCountAB, bugCountAB, caseCountAB,
    openedBy, openedDate, assignedTo, assignedDate, mailto,
    reviewedBy, reviewedDate,
    closedBy, closedDate, closedReason,
    lastEditedBy, lastEditedDate,
    rspRecievedTime, rspResearchTime, rspAcceptTime, rspRejectTime,scoreNum,
    childStories, linkStories, duplicateStory, files';

$config->story->list->customCreateFields = 'warning,planReleaseDate,purchaser,bzCategory,prCategory,uatDate,source,verify,pri';
$config->story->list->customBatchCreateFields = 'purchaser,bzCategory,prCategory,uatDate,spec,source,verify,pri';
$config->story->list->customBatchEditFields = 'purchaser,bzCategory,prCategory,uatDate,pri,assignedTo,rearDays,frontDays,testDays';

$config->story->custom->createFields = $config->story->list->customCreateFields;
$config->story->custom->batchCreateFields = 'purchaser,bzCategory,prCategory,uatDate,spec,pri,%s';
$config->story->custom->batchEditFields = 'purchaser,bzCategory,prCategory,uatDate,pri,assignedTo,rearDays,frontDays,testDays';

$config->story->datatable->defaultField = array('id', 'title', 'pri', 'plan', 'status', 'openedBy', 'warning', 'stage', 'assignedTo', 'taskCount', 'actions');
if ($app->tab == 'execution') {
    $config->story->datatable->defaultField = array('id', 'order', 'pri', 'title', 'purchaser', 'uatDate', 'openedBy', 'assignedTo', 'status', 'stage', 'taskCount', 'actions');
} else {
    $config->story->datatable->defaultField = array('id', 'pri', 'title', 'purchaser', 'warning', 'uatDate', 'openedBy', 'assignedTo', 'status', 'stage', 'taskCount', 'actions');

}

$config->story->datatable->fieldList['id']['width'] = '30';
$config->story->datatable->fieldList['pri']['width'] = '20';
$config->story->datatable->fieldList['title']['width'] = 'auto';
$config->story->datatable->fieldList['module']['width'] = '50';

$config->story->datatable->fieldList['planReleaseDate']['title'] = 'planReleaseDate';
$config->story->datatable->fieldList['planReleaseDate']['fixed'] = 'left';
$config->story->datatable->fieldList['planReleaseDate']['width'] = '50';
$config->story->datatable->fieldList['planReleaseDate']['required'] = 'no';

$config->story->datatable->fieldList['prdReviewTime']['title'] = 'prdReviewTime';
$config->story->datatable->fieldList['prdReviewTime']['fixed'] = 'left';
$config->story->datatable->fieldList['prdReviewTime']['width'] = '50';
$config->story->datatable->fieldList['prdReviewTime']['required'] = 'no';

$config->story->datatable->fieldList['releaseTime']['title'] = 'releaseTime';
$config->story->datatable->fieldList['releaseTime']['fixed'] = 'left';
$config->story->datatable->fieldList['releaseTime']['width'] = '50';
$config->story->datatable->fieldList['releaseTime']['required'] = 'no';

$config->story->datatable->fieldList['warning']['title'] = 'warning';
$config->story->datatable->fieldList['warning']['fixed'] = 'left';
$config->story->datatable->fieldList['warning']['width'] = '50';
$config->story->datatable->fieldList['warning']['required'] = 'no';
$config->story->datatable->fieldList['warning']['control'] = 'select';
$config->story->datatable->fieldList['warning']['dataSource'] = $lang->story->warningList;

$config->story->datatable->fieldList['bzCategory']['title'] = 'bzCategory';
$config->story->datatable->fieldList['bzCategory']['fixed'] = 'left';
$config->story->datatable->fieldList['bzCategory']['width'] = '50';
$config->story->datatable->fieldList['bzCategory']['required'] = 'no';
$config->story->datatable->fieldList['bzCategory']['control'] = 'select';
$config->story->datatable->fieldList['bzCategory']['dataSource'] = $config->story->bzCategories;

$config->story->datatable->fieldList['prCategory']['title'] = 'prCategory';
$config->story->datatable->fieldList['prCategory']['fixed'] = 'left';
$config->story->datatable->fieldList['prCategory']['width'] = '50';
$config->story->datatable->fieldList['prCategory']['required'] = 'no';
$config->story->datatable->fieldList['prCategory']['control'] = 'select';
$config->story->datatable->fieldList['prCategory']['dataSource'] = $lang->story->prCategoryList;

$config->story->datatable->fieldList['uatDate']['title'] = 'uatDate';
$config->story->datatable->fieldList['uatDate']['fixed'] = 'left';
$config->story->datatable->fieldList['uatDate']['width'] = '50';
$config->story->datatable->fieldList['uatDate']['required'] = 'no';

$config->story->datatable->fieldList['purchaser']['title'] = 'purchaser';
$config->story->datatable->fieldList['purchaser']['fixed'] = 'left';
$config->story->datatable->fieldList['purchaser']['width'] = '90';
$config->story->datatable->fieldList['purchaser']['required'] = 'no';
$config->story->datatable->fieldList['purchaser']['control'] = 'select';
$config->story->datatable->fieldList['purchaser']['dataSource'] = $config->story->purchasers;

$config->story->datatable->fieldList['bizProject']['title'] = 'bizProject';
$config->story->datatable->fieldList['bizProject']['fixed'] = 'left';
$config->story->datatable->fieldList['bizProject']['width'] = '50';
$config->story->datatable->fieldList['bizProject']['required'] = 'no';
$config->story->datatable->fieldList['bizProject']['control'] = 'select';
$config->story->datatable->fieldList['bizProject']['dataSource'] = array('module' => 'project', 'method' => 'getPairsListForB100', 'params' => 'B100项目集&project');

$config->story->datatable->fieldList['responseResult']['title'] = 'responseResult';
$config->story->datatable->fieldList['responseResult']['fixed'] = 'left';
$config->story->datatable->fieldList['responseResult']['width'] = '50';
$config->story->datatable->fieldList['responseResult']['required'] = 'no';
$config->story->datatable->fieldList['responseResult']['control'] = 'select';
$config->story->datatable->fieldList['responseResult']['dataSource'] = $config->story->responseResultes;

$config->story->datatable->fieldList['prLevel']['title'] = 'prLevel';
$config->story->datatable->fieldList['prLevel']['fixed'] = 'no';
$config->story->datatable->fieldList['prLevel']['width'] = '50';
$config->story->datatable->fieldList['prLevel']['required'] = 'no';
$config->story->datatable->fieldList['prLevel']['control'] = 'select';
$config->story->datatable->fieldList['prLevel']['dataSource'] = $config->story->prLevels;

$config->story->datatable->fieldList['rspRecievedTime']['title'] = 'rspRecievedTime';
$config->story->datatable->fieldList['rspRecievedTime']['fixed'] = 'no';
$config->story->datatable->fieldList['rspRecievedTime']['width'] = '50';
$config->story->datatable->fieldList['rspRecievedTime']['required'] = 'no';

$config->story->datatable->fieldList['rspResearchTime']['title'] = 'rspResearchTime';
$config->story->datatable->fieldList['rspResearchTime']['fixed'] = 'no';
$config->story->datatable->fieldList['rspResearchTime']['width'] = '50';
$config->story->datatable->fieldList['rspResearchTime']['required'] = 'no';

$config->story->datatable->fieldList['rspAcceptTime']['title'] = 'rspAcceptTime';
$config->story->datatable->fieldList['rspAcceptTime']['fixed'] = 'no';
$config->story->datatable->fieldList['rspAcceptTime']['width'] = '50';
$config->story->datatable->fieldList['rspAcceptTime']['required'] = 'no';

$config->story->datatable->fieldList['rspRejectTime']['title'] = 'rspRejectTime';
$config->story->datatable->fieldList['rspRejectTime']['fixed'] = 'no';
$config->story->datatable->fieldList['rspRejectTime']['width'] = '50';
$config->story->datatable->fieldList['rspRejectTime']['required'] = 'no';


$config->story->datatable->fieldList['asort']['title'] = 'asort';
$config->story->datatable->fieldList['asort']['fixed'] = 'left';
$config->story->datatable->fieldList['asort']['width'] = '40';
$config->story->datatable->fieldList['asort']['required'] = 'no';


$config->story->datatable->fieldList['scoreNum']['title'] = 'scoreNum';
$config->story->datatable->fieldList['scoreNum']['fixed'] = 'left';
$config->story->datatable->fieldList['scoreNum']['width'] = '40';
$config->story->datatable->fieldList['scoreNum']['required'] = 'no';

$config->story->datatable->fieldList['workType']['title'] = 'workType';
$config->story->datatable->fieldList['workType']['fixed'] = 'left';
$config->story->datatable->fieldList['workType']['width'] = '50';
$config->story->datatable->fieldList['workType']['required'] = 'no';

$config->story->datatable->fieldList['rearDays']['title'] = 'rearDays';
$config->story->datatable->fieldList['rearDays']['fixed'] = 'left';
$config->story->datatable->fieldList['rearDays']['width'] = '50';
$config->story->datatable->fieldList['rearDays']['required'] = 'no';
$config->story->datatable->fieldList['frontDays']['title'] = 'frontDays';
$config->story->datatable->fieldList['frontDays']['fixed'] = 'left';
$config->story->datatable->fieldList['frontDays']['width'] = '50';
$config->story->datatable->fieldList['frontDays']['required'] = 'no';
$config->story->datatable->fieldList['testDays']['title'] = 'testDays';
$config->story->datatable->fieldList['testDays']['fixed'] = 'left';
$config->story->datatable->fieldList['testDays']['width'] = '50';
$config->story->datatable->fieldList['testDays']['required'] = 'no';

