<?php

namespace omny\parser\loader;


use omny\curl\Curl;
use omny\parser\cache\Cache;
use omny\parser\Worker;

class BaseLoader extends Worker
{
    /** @var Curl */
    private $curl;
    private $proxy = null;
    /** @var Cache */
    private $cache;

    public function init()
    {
        parent::init();

        $this->curl = $this->setCurl();
        $this->cache = $this->setCache();
        $this->proxy = $this->setProxy();
    }

    public function getNextPage()
    {
        throw new \ErrorException('No method code. ' . __METHOD__);
    }

    public function getContent($url)
    {
        if (($result = $this->cache->getFile($url)) != false) {
            return $result;
        }

        if (!is_null($this->proxy)) {
            $this->curl->setOption(CURLOPT_PROXY, $this->proxy);
        }

        try {
            $result = $this->curl->get($url);
            $this->cache->setFile($url, $result);
        } catch (\Exception $error) {
            var_dump($error->getTraceAsString());
            $result = '';
        }

        sleep($this->options->sleepLimit);

        return $result;
    }

    private function setCurl()
    {
        $curl = new Curl();
        $curl->init();
        $curl->setOption(CURLOPT_MAXREDIRS, 5);

        return $curl;
    }

    private function setCache()
    {
        return new Cache();
    }

    private function setProxy()
    {
        if ($this->options->proxyEnabled) {
            throw new \ErrorException('Plz, set up proxy in ' . __METHOD__);
        }

        return null;
    }

    public function normalizeUrl($url, $baseUrl)
    {
        if(preg_match('/((http)|(www))(.+)/', $url)) {
            return $url;
        }

        return $baseUrl . $url;
    }
}