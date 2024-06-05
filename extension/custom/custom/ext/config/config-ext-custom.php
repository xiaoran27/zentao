<?php

global $lang, $app;

# 

$config->custom->fieldList['project']['create']      .= ',devEvaluate,contractNo';
$config->custom->fieldList['project']['edit']        .= ',devEvaluate,contractNo';

$config->custom->fieldList['story']['create']        .= ',assignedTo';
$config->custom->fieldList['story']['close']         .= ',prdReviewTime,releaseTime';

$config->custom->fieldList['task']['create']         .= ',assignedTo';
$config->custom->fieldList['task']['edit']           .= $config->custom->fieldList['task']['create'];

/*
## from custom/config.php
$config->custom->fieldList['program']['create']      = 'budget,PM,desc';
$config->custom->fieldList['program']['edit']        = 'budget,PM,desc';
$config->custom->fieldList['project']['create']      = 'budget,PM,desc';
$config->custom->fieldList['project']['edit']        = 'budget,PM,desc';
$config->custom->fieldList['product']['create']      = 'PO,QD,RD,type,desc';
$config->custom->fieldList['product']['edit']        = 'PO,QD,RD,type,desc,status';
$config->custom->fieldList['story']['create']        = 'module,plan,source,pri,estimate,keywords,spec,verify';
$config->custom->fieldList['story']['change']        = 'comment,spec,verify';
$config->custom->fieldList['story']['close']         = 'comment';
$config->custom->fieldList['story']['review']        = 'reviewedDate,comment';
$config->custom->fieldList['productplan']            = 'begin,end,desc';
$config->custom->fieldList['release']['create']      = 'desc';
$config->custom->fieldList['release']['edit']        = 'desc';
$config->custom->fieldList['execution']['create']    = 'days,desc,PO,PM,QD,RD';
$config->custom->fieldList['execution']['edit']      = 'days,desc,PO,PM,QD,RD';
$config->custom->fieldList['task']['create']         = 'module,story,pri,estimate,desc,estStarted,deadline';
$config->custom->fieldList['task']['edit']           = 'module,pri,estimate,estStarted,deadline';
$config->custom->fieldList['task']['finish']         = 'comment';
$config->custom->fieldList['task']['activate']       = 'assignedTo,comment';
$config->custom->fieldList['build']                  = 'scmPath,filePath,desc';
$config->custom->fieldList['bug']['create']          = 'module,project,execution,deadline,type,os,browser,severity,pri,steps,keywords';
$config->custom->fieldList['bug']['edit']            = 'plan,project,assignedTo,deadline,type,os,browser,severity,pri,steps,keywords';
$config->custom->fieldList['bug']['resolve']         = 'resolvedBuild,resolvedDate,assignedTo,comment';
$config->custom->fieldList['testcase']['create']     = 'stage,story,pri,precondition,keywords,module';
$config->custom->fieldList['testcase']['edit']       = 'stage,story,pri,precondition,keywords,status,module';
$config->custom->fieldList['testsuite']              = 'desc';
$config->custom->fieldList['caselib']                = 'desc';
$config->custom->fieldList['testcase']['createcase'] = 'lib,stage,pri,precondition,keywords';
$config->custom->fieldList['testreport']             = 'begin,end,members,report';
$config->custom->fieldList['testtask']               = 'owner,pri,desc';
$config->custom->fieldList['doc']                    = 'keywords,content';
$config->custom->fieldList['user']['create']         = 'dept,role,email,commiter';
$config->custom->fieldList['user']['edit']           = 'dept,role,email,commiter,skype,qq,mobile,phone,address,zipcode,dingding,slack,whatsapp,weixin';
*/