<?php

namespace omny\parser\library;


use omny\parser\base\Object;

/**
 * Class Cache
 * @package omny\parser\cache
 */
class Cache extends Object
{
    /** @var */
    public $dir;
    /** @var string */
    public $alias = '/runtime/parser/cache';

    /**
     * @param $params
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        $this->setFolder();
    }

    /**
     * @throws \Exception
     */
    private function setFolder()
    {
        if (!file_exists($this->alias)) {
            throw new \Exception(sprintf('Cache folder "%s" dose not exist.', $this->alias));
        }
        $this->dir = $this->alias;
    }

    /**
     * @param $url
     * @return bool|mixed
     */
    public function get(string $url)
    {
        $file = $this->dir . '/f_' . md5($url);
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }

        return false;
    }

    /**
     * @param $url
     * @param $content
     * @return bool
     * @throws \Exception
     */
    public function save(string $url, string $content): bool
    {
        $file = $this->dir . '/f_' . md5($url);
        if (file_put_contents($file, serialize($content))){
            return true;
        };

        throw new \Exception(sprintf('Can\'t save cache file "%s". ', $file) );
    }

}