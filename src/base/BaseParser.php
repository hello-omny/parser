<?php

namespace omny\parser\base;


use omny\parser\ParserInterface;
use omny\parser\ParserSetup;
use omny\parser\providers\ProviderInterface;
use omny\parser\Worker;

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

    /** @var bool */
    public $testMode = true;

    /** @var */
    public $parserName;
    /** @var array */
    protected $handlers = [];
    /** @var Worker[] */
    protected $workers = [];
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
    public function work($url = null)
    {

    }

    /**
     * @param string $name
     * @return string
     */
    public function getHandler(string $name): string
    {
        return $this->handlers[$name];
    }

    /**
     * @param $name string
     * @param $object string
     */
    public function setHandler(string $name, string $object)
    {
        $this->handlers[$name] = $object;
    }

    /**
     * @param string $name
     * @param Worker $object
     */
    public function setWorker(string $name, Worker $object)
    {
        $this->workers[$name] = $object;
    }

    /**
     * @param string $name
     * @return Worker
     */
    public function getWorkers(string $name): Worker
    {
        return $this->workers[$name];
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
