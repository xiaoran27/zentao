<?php
include '../../../../../module/story/control.php';

class myStory extends story
{
    /**
     * send ding message.
     *
     * @param  string    $type= 'single'
     * @param  int    $ltdays=92
     * @param  string    $webhook=''
     * @param  bool    $autoCancel=true
     * @access public
     * @return string
     */
    public function dingsend($type = 'single,robotapi', $ltdays = 92, $webhook='', $autoCancel=true)
    {
        $str = $this->story->dingSend($type, $ltdays, $webhook, $autoCancel);
        echo $str;
    }

}
