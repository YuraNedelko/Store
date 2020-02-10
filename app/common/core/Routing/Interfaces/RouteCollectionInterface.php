<?php


namespace app\common\core\Routing\Interfaces;


interface RouteCollectionInterface
{

    /**
     * @param array $route
     * @return mixed
     */
    static function get(array $route);

    /**
     * @param array $route
     * @return mixed
     */
    static function post(array $route);

    /**
     * Determines controller to call and parameters to path to this method
     * @param string $method
     * @param string $uri
     * @return array
     */
    public function getResolvedRouteWithParams(string $method, string $uri);

    /**
     * @return string|null
     */
    public function getAction();

    /**
     * @return array
     */
    public function getParams();

}