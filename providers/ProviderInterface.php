<?php

namespace omny\parser\providers;


interface ProviderInterface
{
    public function get($options);

    public function load($node);

    public function save();

    public function validate();
}