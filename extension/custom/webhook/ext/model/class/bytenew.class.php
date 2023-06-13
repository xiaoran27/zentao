<?php

class bytenewWebhook extends webhookModel
{
    /**
     * Post hook data.
     *
     * @param  object $webhook
     * @param  string $sendData
     * @param  int    $actionID
     * @access public
     * @return int
     */
    public function fetchHook($webhook, $sendData, $actionID = 0)
    {
        if(!extension_loaded('curl')) return print(helper::jsonEncode($this->lang->webhook->error->curl));

        if($webhook->type == 'dinguser' || $webhook->type == 'wechatuser' || $webhook->type == 'feishuuser' || $webhook->type == 'dingsingleuser')
        {
            if(is_string($webhook->secret)) $webhook->secret = json_decode($webhook->secret);

            $openIdList = $this->getOpenIdList($webhook->id, $actionID);
            if(empty($openIdList)) return false;
            if($webhook->type == 'dinguser')
            {
                $this->app->loadClass('dingapi', true);
                $dingapi = new dingapi($webhook->secret->appKey, $webhook->secret->appSecret, $webhook->secret->agentId);
                $result  = $dingapi->send($openIdList, $sendData);
                return json_encode($result);
            }
            elseif($webhook->type == 'wechatuser')
            {
                $this->app->loadClass('wechatapi', true);
                $wechatapi = new wechatapi($webhook->secret->appKey, $webhook->secret->appSecret, $webhook->secret->agentId);
                $result  = $wechatapi->send($openIdList, $sendData);
                return json_encode($result);
            }
            elseif($webhook->type == 'feishuuser')
            {
                $this->app->loadClass('feishuapi', true);
                $feishuapi = new feishuapi($webhook->secret->appId, $webhook->secret->appSecret);
                $result  = $feishuapi->send($openIdList, $sendData);
                return json_encode($result);
            }elseif($webhook->type == 'dingsingleuser')
            {
                $this->app->loadClass('dingapi', true);
                $dingapi = new dingapi($webhook->secret->appKey, $webhook->secret->appSecret, $webhook->secret->agentId);
                $token = $dingapi->getToken();
                $result = $this->loadModel('common')->sendSignle($token, $webhook->secret->appKey, $openIdList, $sendData);
                return json_encode($result);

            }
        }

        $contentType = "Content-Type: {$webhook->contentType};charset=utf-8";
        if($webhook->type == 'dinggroup' or $webhook->type == 'wechatgroup' or $webhook->type == 'feishugroup') $contentType = "Content-Type: application/json";
        $header[] = $contentType;

        $url = $webhook->url;
        if($webhook->type == 'dinggroup' and $webhook->secret)
        {
            $timestamp = time() * 1000;
            $sign = $timestamp . "\n" . $webhook->secret;
            $sign = urlencode(base64_encode(hash_hmac('sha256', $sign, $webhook->secret, true)));
            $url .= "&timestamp={$timestamp}&sign={$sign}";
        }
        if($webhook->type == 'feishugroup' and $webhook->secret)
        {
            $timestamp = time();
            $sign = $timestamp . "\n" . $webhook->secret;
            $sign = base64_encode(hash_hmac('sha256', '', $sign, true));

            $content = json_decode($sendData);
            $content->timestamp = $timestamp;
            $content->sign      = $sign;
            $sendData = json_encode($content);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if($error)  return $error;
        if($result) return $result;
        return $httpCode;
    }

    /**
     * Create a webhook.
     *
     * @access public
     * @return bool
     */
    public function create()
    {
        $webhook = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->join('products', ',')
            ->join('executions', ',')
            ->skipSpecial('url')
            ->trim('agentId,appKey,appSecret,wechatAgentId,wechatCorpId,wechatCorpSecret,feishuAppId,feishuAppSecret')
            ->remove('allParams, allActions')
            ->get();
        $webhook->domain = trim($webhook->domain, '/');
        $webhook->params = $this->post->params ? implode(',', $this->post->params) . ',text' : 'text';

        if($webhook->type == 'dinguser' || $webhook->type == 'dingsingleuser')
        {
            $webhook->secret = array();
            $webhook->secret['agentId']   = $webhook->agentId;
            $webhook->secret['appKey']    = $webhook->appKey;
            $webhook->secret['appSecret'] = $webhook->appSecret;

            if(empty($webhook->agentId))   dao::$errors['agentId']   = sprintf($this->lang->error->notempty, $this->lang->webhook->dingAgentId);
            if(empty($webhook->appKey))    dao::$errors['appKey']    = sprintf($this->lang->error->notempty, $this->lang->webhook->dingAppKey);
            if(empty($webhook->appSecret)) dao::$errors['appSecret'] = sprintf($this->lang->error->notempty, $this->lang->webhook->dingAppSecret);
            if(dao::isError()) return false;

            $webhook->secret = json_encode($webhook->secret);
            $webhook->url    = $this->config->webhook->dingapiUrl;
        }
        elseif($webhook->type == 'wechatuser')
        {
            $webhook->secret = array();
            $webhook->secret['agentId']   = $webhook->wechatAgentId;
            $webhook->secret['appKey']    = $webhook->wechatCorpId;
            $webhook->secret['appSecret'] = $webhook->wechatCorpSecret;

            if(empty($webhook->wechatCorpId))     dao::$errors['wechatCorpId']     = sprintf($this->lang->error->notempty, $this->lang->webhook->wechatCorpId);
            if(empty($webhook->wechatCorpSecret)) dao::$errors['wechatCorpSecret'] = sprintf($this->lang->error->notempty, $this->lang->webhook->wechatCorpSecret);
            if(empty($webhook->wechatAgentId))    dao::$errors['wechatAgentId']    = sprintf($this->lang->error->notempty, $this->lang->webhook->wechatAgentId);
            if(dao::isError()) return false;

            $webhook->secret = json_encode($webhook->secret);
            $webhook->url    = $this->config->webhook->wechatApiUrl;
        }
        elseif($webhook->type == 'feishuuser')
        {
            $webhook->secret = array();
            $webhook->secret['appId']     = $webhook->feishuAppId;
            $webhook->secret['appSecret'] = $webhook->feishuAppSecret;

            if(empty($webhook->feishuAppId))     dao::$errors['feishuAppId']     = sprintf($this->lang->error->notempty, $this->lang->webhook->feishuAppId);
            if(empty($webhook->feishuAppSecret)) dao::$errors['feishuAppSecret'] = sprintf($this->lang->error->notempty, $this->lang->webhook->feishuAppSecret);
            if(dao::isError()) return false;

            $webhook->secret = json_encode($webhook->secret);
            $webhook->url    = $this->config->webhook->feishuApiUrl;
        }

        $this->dao->insert(TABLE_WEBHOOK)->data($webhook, 'agentId,appKey,appSecret,wechatCorpId,wechatCorpSecret,wechatAgentId,feishuAppId,feishuAppSecret')
            ->batchCheck($this->config->webhook->create->requiredFields, 'notempty')
            ->autoCheck()
            ->exec();
        return $this->dao->lastInsertId();
    }


    /**
     * Update a webhook.
     *
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function update($id)
    {
        $webhook = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->setDefault('products', '')
            ->setDefault('executions', '')
            ->join('products', ',')
            ->join('executions', ',')
            ->skipSpecial('url')
            ->trim('agentId,appKey,appSecret,wechatAgentId,wechatCorpId,wechatCorpSecret,feishuAppId,feishuAppSecret')
            ->remove('allParams, allActions')
            ->get();
        $webhook->domain = trim($webhook->domain, '/');
        $webhook->params = $this->post->params ? implode(',', $this->post->params) . ',text' : 'text';

        if($webhook->type == 'dinguser' || $webhook->type == 'dingsingleuser')
        {
            $webhook->secret = array();
            $webhook->secret['agentId']   = $webhook->agentId;
            $webhook->secret['appKey']    = $webhook->appKey;
            $webhook->secret['appSecret'] = $webhook->appSecret;

            if(empty($webhook->agentId))   dao::$errors['agentId']   = sprintf($this->lang->error->notempty, $this->lang->webhook->dingAgentId);
            if(empty($webhook->appKey))    dao::$errors['appKey']    = sprintf($this->lang->error->notempty, $this->lang->webhook->dingAppKey);
            if(empty($webhook->appSecret)) dao::$errors['appSecret'] = sprintf($this->lang->error->notempty, $this->lang->webhook->dingAppSecret);
            if(dao::isError()) return false;

            $webhook->secret = json_encode($webhook->secret);
        }
        elseif($webhook->type == 'wechatuser')
        {
            $webhook->secret = array();
            $webhook->secret['agentId']   = $webhook->wechatAgentId;
            $webhook->secret['appKey']    = $webhook->wechatCorpId;
            $webhook->secret['appSecret'] = $webhook->wechatCorpSecret;

            if(empty($webhook->wechatCorpId))     dao::$errors['wechatCorpId']     = sprintf($this->lang->error->notempty, $this->lang->webhook->wechatCorpId);
            if(empty($webhook->wechatCorpSecret)) dao::$errors['wechatCorpSecret'] = sprintf($this->lang->error->notempty, $this->lang->webhook->wechatCorpSecret);
            if(empty($webhook->wechatAgentId))    dao::$errors['wechatAgentId']    = sprintf($this->lang->error->notempty, $this->lang->webhook->wechatAgentId);
            if(dao::isError()) return false;

            $webhook->secret = json_encode($webhook->secret);
        }
        elseif($webhook->type == 'feishuuser')
        {
            $webhook->secret = array();
            $webhook->secret['appId']     = $webhook->feishuAppId;
            $webhook->secret['appSecret'] = $webhook->feishuAppSecret;

            if(empty($webhook->feishuAppId))     dao::$errors['feishuAppId']     = sprintf($this->lang->error->notempty, $this->lang->webhook->feishuAppId);
            if(empty($webhook->feishuAppSecret)) dao::$errors['feishuAppSecret'] = sprintf($this->lang->error->notempty, $this->lang->webhook->feishuAppSecret);
            if(dao::isError()) return false;

            $webhook->secret = json_encode($webhook->secret);
        }

        $this->dao->update(TABLE_WEBHOOK)->data($webhook, 'agentId,appKey,appSecret,wechatCorpId,wechatCorpSecret,wechatAgentId,feishuAppId,feishuAppSecret')
            ->batchCheck($this->config->webhook->edit->requiredFields, 'notempty')
            ->autoCheck()
            ->where('id')->eq($id)
            ->exec();
        return !dao::isError();
    }


}