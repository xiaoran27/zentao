<?php
include '../../../../../module/story/control.php';

class myStory extends story
{
    

    /**
     * 在钉钉群里给解决方案(SA)发送业务需求提醒消息
     * 
     * @see dingRobotSend
     *
     * 
     * @access public
     * @return void
     */
    public function dingRobotSendForSA($url=null, $type='requirement', $product=66, $sla=0)
    {
        if (empty($type)){
            $type = 'requirement';
        }
        if (empty($product)){
            $product = 66;
        }
        if (empty($sla)){
            $sla = 0;
        }

        $common = $this->loadModel('common'); 
        $common->log(json_encode(array('url'=>$url,'type'=>$type, 'product'=>$product,'sla'=>$sla),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
        // //模拟encodeURIComponent
        // echo urlencode(iconv("gbk", "UTF-8", '相当'));
        // //模拟decodeURIComponent
        // var_dump(iconv("UTF-8", "gbk",  urldecode('%E7%9B%B8%E5%BD%93')));

        // 钉钉群===BNHRQA机器人
        // https://oapi.dingtalk.com/robot/send?access_token=342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27
        // https%3A%2F%2Foapi.dingtalk.com%2Frobot%2Fsend%3Faccess_token%3D342307906f8961af0690bf236e240de4dc40a7f3eb18401766669681ee7e6a27

        // 读取配置的url
        if ( empty($url) ) {
            $url = $this->config->story->url['dingRobotSendSA'] ;
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
        $dingDatas = $this->story->getTextForDing( $type, $product, $sla);
        if (empty($dingDatas)) {
            echo '无ding数据';
            return;
        }

        $content = $dingDatas['content'] ;
        if (strpos($content, '@PD') === false) {
            $content .= "@PD" ;
        }

        $str = $common->dingRobotSend($content, $url, $dingDatas['atMobiles']);
        echo $str;
    }

}
