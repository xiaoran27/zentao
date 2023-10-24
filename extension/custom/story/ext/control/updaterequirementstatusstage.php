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
    public function updateRequirementStatusStage($days = 1,$reject = 3,$research = 30,$suspend = 30, $todo=92)
    {
 
        $data = $this->story->updateRequirementStatusStage($days,$reject,$research,$suspend, $todo);
        echo json_encode((array('result' => 'success', 'days' => $days, 'reject' => $reject, 'research' => $research, 'suspend' => $suspend, 'todo' => $todo, 'data' => $data)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => 'success', 'data' => $data, 'days' => $days));
        
    }

}