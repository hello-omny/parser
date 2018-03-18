<?php

namespace omny\parser\entities;


use omny\parser\base\Entity;

class Article extends Entity
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

    public function load($data)
    {
        parent::load($data);
    }
}