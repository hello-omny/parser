<?php

namespace omny\parser\crawler;


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
class BaseCrawler extends Component
{
    /** @var AdvancedHtmlDom */
    private $html;

    /**
     * @param $page
     */
    public function loadHtml($page)
    {
        $this->html = $this->createAdvancedHtmlDomFromHtml($page);
    }

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
     * @throws \Exception
     */
    public function getCategoryList($html)
    {
        return $this->getNodeList($html, $this->options->categoryLinkClass, Category::class);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getArticleList()
    {
        return $this->getNodeList($this->html, $this->options->entityLinkClass, Article::class);
    }

    /**
     * @return null|string
     */
    public function getNextPage()
    {
        if (!empty($this->options->paginationNextClass)) {
            $link = $this->html->find($this->options->paginationNextClass, 0);

            return empty($link) ? null : $link->href;
        }

        return null;
    }

    /**
     * @param $page string
     * @return array|bool
     */
    public function getDataFromHtml($page)
    {
        /** @var AdvancedHtmlDom $htmlObject */
        $htmlObject = $this->createAdvancedHtmlDomFromHtml($page);
        /** @var AdvancedHtmlDom $content */
        $content = $htmlObject->find($this->options->contentContainer, 0);

        if (empty($content)) {
            return false;
        }

        return [
            'body' => $this->getContentBody($content),
            'short' => $this->getContentShort($content),
            'preview' => $this->getContentImage($content),
            'date' => $this->getContentDate($htmlObject),
        ];
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
        if (!is_null($this->options->contentPreviewClass)) {
            $contentImage = $html->find($this->options->contentPreviewClass, 0);

            return is_null($contentImage) ? null : $contentImage->src;
        }

        return null;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    private function getContentBody($html)
    {
        if (!is_null($this->options->contentBodyClass)) {
            $contentBody = $html->find($this->options->contentBodyClass);

            return is_null($contentBody) ? null : $contentBody->html;
        }

        return null;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    private function getContentShort($html)
    {
        if (!is_null($this->options->contentShortClass)) {
            $contentDescription = $html->find($this->options->contentShortClass, 0);

            return is_null($contentDescription) ? null : $contentDescription->html;
        }

        return null;
    }

    /**
     * @param $html AdvancedHtmlDom
     * @return null|string
     */
    private function getContentDate($html)
    {
        if (!empty($this->options->contentDateClass)) {
            $date = $html->find($this->options->contentDateClass, 0)->innertext;

            return is_null($date) ? null : strtotime($date);
        }

        return null;
    }
}