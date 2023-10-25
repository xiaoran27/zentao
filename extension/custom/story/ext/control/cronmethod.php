<?php
include '../../../../../module/story/control.php';
// helper::importControl('story');

class myStory extends story
{

    /**
     * 据task+action找到开发的最早启动时间作为对应产品需求的prd完成时间(rspAcceptTime)
     * @link  ./updateStoryAcceptTimeByTask.php
     * 
     * @param  int    $days=1  一天内
     * @param  int    $taskID=0 表示全部
     * @param  bool   $createAction
     * @access public
     * @return void
     */
    public function updateStoryAcceptTimeByTask($days=1, $taskID=0, $createAction = true)
    {
 
        $data = $this->story->updateStoryRspAcceptTimeByTask($days, $taskID, $createAction );
        echo json_encode((array('result' => 'success', 'data' => $data, 'days' => $days, 'taskID' => $taskID, 'createAction' => $createAction)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send((array('result' => 'success', 'data' => $data, 'days' => $days, 'taskID' => $taskID, 'createAction' => $createAction));
        
    }

    /**
     * 更新业务需求的status和stage
     * @link ./updateRequirementStatusStage.php
     *
     * @param  int $$days = 1
     * @access public
     * @return void
     */
    public function updateRequirementStatusStage($days = 1, $reject = 3, $research = 30, $suspend = 30, $todo = 92)
    {
 
        $data = $this->story->updateRequirementStatusStage($days,$reject,$research,$suspend, $todo);
        echo json_encode((array('result' => 'success', 'days' => $days, 'reject' => $reject, 'research' => $research, 'suspend' => $suspend, 'todo' => $todo, 'data' => $data)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => 'success', 'data' => $data, 'days' => $days));
        
    }

    
    /**
     * 据story.id找到关联业务需求IDs，对每一个业务需求ID的status和stage进行更新
     * @link ./updateRequirementStatusStageByStoryID.php
     *
     * @param  int    $storyID
     * @param  bool   $createAction
     * @access public
     * @return void
     */
    public function updateRequirementStatusStageByStoryID($storyID, $createAction = true)
    {
 
        $data = $this->story->updateRequirementStatusStageByStoryID($storyID, $createAction);
        echo json_encode((array('result' => 'success', 'data' => $data, 'storyID' => $storyID, 'createAction' => $createAction)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => 'success', 'data' => $data, 'storyID' => $storyID, 'createAction' => $createAction));
        
    }

    /**
     * 整合所有需要在cron的方法，规避control.fetch(...)仅能加载一个control扩展的
     *
     * @param  string $name
     * @access public
     * @return void
     */
    public function cronMethod()
    {
       
        echo json_encode("success",JSON_UNESCAPED_UNICODE);
    }

    /**
     * get a purchaser.
     * @link ./getPurchaserList.php
     *
     * @param  string $name
     * @access public
     * @return void
     */
    public function getPurchaserList($codeOrName='')
    {
        $common = $this->loadModel('common'); //
        $data = $common->getPurchaserList($codeOrName);
        // $common->log('getPurchaserList: ' . json_encode(array("codeOrName"=>$codeOrName,"data"=>$data),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
       
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 在钉钉群里配置webhook后，据需求类型(requirement,story), 指派人为''重赋值，或SLA超时(hour)发送提醒消息
     * @link ./dingRobotSend.php
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
     * sync starlink purchasers
     * @link ./syncStarlink.php
     * 
     * @access public
     * @return void
     */
    public function syncStarlink($timeout=30)
    {
        $str = $this->loadModel('common')->syncStarlink($timeout);
        echo $str;
    }




}