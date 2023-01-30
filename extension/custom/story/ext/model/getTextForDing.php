<?php


    public function getTextForDing( $type='requirement', $product=-1, $sla=0)
    {
        return $this->loadExtension('bytenew')->getTextForDing( $type, $product, $sla);
    }


