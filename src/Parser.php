<?php

namespace omny\parser;

use omny\parser\base\BaseParser;
use omny\parser\base\Entity;
use omny\parser\components\Loader;
use omny\parser\components\Saver;
use omny\parser\components\Crawler;
use omny\parser\entities\Article;
use omny\parser\handlers\ArticleHandler;

/**
 * Class Parser
 * @package omny\parser
 */
class Parser extends BaseParser
{
    const BASE_CONFIG = __DIR__ . '/config.php';

    public function init()
    {
        parent::init();
        $config = include __DIR__ . '/config.php';
        new Setup($this, $config);
    }

    /**
     * @param null $url
     * @throws \Exception
     */
    public function run()
    {
        if (empty($url)) {
            $url = $this->startUrl;
        }
        $pageCounter = 1;
        /** @var Loader $loader */
        $loader = $this->getComponent('loader');
        /** @var Crawler $crawler */
        $crawler = $this->getComponent('crawler');

        while (!empty($url)) {
            echo sprintf("Parse url: %s. \n", $url);
            $page = $loader->getContent($url);
            $crawler->setHtml($page);

            $url = null;
            if ($this->canParseNextPage($pageCounter)) {
                $url = $crawler->getNextPage();
                $pageCounter++;
            }

            $this->handlePageOfArticles();

            if (!empty($url)) {
                echo sprintf("Current page: %d. Next url: %s \n", $pageCounter, $url);
            }
        }
    }

    /**
     * @param null|string $url
     * @return mixed
     */
    public function getCategoryList($url = null)
    {
        if (empty($url)) {
            $url = $this->startUrl;
        }
        $page = $this->getComponent('loader')->getContent($url);

        $categoryList = $this->getComponent('crawler')->getCategoryList($page);
        $categoryList = $this->formatNodeUrls($categoryList);

        return $categoryList;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function handlePageOfArticles()
    {
        /** @var Crawler $crawler */
        $crawler = $this->getComponent('crawler');

        $articleList = $crawler->getArticleList();
        $articleList = $this->formatNodeUrls($articleList);
        $savedArticles = [];
        $articleHandler = $this->getHandler('article');

        /** @var Article $article */
        foreach ($articleList as $article) {
            /** @var ArticleHandler $handler */
            $handler = new $articleHandler();
            $handler->load([
                'article' => $this->getArticleDataFromHtml($article),
                'articleProvider' => $this->getProvider('article'),
            ]);
            echo sprintf("\nParse article: %s. \nFrom url %s. \n", $article->title, $article->url);
            if ($handler->run()) {
                $savedArticles[] = $article;
            };
        }

        return [$articleList, $savedArticles];
    }

    /**
     * @param Article $article
     * @return Article
     * @throws \Exception
     */
    private function getArticleDataFromHtml(Article $article)
    {
        $articleHtml = $this->getComponent('loader')
            ->getContent($article->url);
        /** @var Crawler $crawler */
        $crawler = $this->getComponent('crawler');
        $crawler->setHtml($articleHtml);
        $data = $crawler->getDataFromHtml($articleHtml);

        $article->load($data);
        $article->parser_hash = $this->createArticleHash($article->url);
        $article->category_id = $this->getArticleCategoryId();
        echo sprintf("Article hash %s. \n", $article->parser_hash);
        echo sprintf("Article category id %s.\n", $article->category_id);

        $article = static::loadExtendedDataToArticle($article);

        if (!empty($article->preview)) {
            $article->preview = $this->getComponent('saver')->savePreview($article->preview);
            echo sprintf("Article preview %s.\n", $article->preview);
        }
        if (!empty($article->body)) {
            $article->body = $this->getComponent('cleaner')->clean($article->body);
            echo sprintf("Article body %s. \n", empty($article->body) ? 'empty' : 'exist');
        }
        if (!empty($article->short)) {
            $article->short = $this->getComponent('cleaner')->clean($article->short);
            echo sprintf("Article short %s.\n", empty($article->short) ? 'empty' : 'exist');
        }

        return $article;
    }

    protected function loadExtendedDataToArticle(Article $article)
    {
        return $article;
    }

    /**
     * @param array $list
     * @return array
     */
    protected function formatNodeUrls(array $list)
    {
        /** @var Entity $node */
        foreach ($list as $node) {
            $node->url = Helper::normalizeUrl($node->url, $this->baseUrl);
        }

        return $list;
    }

    /**
     * @param int $counter
     * @return bool
     */
    protected function canParseNextPage(int $counter)
    {
        return $counter < $this->pagesToParse;
    }

    /**
     * @param $url
     * @return string
     */
    protected function normalizeUrl(string $url)
    {
        Helper::normalizeUrl($url, $this->baseUrl);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function createArticleHash(string $url)
    {
        $source = md5($url) . "_" . $this->id;

        return $source;
    }

    /**
     * @return int
     */
    protected function getArticleCategoryId()
    {
        if (!empty($this->categoryId)) {
            return $this->categoryId;
        }
        return 0;
    }

    /**
     * @return array
     */
    public function getDefaultConfig(): array
    {
        return require self::BASE_CONFIG;
    }
}
