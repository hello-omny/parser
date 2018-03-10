<?php

namespace omny\parser;


use omny\parser\base\BaseComponent;

/**
 * Class Worker
 * @package omny\parser
 */
class Worker extends BaseComponent
{
    protected $options;

    /**
     * @param $params
     * @throws \Exception
     */
    public function init($params)
    {
        echo " > Create worker: " . static::class . "\n";
        $this->loadOptions($params);

        parent::init($params);
    }

    /**
     * @param $options
     * @throws \Exception
     * @return boolean
     */
    protected function loadOptions($options)
    {
        if (!is_string($options)) {
            throw new \Exception('Options class name must be a string.');
        }
        if (class_exists($options, true)) {
            $this->options = new $options();

            return true;
        }

        throw new \Exception('Option class "' . $options . '" not found.');
    }

}
