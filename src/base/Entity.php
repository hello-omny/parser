<?php

namespace omny\parser\base;


class Entity extends Object
{
    public $url;
    public $title;

    public function load($params)
    {
        $this->setAttributes($params);
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
                $names[$property->getName()] = $property->getValue($this);
            }
        }

        return $names;
    }

}