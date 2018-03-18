<?php

namespace omny\parser\components;


use omny\parser\base\Component;
use omny\parser\entities\Article;
use omny\parser\entities\Category;
use omny\parser\library\AdvancedHtmlDom;

/**
 * Class BaseCrawler
 * @package omny\parser\crawler
 *
 * @property BaseCrawlerOptions $options
 */
class Crawler extends Component
{
    /** @var array */
    public $category = [
        'container' => null,
        'link' => null,
    ];
    /** @var array */
    public $entity = [
        'container' => null, // Класс контейнера статьи на странице категории или в выдаче
        'link' => null, // Класс ссылки на страницу статьи
    ];
    /** @var array */
    public $pagination = [
        'container' => null,
        'next' => null, // Класс со ссылкой на следующую страницу
    ];

    /** @var array */
    public $content = [
        'container' => null,
        'title' => null,
        'short' => null, // Класс содержащий в себе описание статьи
        'body' => null, // Класс содержащий в себе текст статьи
        'preview' => null, // Класс картинки
        'tags' => null,
        'tagElement' => null,
        'author' => null,
        'date' => null, // Класс даты статьи
    ];

    /** @var AdvancedHtmlDom */
    private $html = null;

    /**
     * @param $page
     */
    public function setHtml($page)
    {
        $this->html = $this->createAdvancedHtmlDomFromHtml($page);
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getElementByClass($className, $index = null)
    {
        return $this->html->find($className, $index);
    }

    /**
     * @param $html
     * @return array
     * @throws \Exception
     */
    public function getCategoryList($html)
    {
        return $this->getNodeList($html, $this->category['link'], Category::class);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getArticleList()
    {
        return $this->getNodeList($this->html, $this->entity['link'], Article::class);
    }

    /**
     * @return null|string
     */
    public function getNextPage()
    {
        $container = null;
        if (!empty($this->pagination['container'])) {
            $container = $this->getElementByClass($this->pagination['container'], 0);
        }

        if (!empty($this->pagination['next'])) {
            if (!empty($container)) {
                $link = $container->find($this->pagination['next'], 0);
            } else {
                $link = $this->getElementByClass($this->pagination['next'], 0);
            }

            return empty($link) ? null : $link->href;
        }

        return null;
    }

    /**
     * @param $page string
     * @return array|bool
     */
    public function getDataFromHtml()
    {

        /** @var AdvancedHtmlDom $content */
        $content = $this->getElementByClass($this->content['container'], 0);

        if (empty($content)) {
            return false;
        }

        return [
            'body' => $this->getContentBody($content),
            'short' => $this->getContentShort($content),
            'preview' => $this->getContentImage($content),
            'date' => $this->getContentDate(),
        ];
    }

    /**
     * @param $html
     * @param $class
     * @param $nodeEntity
     * @return array
     * @throws \Exception
     */
    protected function getNodeList($html, $class, $nodeEntity)
    {
        $collectedNodeList = [];
        $htmlObject = $this->createAdvancedHtmlDomFromHtml($html);
        $nodeList = $htmlObject->find($class);

        if (!empty($nodeList)) {
            foreach ($nodeList as $nodeObject) {
                if (!is_null($nodeObject)) {
                    /** @var Article $node */
                    $node = new $nodeEntity();
                    $node->load([
                        'url' => trim($nodeObject->href),
                        'title' => trim($nodeObject->plaintext),
                    ]);
                    $collectedNodeList[] = $node;
                } else {
                    echo "BaseCrawler::getNodeList (" . $nodeEntity . ") —> No link found. \n";
                }
            }
            return $collectedNodeList;
        }

        throw new \Exception('Node list empty!');
    }

    /**
     * @param $page string
     * @return AdvancedHtmlDom
     */
    protected function createAdvancedHtmlDomFromHtml($page)
    {
        $html = new AdvancedHtmlDom();
        $html->load($page);

        return $html;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    protected function getContentImage($html)
    {
        if (!is_null($this->content['preview'])) {
            $contentImage = $html->find($this->content['preview'], 0);

            return is_null($contentImage) ? null : $contentImage->src;
        }

        return null;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    protected function getContentBody($html)
    {
        if (!is_null($this->content['body'])) {
            $contentBody = $html->find($this->content['body']);

            return is_null($contentBody) ? null : $contentBody->html;
        }

        return null;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    protected function getContentShort($html)
    {
        if (!is_null($this->content['short'])) {
            $contentDescription = $html->find($this->content['short'], 0);

            return is_null($contentDescription) ? null : $contentDescription->html;
        }

        return null;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    protected function getContentDate()
    {
        if (!empty($this->content['date'])) {
            $date = $this->getElementByClass($this->content['date'], 0);

            return is_null($date) ? null : strtotime($date->innertext);
        }

        return null;
    }
}