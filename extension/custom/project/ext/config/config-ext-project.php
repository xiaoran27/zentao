<?php

global $lang, $app;


$config->project->list->allFields = $config->project->list->allFields . ", contractNo, devEvaluate, poDays, outerDays, selfDays, saasDays";

$config->project->exportFields = $config->project->exportFields . ", contractNo, devEvaluate, poDays, outerDays, selfDays, saasDays";

$config->project->search['fields']['contractNo'] = $lang->project->contractNo;
$config->project->search['params']['contractNo'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['fields']['devEvaluate'] = $lang->project->devEvaluate;
$config->project->search['params']['devEvaluate'] = array('operator' => '=', 'control' => 'input', 'values' => '');

$config->project->search['fields']['poDays'] = $lang->project->poDays;
$config->project->search['params']['poDays'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['fields']['selfDays'] = $lang->project->selfDays;
$config->project->search['params']['selfDays'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['fields']['outerDays'] = $lang->project->outerDays;
$config->project->search['params']['outerDays'] = array('operator' => '=', 'control' => 'input', 'values' => '');
$config->project->search['fields']['saasDays'] = $lang->project->saasDays;
$config->project->search['params']['saasDays'] = array('operator' => '=', 'control' => 'input', 'values' => '');

$config->project->datatable->defaultField = array('id', 'name', 'status', 'PM', 'contractNo', 'devEvaluate', 'budget', 'begin', 'end', 'progress', 'actions');


$config->project->datatable->fieldList['contractNo']['title'] = 'contractNo';
$config->project->datatable->fieldList['contractNo']['fixed'] = 'left';
$config->project->datatable->fieldList['contractNo']['width'] = 'auto';
$config->project->datatable->fieldList['contractNo']['required'] = 'no';

$config->project->datatable->fieldList['devEvaluate']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['devEvaluate']['fixed'] = 'left';
$config->project->datatable->fieldList['devEvaluate']['width'] = 'auto';
$config->project->datatable->fieldList['devEvaluate']['required'] = 'no';

$config->project->datatable->fieldList['poDays']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['poDays']['fixed'] = 'left';
$config->project->datatable->fieldList['poDays']['width'] = 'auto';
$config->project->datatable->fieldList['poDays']['required'] = 'no';

$config->project->datatable->fieldList['outerDays']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['outerDays']['fixed'] = 'left';
$config->project->datatable->fieldList['outerDays']['width'] = 'auto';
$config->project->datatable->fieldList['outerDays']['required'] = 'no';

$config->project->datatable->fieldList['selfDays']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['selfDays']['fixed'] = 'left';
$config->project->datatable->fieldList['selfDays']['width'] = 'auto';
$config->project->datatable->fieldList['selfDays']['required'] = 'no';


$config->project->datatable->fieldList['saasDays']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['saasDays']['fixed'] = 'left';
$config->project->datatable->fieldList['saasDays']['width'] = 'auto';
$config->project->datatable->fieldList['saasDays']['required'] = 'no';
