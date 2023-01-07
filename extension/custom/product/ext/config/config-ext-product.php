<?php

$config->product->search['fields']['bzCategory']     = $lang->story->bzCategory;
$config->product->search['fields']['prCategory']     = $lang->story->prCategory;
$config->product->search['fields']['uatDate']     = $lang->story->uatDate;
$config->product->search['fields']['purchaser']     = $lang->story->purchaser;
$config->product->search['fields']['responseResult']     = $lang->story->responseResult;



$config->product->search['params']['bzCategory']         = array('operator' => '=',       'control' => 'select', 'values' => ( array(''=>'') + $lang->story->bzCategoryList) );
$config->product->search['params']['prCategory']         = array('operator' => '=',       'control' => 'select', 'values' => (array(''=>'') + $lang->story->prCategoryList) );
$config->product->search['params']['uatDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['params']['purchaser']     = array('operator' => '=', 'control' => 'select', 'values' => (array(''=>'') + $lang->story->purchaserList) );
// $config->product->search['params']['purchaser']     = array('operator' => '=', 'control' => 'input', 'values' => 'purchasers');
$config->product->search['params']['responseResult']         = array('operator' => '=',       'control' => 'select', 'values' => (array(''=>'') + $lang->story->responseResultList) );

$config->product->search['fields']['prLevel']     = $lang->story->prLevel;
$config->product->search['params']['prLevel']     = array('operator' => '=', 'control' => 'select', 'values' => (array(''=>'')+$lang->story->prLevelList) );

$config->product->search['fields']['bizProject']     = $lang->story->bizProject;
$config->product->search['params']['bizProject']     = array('operator' => '=', 'control' => 'select', 'values' => 'bizProjects');

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