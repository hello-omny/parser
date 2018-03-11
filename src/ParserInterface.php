<?php

namespace omny\parser;

/**
 * Interface ParserInterface
 * @package omny\parser\base
 */
interface ParserInterface
{
    /**
     * @param null|string $url
     */
    public function work($url = null);

}
