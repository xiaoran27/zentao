<?php
include '../../../../../module/story/control.php';

class myStory extends story
{
    

    /**
     * 在钉钉群里配置webhook后，据需求类型(requirement,story), 指派人为''重赋值，或SLA超时(hour)发送提醒消息
     * 
     * @param  string $url  钉钉群里配置的机器人webhook(必须base64编码)  
     * @param  string $type='requirement'  (all, requirement,story)
     * @param  int $product=0  -1==所有; 0==非SA; >0 某个产品。66=解决方案(SA专用)
     * @param  int $sla=0  0==所有未响应记录; >0 SLA超时(hour)的记录
     * @param int $program =223  <0==所有; 223=正马项目集
     * @param string $responseResult ='todo' 多个用','分隔 (all, todo,recieved,research,suspend )
     * @access public
     * @return void
     */
    public function dingRobotSend($url=null, $type='all', $product=-1, $sla=0, $program = 223, $responseResult = 'todo' )
    {
        $str = $this->story->dingRobotSend($url, $type, $product, $sla, $program , $responseResult );
        echo $str;
    }

    /**
     * 在钉钉群里配置webhook后，据需求类型(requirement,story), 指派人为''重赋值，或SLA超时(hour)发送提醒消息
     * 
     * @param  string $url  钉钉群里配置的机器人webhook(支持base64或encodeURIComponent编码)
     * @param  string $type='requirement'  (all, requirement,story)
     * @param  int $product=0  -1==所有; 0==非SA; >0 某个产品。66=解决方案(SA专用)
     * @param  int $sla=0  0==所有未响应记录; >0 SLA超时(hour)的记录
     * @param int $program =223  <0==所有; 223=正马项目集
     * @param string $responseResult ='todo' 多个用','分隔 (all, todo,recieved,research,suspend )
     * @access public
     * @return void
     */
    public function deprecated_dingRobotSend($url=null, $type='all', $product=-1, $sla=0, $program = 223, $responseResult = 'todo' )
    {
        if (empty($type)){
            $type = 'all';
        }
        if (empty($product)){
            if ( "0" == $product) {
                $product = 0;
            }else{
                $product = -1;
            }
        }
        if (empty($sla)){
            $sla = 0;
        }        
        if (empty($program)) {
            $program = 223;
        }
        if (empty($responseResult)) {
            $responseResult = 'todo';
        }

        $common = $this->loadModel('common'); 
        $common->log(json_encode(array('url'=>$url,'type'=>$type, 'product'=>$product,'sla'=>$sla,'program'=>$program,'responseResult'=>$responseResult),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
        // //模拟encodeURIComponent
        // echo urlencode(iconv("gbk", "UTF-8", '相当'));
        // //模拟decodeURIComponent
        // var_dump(iconv("UTF-8", "gbk",  urldecode('%E7%9B%B8%E5%BD%93')));

        // 钉钉群===BNHRQA机器人
        // https://oapi.dingtalk.com/robot/send?access_token=342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27
        // https%3A%2F%2Foapi.dingtalk.com%2Frobot%2Fsend%3Faccess_token%3D342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27

        // 读取配置的url
        if ( empty($url) ) {
            $url = $this->config->story->url['dingRobotSend'] ;
        }        
        // url支持base64编码
        $url_new = base64_decode($url,true);  
        if (  $url_new &&  $url === base64_encode($url_new) ) {
            $url = $url_new ;
        }
        $url = rawurlencode($url); // encodeURIComponent
        $pattern = "/^https:\/\/oapi[.]dingtalk[.]com\/robot\/send\?access_token=[a-z0-9]{64}$/i";
        $match = preg_match($pattern, $url);
        $common->log(json_encode(array('url' => $url, 'match' => $match) ,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        if (empty($url) ||  $match < 1 ) {
            echo "无效的url='$url'";
            return;
        }
        
        
        if (empty($sla) ) {
            $sla=0;
        }


        $rows = $this->story->resetAssignedTo( $type, $product);
        $dingDatas = $this->story->getTextForDing( $type, $product, $sla, $program, $responseResult);  
        if (empty($dingDatas)) {
            echo '无ding数据';
            return;
        }

        $msgtype='markdown' ;

        // array('content' => $content, 'atMobiles' => $atMobiles, 'realnames' => $realnames, 'contents' => $contents, 'contentMdIds' => $contentMdIds);
        $content = $dingDatas['content'];
        $atMobiles = $dingDatas['atMobiles'];
        $mdTitle='需求提醒 ';

        // +需求指派对象
        if ($product < 0) {
            $content .= "@PD+@SA";
            $mdTitle .= "@PD+@SA";
        } else if ($product >= 0) {
            $content .= ($product != 66 ? "@PD" : "@SA");
            $mdTitle .= ($product != 66 ? "@PD" : "@SA");
        }

        if ($msgtype=='markdown') {
            $realnames = $dingDatas['realnames'];
            $contents = $dingDatas['contents'];
            $contentMdIds = $dingDatas['contentMdIds'];

            $content = '';
            foreach ( $contents as $i=>$value ) {
                // $content .= "- @$atMobiles[$i] ($realnames[$i]) **需求集**: $contentMdIds[$i]  \n";
                $content .= "$value **需求集**: $contentMdIds[$i]  \n";
            }
        }

        $str = $common->dingRobotSend($content, $url, $atMobiles, $msgtype,  $mdTitle);
        echo $str;
    }
}
