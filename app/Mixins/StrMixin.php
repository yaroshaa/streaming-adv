<?php

namespace App\Mixins;

use Closure;

class StrMixin
{
    /**
     * @todo Unit tests, rewrite regex
     * @return Closure
     */
    public function replaceLinesBetweenStrings(): Closure
    {
        return function (string $from, string $to, string $replaceTo, string $content) {
            $start = preg_quote($from, '/');
            $end = preg_quote($to, '/');
            $regex = '/(?<=' . $start . '\n)(.*)(?=$.*' . $end . ')/mis';
            return preg_replace($regex, $replaceTo, $content);
        };
    }
}
