<?php

namespace app\common\core\Routing;

use app\common\core\Routing\Interfaces\RouteRegexTransformerInterface;

class RouteRegexTransformer implements RouteRegexTransformerInterface
{
    /**
     * Parse routes into regex expression
     * @param string $uri
     * @return string
     */
    public function transform(string $uri): string
    {

        $uri = preg_replace(
            ['/\//', '/\\/\{(\w+?)\?\}/', '/\{(\w+?)\}/'],
            ['\/', '/?(\w+)?', '(\w+)'],
            $uri
        );

        return $uri;
    }


}