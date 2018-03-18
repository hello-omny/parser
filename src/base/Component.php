<?php

namespace omny\parser\base;


use omny\parser\handlers\HandlerInterface;

/**
 * Class Component
 * @package omny\parser\base
 */
class Component extends Object implements ComponentInterface
{
    /** @var array */
    public $handlers = [];

    /**
     * Component constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            $this->configure($this, $config);
        }
        $this->init();
    }

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        if (!empty($this->handlers)) {
            static::setHandlers($this->handlers);
        }
    }

    /**
     * @param array $handlers
     * @throws \Exception
     */
    protected function setHandlers(array $handlers)
    {
        foreach ($handlers as $name => $options) {
            $this->setHandler($name, $options['className'], $options['config']);
        }
    }

    /**
     * @param string $name
     * @param string $className
     * @param array $options
     * @return bool|void
     * @throws \Exception
     */
    public function setHandler(string $name, string $className, array $options): bool
    {
        if (class_exists($className)) {
            $this->handlers[$name] = new $className($options);
            return true;
        }

        throw new \Exception(sprintf('Component handler class "%s" not found', $className));
    }

    /**
     * @param string $name
     * @return null|HandlerInterface
     */
    public function getHandler(string $name)
    {
        return isset($this->handlers[$name]) ? $this->handlers[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHandler(string $name): bool
    {
        return (bool)!empty($this->getHandler($name));
    }

}
