<?php

namespace omny\parser\cache;


use omny\parser\Worker;

/**
 * Class Cache
 * @package omny\parser\cache
 */
class Cache extends Worker
{
    /**
     * @var
     */
    public $dir;
    /**
     * @var string
     */
    public $alias = '/runtime/parser/cache';

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $this->setDirAlias();
    }

    /**
     * @throws \Exception
     */
    private function setDirAlias()
    {
        if (!file_exists($this->alias)) {
            throw new \Exception('Folder dose not exist. Plz, create it.');
        }
        $this->dir = $this->alias;
    }

    /**
     * @param $url
     * @return bool|mixed
     */
    public function getFile($url)
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
    public function setFile($url, $content)
    {
        $file = $this->dir . '/f_' . md5($url);
        if (file_put_contents($file, serialize($content))){
            return true;
        };

        throw new \Exception("Can't save cache file. " . __METHOD__ );
    }
}