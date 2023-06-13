<?php
include '../../../../../module/webhook/control.php';

class myWebhook extends webhook
{
    /**
     * Bind dingtalk userid.
     *
     * @param  int    $id
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bind($id, $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        if($_POST)
        {
            $this->webhook->bind($id);
            if(dao::isError()) return print(js::error(dao::getError()));

            return print(js::reload('parent'));
        }

        $webhook = $this->webhook->getById($id);
        if($webhook->type != 'dinguser' && $webhook->type != 'wechatuser' && $webhook->type != 'feishuuser' && $webhook->type != 'dingsingleuser')
        {
            echo js::alert($this->lang->webhook->note->bind);
            return print(js::locate($this->createLink('webhook', 'browse')));
        }
        $webhook->secret = json_decode($webhook->secret);

        /* Get selected depts. */
        if($this->get->selectedDepts)
        {
            setcookie('selectedDepts', $this->get->selectedDepts, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
            $_COOKIE['selectedDepts'] = $this->get->selectedDepts;
        }
        $selectedDepts = $this->cookie->selectedDepts ? $this->cookie->selectedDepts : '';

        if($webhook->type == 'dinguser' || $webhook->type == 'dingsingleuser')
        {
            $this->app->loadClass('dingapi', true);
            $dingapi  = new dingapi($webhook->secret->appKey, $webhook->secret->appSecret, $webhook->secret->agentId);
            $response = $dingapi->getUsers($selectedDepts);
        }
        elseif($webhook->type == 'wechatuser')
        {
            $this->app->loadClass('wechatapi', true);
            $wechatApi = new wechatapi($webhook->secret->appKey, $webhook->secret->appSecret, $webhook->secret->agentId);
            $response  = $wechatApi->getAllUsers();
        }
        elseif($webhook->type == 'feishuuser')
        {
            $this->app->loadClass('feishuapi', true);
            $feishuApi = new feishuapi($webhook->secret->appId, $webhook->secret->appSecret);
            $response  = $feishuApi->getAllUsers($selectedDepts);
        }

        if($response['result'] == 'fail')
        {
            if($response['message'] == 'nodept')
            {
                echo js::error($this->lang->webhook->error->noDept);
                return print(js::locate($this->createLink('webhook', 'chooseDept', "id=$id")));
            }

            echo js::error($response['message']);
            return print(js::locate($this->createLink('webhook', 'browse')));
        }

        $oauthUsers  = $response['data'];
        $bindedPairs = $this->webhook->getBoundUsers($id);
        $useridPairs = array('' => '');
        foreach($oauthUsers as $name => $userid) $useridPairs[$userid] = $name;

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $users = $this->loadModel('user')->getByQuery('inside', $query = '', $pager);

        $unbindUsers = array();
        $bindedUsers = array();
        foreach($users as $user)
        {
            if(isset($bindedPairs[$user->account])) $bindedUsers[$user->account] = $user;
            if(!isset($bindedPairs[$user->account])) $unbindUsers[$user->account] = $user;
        }
        $users = $unbindUsers + $bindedUsers;

        $this->view->title      = $this->lang->webhook->bind;
        $this->view->position[] = html::a($this->createLink('webhook', 'browse'), $this->lang->webhook->common);
        $this->view->position[] = $this->lang->webhook->bind;

        $this->view->webhook       = $webhook;
        $this->view->oauthUsers    = $oauthUsers;
        $this->view->useridPairs   = $useridPairs;
        $this->view->users         = $users;
        $this->view->pager         = $pager;
        $this->view->bindedUsers   = $bindedPairs;
        $this->view->selectedDepts = $selectedDepts;
        $this->display();
    }

}
