<?php

namespace omny\parser\base;


/**
 * Class BaseParserOptions
 * @package omny\parser\base
 */
class BaseParserOptions extends BaseOptions
{
    /** @var string */
    public $startUrl;

    /** @var integer */
    public $categoryId;

    /**
     * @var int
     */
    public $pagesToParse = 3;

    /**
     * @var array
     */
    public $workers = [];
    /**
     * @var array
     */
    private $defaultWorkers = [
        'loader' => [
            'className' => 'omny\parser\loader\BaseLoader',
            'optionClass' => 'omny\parser\loader\BaseLoaderOptions',
        ],
        'crawler' => [
            'className' => 'omny\parser\crawler\BaseCrawler',
            'optionClass' => 'omny\parser\crawler\BaseCrawlerOptions',
        ],
        'saver' => [
            'className' => 'omny\parser\saver\BaseSaver',
            'optionClass' => 'omny\parser\saver\BaseSaverOptions',
        ],
        'cleaner' => [
            'className' => 'omny\parser\cleaner\BaseCleaner',
            'optionClass' => 'omny\parser\cleaner\BaseCleanerOptions',
        ],
    ];

    /**
     * @var array
     */
    public $providers = [];
    /**
     * @var array
     */
    private $defaultProviders = [
        'article' => [
            'className' => 'omny\parser\providers\BaseArticleProvider',
            'params' => [],
        ],
        'category' => [
            'className' => 'omny\parser\providers\BaseCategoryProvider',
            'params' => [],
        ],
    ];

    /**
     * @param $params
     */
    public function init($params)
    {
        parent::init($params);

        $this->workers = $this->mergeWorkers();
        $this->providers = $this->mergeProviders();
    }

    /**
     * @return array
     */
    private function mergeWorkers()
    {
        return array_merge($this->defaultWorkers, $this->workers);
    }

    /**
     * @return array
     */
    private function mergeProviders()
    {
        return array_merge($this->defaultProviders, $this->providers);
    }

}