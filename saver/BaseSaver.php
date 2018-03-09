<?php

namespace omny\parser\saver;


use omny\parser\Article;
use omny\parser\Worker;

class BaseSaver extends Worker
{
    /**
     * @param $article Article
     * @throws \Exception
     */
    public function save(Article $article)
    {
        throw new \Exception('No method code. ' . __METHOD__);
    }
}