<?php


    public function create($objectType, $objectID, $actionType, $comment = '', $extra = '', $actor = '', $autoDelete = true, $changes=array())
    {
        return $this->loadExtension('bytenew')->create($objectType, $objectID, $actionType, $comment, $extra, $actor , $autoDelete, $changes);
    }


