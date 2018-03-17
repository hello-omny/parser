<?php

namespace omny\parser\base;

use omny\parser\cleaner\BaseCleaner;
use omny\parser\cleaner\BaseCleanerOptions;
use omny\parser\crawler\BaseCrawler;
use omny\parser\crawler\BaseCrawlerOptions;
use omny\parser\entities\Article;
use omny\parser\entities\Category;
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
    /** @var array */
    protected $handlers = [];
    /** @var array */
    private $defaultHandlers = [
        'article' => [
            'className' => ArticleHandler::class,
            'config' => [],
        ],
    ];
    /** @var array */
    protected $components = [];
    /** @var array */
    private $defaultComponents = [
        'loader' => [
            'className' => BaseLoader::class,
            'config' => BaseLoaderOptions::class,
        ],
        'crawler' => [
            'className' => BaseCrawler::class,
            'config' => BaseCrawlerOptions::class,
        ],
        'saver' => [
            'className' => BaseSaver::class,
            'config' => BaseSaverOptions::class,
        ],
        'cleaner' => [
            'className' => BaseCleaner::class,
            'config' => BaseCleanerOptions::class,
        ],
    ];
    /** @var array */
    protected $providers = [];
    /** @var array */
    private $defaultProviders = [
        'article' => [
            'className' => BaseArticleProvider::class,
            'config' => [],
        ],
        'category' => [
            'className' => BaseCategoryProvider::class,
            'config' => [],
        ],
    ];
    /** @var array */
    protected $entities = [];
    /** @var array */
    private $defaultEntities = [
        'article' => [
            'className' => Article::class,
        ],
        'category' => [
            'className' => Category::class,
        ]
    ];

    /**
     * @param $params
     */
    public function init($params)
    {
        parent::init($params);

        $this->components = $this->mergeOptions($this->defaultComponents, $this->components);
        $this->providers = $this->mergeOptions($this->defaultProviders, $this->providers);
        $this->handlers = $this->mergeOptions($this->defaultHandlers, $this->handlers);
        $this->entities = $this->mergeOptions($this->defaultEntities, $this->entities);
    }

    /**
     * @return array
     */
    public function getDefaultComponents()
    {
        return $this->defaultComponents;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getDefaultComponent($name)
    {
        return isset($this->defaultComponents[$name]) ? $this->defaultComponents[$name] : null;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getEntity($name)
    {
        return isset($this->entities[$name]) ? $this->entities[$name] : null;
    }

    /**
     * @param $default
     * @param $option
     * @return array
     */
    protected function mergeOptions($default, $option)
    {
        $result = [];
        foreach ($default as $name => $config) {
            $result[$name] = $config;
            if (isset($option[$name])) {
                if (isset($option[$name]['className'])) {
                    $result[$name]['className'] = $option[$name]['className'];
                }
                if (isset($option[$name]['config'])) {
                    $result[$name]['config'] = $option[$name]['config'];
                }
            }
        }

        return $result;
    }

    public function getComponents()
    {
        return $this->components;
    }

    public function getProviders()
    {
        return $this->providers;
    }

}
