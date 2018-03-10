<?php

namespace omny\parser\base;
use omny\parser\cleaner\BaseCleaner;
use omny\parser\cleaner\BaseCleanerOptions;
use omny\parser\crawler\BaseCrawler;
use omny\parser\crawler\BaseCrawlerOptions;
use omny\parser\handlers\ArticleHandler;
use omny\parser\loader\BaseLoader;
use omny\parser\loader\BaseLoaderOptions;
use omny\parser\providers\BaseArticleProvider;
use omny\parser\providers\BaseCategoryProvider;
use omny\parser\saver\BaseSaver;
use omny\parser\saver\BaseSaverOptions;


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
    /** @var array  */
    public $handlers = [];
    /** @var array  */
    private $defaultHandlers = [
        'article' => ArticleHandler::class,
    ];
    /** @var array */
    public $workers = [];
    /** @var array */
    private $defaultWorkers = [
        'loader' => [
            'className' => BaseLoader::class,
            'optionClass' => BaseLoaderOptions::class,
        ],
        'crawler' => [
            'className' => BaseCrawler::class,
            'optionClass' => BaseCrawlerOptions::class,
        ],
        'saver' => [
            'className' => BaseSaver::class,
            'optionClass' => BaseSaverOptions::class,
        ],
        'cleaner' => [
            'className' => BaseCleaner::class,
            'optionClass' => BaseCleanerOptions::class,
        ],
    ];
    /** @var array */
    public $providers = [];
    /** @var array */
    private $defaultProviders = [
        'article' => [
            'className' => BaseArticleProvider::class,
            'params' => [],
        ],
        'category' => [
            'className' => BaseCategoryProvider::class,
            'params' => [],
        ],
    ];

    /**
     * @param $params
     */
    public function init($params)
    {
        parent::init($params);

        $this->workers = $this->mergeOptions($this->defaultWorkers, $this->workers);
        $this->providers = $this->mergeOptions($this->defaultProviders, $this->providers);
        $this->handlers = $this->mergeOptions($this->defaultHandlers, $this->handlers);
    }

    private function mergeOptions($default, $option)
    {
        return array_merge($default, $option);
    }
}
