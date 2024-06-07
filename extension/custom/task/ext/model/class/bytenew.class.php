<?php

class bytenewTask extends TaskModel
{

    public function checkEstStartedAndDeadline($executionID, $estStarted, $deadline, $pre = '')
    {

        if(empty($estStarted) or helper::isZeroDate($estStarted) or empty($deadline) or helper::isZeroDate($deadline) or $deadline < $estStarted ){
            dao::$errors['estStarted'][] = $pre . sprintf("[%s]不合法", $this->lang->task->estStarted );
            dao::$errors['deadline'][] = $pre . sprintf("[%s]不合法或小于[%s]", $this->lang->task->deadline , $this->lang->task->estStarted);
            return;
        }

        $d1 = DateTime::createFromFormat("Y-m-d", substr($estStarted,0,10));
        $d2 = DateTime::createFromFormat("Y-m-d", substr($deadline,0,10));
        // Set the start of the week to Sunday (default in many locales)
        // Modify the date to the start of the week (Sunday)
        $d1->modify('this week');
        $d2->modify('this week');

        // Set the time to the start of the day to avoid time comparisons
        $d1->setTime(0, 0, 0);
        $d2->setTime(0, 0, 0);

        // Compare the start of the weeks
        $sameweek = $d1->format('Y-m-d') === $d2->format('Y-m-d') ;
        if ( $sameweek === false ){
            dao::$errors['estStarted'][] = $pre . sprintf("[%s]和[%s]必须在同一周内", $this->lang->task->estStarted , $this->lang->task->deadline );
            return;
        }


        $execution = $this->loadModel('execution')->getByID($executionID);
        if(empty($execution) or empty($this->config->limitTaskDate)) return false;
        if(empty($execution->multiple)) $this->lang->execution->common = $this->lang->project->common;

        if(!empty($estStarted) and !helper::isZeroDate($estStarted) and $estStarted < $execution->begin) dao::$errors['estStarted'][] = $pre . sprintf($this->lang->task->error->beginLtExecution, $this->lang->execution->common, $execution->begin);
        if(!empty($estStarted) and !helper::isZeroDate($estStarted) and $estStarted > $execution->end)   dao::$errors['estStarted'][] = $pre . sprintf($this->lang->task->error->beginGtExecution, $this->lang->execution->common, $execution->end);
        if(!empty($deadline) and !helper::isZeroDate($deadline) and $deadline > $execution->end)       dao::$errors['deadline'][]   = $pre . sprintf($this->lang->task->error->endGtExecution, $this->lang->execution->common, $execution->end);
        if(!empty($deadline) and !helper::isZeroDate($deadline) and $deadline < $execution->begin)     dao::$errors['deadline'][]   = $pre . sprintf($this->lang->task->error->endLtExecution, $this->lang->execution->common, $execution->begin);
    }

