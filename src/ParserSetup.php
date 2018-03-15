<?php

namespace omny\parser;


use omny\parser\base\BaseParser;
use omny\parser\base\BaseParserOptions;

/**
 * Class ParserSetup
 * @package omny\parser
 */
class ParserSetup
{
    /** @var BaseParser|Parser */
    private $parser;
    /** @var BaseParserOptions */
    private $options;

    /**
     * ParserSetup constructor.
     * @param Parser $parser
     * @param BaseParserOptions $options
     * @throws \Exception
     */
    public function __construct(BaseParser $parser, BaseParserOptions $options)
    {
        $this->parser = $parser;
        $this->options = $options;

        if ($this->validateOptions()) {
            $this->setUpComponents($this->options->components);
            $this->setUpProviders($this->options->providers);
            $this->setHandlers($this->options->handlers);
        } else {
            throw new \Exception('Validation of parser options fail.');
        }
    }

    /**
     * @return bool
     */
    public function validateOptions()
    {
        return true;
    }

    /**
     * @param $handlers
     * @throws \Exception
     */
    private function setHandlers(array $handlers)
    {
        foreach ($handlers as $name => $config) {
            $object = $this->createObject($config['className'], $config['config']);
            $this->parser->setHandler($name, $object);
        }
    }

    /**
     * @param array $components
     * @throws \Exception
     */
    private function setUpComponents(array $components)
    {
        foreach ($components as $name => $config) {
            $object = $this->createObject($config['className'], $config['config']);
            $this->parser->setComponent($name, $object);
        }
    }

    /**
     * @param array $providers
     * @throws \Exception
     */
    private function setUpProviders(array $providers)
    {
        foreach ($providers as $name => $config) {
            $provider = $this->createObject($config['className'], $config['config']);
            $this->parser->setProvider($name, $provider);
        }
    }

    /**
     * @param $className
     * @param $options
     * @return mixed
     * @throws \Exception
     */
    private function createObject($className, $options)
    {
        if (class_exists($className)) {
            return new $className($options);
        }
        throw new \Exception('Ops! Class "' . $className . '" not found.');
    }

}