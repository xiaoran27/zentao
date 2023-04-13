<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


    /**
     * add a purchaser.
     *
     * @param  string $name
     * @access public
     * @return void
     */
    public function resetAssignedTo( $type='requirement', $product=-1)
    {
        $rows = $this->story->resetAssignedTo( $type, $product);
        echo json_encode((array('result' => ($rows>=0?'success':'failure'), 'rows' => $rows)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => ($rows>=0?'success':'failure'), 'rows' => $rows));
        
    }

}