<?php


namespace app\common\core\AppDependencyBinders;


use app\common\core\App;

interface AppDependencyBinderInterface
{
    /**
     * Register bindings in the container.
     * @param App $app
     * @return void
     */
    function register(App $app);

}