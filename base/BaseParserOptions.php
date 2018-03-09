<?php

namespace omny\parser\base;


use omny\parser\providers\BaseArticleProvider;
use omny\parser\providers\BaseCategoryProvider;

class BaseParserOptions extends BaseOptions
{
    /** @var string */
    public $startUrl;

    /** @var integer */
    public $categoryId;

    public $pagesToParse = 3;

    public $loaderOptions;
    public $crawlerOptions;
    public $saverOptions;
    public $cleanerOptions;

    public $articleProvider;
    public $categoryProvider;

    public function __construct()
    {
        $this->articleProvider = BaseArticleProvider::getClassName();
        $this->categoryProvider = BaseCategoryProvider::getClassName();
    }

}