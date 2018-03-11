<?php

namespace omny\parser\handlers;


use omny\parser\Article;
use omny\parser\cleaner\BaseCleaner;
use omny\parser\crawler\BaseCrawler;
use omny\parser\loader\BaseLoader;
use omny\parser\providers\BaseArticleProvider;

/**
 * Class ArticleHandler
 * @package omny\parser\handlers
 */
class ArticleHandler implements HandlerInterface
{
    /** @var Article */
    private $article;
    /** @var integer */
    private $category_id;
    /** @var BaseArticleProvider $articleProvider */
    private $articleProvider;
    /** @var BaseLoader */
    private $loader;
    /** @var BaseCrawler */
    private $crawler;
    /** @var BaseCleaner */
    private $cleaner;

    /**
     * ArticleHandler constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->article = $options['article'];
        $this->article->parser_hash = $options['article_hash'];
        $this->articleProvider = $options['articleProvider'];
        $this->loader = $options['loader'];
        $this->crawler = $options['crawler'];
        $this->cleaner = $options['cleaner'];
        $this->category_id = $options['category_id'];
    }

    /**
     * @return bool|mixed
     */
    public function run()
    {
        if (!empty($this->articleProvider->get($this->article->parser_hash))) {
            return false;
        }

        $this->extendArticleData();

        // чистит свойства статьи
        $this->cleanArticleContent();

        // сохраняем в базу
        return $this->saveArticle();
    }

    /**
     *
     */
    protected function extendArticleData()
    {
        $articleHtml = $this->loader->getContent($this->article->url);
        $articleData = $this->crawler->getDataFromHtml($articleHtml);

        // дополняем объект данными
        $this->article->load($articleData);
        $this->article->category_id = $this->category_id;
    }

    /**
     * @param $article Article
     */
    private function cleanArticleContent()
    {
        if (!empty($this->article->body)) {
            $this->article->body = $this->cleaner
                ->clean($this->article->body);
        }
        if (!empty($this->article->short)) {
            $this->article->short = $this->cleaner
                ->clean($this->article->short);
        }
    }

    /**
     * @return bool|mixed
     */
    protected function saveArticle()
    {
        $this->articleProvider
            ->load($this->article);

        return $this->articleProvider->save();
    }

}
