<?php

namespace omny\parser\base;


use omny\parser\handlers\HandlerInterface;
use omny\parser\ParserInterface;
use omny\parser\providers\ProviderInterface;

/**
 * Class BaseParser
 * @package omny\parser\base
 */
class BaseParser extends Object implements ParserInterface
{
    /** @var */
    public $id;

    /** @var string */
    public $baseUrl;
    /** @var string */
    public $startUrl;
    /** @var integer */
    public $categoryId;
    /** @var int */
    public $pagesToParse = 3;

    /** @var Component[] */
    private $components = [];

    /** @var array */
    private $handlers = [];

    /** @var ProviderInterface[] */
    private $providers = [];

    /** @var array */
    private $entities = [];

    /**
     * BaseParser constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->init();
    }

    /**
     *
     */
    public function init()
    {

    }

    /**
     *
     */
    public function run()
    {

    }

    /**
     * @param string $name
     * @return HandlerInterface
     */
    public function getHandler(string $name): HandlerInterface
    {
        return $this->handlers[$name];
    }

    /**
     * @param string $name
     * @param HandlerInterface $object
     */
    public function setHandler(string $name, HandlerInterface $object)
    {
        $this->handlers[$name] = $object;
    }

    /**
     * @param string $name
     * @param Component $object
     */
    public function setComponent(string $name, Component $object)
    {
        $this->components[$name] = $object;
    }

    /**
     * @param string $name
     * @return Component
     */
    public function getComponent(string $name): Component
    {
        return $this->components[$name];
    }

    /**
     * @param string $name
     * @param ProviderInterface $object
     */
    public function setProvider(string $name, ProviderInterface $object)
    {
        $this->providers[$name] = $object;
    }

    /**
     * @param string $name
     * @return ProviderInterface
     */
    public function getProvider(string $name): ProviderInterface
    {
        return $this->providers[$name];
    }

    /**
     * @param string $name
     * @param Entity $object
     */
    public function setEntity(string $name, Entity $object)
    {
        $this->entities[$name] = $object;
    }

    /**
     * @param string $name
     * @return Entity
     */
    public function getEntity(string $name): Entity
    {
        return isset($this->entities[$name]) ? $this->entities[$name]: null;
    }

}
