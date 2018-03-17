<?php

namespace omny\parser\base;


/**
 * Class BaseComponent
 * @package omny\parser\base
 */
class BaseComponent
{
    /**
     * BaseComponent constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->init($params);
    }

    /**
     * @param $params
     */
    public function init($params)
    {

    }

    /**
     * @param array $params
     */
    public function setAttributes(array $params)
    {
        $attributes = array_keys($this->attributes());

        foreach ($params as $key => $value) {
            if (in_array($key, $attributes)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[$property->getName()] = $property->getValue();
            }
        }

        return $names;
    }

}
