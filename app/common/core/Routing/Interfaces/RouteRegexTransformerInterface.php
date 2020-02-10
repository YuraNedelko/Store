<?php


namespace app\common\core\Routing\Interfaces;


interface RouteRegexTransformerInterface
{
    /**
     * Parse routes into regex expression
     * @param string $uri
     * @return string
     */
    public function transform(string $uri): string;
}