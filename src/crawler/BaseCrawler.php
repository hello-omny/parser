<?php

namespace omny\parser\crawler;


use omny\parser\library\AdvancedHtmlDom;
use omny\parser\Article;
use omny\parser\Category;
use omny\parser\Worker;

/**
 * Class BaseCrawler
 * @package omny\parser\crawler
 */
class BaseCrawler extends Worker
{
    /** @var  BaseCrawlerOptions */
    public $options;

    /**
     * @param $html
     * @param $class
     * @param $nodeEntity
     * @return array
     * @throws \Exception
     */
    public function getNodeList($html, $class, $nodeEntity)
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
     * @param $html
     * @return array
     */
    public function getCategoryList($html)
    {
        return $this->getNodeList($html, $this->options->categoryLinkClass, Category::getClassName());
    }

    /**
     * @param $html
     * @return array
     */
    public function getArticleList($html)
    {
        return $this->getNodeList($html, $this->options->entityLinkClass, Article::getClassName());
    }

    /**
     * @param $html
     * @return array|bool
     */
    public function getDataFromHtml($html)
    {
        $htmlObject = $this->createAdvancedHtmlDomFromHtml($html);
        $content = $htmlObject->find($this->options->contentContainer, 0);

        if (empty($content)) {
            return false;
        }

        $data = [
            'body' => $this->getContentBody($content),
            'short' => $this->getContentShort($content),
            'preview' => $this->getContentImage($content),
            'date' => $this->getContentDate($htmlObject),
        ];

        return $data;
    }

    /**
     * @param $html
     * @return AdvancedHtmlDom
     */
    protected function createAdvancedHtmlDomFromHtml($html)
    {
        $simpleHtml = new AdvancedHtmlDom();
        $simpleHtml->load($html);

        return $simpleHtml;
    }

    /**
     * @param $content
     * @return null|AdvancedHtmlDom
     */
    protected function getContentImage($content)
    {
        if (!is_null($this->options->contentPreviewClass)) {
            $contentImage = $content->find($this->options->contentPreviewClass, 0);
            if (!is_null($contentImage)) {
                return $contentImage->src;
            }
        }

        return null;
    }

    /**
     * @param $content
     * @return null
     */
    private function getContentBody($content)
    {
        if (!is_null($this->options->contentBodyClass)) {
            $contentBody = $content->find($this->options->contentBodyClass);
            if (!is_null($contentBody)) {
                return $contentBody->html;
            }
        }

        return null;
    }

    /**
     * @param $content
     * @return null
     */
    private function getContentShort($content)
    {
        if (!is_null($this->options->contentShortClass)) {
            $contentDescription = $content->find($this->options->contentShortClass, 0);
            if (!is_null($contentDescription)) {
                return $contentDescription->html;
            }
        }

        return null;
    }

    /**
     * @param $htmlObject
     * @return false|int|null
     */
    private function getContentDate($htmlObject)
    {
        if (!empty($this->options->contentDateClass)) {
            $date = $htmlObject->find($this->options->contentDateClass, 0)->innertext;
            if (!is_null($date)) {
                return strtotime($date);
            }
        }

        return null;
    }
}