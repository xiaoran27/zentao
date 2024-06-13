<?php
    public function getProductBugPairs($productID, $branch = '')
    {
        return $this->loadExtension('bytenew')->getProductBugPairs($productID, $branch);
    }