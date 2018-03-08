<?php

namespace omny\parser;


class BaseEntity extends BaseComponent
{
    public $url;
    public $title;

    public function load($params)
    {
        $this->setAttributes($params);
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