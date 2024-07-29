<?php
include '../../../../../module/task/control.php';

class myTask extends task
{
    /**
     * send ding message.
     *
     * @param  string    $type= 'single,robotapi'
     * @param  int    $ltdays=92
     * @param  string    $webhook=''
     * @param  bool    $autoCancel=true
     * @access public
     * @return string
     */
    public function dingsend($type = 'single,robotapi', $ltdays = 92, $webhook='', $autoCancel=true)
    {
        $str = $this->task->dingSend($type, $ltdays, $webhook, $autoCancel);
        echo $str;
    }

}
