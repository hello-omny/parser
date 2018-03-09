<?php

namespace omny\parser\base;


class BaseComponent
{
    public static function getClassName()
    {
        return get_called_class();
    }
}