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
    private $parser;
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
            $this->setUpWorkers();
            $this->setUpProviders();
            $this->setHandlers();
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

    private function setHandlers()
    {
        $handlers = $this->options->handlers;

        foreach ($handlers as $name => $className) {
            $this->parser->setHandler($name, $className);
        }
    }

    /**
     * @throws \Exception
     */
    private function setUpWorkers()
    {
        $workers = $this->options->workers;
        echo $this->parser->testMode ? "" : " > Parser::setUpWorkers \n";

        foreach ($workers as $name => $config) {
            $className = $config['className'];
            $options = $config['optionClass'];

            echo $this->parser->testMode ? "" : " > Worker: " . $className . " \n";
            echo $this->parser->testMode ? "" : " > Options: " . $options . " \n";
            $object = $this->createObject($className, $options);

            $this->parser->setWorker($name, $object);
        }
    }

    /**
     * @throws \Exception
     */
    private function setUpProviders()
    {
        $providers = $this->options->providers;

        foreach ($providers as $name => $config) {
            // TODO: validate parameters
            $className = $config['className'];
            $params = $config['params'];
            $provider = $this->createObject($className, $params);

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
