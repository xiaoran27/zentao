<?php

global $lang, $app;


// $config->project->list->allFields = $config->project->list->allFields . ", contractNo, devEvaluate, poDays, outerDays, selfDays, saasDays";

$config->project->list->exportFields = $config->project->list->exportFields . ", contractNo, devEvaluate, poDays, outerDays, selfDays, saasDays, bd, sa, cs, deciders, poAmount";
$config->project->create->requiredFields = $config->project->create->requiredFields.",bd,deciders,PM,stage";
$config->project->edit->requiredFields = $config->project->edit->requiredFields.",bd,deciders,PM,stage";

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
$config->project->search['fields']['outerPoDays'] = $lang->project->outerPoDays;
$config->project->search['params']['outerPoDays'] = array('operator' => '=', 'control' => 'input', 'values' => '');

$config->project->search['params']['stage']     = array('operator' => '='      , 'control' => 'select', 'values' => array('' => '') + $lang->project->stageList);


$config->project->search['fields']['poAmount'] = $lang->project->poAmount;
$config->project->search['params']['poAmount'] = array('operator' => '=', 'control' => 'input', 'values' => '');

$config->project->datatable->defaultField = array('id', 'name', 'status', 'PM', 'contractNo', 'devEvaluate', 'budget', 'begin', 'end', 'progress', 'actions');


$config->project->datatable->fieldList['contractNo']['title'] = 'contractNo';
$config->project->datatable->fieldList['contractNo']['fixed'] = 'left';
$config->project->datatable->fieldList['contractNo']['width'] = 'auto';
$config->project->datatable->fieldList['contractNo']['required'] = 'no';

$config->project->datatable->fieldList['devEvaluate']['title'] = 'devEvaluate';
$config->project->datatable->fieldList['devEvaluate']['fixed'] = 'left';
$config->project->datatable->fieldList['devEvaluate']['width'] = 'auto';
$config->project->datatable->fieldList['devEvaluate']['required'] = 'no';

$config->project->datatable->fieldList['poDays']['title'] = 'poDays';
$config->project->datatable->fieldList['poDays']['fixed'] = 'left';
$config->project->datatable->fieldList['poDays']['width'] = 'auto';
$config->project->datatable->fieldList['poDays']['required'] = 'no';

$config->project->datatable->fieldList['outerDays']['title'] = 'outerDays';
$config->project->datatable->fieldList['outerDays']['fixed'] = 'left';
$config->project->datatable->fieldList['outerDays']['width'] = 'auto';
$config->project->datatable->fieldList['outerDays']['required'] = 'no';

$config->project->datatable->fieldList['selfDays']['title'] = 'selfDays';
$config->project->datatable->fieldList['selfDays']['fixed'] = 'left';
$config->project->datatable->fieldList['selfDays']['width'] = 'auto';
$config->project->datatable->fieldList['selfDays']['required'] = 'no';


$config->project->datatable->fieldList['saasDays']['title'] = 'saasDays';
$config->project->datatable->fieldList['saasDays']['fixed'] = 'left';
$config->project->datatable->fieldList['saasDays']['width'] = 'auto';
$config->project->datatable->fieldList['saasDays']['required'] = 'no';

$config->project->datatable->fieldList['outerPoDays']['title'] = 'outerPoDays';
$config->project->datatable->fieldList['outerPoDays']['fixed'] = 'left';
$config->project->datatable->fieldList['outerPoDays']['width'] = 'auto';
$config->project->datatable->fieldList['outerPoDays']['required'] = 'no';

$config->project->datatable->fieldList['discountPoDays']['title'] = 'discountPoDays';
$config->project->datatable->fieldList['discountPoDays']['fixed'] = 'left';
$config->project->datatable->fieldList['discountPoDays']['width'] = 'auto';
$config->project->datatable->fieldList['discountPoDays']['required'] = 'no';

$config->project->datatable->fieldList['sa']['title'] = 'sa';
$config->project->datatable->fieldList['sa']['fixed'] = 'left';
$config->project->datatable->fieldList['sa']['width'] = 'auto';
$config->project->datatable->fieldList['sa']['required'] = 'no';


$config->project->datatable->fieldList['bd']['title'] = 'bd';
$config->project->datatable->fieldList['bd']['fixed'] = 'left';
$config->project->datatable->fieldList['bd']['width'] = 'auto';
$config->project->datatable->fieldList['bd']['required'] = 'no';


$config->project->datatable->fieldList['cs']['title'] = 'cs';
$config->project->datatable->fieldList['cs']['fixed'] = 'left';
$config->project->datatable->fieldList['cs']['width'] = 'auto';
$config->project->datatable->fieldList['cs']['required'] = 'no';


$config->project->datatable->fieldList['deciders']['title'] = 'deciders';
$config->project->datatable->fieldList['deciders']['fixed'] = 'left';
$config->project->datatable->fieldList['deciders']['width'] = 'auto';
$config->project->datatable->fieldList['deciders']['required'] = 'no';

$config->project->datatable->fieldList['poAmount']['title'] = 'poAmount';
$config->project->datatable->fieldList['poAmount']['fixed'] = 'left';
$config->project->datatable->fieldList['poAmount']['width'] = 'auto';
$config->project->datatable->fieldList['poAmount']['required'] = 'no';

$config->project->datatable->fieldList['stage']['title'] = 'stage';
$config->project->datatable->fieldList['stage']['fixed'] = 'left';
$config->project->datatable->fieldList['stage']['width'] = 'auto';
$config->project->datatable->fieldList['stage']['required'] = 'no';

