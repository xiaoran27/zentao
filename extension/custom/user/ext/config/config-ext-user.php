<?php

global $lang, $app, $config;


/* 钉钉登录配置 https://open.dingtalk.com/document/orgapp/scan-qr-code-to-log-on-to-third-party-websites*/
$config->ding->ddturnon = true;/* 是否开启钉钉登录 */
$config->ding->logintype = 1;/* 钉钉登录方式,0仅允许绑定登录,1允许自动注册登录(建议新平台使用此方法,方便人员自行添加) */
$config->ding->appid = 'dingooddxzzmdvlici1v';/* 如果是企业内部应用，appid则为应用的AppKey；如果是第三方企业应用，appid则为应用的SuiteKey */
$config->ding->appsecret = 'PF_vB11JWT3tE5SX6qGAAYCxNNx-LB2alF-0Mfu0WJLwZNxUzPMDfK6fTXFB6qEI';/* 钉钉应用凭证的AppSecret */
$config->ding->redirect = 'http://127.0.0.1:81/';/* 回调地址域名,与钉钉管理后台保持一致 */

$config->ding->defpwd = 'DingA1b2c3d4';

$config->safe->loginCaptcha = false;

$config->sso->turnon           = false;
$config->sso->code = 'dingooddxzzmdvlici1v';
$config->sso->key = 'PF_vB11JWT3tE5SX6qGAAYCxNNx-LB2alF-0Mfu0WJLwZNxUzPMDfK6fTXFB6qEI';
$config->sso->addr = '';


## 
# $config->baseurl="http://127.0.0.1:8081/zentao/";
# $config->baseurl="http://47.92.142.215:30080/zentao/";
$config->baseurl = "https://chandao.bytenew.com/zentao/";


## ding 相关配置
// $config->ding = new stdclass();
$config->ding->default = new stdclass();
$config->ding->default->app='robotapi';
$config->ding->default->groupName='自用机器人';
$config->ding->default->groupRobot='ROT240522';

       

# 企业内部程序 https://open-dev.dingtalk.com/fe/app
$config->ding->apps = array();
$config->ding->apps['robotapi'] = array('name'=>'robotapi','agentId'=>'1990383324','appKey'=>'dingcrquldohmljagits','appSecret'=>'B31x2jWJKgFsMT-RVFFI8usEwTP2mugjwGz01yQ0WpUjaliqms90qGZkaVQ_P0Nk','robotCode'=>'dingcrquldohmljagits');

# 群主获取群openConversationId https://open.dingtalk.com/tools/explorer/jsapi?spm=ding_open_doc.document.0.0.1319388cuhWImJ&id=10303
$config->ding->openConversationIds = array();
$config->ding->openConversationIds['自用机器人'] = 'cidK7WHgleolZYw9ate7v4FNA==';
$config->ding->openConversationIds['班牛研发中心'] = 'cidwwuBaDbDnR95vnyntPlLMg==';

# 群自定义机器人webhook
$config->ding->robotWebhooks = array();
$config->ding->robotWebhooks['业务需求处理进度提醒'] = 'https://oapi.dingtalk.com/robot/send?access_token=342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27';
$config->ding->robotWebhooks['bug处理提前机器人'] = 'https://oapi.dingtalk.com/robot/send?access_token=a3da8d4854e574a7993d1ded045087b0661575a689c639f53c766e77e8a30750';
$config->ding->robotWebhooks['ROT240522'] = 'https://oapi.dingtalk.com/robot/send?access_token=6a8790b4a643a8576e7c02c13fea4b94d001f32006c5a99a98687b9d8274d526';



