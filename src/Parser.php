<?php

namespace omny\parser;

use omny\parser\base\BaseEntity;
use omny\parser\base\BaseParserOptions;
use omny\parser\cleaner\BaseCleaner;
use omny\parser\crawler\BaseCrawler;
use omny\parser\handlers\ArticleHandler;
use omny\parser\loader\BaseLoader;
use omny\parser\providers\BaseArticleProvider;
use omny\parser\providers\ProviderInterface;

/**
 * Class Parser
 * @package omny\parser
 */
class Parser
{
    /**
     * @var
     */
    protected $baseUrl;
    /**
     * @var
     */
    protected $parserName;

    /** @var bool */
    public $testMode = true;

    /** @var BaseParserOptions */
    public $options;

    /** @var Worker[] */
    private $worker = [];

    /** @var ProviderInterface[] */
    protected $providers = [];

    /**
     * Parser constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->init();
    }

    /**
     *
     */
    public function init()
    {
        new ParserSetup($this, $this->options);
    }

    /**
     * @param null $url
     */
    public function work($url = null)
    {
        if (empty($url)) {
            $url = $this->options->startUrl;
        }
        $pageCounter = 1;
        while (!empty($url)) {
            $this->handlePageOfArticles($url);
            $url = null;

            if ($this->canParseNextPage($pageCounter)) {
                $url = $this->getWorker('loader')->getNextPage();
                $pageCounter++;
            }
        }
    }

    /**
     * @param null $url
     * @return mixed
     */
    public function getCategoryList($url = null)
    {
        if (empty($url)) {
            $url = $this->options->startUrl;
        }
        $page = $this->getWorker('loader')->getContent($url);

        $categoryList = $this->getWorker('crawler')->getCategoryList($page);
        $categoryList = $this->formatNodeUrls($categoryList);

        return $categoryList;
    }

    /**
     * @param $url
     */
    public function handlePageOfArticles($url)
    {
        /** @var BaseLoader $loader */
        $loader = $this->getWorker('loader');
        /** @var BaseCrawler $crawler */
        $crawler = $this->getWorker('crawler');

        $page = $loader->getContent($url);

        if ($this->testMode) {
            echo "Parser::handlePageOfArticles -> url: " . $url . " \n";
        }

        // массив объектов типа Article
        $articleList = $crawler->getArticleList($page);
        $articleList = $this->formatNodeUrls($articleList);

        $savedArticles = [];

        /** @var Article $article */
        foreach ($articleList as $article) {
            $handler = new ArticleHandler([
                'article' => $article,
                'article_hash' => $this->createArticleHash($article->url),
                'category_id' => $this->getArticleCategoryId(),
                'articleProvider' => $this->getProvider('article'),
                'loader' => $this->getWorker('loader'),
                'crawler' => $this->getWorker('crawler'),
                'cleaner' => $this->getWorker('cleaner'),
            ]);
            if ($handler->run()) {
                $savedArticles[] = $article;
            };
        }

        return [$articleList, $savedArticles];
    }

    /**
     * @param $list
     * @return mixed
     */
    private function formatNodeUrls($list)
    {
        /** @var BaseEntity $node */
        foreach ($list as $node) {
            $node->url = Helper::normalizeUrl($node->url, $this->baseUrl);
        }

        return $list;
    }

    /**
     * @param $counter
     * @return bool
     */
    protected function canParseNextPage($counter)
    {
        return $counter < $this->options->pagesToParse;
    }

    /**
     * @param $url
     * @return string
     */
    protected function normalizeUrl($url)
    {
        Helper::normalizeUrl($url, $this->baseUrl);
    }

    /**
     * @param $name
     * @param $object
     */
    public function setWorker($name, $object)
    {
        $this->worker[$name] = $object;
    }

    /**
     * @param $name
     * @return Worker
     */
    public function getWorker($name)
    {
        return $this->worker[$name];
    }

    /**
     * @param $name
     * @param $object
     */
    public function setProvider($name, $object)
    {
        $this->providers[$name] = $object;
    }

    /**
     * @param $name
     * @return ProviderInterface
     */
    public function getProvider($name)
    {
        return $this->providers[$name];
    }

    /**
     * @param $url
     * @return string
     */
    private function createArticleHash($url)
    {
        $source = md5($url) . "_" . $this->parserName;

        return $source;
    }

    /**
     * @param $article
     * @return mixed
     */
    private function getArticleCategoryId()
    {
        if (!empty($this->categoryId)) {
            return $this->categoryId;
        }
        return 0;
    }

}
