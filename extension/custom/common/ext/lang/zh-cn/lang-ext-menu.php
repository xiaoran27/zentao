<?php
global $config;

$lang->mainNav->gantt    = "{$lang->navIcons['report']} {$lang->gantt->common}|gantt|frappe|";

/* Report menu.*/
// $lang->report->menu->gantt = array('link' => "甘特图|report|gantt");
$lang->report->menu->gantt = array('link' => "甘特图|gantt|frappe");

/* Report menu order. */
$lang->report->menuOrder[15] = 'gantt';

// $lang->report->menu->gantt['subMenu'] = new stdclass();

// $lang->report->menu->gantt['subMenu']->frappe    = array('link' => "{$lang->gantt->common}|gantt|frappe");
// $lang->report->menu->gantt['subMenu']->gantt    = array('link' => "{$lang->gantt->common}|report|gantt");

// $lang->report->menu->gantt['menuOrder'][10]  = 'frappe';
// $lang->report->menu->gantt['menuOrder'][20]  = 'gantt';



