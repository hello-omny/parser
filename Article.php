<?php

namespace omny\parser;


use omny\parser\base\BaseEntity;

class Article extends BaseEntity
{
    public $title;
    public $preview;
    public $short;
    public $body;
    public $type_id;
    public $category_id = 1;
    public $user_id = 1;
    public $date;
    public $author;
    public $url;

    public $parser_hash;
}