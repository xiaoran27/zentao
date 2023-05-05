<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


        /**
     * 据task+action找到开发的最早启动时间作为对应产品需求的prd完成时间(rspAcceptTime)
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

}