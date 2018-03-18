<?php

namespace omny\parser;


use omny\parser\base\BaseParser;

/**
 * Class Setup
 * @package omny\parser
 */
class Setup
{
    /** @var Parser */
    private $app;
    /** @var array */
    private $config;

    /**
     * Setup constructor.
     * @param BaseParser $app
     * @param array $config
     * @throws \Exception
     */
    public function __construct(BaseParser $app, array $config = [])
    {
        $this->app = $app;
        $this->config = $config;

        // TODO: try to set default options if needed
        // configure system defaults
        $this->setComponents();
        $this->setHandlers();
        $this->setEntities();
        $this->setProviders();

        // configure properties
        $this->setProperties($config);
    }

    /**
     * @throws \Exception
     */
    private function setComponents()
    {
        if (!isset($this->config['components']) || empty($this->config['components'])) {
            return;
        }
        $this->createContainers($this->config['components'], 'component');
        unset($this->config['components']);
    }

    /**
     * @throws \Exception
     */
    private function setHandlers()
    {
        if (!isset($this->config['handlers']) || empty($this->config['handlers'])) {
            return;
        }
        $this->createContainers($this->config['handlers'], 'handler');
        unset($this->config['handlers']);
    }

    /**
     * @throws \Exception
     */
    private function setEntities()
    {
        if (!isset($this->config['entities']) || empty($this->config['entities'])) {
            return;
        }
        $this->createContainers($this->config['entities'], 'entity');
        unset($this->config['entities']);
    }

    /**
     * @throws \Exception
     */
    private function setProviders()
    {
        if (!isset($this->config['providers']) || empty($this->config['providers'])) {
            return;
        }
        $this->createContainers($this->config['providers'], 'provider');
        unset($this->config['providers']);
    }

    /**
     * @param $config
     */
    private function setProperties($config)
    {
        Helper::setProperties($config, $this->app, function ($config, $name) {
            unset($this->config[$name]);
        });
    }

    /**
     * @param $objectsConfig
     * @param $id
     * @throws \Exception
     */
    private function createContainers($objectsConfig, $id)
    {
        $setter = $this->getSetter($id);
        foreach ($objectsConfig as $name => $config) {
            $options = isset($config['config']) ? $config['config'] : [];
            $object = $this->createObject($config['className'], $options);
            $this->app->$setter($name, $object);
        }
    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    private function getSetter($id)
    {
        $setterName = 'set' . ucfirst($id);
        if (method_exists($this->app, $setterName)) {
            return $setterName;
        }

        throw new \Exception(sprintf('Setter "%s" not found.', $id));
    }

    /**
     * @param $className
     * @param $options
     * @return mixed
     * @throws \Exception
     */
    private function createObject($className, $options)
    {
        if (!class_exists($className)) {
            throw new \Exception(sprintf('Component class "%s" not found.', $className));
        }
        echo sprintf("Creating container %s. \n", $className);

        return new $className($options);
    }

}
