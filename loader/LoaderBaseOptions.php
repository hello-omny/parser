<?php

namespace omny\parser\loader;


use omny\parser\base\BaseOptions;

class LoaderBaseOptions extends BaseOptions
{
    public $proxyEnabled = false;
    public $cacheEnabled = true;
    public $sleepLimit = 5;
}