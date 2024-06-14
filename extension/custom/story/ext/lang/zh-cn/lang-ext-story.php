<?php
global $config;

// 2023-4-18 22:41:56
$lang->story->planReleaseDate = '计划上线日期';
$lang->story->warning = '预警级别';
$lang->story->warningList = array();
$lang->story->warningList['0'] = '无';
$lang->story->warningList['1'] = '红';
$lang->story->warningList['2'] = '橙';
$lang->story->warningList['3'] = '黄';
$lang->story->warningList['4'] = '蓝';


$lang->story->report->charts['planReleaseDate'] = "按{$lang->story->planReleaseDate}来进行统计";
$lang->story->report->planReleaseDate = new stdclass();
$lang->story->report->planReleaseDate->item = $lang->story->planReleaseDate;
$lang->story->report->planReleaseDate->graph = new stdclass();
$lang->story->report->planReleaseDate->graph->xAxisName = $lang->story->planReleaseDate;

$lang->story->report->charts['warning'] = "按{$lang->story->warning}来进行统计";
$lang->story->report->warning = new stdclass();
$lang->story->report->warning->item = $lang->story->warning;
$lang->story->report->warning->graph = new stdclass();
$lang->story->report->warning->graph->xAxisName = $lang->story->warning;


// move to group/ext/lang/zh-cn/story.php

$lang->story->bzCategory = '客户分层';
$lang->story->bzCategoryList = array();
$lang->story->bzCategoryList[''] = '';
$lang->story->bzCategoryList['B5'] = 'B5';
$lang->story->bzCategoryList['B100'] = 'B100';
$lang->story->bzCategoryList['B100-1'] = 'B100-1';
$lang->story->bzCategoryList['B100-2'] = 'B100-2';
$lang->story->bzCategoryList['B100-7'] = 'B100-7';
$lang->story->bzCategoryList['B500'] = 'B500';
$lang->story->bzCategoryList['B5000'] = 'B5000';
$lang->story->bzCategoryList['LKA'] = 'B5000(LKA)';
$lang->story->bzCategoryList['SMB'] = 'SMB';
$lang->story->bzCategoryList['OKR'] = 'OKR专用';
$lang->story->prCategory = '需求分类';
$lang->story->prCategoryList = array();   // for产品需求
$lang->story->prCategoryList[''] = '';
$lang->story->prCategoryList['project'] = '共创需求';
$lang->story->prCategoryList['product'] = '功能迭代'; //标品需求
$lang->story->prCategoryList['new'] = '新产品需求'; //标品需求
$lang->story->prCategoryList['myself'] = '个性化需求'; //个性化需求
$lang->story->prCategoryList['yourself'] = '客制化需求'; //个性化需求
$lang->story->prCategoryList['platform'] = '电商平台'; //技术改造
$lang->story->prCategoryList['tech'] = '技术改造'; //技术改造
$lang->story->prCategoryList['sa_support'] = '方案支持';
$lang->story->prCategoryList['pd_support'] = '产品支持'; // 标品支持
$lang->story->prCategoryList['std_support'] = '标品支持';
$lang->story->prCategoryList['it_support'] = '技术支持';
$lang->story->prCategoryList['other'] = '其他';
$lang->story->prCategoryList['OKR'] = 'OKR专用';

$lang->story->prCategoryList0 = array();   // for业务需求
$lang->story->prCategoryList0[''] = '';
$lang->story->prCategoryList0['project'] = '共创需求';
$lang->story->prCategoryList0['product'] = '标品需求';
$lang->story->prCategoryList0['myself'] = '个性化需求';
$lang->story->prCategoryList0['tech'] = '技术改造';
$lang->story->prCategoryList0['sa_support'] = '项目支持';
$lang->story->prCategoryList0['std_support'] = '标品支持';
$lang->story->prCategoryList0['it_support'] = '技术支持';
// $lang->story->prCategoryList0['new'] = '新产品需求';
$lang->story->prCategoryList0['OKR'] = 'OKR专用';
$lang->story->prCategoryList0['other'] = '其他';
$lang->story->prCategoryList0['pre_sale'] = '售前需求';


$lang->story->uatDate = 'UAT日期';
$lang->story->purchaser = '客户名称';
$lang->story->deliveryMonth = '交付年月';


$lang->story->purchaserList = array();
//https://www.qqxiuzi.cn/zh/pinyin/
$lang->story->purchaserList['hzzmrjkjyxgs'] = '杭州正马软件科技有限公司0';
$lang->story->purchaserList['shzmrjkjyxgs'] = '上海正马软件科技有限公司0';


$lang->story->responseResult = '响应结果';
$lang->story->responseResultList = array();   // for业务需求
$lang->story->responseResultList['todo'] = '未处理';
$lang->story->responseResultList['recieved'] = '已收到';
$lang->story->responseResultList['research'] = '调研';
$lang->story->responseResultList['accept'] = '接受';
$lang->story->responseResultList['reject'] = '拒绝';
$lang->story->responseResultList['suspend'] = '挂起';

