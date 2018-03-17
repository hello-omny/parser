<?php

namespace omny\parser\base;


use omny\parser\handlers\HandlerInterface;

/**
 * Class Component
 * @package omny\parser\base
 */
class Component extends BaseComponent implements ComponentInterface
{
    /** @var BaseOptions */
    protected $options;
    /** @var array */
    protected $handlers = [];

    /**
     * @param $params
     * @throws \Exception
     */
    public function init($params)
    {
        $this->loadOptions($params);
        static::setHandlers($this->options->handlers);

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

        if (class_exists($options)) {
            $this->options = new $options();
            return true;
        }

        throw new \Exception('Option class "' . $options . '" not found.');
    }

    protected function setHandlers(array $handlers)
    {
        if (empty($handlers)) {
            return true;
        }
        foreach ($handlers as $name => $handler) {
            $this->setHandler($name, $handler['className'], $handler['config']);
        }
    }

    public function setHandler(string $name, string $className, array $options)
    {
        $this->handlers[$name] = new $className($options);
    }

    public function getHandler(string $name): HandlerInterface
    {
        return $this->handlers[$name];
    }

    public function hasHandler(string $name): bool
    {
        return isset($this->handlers[$name]);
    }

}
