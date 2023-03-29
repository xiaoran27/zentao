
<?php
include '../../../../../module/cron/control.php';


class myCron extends cron
{


     /**
     * Ajax exec cron.
     *
     * @param  bool    $restart
     * @access public
     * @return void
     */
    public function ajaxExec($restart = false)
    {
        if ('cli' !== PHP_SAPI)
        {
            ignore_user_abort(true);
            set_time_limit(0);
            session_write_close();
        }
        /* Check cron turnon. */
        if(empty($this->config->global->cron)) return;

        /* Create restart tag file. */
        $restartTag = $this->app->getCacheRoot() . 'restartcron';
        if($restart) touch($restartTag);

        /* make cron status to running. */
        $configID = $this->cron->getConfigID();
        $configID = $this->cron->markCronStatus('running', $configID);

        /* Get and parse crons. */
        $crons       = $this->cron->getCrons('nostop');
        $parsedCrons = $this->cron->parseCron($crons);

        /* Update last time. */
        $this->cron->changeStatus(key($parsedCrons), 'normal', true);
        $this->loadModel('common');
        $startedTime = time();
        while(true)
        {
            dao::$cache = array();

            /* When cron is null then die. */
            if(empty($crons)) break;
            if(empty($parsedCrons)) break;
            if(!$this->cron->getTurnon()) break;

            /* Die old process when restart. */
            if(file_exists($restartTag) and !$restart) return unlink($restartTag);
            $restart = false;

            /* Run crons. */
            $now = new datetime('now');
            unset($_SESSION['company']);
            unset($this->app->company);
            $this->common->setCompany();
            $this->common->loadConfigFromDB();
            foreach($parsedCrons as $id => $cron)
            {
                $cronInfo = $this->cron->getById($id);
                /* Skip empty and stop cron.*/
                if(empty($cronInfo) or $cronInfo->status == 'stop') continue;
                /* Skip cron that status is running and run time is less than max. */
                if($cronInfo->status == 'running' and (time() - strtotime($cronInfo->lastTime)) < $this->config->cron->maxRunTime) continue;
                /* Skip cron that last time is more than this cron time. */
                if('cli' === PHP_SAPI)
                {
                    if($cronInfo->lastTime >= $cron['time']->format(DT_DATETIME1)) continue;
                }
                else
                {
                    if($cronInfo->lastTime > $cron['time']->format(DT_DATETIME1)) return;
                }

                if($now > $cron['time'])
                {
                    if(!$this->cron->changeStatusRunning($id)) continue;
                    $parsedCrons[$id]['time'] = $cron['cron']->getNextRunDate();

                    /* Execution command. */
                    $output = '';
                    $return = '';
                    if($cron['command'])
                    {
                        if(isset($crons[$id]) and $crons[$id]->type == 'zentao')
                        {
                            parse_str($cron['command'], $params);
                            // $this->common->log(json_encode(array('cron' => $cron) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

                            //加载为cron整合的扩展文件cronmethod.php
                            if(isset($params['moduleName']) and isset($params['methodName']) ){
                                $actionExtPath     = $this->app->getModuleExtPath( $this->appName, $params['moduleName'], 'control');
                                if(!empty($actionExtPath) and isset($actionExtPath['custom']))
                                {
                                    $file2Included = $actionExtPath['custom'] . 'cronmethod.php';
                                    $classNameToFetch = "my{$params['moduleName']}";
                                    $cls_exists = class_exists($classNameToFetch);
                                    $this->common->log(json_encode(array('appName' => $this->appName, 'classNameToFetch' => $classNameToFetch, 'cls_exists' => $cls_exists, 'file2Included' => $file2Included, 'params' => $params) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
                                    if(!$cls_exists)
                                    {
                                        if (!class_exists($params['moduleName'])) helper::importControl($params['moduleName']);
                                        helper::import($file2Included);
                                    }
                                }
                            }
                            

                            if(isset($params['moduleName']) and isset($params['methodName']) and isset($params['params']))
                            {
                                
                                // 支持输入参数params={encodeURIComponent编码后的参数值}
                                $fetch_param = $params['params'] ;
                                $fetch_param = str_replace("%3A", ":", $fetch_param);
                                $fetch_param = str_replace("%2F", "/", $fetch_param);
                                $fetch_param = str_replace("%3F", "?", $fetch_param);
                                $fetch_param = str_replace("%3D", "=", $fetch_param);
                                $fetch_param = str_replace("%24", "$", $fetch_param);
                                $fetch_param = str_replace("%26", "&", $fetch_param);

                                parse_str($fetch_param, $fetch_params);
                                $this->common->log(json_encode(array('params' => $params, 'fetch_params' => $fetch_params) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

                                $this->app->loadConfig($params['moduleName']);
                                $output = $this->fetch($params['moduleName'], $params['methodName'], $fetch_params);
                            }elseif(isset($params['moduleName']) and isset($params['methodName']) )
                            {
                                $this->app->loadConfig($params['moduleName']);
                                $output = $this->fetch($params['moduleName'], $params['methodName']);
                            }
                        }
                        elseif(isset($crons[$id]) and $crons[$id]->type == 'system')
                        {
                            exec($cron['command'], $output, $return);
                            if($output) $output = join("\n", $output);
                        }

                        /* Save log. */
                        $log    = '';
                        $time   = $now->format('G:i:s');
                        $output = "\n" . $output;

                        $log = "$time task " . $id . " executed,\ncommand: $cron[command].\nreturn : $return.\noutput : $output\n";
                        $this->cron->logCron($log);
                        unset($log);
                    }

                    /* Revert cron status. */
                    $this->cron->changeStatus($id, 'normal');
                }
            }

            /* Check whether the task change. */
            $newCrons = $this->cron->getCrons('nostop');
            $changed  = $this->cron->checkChange();
            if(count($newCrons) != count($crons) or $changed)
            {
                $crons       = $newCrons;
                $parsedCrons = $this->cron->parseCron($newCrons);
            }

            /* Sleep some seconds. */
            $sleepTime = 60 - ((time() - strtotime($now->format('Y-m-d H:i:s'))) % 60);
            sleep($sleepTime);

            /* Break while. */
            if('cli' !== PHP_SAPI && connection_status() != CONNECTION_NORMAL) break;
            if(((time() - $startedTime) / 3600 / 24) >= $this->config->cron->maxRunDays) break;
        }

        /* Revert cron status to stop. */
        $this->cron->markCronStatus('stop', $configID);
    }

}