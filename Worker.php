<?php

namespace omny\parser;


use omny\parser\base\BaseComponent;

class Worker extends BaseComponent
{
    protected $options;

    public function __construct($config = [])
    {
        if(!empty($config)) {
            $this->loadOptions($config);
        }

        $this->init();
    }

    public function init()
    {

    }

    public function loadOptions($options)
    {
        $this->options = $options;
    }
}