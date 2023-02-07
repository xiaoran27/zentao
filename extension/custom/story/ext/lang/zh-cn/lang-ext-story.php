<?php
global $config;

$lang->story->bzCategory = '客户分层';
$lang->story->bzCategoryList = array();
$lang->story->bzCategoryList['']  = '';
$lang->story->bzCategoryList['B5']  = '灯塔客户';
$lang->story->bzCategoryList['B100'] = '头部客户';
// $lang->story->bzCategoryList['B101-500'] = '次头部客户';
$lang->story->bzCategoryList['B500'] = '次头部客户';
$lang->story->bzCategoryList['LKA'] = '区域客户';
$lang->story->bzCategoryList['SMB'] = '普通客户';
$lang->story->bzCategoryList['OKR'] = 'OKR专用';
//$lang->story->bzCategoryList['KA']  = '重要客户';
//$lang->story->bzCategoryList['SMB'] = '腰部客户';
$lang->story->prCategory = '需求分类';
$lang->story->prCategoryList = array();
$lang->story->prCategoryList['']  = '';
$lang->story->prCategoryList['project']  = '共创需求';
$lang->story->prCategoryList['product'] = '功能迭代';
$lang->story->prCategoryList['new'] = '新产品需求';
$lang->story->prCategoryList['myself']  = '个性化需求';
$lang->story->prCategoryList['yourself']  = '客制化需求';
$lang->story->prCategoryList['platform'] = '电商平台';
$lang->story->prCategoryList['tech'] = '技术改造';
$lang->story->prCategoryList['sa_support']  = '方案支持';
$lang->story->prCategoryList['pd_support']  = '产品支持';
$lang->story->prCategoryList['std_support']  = '标品支持';
$lang->story->prCategoryList['it_support']  = '技术支持';
$lang->story->prCategoryList['other'] = '其他';
$lang->story->prCategoryList['OKR'] = 'OKR专用';

$lang->story->prCategoryList0 = array();
$lang->story->prCategoryList0['']  = '';
$lang->story->prCategoryList0['product'] = '标品需求';
$lang->story->prCategoryList0['project']  = '共创需求';
$lang->story->prCategoryList0['myself']  = '个性化需求';
$lang->story->prCategoryList0['sa_support']  = '项目支持';
$lang->story->prCategoryList0['std_support']  = '标品支持';
$lang->story->prCategoryList0['it_support']  = '技术支持';
// $lang->story->prCategoryList0['new'] = '新产品需求';
$lang->story->prCategoryList0['OKR'] = 'OKR专用';


$lang->story->uatDate = 'UAT日期';
$lang->story->purchaser = '客户名称';


$lang->story->purchaserList  = array();
//https://www.qqxiuzi.cn/zh/pinyin/
$lang->story->purchaserList['hzzmrjkjyxgs']  = '杭州正马软件科技有限公司0';
$lang->story->purchaserList['shzmrjkjyxgs']  = '上海正马软件科技有限公司0';


$lang->story->responseResult = '响应结果';
$lang->story->responseResultList = array();
$lang->story->responseResultList['todo']  = '未处理';
$lang->story->responseResultList['recieved']  = '已接到';
$lang->story->responseResultList['research']  = '调研';
$lang->story->responseResultList['accept']  = '接受';
$lang->story->responseResultList['reject']  = '拒绝';
$lang->story->responseResultList['suspend']  = '挂起';

$lang->story->rspRecievedTime = '接收时间';
$lang->story->rspRejectTime = '拒绝时间';
$lang->story->rspResearchTime = '调研时间';
$lang->story->rspAcceptTime = '接受时间';

$lang->story->prLevel = '需求等级';
$lang->story->prLevelList = array();
$lang->story->prLevelList['C']  = '小迭代(C)';
$lang->story->prLevelList['B'] = '大迭代(B)';
$lang->story->prLevelList['A']  = '新产品(A)';


$lang->story->bizProject = '项目名称';

$lang->story->asort = '绝对序';

$lang->story->bizStage = '业务场景';
$lang->story->bizNodus = '业务痛点';
$lang->story->bizTarget = '达成目标';
$lang->story->bizValue = '萃取价值';


/* 统计报表。*/
$lang->story->report->charts['purchaser']         = "按{$lang->story->purchaser}来进行统计";
$lang->story->report->purchaser       = new stdclass();
$lang->story->report->purchaser->item       = $lang->story->purchaser;
$lang->story->report->purchaser->graph       = new stdclass();
$lang->story->report->purchaser->graph->xAxisName      = $lang->story->purchaser;

$lang->story->report->charts['prCategory']         = "按{$lang->story->prCategory}来进行统计";
$lang->story->report->prCategory       = new stdclass();
$lang->story->report->prCategory->item       = $lang->story->prCategory;
$lang->story->report->prCategory->graph       = new stdclass();
$lang->story->report->prCategory->graph->xAxisName      = $lang->story->prCategory;

$lang->story->report->charts['bzCategory']         = "按{$lang->story->bzCategory}来进行统计";
$lang->story->report->bzCategory       = new stdclass();
$lang->story->report->bzCategory->item       = $lang->story->bzCategory;
$lang->story->report->bzCategory->graph       = new stdclass();
$lang->story->report->bzCategory->graph->xAxisName      = $lang->story->bzCategory;

$lang->story->report->charts['uatDate']         = "按{$lang->story->uatDate}来进行统计";
$lang->story->report->uatDate       = new stdclass();
$lang->story->report->uatDate->item       = $lang->story->uatDate;
$lang->story->report->uatDate->graph       = new stdclass();
$lang->story->report->uatDate->graph->xAxisName      = $lang->story->uatDate;

$lang->story->report->charts['responseResult']         = "按{$lang->story->responseResult}来进行统计";
$lang->story->report->responseResult       = new stdclass();
$lang->story->report->responseResult->item       = $lang->story->responseResult;
$lang->story->report->responseResult->graph       = new stdclass();
$lang->story->report->responseResult->graph->xAxisName      = $lang->story->responseResult;


$lang->story->report->charts['prLevel']         = "按{$lang->story->prLevel}来进行统计";
$lang->story->report->prLevel       = new stdclass();
$lang->story->report->prLevel->item       = $lang->story->prLevel;
$lang->story->report->prLevel->graph       = new stdclass();
$lang->story->report->prLevel->graph->xAxisName      = $lang->story->prLevel;


$lang->story->report->charts['bizProject']         = "按{$lang->story->bizProject}来进行统计";
$lang->story->report->bizProject       = new stdclass();
$lang->story->report->bizProject->item       = $lang->story->bizProject;
$lang->story->report->bizProject->graph       = new stdclass();
$lang->story->report->bizProject->graph->xAxisName      = $lang->story->bizProject;
