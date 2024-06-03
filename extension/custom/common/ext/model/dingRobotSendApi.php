<?php


    /**
     * 用发布的应用中robot发送群聊消息
     * $this->loadModel('common')->dingRobotSendApi($message, $accessToken=null, $openConversationId=null, $robotCode=null);
     *
     * @param  string $message  // {"title":"myTitle","text":"mytext"}
     * @param  string $accessToken=null  默认$config->ding->apps[$config->ding->default->app]的授权值
     * @param  string $openConversationId=null 默认$config->ding->apps[$config->ding->default->app]的openConversationId @see https://open.dingtalk.com/tools/explorer/jsapi?spm=ding_open_doc.document.0.0.1319388cuhWImJ&id=10303
     * @param  string $robotCode=null  默认$config->ding->apps[$config->ding->default->app]的robotCode或appKey
     * 
     * @access public
     * @return string
     */
public function dingRobotSendApi($message, $accessToken=null, $openConversationId=null, $robotCode=null)
{

    $type='ding';
    if (empty($accessToken) ){
        $accessToken = $this->getToken($type);
    }
    if ( $accessToken === false ) return false;

    
    if (!isset($this->config->$type)) return false;
    $appname = $this->config->$type->default->app;
    
    if (empty($openConversationId) ){
        $groupName = $this->config->$type->default->groupName;
        $openConversationId = $this->config->$type->openConversationIds["$groupName"];
    }
    if (empty($robotCode) ){
        $robotCode = $this->config->$type->apps["$appname"]['robotCode'];
    }
    if (empty($robotCode) ){
        $robotCode = $this->config->$type->apps["$appname"]['appKey'];
    }


    $url = "https://api.dingtalk.com/v1.0/robot/groupMessages/send";

    $headers = array(
        'Host: api.dingtalk.com',
        'x-acs-dingtalk-access-token: ' . $accessToken,
        'Content-Type: application/json'
    );


    $data = array(
        'robotCode' => $robotCode,
        'openConversationId' => $openConversationId,
        'msgKey' => 'sampleMarkdown',
        'msgParam' => $message
    );
    
    $this->log(json_encode(array('url'=>$url,'headers'=>$headers,'data'=>$data),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

    $resp = common::http($url, $data, array(), $headers, 'json', 'POST');
    $errors   = commonModel::$requestErrors;

    $this->log(json_encode(array('resp'=>$resp, 'errors'=>$errors),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

    if(empty($errors)) return array('code' => 0,'resp'=>$resp);
    return array('code' => 1, 'message' => $errors);
    

}

    /**
     * 用发布的应用中robot发送群聊消息
     * $this->loadModel('common')->dingRobotSendApi2($text, $msgtype='sampleMarkdown', $mdTitle='通知', $agentId='1990383324', $appKey='dingooddxzzmdvlici1v', $appSecret='PF_vB11JWT3tE5SX6qGAAYCxNNx-LB2alF-0Mfu0WJLwZNxUzPMDfK6fTXFB6qEI',$robotCode='dingcrquldohmljagits',$openConversationId='cidK7WHgleolZYw9ate7v4FNA==');
     *
     * @param  string $mdText
     * @param  string $msgtype=='sampleMarkdown'  see https://open.dingtalk.com/document/orgapp/types-of-messages-sent-by-robots?spm=ding_open_doc.document.0.0.263e1563DioTEL
     * @param  string $mdTitle='通知'
     * @param  string $agentId='1990383324'  //robotApi
     * @param  string $appKey='dingcrquldohmljagits'
     * @param  string $appSecret='B31x2jWJKgFsMT-RVFFI8usEwTP2mugjwGz01yQ0WpUjaliqms90qGZkaVQ_P0Nk'
     * @param  string $robotCode='dingcrquldohmljagits'
     * @param  string $openConversationId='cidK7WHgleolZYw9ate7v4FNA=='  see https://open.dingtalk.com/tools/explorer/jsapi?spm=ding_open_doc.document.0.0.1319388cuhWImJ&id=10303
     * @access public
     * @return string
     */
    public function dingRobotSendApi2($text, $msgtype='sampleMarkdown', $mdTitle='通知', $agentId='1990383324', $appKey='dingcrquldohmljagits', $appSecret='B31x2jWJKgFsMT-RVFFI8usEwTP2mugjwGz01yQ0WpUjaliqms90qGZkaVQ_P0Nk',$robotCode='dingcrquldohmljagits',$openConversationId='cidK7WHgleolZYw9ate7v4FNA==')
    {
        $mobiles = array_unique($mobiles);
        $this->log(json_encode(array('text' => $text, 'msgtype' => $msgtype, 'mdTitle' => $mdTitle) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        $this->log(json_encode(array('agentId' => $agentId, 'appKey' => $appKey, 'appSecret' => $appSecret, 'robotCode' => $robotCode, 'openConversationId' => $openConversationId) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
        $err = 'OK';
        if (empty($text)) {
            $err = "Illegal param: text is '$text' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($msgtype)) {
            $err = "Illegal param: msgtype is '$msgtype' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($agentId)) {
            $err = "Illegal param: agentId is '$agentId' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($appKey)) {
            $err = "Illegal param: appKey is '$appKey' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($appSecret)) {
            $err = "Illegal param: appSecret is '$appSecret' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($robotCode)) {
            $err = "Illegal param: robotCode is '$robotCode' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($openConversationId)) {
            $err = "Illegal param: openConversationId is '$openConversationId' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }

        $msgtype = str_contains(strtolower($msgtype),'markdown')?'sampleMarkdown':'sampleText';


        $this->app->loadClass('dingapi', true);
        $dingapi = new dingapi($appKey, $appSecret, $agentId);
    

        $accessToken=$dingapi->getToken();
        if ($accessToken == false) {
            $err = "Fail to getToken: " . json_encode(array('agentId' => $agentId, 'appKey' => $appKey, 'appSecret' => $appSecret) ,JSON_UNESCAPED_UNICODE);
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }
        
        $url = "https://api.dingtalk.com/v1.0/robot/groupMessages/send";

        $headers = array('Content-Type' => 'application/json','Accept' => 'application/json','x-acs-dingtalk-access-token' => $accessToken);
        $data = array(
                "robotCode"=> $robotCode,
                "openConversationId"=> $openConversationId,
                "msgKey"=> $msgtype,
                "msgParam"=> "{'content':'text'}"
        );

        if ($msgtype=='sampleMarkdown'){
            $data['msgParam'] = array( "title"=>$mdTitle,"text"=>$text);
        }else{  // sampleText
            $data['msgParam'] = array( "content"=>$text);
        }

        $json_data = json_encode($data ,JSON_UNESCAPED_UNICODE);
        $this->log($json_data, __FILE__, __LINE__);
        $options = array('timeout' => '10') ;
        
        global $app;
        $app->loadClass('requests',true);
        $resp = requests::post($url, $headers, $json_data, $options);
        $this->log(json_encode($resp,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
    
        $err = "OK";
        if ($resp->status_code != 200 ){
            $err = json_encode($resp ,JSON_UNESCAPED_UNICODE) ;
        }elseif ( $resp->status_code == 200  ) {
            $body = json_decode($resp->body,JSON_UNESCAPED_UNICODE);
            if(!isset($body->processQueryKey)) {  //{"processQueryKey": "tV+mDOl5OYGPEuTkduIk9IJwSGchimgqyVLhd57gWNc="}
                $err = $body ;  //{"code":"invalidParameter.param.invalid","requestid":"8CF5418B-1C40-7222-9631-65C9C9FD7116","message":"参数不合法"}
            }
        }
    
        return $err ;
    }


