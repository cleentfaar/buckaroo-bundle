<?php

namespace TreeHouse\BuckarooBundle\Request;

interface RequestInterface
{
    /**
     * @return string
     */
    public static function getResponseClass() : string;

    /**
     * @return array
     */
    public function toArray() : array;
}
