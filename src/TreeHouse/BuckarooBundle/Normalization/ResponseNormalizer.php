<?php

declare (strict_types = 1);

namespace TreeHouse\BuckarooBundle\Normalization;

class ResponseNormalizer
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function normalize(array $data)
    {
        $data = array_map('urldecode', $data);
        $data = array_change_key_case($data, CASE_UPPER);

        return $data;
    }
}
