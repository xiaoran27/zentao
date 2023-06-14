<?php


    public function getTextForDing( $type='requirement', $product=-1, $sla=0, $program = 223, $responseResult = 'todo')
    {
        return $this->loadExtension('bytenew')->getTextForDing( $type, $product, $sla, $program, $responseResult );
    }


