<?php

namespace omny\parser\loader;


use omny\parser\base\BaseOptions;

class BaseLoaderOptions extends BaseOptions
{
    public $proxyEnabled = false;
    public $cacheEnabled = true;
    public $sleepLimit = 5;

    public $cacheOptions = [
        'alias' => '/runtime/parser/cache'
    ];

    public $curlOptions = [];

    public $proxyOptions = [];
}