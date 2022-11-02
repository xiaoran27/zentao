<?php

$lang->bug->purchaser      = '客户名称';
$lang->bug->occursEnv      = '发生环境';
$lang->bug->feedbackTime      = '反馈时间';
$lang->bug->collectTime      = '收集时间';

$lang->bug->occursEnvList = array();
$lang->bug->occursEnvList['']  = '';
$lang->bug->occursEnvList['dev'] = '开发环境';
$lang->bug->occursEnvList['test'] = '测试环境';
$lang->bug->occursEnvList['pre'] = '预发环境';
$lang->bug->occursEnvList['online'] = '正式环境';
$lang->bug->occursEnvList['loreal'] = '欧莱雅环境';
$lang->bug->occursEnvList['elc'] = '雅思兰黛环境';

$lang->bug->purchaserList = array();
$lang->bug->purchaserList['']  = '';


/* 统计报表。*/
$lang->bug->report->charts['purchaser']         = "按{$lang->bug->purchaser}来进行统计";
$lang->bug->report->purchaser       = new stdclass();
$lang->bug->report->purchaser->item       = $lang->bug->purchaser;
$lang->bug->report->purchaser->graph       = new stdclass();
$lang->bug->report->purchaser->graph->xAxisName      = $lang->bug->purchaser;


$lang->bug->report->charts['occursEnv']         = "按{$lang->bug->occursEnv}(拆分)来进行统计";
$lang->bug->report->occursEnv       = new stdclass();
$lang->bug->report->occursEnv->item       = $lang->bug->occursEnv;
$lang->bug->report->occursEnv->graph       = new stdclass();
$lang->bug->report->occursEnv->graph->xAxisName      = $lang->bug->occursEnv;
