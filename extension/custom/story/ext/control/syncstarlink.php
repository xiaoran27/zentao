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
    public function syncStarlink()
    {
        $str = $this->loadModel('common')->syncStarlink();
        echo $str;
    }
}
