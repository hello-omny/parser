<?php

namespace omny\parser\base;

class BaseOptions
{
    protected $handlers = [];

    public function __construct(array $options = [])
    {
        $this->init($options);
    }

    public function init($option)
    {

    }

    public function getHandler($name)
    {
        return isset($this->handlers[$name]) ? $this->handlers[$name] : null;
    }

    public function getHandlers()
    {
        return $this->handlers;
    }

    protected function mergeOptions($default, $option)
    {
        return array_merge($default, $option);
    }
}