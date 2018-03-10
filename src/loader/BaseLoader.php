<?php

namespace omny\parser\loader;


use omny\curl\Curl;
use omny\parser\Worker;

/**
 * Class BaseLoader
 * @package omny\parser\loader
 */
class BaseLoader extends Worker
{
    /** @var BaseLoaderOptions */
    protected $options;

    /** @var Curl */
    private $curl;
    /**
     * @var null
     */
    private $proxy = null;
    /** @var Cache */
    private $cache;

    /**
     * @param $params
     * @throws \Exception
     */
    public function init($params)
    {
        parent::init($params);

        $this->curl = $this->setCurl($this->options->curlOptions);
        $this->cache = $this->setCache($this->options->cacheOptions);
        $this->proxy = $this->setProxy($this->options->proxyOptions);
    }

    /**
     * @throws \Exception
     */
    public function getNextPage()
    {
        throw new \Exception('No method code. ' . __METHOD__);
    }

    /**
     * @param $url
     * @return bool|mixed|string
     */
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

    /**
     * @param $url
     * @param $baseUrl
     * @return string
     */
    public function normalizeUrl($url, $baseUrl)
    {
        if(preg_match('/((http)|(www))(.+)/', $url)) {
            return $url;
        }

        return $baseUrl . $url;
    }

    /**
     * @param $options
     * @return Curl
     * @throws \Exception
     */
    protected function setCurl($options)
    {
        $curl = new Curl($options);
        $curl->init();
        $curl->setOption(CURLOPT_MAXREDIRS, 5);

        return $curl;
    }

    /**
     * @param $options
     * @return null|Cache
     */
    protected function setCache($options)
    {
        if ($this->options->cacheEnabled) {
            return new Cache($options);
        }

        return null;
    }

    /**
     * @return null
     * @throws \Exception
     */
    protected function setProxy()
    {
        if ($this->options->proxyEnabled) {
            throw new \Exception('Plz, set up proxy in ' . __METHOD__);
        }

        return null;
    }

}
