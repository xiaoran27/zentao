<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


    /**
     * 据story.id找到关联业务需求IDs，对每一个业务需求ID的status和stage进行更新
     *
     * @param  int    $storyID
     * @param  bool   $createAction
     * @access public
     * @return void
     */
    public function updateReqStatusStageByID($storyID, $createAction = true)
    {
 
        $data = $this->story->updateRequirementStatusStageByStoryID($storyID, $createAction);
        echo json_encode((array('result' => 'success', 'data' => $data, 'storyID' => $storyID, 'createAction' => $createAction)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => 'success', 'data' => $data, 'storyID' => $storyID, 'createAction' => $createAction));
        
    }

}