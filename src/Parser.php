<?php

namespace omny\parser;

use omny\parser\base\BaseEntity;
use omny\parser\base\BaseParser;
use omny\parser\crawler\BaseCrawler;
use omny\parser\handlers\ArticleHandler;
use omny\parser\loader\BaseLoader;

/**
 * Class Parser
 * @package omny\parser
 */
class Parser extends BaseParser
{
    /**
     * @param null|string $url
     */
    public function run($url = null)
    {
        if (empty($url)) {
            $url = $this->options->startUrl;
        }
        $pageCounter = 1;
        while (!empty($url)) {
            $this->handlePageOfArticles($url);
            $url = null;

            if ($this->canParseNextPage($pageCounter)) {
                $url = $this->getComponent('loader')->getNextPage();
                $pageCounter++;
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
            $url = $this->options->startUrl;
        }
        $page = $this->getComponent('loader')->getContent($url);

        $categoryList = $this->getComponent('crawler')->getCategoryList($page);
        $categoryList = $this->formatNodeUrls($categoryList);

        return $categoryList;
    }

    /**
     * @param string $url
     * @return array
     */
    public function handlePageOfArticles(string $url)
    {
        /** @var BaseLoader $loader */
        $loader = $this->getComponent('loader');
        /** @var BaseCrawler $crawler */
        $crawler = $this->getComponent('crawler');
        $page = $loader->getContent($url);

        if ($this->testMode) {
            echo "Parser::handlePageOfArticles -> url: " . $url . " \n";
        }

        $articleList = $crawler->getArticleList($page);
        $articleList = $this->formatNodeUrls($articleList);
        $savedArticles = [];
        $articleHandler = $this->getHandler('article');

        /** @var Article $article */
        foreach ($articleList as $article) {
            /** @var ArticleHandler $handler */
            $handler = new $articleHandler();
            $handler->load([
                'article' => $article,
                'article_hash' => $this->createArticleHash($article->url),
                'category_id' => $this->getArticleCategoryId(),
                'articleProvider' => $this->getProvider('article'),
                'loader' => $this->getComponent('loader'),
                'crawler' => $this->getComponent('crawler'),
                'cleaner' => $this->getComponent('cleaner'),
            ]);
            if ($handler->run()) {
                $savedArticles[] = $article;
            };
        }

        return [$articleList, $savedArticles];
    }

    /**
     * @param array $list
     * @return array
     */
    protected function formatNodeUrls(array $list)
    {
        /** @var BaseEntity $node */
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
        return $counter < $this->options->pagesToParse;
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
        $source = md5($url) . "_" . $this->parserName;

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

}
