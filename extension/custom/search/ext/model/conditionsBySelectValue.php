<?php

    public function conditionsBySelectValue($value, $operator='=')
    {
        return $this->loadExtension('bytenew')->conditionsBySelectValue($value, $operator='=');
    }

    /**
     * 据select组件拼装sql条件
     *
     * @access public
     * @return str
     */
    public function deprecated_conditionsBySelectValue($value, $operator='=')
    // public function conditionsBySelectValue($value, $operator='=')
    {
        $values    = Array_filter ( $value ); 
        foreach ( $values as $k=>$v) {
            if ( 'all' == $v ) {
                $values=array();  // 选择的有all值,认为是无条件
                break;
            }
        }
        if (empty($values))  return '';
           
        
        $keys = array_keys($values);
        foreach ( $values as $k=>$v) if ( 'null' == $v ) $values[$k]='';
        $this->loadModel('common')->log(json_encode($values,JSON_UNESCAPED_UNICODE) . ': count='. count($values) . ', $values[$keys[0]]=' . $values[$keys[0]], __FILE__, __LINE__);

        $notin = $operator == '!=' || $operator == "notinclude" ;
        $condition = ( $notin ? " NOT " : "" ) . helper::dbIN($values);
        return $condition;
        
    }
                