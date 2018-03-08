<?php

namespace omny\parser;

use omny\parser\cleaner\BaseCleaner;
use omny\parser\crawler\BaseCrawler;
use omny\parser\loader\BaseLoader;
use omny\parser\providers\ProviderInterface;
use omny\parser\saver\BaseSaver;

/**
 * Class Parser
 * @package omny\parser
 */
class Parser
{
    protected $baseUrl;
    protected $parserName;
    protected $categoryId;

    /** @var bool */
    public $testMode = true;

    public $options;

    /** @var BaseLoader */
    protected $loader;
    /** @var BaseCrawler */
    protected $crawler;
    /** @var BaseSaver */
    protected $saver;
    /** @var BaseCleaner */
    protected $cleaner;
    /** @var ProviderInterface */
    protected $articleProvider;
    /** @var ProviderInterface */
    protected $categoryProvider;

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
        $this->setUpWorkers();
        $this->setUpProviders();
    }

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
                $url = $this->loader->getNextPage();
                $pageCounter++;
            }
        }
    }

    public function getCategoryList($url = null)
    {
        if (empty($url)) {
            $url = $this->options->startUrl;
        }
        $page = $this->loader->getContent($url);

        $categoryList = $this->crawler->getCategoryList($page);
        $categoryList = $this->formatNodeUrls($categoryList);

        return $categoryList;
    }

    public function handlePageOfArticles($url)
    {
        $page = $this->loader->getContent($url);

        if($this->testMode) {
            echo "Parser::handlePageOfArticles -> url: " . $url . " \n";
        }

        // массив объектов типа Article
        $articleList = $this->crawler->getArticleList($page);
        $articleList = $this->formatNodeUrls($articleList);

        /** @var Article $article */
        foreach ($articleList as $article) {

            $hash = $this->createArticleHash($article->url);
            if (!empty($this->articleProvider->get($hash))) {
                continue;
            }

            $articlePage = $this->loader->getContent($article->url);

            // articleData массив значений
            $articleData = $this->crawler->getData($articlePage);

            // загружаем данные в объект
            $article->load($articleData);
            $article = $this->setContentCategory($article);
            $article = $this->setParserHash($article);

            // чистит свойства статьи
            $article = $this->cleanArticleContent($article);

            // сохраняем в базу
            $articleModel = $this->articleProvider->load($article);
            $this->articleProvider->save($articleModel);
        }
    }

    protected function canParseNextPage($counter)
    {
        return $counter < $this->options->pagesToParse;
    }

    protected function normalizeUrl($url)
    {
        if(preg_match('/((http)|(www))(.+)/', $url)) {
            return $url;
        }
        return $this->baseUrl . $url;
    }

    protected function cleanArticleContent($article)
    {
        /** @var Article $article */
        if(!empty($article->body)) {
            $article->body = $this->cleaner->clean($article->body);
        }
        if(!empty($article->short)) {
            $article->short = $this->cleaner->clean($article->short);
        }

        return $article;
    }

    private function setUpWorkers()
    {
        if($this->testMode) {
            echo "Parser::setUpWorkers \n";
        }
        $loaderOptions = $this->options->loaderOptions;
        $this->loader = $this->setWorker(BaseLoader::getClassName(), new $loaderOptions());

        $crawlerOptions = $this->options->crawlerOptions;
        $this->crawler = $this->setWorker(BaseCrawler::getClassName(), new $crawlerOptions());

        $saverOptions = $this->options->saverOptions;
        $this->saver = $this->setWorker(BaseSaver::getClassName(), new $saverOptions());

        $cleanerOptions = $this->options->cleanerOptions;
        $this->cleaner = $this->setWorker(BaseCleaner::getClassName(), new $cleanerOptions());
    }

    private function setWorker($className, $options)
    {
        return new $className($options);
    }

    private function setUpProviders()
    {
        $this->articleProvider = $this->setProvider($this->options->articleProvider);
        $this->categoryProvider = $this->setProvider($this->options->categoryProvider);
    }

    private function setProvider($className) {
        return new $className();
    }

    private function formatNodeUrls($list)
    {
        /** @var BaseEntity $node */
        foreach ($list as $node) {
            $node->url = $this->normalizeUrl($node->url);
        }

        return $list;
    }

    private function createArticleHash($url)
    {
        $source = md5($url) . "_" . $this->parserName;

        return$source;
    }

    private function setContentCategory($article)
    {
        if (!empty($this->categoryId)) {
            $article->category_id = $this->categoryId;
        }

        return $article;
    }

    private function setParserHash($article)
    {
        $article->parser_hash = $this->createArticleHash($article->url);

        return $article;
    }
}