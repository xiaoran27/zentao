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
    public static function pinyin($zhstr, $fn='abbr')
    {
        static $pinyin;
        if(empty($pinyin)) $pinyin = $app->loadClass('pinyin');

        if(empty($pinyin)) $fn='abbr';

        if ($fn == 'convert'){
            $vals = $pinyin->convert($zhstr,PINYIN_UMLAUT_V);
            $pystr = implode(vals);
        }else if ($fn == 'permalink'){
            $pystr = $pinyin->permalink($zhstr);
        }else if ($fn == 'sentence'){
            $pystr = $pinyin->sentence($zhstr);
        }else if ($fn == 'name'){
            $vals = $pinyin->name($zhstr);
            $pystr = implode(vals);
        }else{
            $pystr = $pinyin->abbr($zhstr);
        }

        return $pystr;
    }
