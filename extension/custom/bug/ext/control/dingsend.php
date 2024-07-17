<?php
include '../../../../../module/bug/control.php';

class myBug extends bug
{
    /**
     * send ding message.
     *
     * @param  string    $type= 'single'
     * @param  int    $ltdays=31
     * @param  string    $webhook=''
     * @param  bool    $autoClosed=true
     * @access public
     * @return string
     */
    public function dingsend($type = 'single', $ltdays = 31, $webhook='', $autoClosed=true)
    {
        $str = $this->bug->dingSend($type, $ltdays, $webhook, $autoClosed);
        echo $str;
    }

}
