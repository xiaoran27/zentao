<?php
include '../../../../../module/story/control.php';

class myStory extends story
{
    

    /**
     * 在钉钉群()里给产品经理发送业务需求提醒消息
     * 
     * @see dingRobotSend
     * 
     * 
     * @access public
     * @return void
     */
    // @Deprecated
    public function dingRobotSendForPD($url=null, $type='requirement', $product=0, $sla=0, $program = 223, $responseResult = 'todo')
    {
        if (empty($type)){
            $type = 'requirement';
        }
        if (empty($product)){
            $product = 0;
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
            $url = $this->config->story->url['dingRobotSendPD'] ;
        }
        if ( empty($url) ) {
            $url = $this->config->story->url['dingRobotSend'] ;
        }

        $url = str_replace("%3A", ":", $url);
        $url = str_replace("%2F", "/", $url);
        $url = str_replace("%3F", "?", $url);
        $url = str_replace("%3D", "=", $url);
        $url = str_replace("%26", "&", $url);

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
        $dingDatas = $this->story->getTextForDing( $type, $product, $sla, $program, $responseResult );
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
            foreach ( $atMobiles as $i => $mobile ) {
                $content .= "- @$mobile($realnames[$i]) **需求集**: $contentMdIds[$i]  \n";
            }
        }

        $str = $common->dingRobotSend($content, $url, $atMobiles, $msgtype,  $mdTitle);
        echo $str;
    }

}
