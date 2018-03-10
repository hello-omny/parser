<?php

namespace omny\parser\base;


class BaseComponent
{
    public function __construct($params = [])
    {
        $this->init($params);
    }

    public function init($params)
    {

    }

    public static function getClassName()
    {
        return get_called_class();
    }

    public function setAttributes($params)
    {
        $attributes = $this->attributes();

        foreach ($params as $key => $value) {
            if(in_array($key, $attributes)) {
                $this->$key = $value;
            }
        }
    }

    public function attributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }
}