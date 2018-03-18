<?php
return [
    'id' => 'default',
    'baseUrl' => 'github.com',
    'startUrl' => 'github.com',
    'pagesToParse' => 2,

    'components' => [
        'loader' => [
            'className' => \omny\parser\components\Loader::class,
            'config' => [
                'proxyEnabled' => false,
                'cacheEnabled' => true,
                'sleepLimit' => 10,
                'cacheOptions' => [
                    'alias' => '/runtime/parser/cache'
                ]
            ],
        ],
        'crawler' => [
            'className' => \omny\parser\components\Crawler::class,
            'config' => [
                'category' => [
                    'container' => null,
                    'link' => null,
                ],
                'entity' => [
                    'container' => null, // Класс контейнера статьи на странице категории или в выдаче
                    'link' => null, // Класс ссылки на страницу статьи
                ],
                'pagination' => [
                    'container' => null,
                    'next' => null, // Класс со ссылкой на следующую страницу
                ],
                'content' => [
                    'container' => null,
                    'title' => null,
                    'short' => null, // Класс содержащий в себе описание статьи
                    'body' => null, // Класс содержащий в себе текст статьи
                    'preview' => null, // Класс картинки
                    'tags' => null,
                    'tagElement' => null,
                    'author' => null,
                    'date' => null, // Класс даты статьи
                ],
            ],
        ],
        'saver' => [
            'className' => \omny\parser\components\Saver::class,
            'config' => [
                'reSave' => true,
                'handlers' => [
                    'save' => [
                        'className' => \omny\parser\handlers\SaveHandler::class,
                        'config' => [
                            'baseUploadDir' => '/www/site.local/web/upload',
                            'baseHttpPath' => '/upload',
                            'curlTimeout' => 500,
                            'reSave' => true,
                        ]
                    ]
                ]
            ],
        ],
        'cleaner' => [
            'className' => \omny\parser\components\Cleaner::class,
        ]
    ],
    'handlers' => [
        'article' => [
            'className' => \omny\parser\handlers\ArticleHandler::class
        ],
    ],
    'providers' => [
        'article' => [
            'className' => \omny\parser\providers\ArticleProvider::class
        ],
        'category' => [
            'className' => \omny\parser\providers\CategoryProvider::class
        ],
    ],
    'entities' => [
        'article' => [
            'className' => \omny\parser\entities\Article::class
        ],
        'category' => [
            'className' => \omny\parser\entities\Category::class
        ]
    ],
];