<?php
include '../../../../../module/task/control.php';

class myTask extends task
{
    /**
     * send ding message.
     *
     * @param  string    $type= 'single'
     * @param  int    $ltdays=93
     * @param  string    $webhook=''
     * @param  bool    $autoCancel=true
     * @access public
     * @return string
     */
    public function dingsend($type = 'single', $ltdays = 93, $webhook='', $autoCancel=true)
    {
        $str = $this->task->dingSend($type, $ltdays, $webhook, $autoCancel);
        echo $str;
    }

}
