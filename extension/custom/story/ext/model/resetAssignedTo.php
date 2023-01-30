<?php


    public function resetAssignedTo( $type='requirement', $product=-1)
    {
        return $this->loadExtension('bytenew')->resetAssignedTo( $type, $product);
    }


