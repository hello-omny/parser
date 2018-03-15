<?php

namespace omny\parser\base;

class BaseOptions
{
    public $handlers = [];

    public function __construct(array $options = [])
    {
        $this->init($options);
    }

    public function init($option)
    {

    }

    protected function mergeOptions($default, $option)
    {
        return array_merge($default, $option);
    }
}