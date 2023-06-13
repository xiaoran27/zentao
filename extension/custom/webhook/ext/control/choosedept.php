<?php

class myWebhook extends webhook
{
    /**
     * choose dept.
     *
     * @param int $id
     * @access public
     * @return void
     */
    public function chooseDept($id)
    {
        $webhook = $this->webhook->getById($id);
        if ($webhook->type != 'dinguser' && $webhook->type != 'wechatuser' && $webhook->type != 'feishuuser' && $webhook->type != 'dingsingleuser') {
            echo js::alert($this->lang->webhook->note->bind);
            return print(js::locate($this->createLink('webhook', 'browse')));
        }
        $webhook->secret = json_decode($webhook->secret);

        if ($webhook->type == 'dinguser' || $webhook->type == 'dingsingleuser') {
            $this->app->loadClass('dingapi', true);
            $dingapi = new dingapi($webhook->secret->appKey, $webhook->secret->appSecret, $webhook->secret->agentId);
            $response = $dingapi->getDeptTree();
        }

        if ($webhook->type == 'feishuuser') $response = array('result' => 'success', 'data' => array());

        if ($response['result'] == 'fail') {
            echo js::error($response['message']);
            return print(js::locate($this->createLink('webhook', 'browse')));
        }

        if ($response['result'] == 'selected') {
            $locateLink = $this->createLink('webhook', 'bind', "id={$id}");
            $locateLink .= strpos($locateLink, '?') !== false ? '&' : '?';
            $locateLink .= 'selectedDepts=' . join(',', $response['data']);
            return print(js::locate($locateLink));
        }

        $this->view->title = $this->lang->webhook->chooseDept;
        $this->view->position[] = $this->lang->webhook->chooseDept;

        $this->view->webhookType = $webhook->type;
        $this->view->deptTree = $response['data'];
        $this->view->webhookID = $id;
        $this->display();
    }
}