<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


    /**
     * 更新业务需求的status和stage
     *
     * @param  int $$days = 1
     * @access public
     * @return void
     */
    public function updateRequirementStatusStage($days = 1)
    {
 
        $data = $this->story->updateRequirementStatusStage($days);
        echo json_encode((array('result' => 'success', 'data' => $data, 'days' => $days)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => 'success', 'data' => $data, 'days' => $days));
        
    }

}