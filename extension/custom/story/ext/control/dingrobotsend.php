<?php
include '../../../../../module/story/control.php';

class myStory extends story
{
    

    /**
     * 在钉钉群里配置webhook后，据需求类型(requirement,story), 指派人为''重赋值，或SLA超时(hour)发送提醒消息
     * 
     * @param  string $url  钉钉群里配置的机器人webhook(支持javascript: encodeURIComponent编码)
     * @param  string $type='requirement'  (all, requirement,story)
     * @param  int $product=0  -1==所有; 0==非SA; >0 某个产品。66=解决方案(SA专用)
     * @param  int $sla=0  0==所有未响应记录; >0 SLA超时(hour)的记录
     * @access public
     * @return void
     */
    public function dingRobotSend($url=null, $type='all', $product=-1, $sla=0)
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
        }-

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
            $url = $this->config->story->dingRobotSend->url ;
        }

        $url = str_replace("%3A", ":", $url);
        $url = str_replace("%2F", "/", $url);
        $url = str_replace("%3F", "?", $url);
        $url = str_replace("%3D", "=", $url);

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

        $str = $common->dingRobotSend($dingDatas['content'], $url, $dingDatas['atMobiles']);
        echo $str;
    }
}
