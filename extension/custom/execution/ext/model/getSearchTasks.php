<?php


    public function getSearchTasks($condition, $pager, $orderBy)
    {
        return $this->loadExtension('bytenew')->getSearchTasks($condition, $pager, $orderBy);
    }


