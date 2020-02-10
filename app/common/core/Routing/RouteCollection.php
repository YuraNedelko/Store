<?php


namespace app\common\core\Routing;


use app\common\core\App;
use app\common\core\Routing\Interfaces\RouteCollectionInterface;
use app\common\core\Routing\Interfaces\RouteRegexTransformerInterface;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var array
     */
    static protected $getRoutes = [];

    /**
     * @var array
     */
    static protected $postRoutes = [];

    /**
     * @var array
     */
    protected $transformedRoutes;

    /**
     * @var string
     */
    protected $action = null;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var RouteRegexTransformerInterface
     */
    protected $transformer;

    /**
     * RouteCollection constructor.
     * @param RouteRegexTransformerInterface $transformer
     */
    public function __construct(RouteRegexTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param array $route
     * @return mixed|void
     */
    public static function get(array $route)
    {
        self::$getRoutes[] = $route;
    }

    /**
     * @param array $route
     * @return mixed|void
     */
    public static function post(array $route)
    {
        self::$postRoutes[] = $route;
    }

    /**
     * Transform routes defined in routes folder into regex expression
     * @param string $method
     * @return array
     * @throws \Exception
     */
    protected function getTransformedRoutes(string $method): array
    {
        $transformed = [];
        switch ($method) {
            case "GET":
            {
                $transformed = $this->transformRoutes(self::$getRoutes);
                break;
            }
            case "POST":
            {
                $transformed = $this->transformRoutes(self::$postRoutes);
                break;
            }
        }
        return $transformed;
    }

    /**
     * @param array $routes
     * @return array
     * @throws \Exception
     */
    protected function transformRoutes(array $routes): array
    {
        $transformedRoutes = [];
        foreach ($routes as $route) {
            $transformedRoutes[] = [
                'route' => $this->transformer->transform(key($route)),
                'action' => $route[key($route)]
            ];
        }
        return $transformedRoutes;
    }

    /**
     * Determines controller to call and parameters to path to this method
     * @param string $method
     * @param string $uri
     * @return array
     * @throws \Exception
     */
    public function getResolvedRouteWithParams(string $method, string $uri): array
    {
        $transformedRoutes = $this->getTransformedRoutes($method);
        $resolvedRouteWithParams = [];
        foreach ($transformedRoutes as $transformedRoute) {
            preg_match_all("/^{$transformedRoute['route']}\/?$/", $uri, $matches);
            if ($matches[0]) {
                $this->action = $transformedRoute['action'];
                array_splice($matches, 0, 1);
                foreach ($matches as $match) {
                    if ($match[0])
                        $this->params[] = $match[0];
                }
            }
        }
        return $resolvedRouteWithParams;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}