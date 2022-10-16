<?php

    /**
     * Init the search session for the first time search.
     *
     * @param  string   $module
     * @param  array    $fields
     * @param  array    $fieldParams
     * @access public
     * @return void
     */
    public function initSession($module, $fields, $fieldParams)
    {
        $formSessionName  = $module . 'Form';
        if(isset($_SESSION[$formSessionName]) and $_SESSION[$formSessionName] != false) return;

        for($i = 1; $i <= $this->config->search->groupItems * $this->config->search->groups; $i ++)
        {
            /* Var names. */
            $fieldName    = "field$i";
            $andOrName    = "andOr$i";
            $operatorName = "operator$i";
            $valueName    = "value$i";

            $currentField = key($fields);
            $operator     = isset($fieldParams[$currentField]['operator']) ? $fieldParams[$currentField]['operator'] : '=';

            $queryForm[$fieldName]    = key($fields);
            $queryForm[$andOrName]    = 'and';
            $queryForm[$operatorName] = $operator;
            $queryForm[$valueName]    =  '';

            if(!next($fields)) reset($fields);
        }
        $queryForm['groupAndOr'] = 'and';
        $queryForm['groupAndOr3'] = 'and';
        $this->session->set($formSessionName, $queryForm);
    }
