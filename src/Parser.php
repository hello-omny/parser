<?php

namespace omny\parser;

use omny\parser\base\BaseEntity;
use omny\parser\base\BaseParser;
use omny\parser\crawler\BaseCrawler;
use omny\parser\entities\Article;
use omny\parser\handlers\ArticleHandler;
use omny\parser\loader\BaseLoader;
use omny\parser\saver\BaseSaver;

/**
 * Class Parser
 * @package omny\parser
 */
class Parser extends BaseParser
{
    /**
     * @param null $url
     * @throws \Exception
     */
    public function run($url = null)
    {
        if (empty($url)) {
            $url = $this->options->startUrl;
        }
        $pageCounter = 1;
        /** @var BaseLoader $loader */
        $loader = $this->getComponent('loader');
        /** @var BaseCrawler $crawler */
        $crawler = $this->getComponent('crawler');

        while (!empty($url)) {
            $page = $loader->getContent($url);
            $crawler->loadHtml($page);
            $this->handlePageOfArticles();

            $url = null;
            if ($this->canParseNextPage($pageCounter)) {
                $url = $crawler->getNextPage();
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
     * @return array
     * @throws \Exception
     */
    public function handlePageOfArticles()
    {
        /** @var BaseCrawler $crawler */
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
        /** @var BaseSaver $saver */
        $saver = $this->getComponent('saver');

        $articleHtml = $this->getComponent('loader')->getContent($article->url);
        $data = $this->getComponent('crawler')->getDataFromHtml($articleHtml);

        $article->load($data);
        $article->parser_hash = $this->createArticleHash($article->url);
        $article->category_id = $this->getArticleCategoryId();

        if (!empty($article->preview)) {
            $article->preview = $saver->savePreview($article->preview);
        }
        if (!empty($article->body)) {
            $article->body = $this->getComponent('cleaner')->clean($article->body);
        }
        if (!empty($article->short)) {
            $article->short = $this->getComponent('cleaner')->clean($article->short);
        }

        return $article;
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
