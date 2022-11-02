<?php

    public function buildSearchForm($productID, $products, $queryID, $actionURL, $branch = 0)
    {
        return $this->loadExtension('bytenew')->buildSearchForm($productID, $products, $queryID, $actionURL, $branch);
    }

