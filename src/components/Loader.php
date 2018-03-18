<?php

namespace omny\parser\components;


use omny\curl\Curl;
use omny\parser\base\Component;
use omny\parser\library\Cache;

/**
 * Class BaseLoader
 * @package omny\parser\loader
 */
class Loader extends Component
{
    public $proxyEnabled = false;
    public $cacheEnabled = true;
    public $sleepLimit = 5;

    public $cacheOptions = [
        'alias' => '/runtime/parser/cache'
    ];
    public $curlOptions = [];
    public $proxyOptions = [];

    /** @var Curl */
    private $curl = null;
    /**
     * @var null
     */
    private $proxy = null;
    /** @var Cache */
    private $cache = null;

    /**
     * @param $params
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $this->curl = $this->setCurl($this->curlOptions);
        $this->cache = $this->setCache($this->cacheOptions);
        $this->proxy = $this->setProxy($this->proxyOptions);
    }

    /**
     * @param $url
     * @return bool|mixed|string
     */
    public function getContent(string $url)
    {
        if (($result = $this->cache->get($url)) != false) {
            return $result;
        }

        if (!is_null($this->proxy)) {
            $this->curl->setOption(CURLOPT_PROXY, $this->proxy);
        }

        try {
            $result = $this->curl->get($url);
            $this->cache->save($url, $result);
        } catch (\Exception $error) {
            var_dump($error->getTraceAsString());
            $result = '';
        }

        sleep($this->sleepLimit);

        return $result;
    }

    /**
     * @param $url
     * @param $baseUrl
     * @return string
     */
    public function normalizeUrl(string $url, string $baseUrl): string
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
    protected function setCurl(array $options): Curl
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
    protected function setCache(array $options)
    {
        if ($this->cacheEnabled) {
            return new Cache($options);
        }

        return null;
    }

    /**
     * @return null
     * @throws \Exception
     */
    protected function setProxy(array $options)
    {
        if ($this->proxyEnabled) {
            throw new \Exception('Plz, set up proxy in.');
        }

        return null;
    }

}
