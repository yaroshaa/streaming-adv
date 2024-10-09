<?php


namespace App\ClickHouse;


final class Layout
{
    public const DIRECT = 'direct()';
    public const HASHED = 'hashed()';
    public const COMPLEX_HASHED = 'COMPLEX_KEY_HASHED()';
    public const COMPLEX_DIRECT = 'COMPLEX_KEY_DIRECT()';
}
