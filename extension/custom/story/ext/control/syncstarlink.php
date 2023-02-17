<?php
include '../../../../../module/story/control.php';

class myStory extends story
{
    

    /**
     * sync starlink purchasers
     *
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
