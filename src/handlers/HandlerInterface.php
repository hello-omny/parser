<?php

namespace omny\parser\handlers;


/**
 * Interface HandlerInterface
 * @package omny\parser\base
 */
interface HandlerInterface
{
    /**
     * @param array $params
     * @return mixed
     */
    public function load(array $params);

    /**
     * @param bool $validate
     * @return mixed|bool
     */
    public function run($validate = true);

    /**
     * @return bool
     */
    public function validate(): bool;
}
