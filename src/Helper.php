<?php

namespace omny\parser;


class Helper
{
    public static function normalizeUrl($url, $baseUrl = null)
    {
        if (preg_match('/((http)|(www))(.+)/', $url)) {
            return $url;
        }
        return empty($baseUrl) ? $url : $baseUrl . $url;
    }

    public static function setProperties($config, $object, $afterSet = null)
    {
        $class = new \ReflectionClass($object);
        $publicProperties = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        $publicProperties = array_map(function ($item) {
            return $item->name;
        }, $publicProperties);

        foreach ($config as $name => $value) {
            if (!is_array($value)) {
                if (property_exists($object, $name) && in_array($name, $publicProperties)) {
                    $object->$name = $value;
                    if (!empty($afterSet) && is_callable($afterSet)) {
                        call_user_func($afterSet, $config, $name);
                    }
                }
            }
        }
    }
}