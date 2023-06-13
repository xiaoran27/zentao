<?php

    /**
     * Convert zhstr to Pinyin.
     *
     * @param  string    $zhstr
     * @param  string    $fn='abbr'
     * @static
     * @access public
     * @return string
     */
    public function pinyin($zhstr, $full=false, $fn='convert')
    {
        global $app;
        static $pinyin;
        if(empty($pinyin)) $pinyin = $app->loadClass('pinyin');
        $vals = $pinyin->convert($zhstr);
        $pyfull = '';
        $pystr = '';
        foreach($vals as $py ) {
            $pyfull .= $py;
            $pystr .= $py[0];
        }
        // $this->log("zhstr=$zhstr, pystr=$pystr, pyfull=$pyfull", __FILE__, __LINE__);

        return $full ? $pyfull : $pystr ;

        /*
        if(empty($pinyin)) $fn='abbr';
        $this->log("zhstr=$zhstr, fn=$fn" , __FILE__, __LINE__);

        // https://github.com/overtrue/pinyin
        if ($fn == 'convert'){
            $vals = $pinyin->convert($zhstr,PINYIN_UMLAUT_V);
            $pystr = implode($vals);
        }else if ($fn == 'permalink'){
            $pystr = $pinyin->permalink($zhstr);
        }else if ($fn == 'sentence'){
            $pystr = $pinyin->sentence($zhstr);
        }else if ($fn == 'name'){
            $vals = $pinyin->name($zhstr);
            $pystr = implode($vals);
        }else{
            $pystr = $pinyin->abbr($zhstr);
        }

        $this->log("zhstr=$zhstr, pystr=$pystr" , __FILE__, __LINE__);

        return $pystr;
        */
    }
