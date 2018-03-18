<?php

namespace omny\parser\base;


use omny\parser\handlers\HandlerInterface;

/**
 * Interface WorkerInterface
 * @package omny\parser\base
 */
interface ComponentInterface
{
    /**
     * @param string $name
     * @param string $className
     * @param array $options
     * @return bool
     */
    public function setHandler(string $name, string $className, array $options): bool;

    /**
     * @param string $name
     * @return null|HandlerInterface
     */
    public function getHandler(string $name);

}
