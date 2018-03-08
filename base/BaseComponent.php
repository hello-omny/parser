<?php

namespace omny\parser;


class BaseComponent
{
    public static function getClassName()
    {
        return get_called_class();
    }
}