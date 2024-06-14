<?php


    public function dbhFetchAll($sql, $table='__table__', $keyField = '')
    {
        return $this->loadExtension('bytenew')->dbhFetchAll($sql, $table, $keyField );
    }

    /**
     * 获取所有记录。
     * Fetch all records.
     *
     * @param  string $sql     SQL语句
      * @param  string $table='__table__'  缓存的名称
     *                              the key field, thus the return records is keyed by this field
      * @param  string $keyField     返回以该字段做键的记录
     *                              the key field, thus the return records is keyed by this field
     * @access public
     * @return array the records
     */
    public function dbhFetchAll0($sql, $table='__table__', $keyField = '', $reload=false)
    {
        $table=empty($table)?'__table__':$table;

        $key   = 'fetchAll-' . md5($sql . $keyField);
        if($reload === false and isset(dao::$cache[$table][$key]))
        {
            $rows   = dao::$cache[$table][$key];
            $result = array();
            foreach($rows as $i => $row) $result[$i] = $this->dao->getRow($row);
            return $result;
        }

        $stmt = null;
        try
        {
            $stmt = $this->dbh->query($sql);
        }
        catch (PDOException $e)
        {
            $message  = $e->getMessage();
            $message .= ' ' . helper::checkDB2Repair($e);

            $this->app->triggerError($message . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
            return false;
        }


        dao::$cache[$table][$key] = array();
        if(empty($keyField))
        {
            $rows   = $stmt->fetchAll();
            dao::$cache[$table][$key] = $rows;
            $result = array();
            foreach($rows as $i => $row) $result[$i] = $this->dao->getRow($row);
            return $result;
        }

        $rows = array();
        while($row = $stmt->fetch())
        {
            dao::$cache[$table][$key][$row->$keyField] = $row;
            $rows[$row->$keyField] = $this->dao->getRow($row);
        }

        return $rows;
    }