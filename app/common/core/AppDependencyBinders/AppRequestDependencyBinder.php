<?php


namespace app\common\core\AppDependencyBinders;


use app\common\core\App;
use app\common\core\Request\Request;
use app\common\core\Request\RequestInterface;


class AppRequestDependencyBinder implements AppDependencyBinderInterface
{
    /**
     * Register bindings in the container.
     * @param App $app
     * @return void
     * @throws \Exception
     */
    public function register(App $app)
    {
        $app->instance(RequestInterface::class, Request::class);
    }

}