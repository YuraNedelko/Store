<?php


namespace app\common\core\AppDependencyBinders;


use app\common\core\App;
use app\common\core\Request\Request;
use app\common\core\Request\RequestInterface;
use app\common\core\Routing\Interfaces\RouteCollectionInterface;
use app\common\core\Routing\Interfaces\RouteRegexTransformerInterface;
use app\common\core\Routing\RouteCollection;
use app\common\core\Routing\RouteRegexTransformer;


class AppRouteDependencyBinder implements AppDependencyBinderInterface
{
    /**
     * Register bindings in the container.
     * @param App $app
     * @return void
     * @throws \Exception
     */
    public function register(App $app)
    {
        $app->bind(RouteRegexTransformerInterface::class, RouteRegexTransformer::class);
        $app->instance(RouteCollectionInterface::class, RouteCollection::class);
    }

}