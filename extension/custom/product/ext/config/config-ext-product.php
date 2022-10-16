<?php

$config->product->search['fields']['bzCategory']     = $lang->story->bzCategory;
$config->product->search['fields']['prCategory']     = $lang->story->prCategory;
$config->product->search['fields']['uatDate']     = $lang->story->uatDate;
$config->product->search['fields']['purchaser']     = $lang->story->purchaser;

$config->product->search['params']['bzCategory']         = array('operator' => '=',       'control' => 'select', 'values' => $lang->story->bzCategoryList);
$config->product->search['params']['prCategory']         = array('operator' => '=',       'control' => 'select', 'values' => $lang->story->prCategoryList);
$config->product->search['params']['uatDate']     = array('operator' => '=', 'control' => 'input', 'values' => '', 'class' => 'date');
$config->product->search['params']['purchaser']     = array('operator' => '=', 'control' => 'input', 'values' => '');

