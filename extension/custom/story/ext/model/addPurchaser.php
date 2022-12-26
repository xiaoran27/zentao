<?php


    public function addPurchaser($name, $code='', $category='B100')
    {
        return $this->loadExtension('bytenew')->addPurchaser($name, $code, $category);
    }


