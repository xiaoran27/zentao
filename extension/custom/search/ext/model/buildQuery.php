<?php


    /**
     * Build the query to execute.
     *
     * @access public
     * @return void
     */
    public function buildQuery()
    {
        /* Init vars. */
        $where        = '';
        $groups   = $this->config->search->groups;
        $groupItems   = $this->config->search->groupItems;
        $groupAndOr   = strtoupper($this->post->groupAndOr);
        $groupAndOr3   = strtoupper($this->post->groupAndOr3);
        $module       = $this->session->searchParams['module'];
        $searchParams = $module . 'searchParams';
        $fieldParams  = json_decode($_SESSION[$searchParams]['fieldParams']);
        $scoreNum     = 0;

        if($groupAndOr != 'AND' and $groupAndOr != 'OR') $groupAndOr = 'AND';
        if($groupAndOr3 != 'AND' and $groupAndOr3 != 'OR') $groupAndOr3 = 'AND';


        $formSessionName = $module . 'Form';
        $formSession     = $_SESSION[$formSessionName];
        // $this->loadModel('common')->log(json_encode($formSession,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

        for($i = 1; $i <= $groupItems * $groups; $i ++)
        {
            /* The and or between two groups. */
            if($i == 1) $where .= '(( 1  ';
            if($i == $groupItems + 1) $where .= " ) $groupAndOr ( 1 ";
            if($i == $groupItems * 2 + 1) $where .= " ) $groupAndOr3 ( 1 ";

            /* Set var names. */
            $fieldName    = "field$i";
            $andOrName    = "andOr$i";
            $operatorName = "operator$i";
            $valueName    = "value$i";


            /* Fix bug #2704. */
            $field = $this->post->$fieldName;
            if(isset($fieldParams->$field) and $fieldParams->$field->control == 'input' and $this->post->$valueName === '0') $this->post->$valueName = 'ZERO';
            if($field == 'id' and $this->post->$valueName === '0') $this->post->$valueName = 'ZERO';

            /* Skip empty values. */
            if($this->post->$valueName == false) continue;
            if($this->post->$valueName == 'ZERO') $this->post->$valueName = 0;   // ZERO is special, stands to 0.
            if(isset($fieldParams->$field) and $fieldParams->$field->control == 'select' and $this->post->$valueName === 'null') $this->post->$valueName = '';   // Null is special, stands to empty if control is select. Fix bug #3279.

            $scoreNum += 1;

            /* Set and or. */
            $andOr = strtoupper($this->post->$andOrName);
            if($andOr != 'AND' and $andOr != 'OR') $andOr = 'AND';

            /* Set operator. */
            // $this->loadModel('common')->log(json_encode($this->post->$valueName,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            $value    = addcslashes(trim($this->post->$valueName), '%');
            $operator = $this->post->$operatorName;
            if(!isset($this->lang->search->operators[$operator])) $operator = '=';

            /* Set condition. */
            $condition = '';
            if($operator == "include")
            {
                if($this->post->$fieldName == 'module')
                {
                    $allModules = $this->loadModel('tree')->getAllChildId($value);
                    if($allModules) $condition = helper::dbIN($allModules);
                }
                elseif($this->post->$fieldName == 'openedBy' || $this->post->$fieldName == 'assignedTo' )  
                {
                    // openedBy,assignedTo 支持多选查询
                    // $values    = $formSession[$valueName];
                    $values    = Array_filter ( $this->post->$valueName ); 
                    if (empty($values)) {
                        continue;
                    }else{
                        $keys = array_keys($values);
                        foreach ( $values as $k=>$v) if ( 'null' == $v ) $values[$k]='';
                        $this->loadModel('common')->log(json_encode($values,JSON_UNESCAPED_UNICODE) . ': count='. count($values) . ', $values[$keys[0]]=' . $values[$keys[0]], __FILE__, __LINE__);
    
                        $condition = helper::dbIN($values);
                    }
                }
                else
                {
                    $condition = ' LIKE ' . $this->dbh->quote("%$value%");
                }
            }
            elseif($operator == "notinclude")
            {
                if($this->post->$fieldName == 'module')
                {
                    $allModules = $this->loadModel('tree')->getAllChildId($value);
                    if($allModules) $condition = " NOT " . helper::dbIN($allModules);
                }
                else
                {
                    $condition = ' NOT LIKE ' . $this->dbh->quote("%$value%");
                }
            }
            elseif($operator == 'belong')
            {
                if($this->post->$fieldName == 'module')
                {
                    $allModules = $this->loadModel('tree')->getAllChildId($value);
                    if($allModules) $condition = helper::dbIN($allModules);
                }
                elseif($this->post->$fieldName == 'dept')
                {
                    $allDepts = $this->loadModel('dept')->getAllChildId($value);
                    $condition = helper::dbIN($allDepts);
                }
                elseif($this->post->$fieldName == 'openedBy' || $this->post->$fieldName == 'assignedTo' )
                {
                    // openedBy,assignedTo 支持多选查询
                    // $values    = $formSession[$valueName];
                    $values    = Array_filter ( $this->post->$valueName ); 
                    if (empty($values)) {
                        continue;
                    }else{
                        $keys = array_keys($values);
                        foreach ( $values as $k=>$v) if ( 'null' == $v ) $values[$k]='';
                        $this->loadModel('common')->log(json_encode($values,JSON_UNESCAPED_UNICODE) . ': count='. count($values) . ', $values[$keys[0]]=' . $values[$keys[0]], __FILE__, __LINE__);
    
                        $condition = helper::dbIN($values);
                    }
                }
                else
                {
                    $condition = ' = ' . $this->dbh->quote($value) . ' ';
                }
            }
            else
            {
                if($operator == 'between' and !isset($this->config->search->dynamic[$value])) $operator = '=';
                $condition = $operator . ' ' . $this->dbh->quote($value) . ' ';

                if($operator == '=' and $this->post->$fieldName == 'id' and preg_match('/^[0-9]+(,[0-9]*)+$/', $value) and !preg_match('/[\x7f-\xff]+/', $value))
                {
                    $values = array_filter(explode(',', trim($this->dbh->quote($value), "'")));
                    foreach($values as $value) $value = "'" . $value . "'";

                    $value     = implode(',', $values);
                    $operator  = 'IN';
                    $condition = $operator . ' (' . $value . ') ';
                }elseif($operator == '=' and ( $this->post->$fieldName == 'openedBy' || $this->post->$fieldName == 'assignedTo' ) )
                {
                    // openedBy,assignedTo 支持多选查询
                    // $values    = $formSession[$valueName];
                    $values    = Array_filter ( $this->post->$valueName ); 
                    if (empty($values)) {
                        continue;
                    }else{
                        $keys = array_keys($values);
                        foreach ( $values as $k=>$v) if ( 'null' == $v ) $values[$k]='';
                        $this->loadModel('common')->log(json_encode($values,JSON_UNESCAPED_UNICODE) . ': count='. count($values) . ', $values[$keys[0]]=' . $values[$keys[0]], __FILE__, __LINE__);
    
                        $condition = helper::dbIN($values);
                    }
                }
            }

            /* Processing query criteria. */
            if($operator == '=' and preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value))
            {
                $condition  = '`' . $this->post->$fieldName . "` >= '$value' AND `" . $this->post->$fieldName . "` <= '$value 23:59:59'";
                $where     .= " $andOr ($condition)";
            }
            elseif($operator == '!=' and preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value))
            {
                $condition  = '`' . $this->post->$fieldName . "` < '$value' OR `" . $this->post->$fieldName . "` > '$value 23:59:59'";
                $where     .= " $andOr ($condition)";
            }
            elseif($operator == '<=' and preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value))
            {
                $where .= " $andOr " . '`' . $this->post->$fieldName . "` <= '$value 23:59:59'";
            }
            elseif($operator == '>' and preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value))
            {
                $where .= " $andOr " . '`' . $this->post->$fieldName . "` > '$value 23:59:59'";
            }
            elseif($condition)
            {
                $where .= " $andOr " . '`' . $this->post->$fieldName . '` ' . $condition;
            }
        }

        $where .=" ))";
        $where  = $this->replaceDynamic($where);

        $this->loadModel('common')->log($where, __FILE__, __LINE__);

        /* Save to session. */
        $querySessionName = $this->post->module . 'Query';
        $formSessionName  = $this->post->module . 'Form';
        $this->session->set($querySessionName, $where);
        $this->session->set($formSessionName,  $_POST);
        if($scoreNum > 2 && !dao::isError()) $this->loadModel('score')->create('search', 'saveQueryAdvanced');
    }
