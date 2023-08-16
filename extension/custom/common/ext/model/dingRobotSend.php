<?php

    /**
     * 用robot在群里配置后，定时发送消息
     * $this->loadModel('common')->send(text,webhook_url, mobiles=[]);
     *
     * @param  string $text
     * @param  string $webhook_url
     * @param  string[] $mobiles
     * @access public
     * @return string
     */
    public function dingRobotSend($text, $webhook_url, $mobiles=array(), $msgtype='text', $mdTitle='通知')
    {
        $mobiles = array_unique($mobiles);
        $this->log(json_encode(array('text' => $text, 'webhook_url' => $webhook_url, 'mobiles' => $mobiles, 'msgtype' => $msgtype, 'mdTitle' => $mdTitle) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
        $err = 'OK';
        if (empty($webhook_url)) {
            $err = "Illegal param: webhook_url is '$webhook_url' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }else if (empty($text)) {
            $err = "Illegal param: text is '$text' ";
            $this->log(json_encode($err,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return $err ;
        }

        global $app;
        $app->loadClass('requests',true);
        
        $url = $webhook_url;
        $headers = array('Content-Type' => 'application/json','Accept' => 'application/json');
        $data = array();

        // fixed: 生产环境(php7.0)输出的是(k,v)
        $mobiles = array_values($mobiles);

        if ($msgtype=='markdown'){
            $markdown = array( "title"=>$mdTitle,"text"=>$text);
            $data = array(
                "msgtype"=>"markdown",
                "markdown"=>$markdown,
                "at"=>array( "atMobiles"=>$mobiles)
            );
        }else{
            $data = array(
                "msgtype"=>"text",
                "text"=>array( "content"=>$text),
                "at"=>array( "atMobiles"=>$mobiles)
            );
        }
        if (empty($mobiles)) $data["at"]["isAll"] = true ;


        // $this->log(json_encode($data,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        $json_data = json_encode($data ,JSON_UNESCAPED_UNICODE);
        $this->log($json_data, __FILE__, __LINE__);
        $options = array('timeout' => '10') ;
        
        $resp = requests::post($url, $headers, $json_data, $options);
        $this->log(json_encode($resp,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        $err = "OK";
        if ($resp->status_code != 200 ){
            $err = json_encode($resp ,JSON_UNESCAPED_UNICODE) ;
        }elseif ( $resp->status_code == 200  ) {
            $body = json_decode($resp->body);
            if ($body->errcode > 0 ) {
                $err = $resp->body ;  //{"errcode":0,"errmsg":"ok"}
            }
        }
    
        return $err ;
    }
