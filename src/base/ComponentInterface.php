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
     * @return void
     */
    public function setHandler(string $name, string $className, array $options);

    /**
     * @param string $name
     * @return HandlerInterface
     */
    public function getHandler(string $name): HandlerInterface;

}
