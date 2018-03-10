<?php

namespace omny\parser;


class Helper
{
    public static function normalizeUrl($url, $baseUrl = null)
    {
        if (preg_match('/((http)|(www))(.+)/', $url)) {
            return $url;
        }
        return empty($baseUrl) ? $url : $baseUrl . $url;
    }
}