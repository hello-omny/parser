<?php

namespace omny\parser\crawler;


use omny\parser\base\BaseOptions;

class CrawlerBaseOptions extends BaseOptions
{
    //страница категорий
    /** @var string|null */
    public $categoryContainerClass = null;
    /** @var string|null */
    public $categoryLinkClass = null;

    // страница категории со списком статей
    /** @var null|string Класс контейнера статьи на странице категории или в выдаче */
    public $entityContainerClass = null;
    /** @var null|string Класс ссылки на страницу статьи */
    public $entityLinkClass = null;
    /** @var null|string Класс со ссылкой на следующую страницу */
    public $paginationNextClass = null;

    // страница статьи
    /** @var string|null */
    public $contentContainer = null;
    /** @var string|null */
    public $contentTitleClass;
    /** @var null|string Класс содержащий в себе описание статьи */
    public $contentShortClass = null;
    /** @var null|string Класс содержащий в себе текст статьи */
    public $contentBodyClass = null;
    /** @var null|string Класс картинки */
    public $contentPreviewClass = null;
    /** @var string|null */
    public $contentTagsClass = null;
    /** @var string|null */
    public $contentTagElementClass = null;
    /** @var string|null */
    public $contentAuthorClass = null;
    /** @var null|string Класс с контейнера даты статьи */
    public $contentDateClass = null;

    public function __construct() {

    }
}