<?php

    /**
     * Save a log.
     * $this->loadModel('common')->log(json_encode($val,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
     *
     * @param  string $message
     * @param  string $file
     * @param  string $line
     * @access public
     * @return void
     */
    public function log($message, $file = '', $line = '')
    {
        global $app;

        $log = "\n" . date('H:i:s') . " $message";
        if($file) $log .= " f[$file]";
        if($line) $log .= " L=$line";
       

        $file = $app->getLogRoot() . 'banniu.' . date('Ymd') . '.log.php';
        if(!is_file($file)) file_put_contents($file, "<?php\n die();\n?>\n");

        $fh = @fopen($file, 'a');
        if($fh) fwrite($fh, $log) && fclose($fh);
    }