    /**
     * 获取doing或wait的任务构建ding消息
     *
     * @param  int $ltdays  近n天数(默认93)
     * @access public
     * @return array
     */
    public function getTextForDing($ltdays = 93, $excludeUsers = 'admin,system')
    {
        if (empty($ltdays) or $ltdays <1 ) {
            $ltdays = 93;
        }
        if (empty($excludeUsers)) {
            $excludeUsers = '';
        }
        $excludeUsers .= ',admin,system,';
        if (isset($this->config->excludeUsers)) $excludeUsers .= $this->config->excludeUsers.',';  //config-ext-user.php
        if (isset($this->config->task->excludeUsers)) $excludeUsers .= $this->config->task->excludeUsers.',';
        $excludeUsers = str_replace(",,",",",",$excludeUsers");

        $includeUsers = ',';
        if (isset($this->config->includeUsers)) $includeUsers .= $this->config->includeUsers.',';  //config-ext-user.php
        if (isset($this->config->task->includeUsers)) $includeUsers .= $this->config->task->includeUsers.',';
        $includeUsers = str_replace(",,",",",",$includeUsers");
        

        $common = $this->loadModel('common');
        $common->log(json_encode(array('ltdays' => $ltdays, 'excludeUsers' => $excludeUsers), JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);


        $dingdingDatas = $this->dao->select("zu.account as account, zu.realname  as realname , if( ifnull( zu.dingding, '') = '' , zu.mobile , zu.dingding  ) as dingding, count(distinct zt.id) as total, group_concat(distinct zt.id) as ids ")
            ->from(TABLE_TASK)->alias('zt')
            ->leftJoin(TABLE_USER)->alias('zu')->on('zt.assignedTo = zu.account')
            ->where('zt.deleted')->eq(0)
            ->andWhere('zt.assignedTo')->ne('')
            ->andWhere('zt.status')->in("wait,doing")
            ->beginIF($ltdays > 0)->andWhere('datediff(now(), COALESCE(if(left(CONCAT("",ifnull(realStarted,"0000-00-00")),4)="0000",estStarted,realStarted),deadline,openedDate))')->between(0,$ltdays)->fi()
            ->andWhere("datediff(now(), if(left(CONCAT('',ifnull(lastEditedDate,'0000-00-00')),4)='0000',openedDate,lastEditedDate))")->gt(0)
            ->groupby("realname , dingding ")
            ->orderby("total  DESC")
            ->fetchAll();
        $common->log(json_encode(array('dingdingDatas' => $dingdingDatas), JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        if (empty($dingdingDatas)) return array();

        $webroot = common::getSysUrl(). $this->config->webRoot;  // 直接用禅道自己的系统配置   有nginx代理就不可用
        $webroot = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $this->config->webRoot;  //  拼接可能不对
        if (isset($this->config->baseurl)) $webroot = $this->config->baseurl;

        $content = '';
        $contents = array();
        $contentMdIds = array();
        $atMobiles = array();
        $accounts = array();
        $realnames = array();
        foreach ($dingdingDatas as $e) {

            $onlyUsers = false;
            if ($includeUsers!=','){
                $onlyUsers = (strpos($includeUsers, ",$e->account,") !== false);
                $onlyUsers = $onlyUsers || (strpos($includeUsers, ",$e->dingding,") !== false);
                $onlyUsers = $onlyUsers || (strpos($includeUsers, ",$e->realname,") !== false);
            }
            if (!$onlyUsers){
                // 忽略过滤名单
                $ignore = (strpos($excludeUsers, ",$e->account,") !== false);
                $ignore = $ignore || (strpos($excludeUsers, ",$e->dingding,") !== false);
                $ignore = $ignore || (strpos($excludeUsers, ",$e->realname,") !== false);
                if ($ignore) continue;
            }
            

            //消息内容content中要带上"@手机号"，跟atMobiles参数结合使用，才有@效果，如上示例。
            $str = "- @$e->dingding ($e->realname) 亲,抽空处理下这 [$e->total]({$webroot}/my-work-task.html) 个任务哦!";
            $content .= $str;
            $contents[] = $str;

            $ids = explode(',', $e->ids);
            $ids_md = '';
            foreach ($ids as $i => $id) {
                $ids_md .= " [{$id}]({$webroot}/task-view-{$id}.html)";
                if ($i >= 10) {
                    $ids_md .= " [更多]({$webroot}/my-work-task.html)";
                    break;
                }
            }
            $contentMdIds[] = $ids_md;
            $accounts[] = '' . $e->account;
            $atMobiles[] = '' . $e->dingding;
            $realnames[] = $e->realname;
        }

        $returns = array('content' => $content, 'accounts' => $accounts, 'atMobiles' => $atMobiles, 'realnames' => $realnames, 'contents' => $contents, 'contentMdIds' => $contentMdIds);
        $common->log(json_encode($returns, JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        return $returns;
    }

    /**
     * 取消超过n天的任务
     * 
     * @param int $timeoutDays = 93
     * @access public
     * @return int
     */
    public function timeoutCancel($timeoutDays = 93)
    {
        $now    = helper::now();
        $wait_date = substr($now,0,13).':00:00';
        $doing_date = substr($now,0,13).':11:11';
        $sql = "update zt_task set status='cancel',canceledDate=if(status='wait','$wait_date','$doing_date'),canceledBy='system'
          where deleted = '0'
            and status in ('wait','doing')
            and datediff(now(), COALESCE(deadline,realStarted,estStarted,openedDate))  >= $timeoutDays ";
        $rows = $this->dao->exec($sql);

        $common = $this->loadModel('common');
        $common->log(json_encode(array('cancel tasks: timeoutDays' => $timeoutDays, 'rows' => $rows, 'wait_date' => $wait_date, 'doing_date' => $doing_date), JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

        return $rows;
    }

    /**
     * 
     * 
     * @param string $type ='single'  (single,robotapi,webhook)
     * @param int $ltdays = 93
     * @param string $webhook = ''   钉钉群机器人的webhook(支持base64或encodeURIComponent编码)，仅type中有webhook有效
     * @param bool $autoCancel=true 
     * @access public
     * @return string
     */
    public function dingSend($type = 'single', $ltdays = 93, $webhook='', $autoCancel=true)
    {
        if (empty($type)) {
            $type = 'single';
        }
        $type = ','.strtolower($type).',';
        if (empty($ltdays) or $ltdays<1) {
            $ltdays = 93;
        }

        $common = $this->loadModel('common');
        $common->log(json_encode(array('type' => $type, 'ltdays' => $ltdays, 'webhook' => $webhook, 'autoCancel' => $autoCancel), JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);


        if (strpos($type, ",webhook,") !== false ){
            if(empty($webhook)) {
                // $type=str_replace(",webhook,",',',$type);
                // $common->log("IGNORE: 'webhook' is EMPTY!!!", __FILE__, __LINE__);

                $webhook = $this->config->ding->robotWebhooks[$this->config->ding->default->groupRobot];
                $common->log("WARNING: 'webhook' is EMPTY, using default={$webhook}!!!", __FILE__, __LINE__);
            }else{
                $url=$webhook;
                // url支持base64编码
                $url_new = base64_decode($url, true);
                if ($url_new && $url === base64_encode($url_new)) {
                    $url = $url_new;
                }

                $url = rawurldecode($url); // encodeURIComponent
                $pattern = "/^https:\/\/oapi[.]dingtalk[.]com\/robot\/send\?access_token=[a-z0-9]{64}$/i";
                $match = preg_match($pattern, $url);
                if (empty($url) || $match < 1) {
                    $type=str_replace(",webhook,",',',$type);
                    $common->log("IGNORE: 无效的url!!!".json_encode(array('url' => $url, 'match' => $match,'webhook' => $webhook, ), JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
                }else{
                    $webhook=$url;
                }
            }
        }


        if ($autoCancel)  $this->timeoutCancel($ltdays);

        $dingDatas = $this->getTextForDing($ltdays);
        if (empty($dingDatas)) return '无ding数据';

        // array('content' => $content, 'atMobiles' => $atMobiles, 'realnames' => $realnames, 'contents' => $contents, 'contentMdIds' => $contentMdIds);
        $contentTitle = $dingDatas['content'];
        $atMobiles = $dingDatas['atMobiles'];
        $accounts = $dingDatas['accounts'];
        $openIds = $common->getOpenIDsByAccounts('dingsingleuser',$accounts);
        $realnames = $dingDatas['realnames'];
        $contents = $dingDatas['contents'];
        $contentMdIds = $dingDatas['contentMdIds'];
        $mdTitle = '任务提醒 ';
        $sendData=""; // {"title":"myTitle","text":"mytext"}

        $result = array();
        if (strpos($type, ",single,") !== false ){

            $content = '';
            foreach ($contents as $i => $value) {

                // if ( $atMobiles[$i] != '13788992292' or  $realnames[$i] != '喜鹊'  ) continue;  //仅测试用

                $content = "$value **任务集**: $contentMdIds[$i]  \n";

                $data = new stdclass();
                $data->title = $mdTitle;
                $data->text  = $content;
                $sendData=json_encode($data,JSON_UNESCAPED_UNICODE);
                    
                
                $tmp = $common->dingSingleSend($sendData, array($openIds["$accounts[$i]"]));
                $result["single-$accounts[$i]"]=$tmp;
            }
            
        }
        if (strpos($type, ",robotapi,") !== false  or strpos($type, ",webhook,") !== false  ){
            $content = '';
            foreach ($contents as $i => $value) {
                $content .= "$value **任务集**: $contentMdIds[$i]  \n";
            }
            $data = new stdclass();
            $data->title = $mdTitle;
            $data->text  = $content;
            $sendData=json_encode($data,JSON_UNESCAPED_UNICODE);

            if (strpos($type, ",robotapi,") !== false  ){
                $tmp = $common->dingRobotSendApi($sendData);
                $result["robotapi"]=$tmp;
            }
            if (strpos($type, ",webhook,") !== false  ){
                $tmp = $common->dingRobotSend($content, $webhook, $atMobiles, 'markdown', $mdTitle);
                $result["webhook"]=$tmp;
            }
        }
        

        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }

}