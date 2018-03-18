<?php

namespace omny\parser\providers;


use omny\parser\base\Object;

class ArticleProvider extends Object implements ProviderInterface
{
    protected $model;

    public function get($options)
    {
        return false;
    }

    public function load($params)
    {
        return false;
    }

    public function save()
    {
        return false;
    }

    public function validate()
    {
        return false;
    }
}