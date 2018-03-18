<?php

namespace omny\parser\handlers;


use omny\parser\entities\Article;
use omny\parser\loader\BaseLoader;
use omny\parser\providers\ArticleProvider;
use omny\parser\base\Object;

/**
 * Class ArticleHandler
 * @package omny\parser\handlers
 */
class ArticleHandler extends Object implements HandlerInterface
{
    /** @var Article */
    private $article;

    public $reSave = true;

    /** @var ArticleProvider $articleProvider */
    private $articleProvider;
    /** @var BaseLoader */

    public function load(array $params)
    {
        $this->article = $params['article'];
        $this->articleProvider = $params['articleProvider'];
    }

    public function validate(): bool
    {
        return true;
    }

    /**
     * @param bool $validate
     * @return bool
     */
    public function run($validate = true): bool
    {
        if ($validate && !$this->validate()) {
            return false;
        };
        $this->articleProvider->init();
        $existingModel = $this->articleProvider->get($this->article->parser_hash);

        if (empty($existingModel) || $this->reSave) {
            return $this->saveArticle();
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    protected function saveArticle()
    {
        $this->articleProvider->load($this->article->attributes());

        return $this->articleProvider->save();
    }

}
