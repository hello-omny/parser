<?php

namespace omny\parser\providers;


/**
 * Interface ProviderInterface
 * @package omny\parser\providers
 */
interface ProviderInterface
{
    /**
     * @param $options
     * @return mixed
     */
    public function get($options);

    /**
     * @param $node
     * @return mixed
     */
    public function load($node);

    /**
     * @return mixed
     */
    public function save();

    /**
     * @return mixed
     */
    public function validate();
}