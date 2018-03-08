<?php

namespace omny\parser\saver;


use omny\parser\Worker;

class BaseSaver extends Worker
{
    public function save($article)
    {
        throw new \ErrorException('No method code. ' . __METHOD__);
    }
}