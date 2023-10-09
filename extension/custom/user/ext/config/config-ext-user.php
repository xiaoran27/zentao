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


