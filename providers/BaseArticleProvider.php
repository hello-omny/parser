<?php

namespace omny\parser\providers;


use omny\parser\base\BaseComponent;

class BaseArticleProvider extends BaseComponent implements ProviderInterface
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