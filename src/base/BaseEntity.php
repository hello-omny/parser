<?php

namespace omny\parser\base;


class BaseEntity extends BaseComponent
{
    public $url;
    public $title;

    public function load($params)
    {
        $this->setAttributes($params);
    }

}