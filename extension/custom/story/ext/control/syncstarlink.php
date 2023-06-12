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
    public function syncStarlink($timeout=30, $minutes=5)
    {
        $str = $this->loadModel('common')->syncStarlink($timeout, $minutes);
        echo $str;
    }
}
