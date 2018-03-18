<?php

namespace omny\parser\base;
use omny\parser\Helper;


/**
 * Class Object
 * @package omny\parser\base
 */
class Object
{
    /**
     * Object constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            $this->configure($this, $config);
        }
        $this->init();
    }

    /**
     *
     */
    public function init()
    {
    }

    /**
     * @param $object
     * @param $properties
     * @return mixed
     */
    protected function configure($object, $properties)
    {
        $class = new \ReflectionClass($this);

        foreach ($properties as $name => $value) {
            $property = $class->getProperty($name);
            if (!empty($property) && $property->isPublic() && !$property->isStatic()) {
                $object->$name = $value;
            }
        }

        return $object;
    }

}
