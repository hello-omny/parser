<?php

namespace omny\parser\cleaner;


use omny\parser\base\Component;

/**
 * Class BaseCleaner
 * @package omny\parser\cleaner
 */
class BaseCleaner extends Component
{
    /**
     * @param $html
     * @return mixed|null|string|string[]
     */
    public function clean($html)
    {
        $html = $this->cleanContent($html);
        $html = $this->eraseJs($html);
        $html = $this->removeEmptyTags($html);

        return $html;
    }

    /**
     * Подчищяет выходной хтмл содержимого статьи на входе html
     *
     * @param $content
     * @return mixed|string
     */
    protected function cleanContent($content)
    {
        $content = preg_replace('/<img (.*?)>/', '', $content);
        $content = strip_tags($content, '<p><h1><h2><h3><ul><li><img><strong><i>');

        return $content;
    }

    /**
     * @param $html
     * @return null|string|string[]
     */
    protected function removeEmptyTags($html)
    {
        return preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $html);
    }

    /**
     * Вырезаем js теги и содержимое
     *
     * @param string $content
     * @return string
     */
    protected function eraseJs($content)
    {
        $content = preg_replace('/<script>(.*?)<\/script>/', "", $content);
        $content = preg_replace('/<script type="text\/javascript">(.*?)<\/script>/', "", $content);
        $content = preg_replace('/<script type=\'text\/javascript\'>(.*?)<\/script>/', "", $content);

        return $content;
    }
}