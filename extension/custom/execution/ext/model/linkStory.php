<?php

public function linkStory($executionID, $stories = array(), $products = array(), $extra = '', $lanes = array())
{
    return $this->loadExtension('bytenew')->linkStory($executionID, $stories, $products, $extra, $lanes);
}
