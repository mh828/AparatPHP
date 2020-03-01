<?php


namespace MH828\Aparat;


class Pagination
{
    public $currentOffset = 0;
    public $currentPage = 0;
    public $nextOffset;
    public $previousOffset;
    public $data = [];

    public static function fetchOffset($url)
    {
        $math = '';

        preg_match("/\/curoffset\/(\d+)\/?/", $url, $math);

        return isset($math[1]) ? $math[1] : null;
    }
}