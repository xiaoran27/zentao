<?php
global $lang, $app;

$config->product->search['fields']['planReleaseDate']     = $lang->story->planReleaseDate;
$config->product->search['params']['planReleaseDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');

$config->product->search['fields']['warning']     = $lang->story->warning;
$config->product->search['params']['warning']     = array('operator' => '=', 'control' => 'multi-select', 'values' => (array(''=>'')+$lang->story->warningList) );



$config->product->search['fields']['bzCategory']     = $lang->story->bzCategory;
$config->product->search['fields']['prCategory']     = $lang->story->prCategory;
$config->product->search['fields']['uatDate']     = $lang->story->uatDate;
$config->product->search['fields']['purchaser']     = $lang->story->purchaser;
$config->product->search['fields']['responseResult']     = $lang->story->responseResult;

$config->product->search['params']['bzCategory']         = array('operator' => '=',       'control' => 'multi-select', 'values' => ( array(''=>'') + $lang->story->bzCategoryList) );
$config->product->search['params']['prCategory']         = array('operator' => '=',       'control' => 'multi-select', 'values' => (array(''=>'') + $lang->story->prCategoryList) );
$config->product->search['params']['uatDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['params']['purchaser']     = array('operator' => '=', 'control' => 'multi-select', 'values' => (array(''=>'') + $lang->story->purchaserList) );
// $config->product->search['params']['purchaser']     = array('operator' => '=', 'control' => 'input', 'values' => 'purchasers');
$config->product->search['params']['responseResult']         = array('operator' => '=',       'control' => 'multi-select', 'values' => (array(''=>'') + $lang->story->responseResultList) );


$config->product->search['fields']['prLevel']     = $lang->story->prLevel;
$config->product->search['params']['prLevel']     = array('operator' => '=', 'control' => 'multi-select', 'values' => (array(''=>'')+$lang->story->prLevelList) );

$config->product->search['fields']['bizProject']     = $lang->story->bizProject;
$config->product->search['params']['bizProject']     = array('operator' => '=', 'control' => 'multi-select', 'values' => 'bizProjects');

$config->product->search['fields']['asort']     = $lang->story->asort;
$config->product->search['params']['asort']     = array('operator' => 'include', 'control' => 'input');


$config->product->search['fields']['rspRecievedTime']     = $lang->story->rspRecievedTime;
$config->product->search['params']['rspRecievedTime']     = array('operator' => '>=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['fields']['rspRejectTime']     = $lang->story->rspRejectTime;
$config->product->search['params']['rspRejectTime']     = array('operator' => '>=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['fields']['rspResearchTime']     = $lang->story->rspResearchTime;
$config->product->search['params']['rspResearchTime']     = array('operator' => '>=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['fields']['rspAcceptTime']     = $lang->story->rspAcceptTime;
$config->product->search['params']['rspAcceptTime']     = array('operator' => '>=', 'control' => 'input', 'values' => '', 'class' => 'date');

$config->product->search['fields']['scoreNum']     = $lang->story->scoreNum;
$config->product->search['params']['scoreNum']     = array('operator' => '>=', 'control' => 'input', 'values' => '');

$config->product->search['fields']['workType']     = $lang->story->workType;
$config->product->search['params']['workType']     = array('operator' => '=', 'control' => 'multi-select', 'values' => (array(''=>'')+$lang->story->workTypeList) );
$config->product->search['fields']['prdReviewTime']     = $lang->story->prdReviewTime;
$config->product->search['params']['prdReviewTime']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['fields']['releaseTime']     = $lang->story->releaseTime;
$config->product->search['params']['releaseTime']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