$lang->story->rspRecievedTime = '收到时间';
$lang->story->rspRejectTime = '拒绝时间';
$lang->story->rspResearchTime = '调研时间';
$lang->story->rspAcceptTime = '接受时间';


$lang->story->rearDays = '后端工期';
$lang->story->frontDays = '前端工期';
$lang->story->testDays = '测试工期';




$lang->story->responseResult0 = '处理结果';
$lang->story->responseResultList0 = array();   // for产品需求
$lang->story->responseResultList0['todo'] = '未处理';
$lang->story->responseResultList0['recieved'] = '已收到';
$lang->story->responseResultList0['research'] = '调研完成';
$lang->story->responseResultList0['prd'] = 'PRD内审通过';
$lang->story->responseResultList0['reject'] = '拒绝';
$lang->story->responseResultList0['suspend'] = '挂起';

$lang->story->rspRecievedTime0 = '收到时间';
$lang->story->rspRejectTime0 = '拒绝时间';
$lang->story->rspResearchTime0 = '调研时间';
$lang->story->rspAcceptTime0 = 'PRD通过时间';

$lang->story->prLevel = '需求等级';
$lang->story->prLevelList = array();
$lang->story->prLevelList['C'] = '小迭代(C)';
$lang->story->prLevelList['B'] = '大迭代(B)';
$lang->story->prLevelList['A'] = '新产品(A)';


$lang->story->bizProject = '项目名称';
$lang->story->asort = '绝对序';
$lang->story->scoreNum = '行为分';

$lang->story->bizStage = '业务场景';
$lang->story->bizNodus = '业务痛点';
$lang->story->bizTarget = '预期目标';
$lang->story->bizValue = '萃取价值';

$lang->story->prdReviewTime = 'PRD通过日期';
$lang->story->releaseTime = '发布日期';

$lang->story->workType = '工时类型';

$lang->story->workTypeList = array();
$lang->story->workTypeList['saas'] = '标品';
$lang->story->workTypeList['self'] = '定开';
$lang->story->workTypeList['outer'] = '外包';



/* 统计报表。*/
$lang->story->report->charts['purchaser'] = "按{$lang->story->purchaser}来进行统计";
$lang->story->report->purchaser = new stdclass();
$lang->story->report->purchaser->item = $lang->story->purchaser;
$lang->story->report->purchaser->graph = new stdclass();
$lang->story->report->purchaser->graph->xAxisName = $lang->story->purchaser;

$lang->story->report->charts['prCategory'] = "按{$lang->story->prCategory}来进行统计";
$lang->story->report->prCategory = new stdclass();
$lang->story->report->prCategory->item = $lang->story->prCategory;
$lang->story->report->prCategory->graph = new stdclass();
$lang->story->report->prCategory->graph->xAxisName = $lang->story->prCategory;

$lang->story->report->charts['bzCategory'] = "按{$lang->story->bzCategory}来进行统计";
$lang->story->report->bzCategory = new stdclass();
$lang->story->report->bzCategory->item = $lang->story->bzCategory;
$lang->story->report->bzCategory->graph = new stdclass();
$lang->story->report->bzCategory->graph->xAxisName = $lang->story->bzCategory;

$lang->story->report->charts['uatDate'] = "按{$lang->story->uatDate}来进行统计";
$lang->story->report->uatDate = new stdclass();
$lang->story->report->uatDate->item = $lang->story->uatDate;
$lang->story->report->uatDate->graph = new stdclass();
$lang->story->report->uatDate->graph->xAxisName = $lang->story->uatDate;

$lang->story->report->charts['responseResult'] = "按{$lang->story->responseResult}来进行统计";
$lang->story->report->responseResult = new stdclass();
$lang->story->report->responseResult->item = $lang->story->responseResult;
$lang->story->report->responseResult->graph = new stdclass();
$lang->story->report->responseResult->graph->xAxisName = $lang->story->responseResult;


$lang->story->report->charts['prLevel'] = "按{$lang->story->prLevel}来进行统计";
$lang->story->report->prLevel = new stdclass();
$lang->story->report->prLevel->item = $lang->story->prLevel;
$lang->story->report->prLevel->graph = new stdclass();
$lang->story->report->prLevel->graph->xAxisName = $lang->story->prLevel;


$lang->story->report->charts['bizProject'] = "按{$lang->story->bizProject}来进行统计";
$lang->story->report->bizProject = new stdclass();
$lang->story->report->bizProject->item = $lang->story->bizProject;
$lang->story->report->bizProject->graph = new stdclass();
$lang->story->report->bizProject->graph->xAxisName = $lang->story->bizProject;
