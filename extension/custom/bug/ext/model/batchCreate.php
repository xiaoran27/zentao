<?php


    public function batchCreate($productID, $branch = 0, $extra = '')   
    {
        return $this->loadExtension('bytenew')->batchCreate($productID, $branch, $extra);
    }
