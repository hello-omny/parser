<?php

namespace omny\parser\base;


use omny\parser\handlers\HandlerInterface;
use omny\parser\ParserInterface;
use omny\parser\ParserSetup;
use omny\parser\providers\ProviderInterface;

/**
 * Class BaseParser
 * @package omny\parser\handlers
 */
class BaseParser implements ParserInterface
{
    /** @var BaseParserOptions */
    public $options;
    /** @var string */
    public $baseUrl;

    /** @var */
    public $parserName;
    /** @var array */
    protected $handlers = [];
    /** @var Component[] */
    protected $components = [];
    /** @var ProviderInterface[] */
    protected $providers = [];

    /**
     * Parser constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->init($options);
    }

    public function init($options)
    {
        new ParserSetup($this, $this->options);
    }

    /**
     * @param null|string $url
     */
    public function run($url = null)
    {

    }

    /**
     * @param string $name
     * @return string
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

}
