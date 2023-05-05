<?php

global $lang, $app;


$config->project->list->allFields = $config->project->list->allFields . ", contractNo, devEvaluate";

$config->project->exportFields = $config->project->exportFields . ", contractNo, devEvaluate";

$config->project->search['fields']['contractNo'] = $lang->project->contractNo;
$config->project->search['params']['contractNo'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['fields']['devEvaluate'] = $lang->project->devEvaluate;
$config->project->search['params']['devEvaluate'] = array('operator' => '=', 'control' => 'input', 'values' => '');

$config->project->datatable->defaultField = array('id', 'name', 'status', 'PM', 'contractNo', 'devEvaluate', 'budget', 'begin', 'end', 'progress', 'actions');


$config->project->datatable->fieldList['contractNo']['title'] = 'contractNo';
$config->project->datatable->fieldList['contractNo']['fixed'] = 'left';
$config->project->datatable->fieldList['contractNo']['width'] = 'auto';
$config->project->datatable->fieldList['contractNo']['required'] = 'no';

$config->project->datatable->fieldList['devEvaluate']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['devEvaluate']['fixed'] = 'left';
$config->project->datatable->fieldList['devEvaluate']['width'] = 'auto';
$config->project->datatable->fieldList['devEvaluate']['required'] = 'no';
