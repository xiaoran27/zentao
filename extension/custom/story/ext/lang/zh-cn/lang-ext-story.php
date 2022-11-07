<?php

$lang->story->bzCategory = '客户分层';
$lang->story->bzCategoryList = array();
$lang->story->bzCategoryList['']  = '';
//$lang->story->bzCategoryList['T10']  = '灯塔客户';
$lang->story->bzCategoryList['B100'] = '头部客户';
$lang->story->bzCategoryList['B101-500'] = '次头部客户';
$lang->story->bzCategoryList['LKA'] = '区域客户';
$lang->story->bzCategoryList['SMB'] = '普通客户';
//$lang->story->bzCategoryList['KA']  = '重要客户';
//$lang->story->bzCategoryList['SMB'] = '腰部客户';
$lang->story->prCategory = '需求分类';
$lang->story->prCategoryList = array();
$lang->story->prCategoryList['']  = '';
$lang->story->prCategoryList['project']  = '共创需求';
$lang->story->prCategoryList['product'] = '标品需求';
$lang->story->prCategoryList['myself']  = '个性化需求';
$lang->story->prCategoryList['new'] = '新产品需求';
$lang->story->prCategoryList['platform'] = '电商平台';
$lang->story->prCategoryList['tech'] = '技术改造';
$lang->story->prCategoryList['other'] = '其他';

$lang->story->prCategoryList0 = array();
$lang->story->prCategoryList0['']  = '';
$lang->story->prCategoryList0['project']  = '共创需求';
$lang->story->prCategoryList0['product'] = '标品需求';
$lang->story->prCategoryList0['myself']  = '个性化需求';
// $lang->story->prCategoryList0['new'] = '新产品需求';


$lang->story->uatDate = 'UAT日期';
$lang->story->purchaser = '客户名称';


$lang->story->purchaserList  = array();
//https://www.qqxiuzi.cn/zh/pinyin/
$lang->story->purchaserList['hzzmrjkjyxgs']  = '杭州正马软件科技有限公司0';
$lang->story->purchaserList['shzmrjkjyxgs']  = '上海正马软件科技有限公司0';
$lang->story->purchaserList['gzolybkwlkjyxgs']  = '广州欧莱雅百库网络科技有限公司';
$lang->story->purchaserList['bxjydq(zg)yxgs']  = '博西家用电器（中国）有限公司';
$lang->story->purchaserList['bjsfhwcyfzyxgs']  = '北京三夫户外产业发展有限公司';



$lang->story->responseResult = '响应结果';
$lang->story->responseResultList = array();
$lang->story->responseResultList[0]  = '未处理';
$lang->story->responseResultList[1]  = '拒绝';
$lang->story->responseResultList[2]  = '调研';
$lang->story->responseResultList[3]  = '接受';


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
$lang->story->report->uatDresponseResultate->item       = $lang->story->responseResult;
$lang->story->report->responseResult->graph       = new stdclass();
$lang->story->report->responseResult->graph->xAxisName      = $lang->story->responseResult;

