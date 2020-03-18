<?php
/**
 * Created by PhpStorm.
 * User: nedel
 * Date: 02.04.2019
 * Time: 10:37
 */

namespace app\common\core;

use app\common\core\DIContainer\DIContainerInterface;
use app\common\core\Request\RequestInterface;
use app\common\helpers\Logger;
use Exception;
use ReflectionMethod;

class App
{
    /**
     * @var \stdClass
     */
    protected static $config;


    /**
     * @var DIContainerInterface
     */
    protected static $container;

    /**
     * @param \stdClass|null $config
     * @param DIContainerInterface|null $container
     * @return App
     */
    public function init(\stdClass $config, DIContainerInterface $container): App
    {
        $this->setConfig($config);
        $this->setTimeZone();
        $this->setContainer($container);
        $this->registerDependencyBinders();
        return $this;
    }

    /**
     * @param DIContainerInterface $container
     */
    public function setContainer(DIContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     *
     */
    protected function setTimeZone()
    {
        date_default_timezone_set(property_exists(self::$config, 'timezone')
            ? self::$config->timezone : 'Europe/Kiev');
    }

    /**
     * @return \stdClass
     */
    public static function getConfig(): \stdClass
    {
        return self::$config;
    }

    /**
     * @param \stdClass $config
     */
    protected function setConfig(\stdClass $config)
    {
        self::$config = $config;
    }

    /**
     * Set DIContainer dependencies
     */
    protected function registerDependencyBinders()
    {
        $configurators = property_exists(self::$config, 'configurators') ? self::$config->configurators : [];
        foreach ($configurators as $configurator) {
            if (class_exists($configurator) && method_exists($configurator, 'register')) {
                try {
                    (new $configurator())->register($this);
                } catch (Exception $e) {
                    Logger::log($e);
                    self::handleError();
                }
            }
        }
    }

    /**
     * @return DIContainerInterface
     */
    public static function getContainer(): DIContainerInterface
    {
        return self::$container;
    }

    /**
     * @param $abstract
     * @param $parameters
     * @param string|null $callingClass
     * @return mixed
     */
    public static function resolve($abstract, $parameters = [], $callingClass = null)
    {
        $concrete = null;
        try {
            $concrete = self::$container->get($abstract, $parameters, $callingClass);
        } catch (Exception $e) {
            Logger::log($e);
            self::handleError();
        }
        return $concrete;
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @throws Exception
     */
    public function instance(string $abstract, $concrete)
    {
        if (self::$container)
            try {
                self::$container->setUniqueInstance($abstract, $concrete);
            } catch (Exception $e) {
                Logger::log($e);
                self::handleError();
            }
        else
            throw new \Exception('No DI container specified');
    }

    /**
     * @param string $abstract
     * @param $concrete
     */
    public function bind(string $abstract, $concrete)
    {
        self::$container->set($abstract, $concrete);
    }


    public function parseRequest()
    {

        try {
            $request = self::resolve(RequestInterface::class, []);
            $request->parseRequest();
            $this->handleResponse($request, $this->handleRequest($request));
        } catch (Exception $e) {
            Logger::log($e);
            self::handleError();
        }
    }

    /**
     * @param RequestInterface $request
     * @param $response
     */
    protected function handleResponse(RequestInterface $request, $response)
    {
        if ($request->isAjax()) {
            if ($response) {
                header('Content-Type: application/json');
                echo json_encode($response);
            } else {
                http_response_code(400);
                exit;
            }
        } else {
            echo $response;
            exit();
        }
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws Exception
     */
    protected function handleRequest(RequestInterface $request)
    {
        $controllerNameAndMethod = $this->getControllerNameAndMethod($request);

        $controllerPath = property_exists(self::$config, 'controllerPath')
            ? self::$config->controllerPath : null;

        if ($controllerPath)
            $controllerName = "{$controllerPath}{$controllerNameAndMethod[0]}";
        else
            throw new Exception('Controller path is not specified');

        if (isset($controllerNameAndMethod[1]))
            $actionName = $controllerNameAndMethod[1];
        else
            throw new Exception('Controller action is not specified');

        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $actionName) && is_callable([$controller, $actionName])) {
                return $this->resolveMethodDependencies($controller, $actionName,$request->getParams());
            } else {
                throw new Exception("Controller $controllerName doesn't have method $actionName");
            }

        } else {
            throw new Exception("Controller $controllerName doesn't exist");
        }
    }

    /**
     * @param Controller $controller
     * @param string $methodName
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    protected function resolveMethodDependencies(Controller $controller, string $methodName, array $params = [])
    {
        $method = new ReflectionMethod($controller, $methodName);

        foreach ($method->getParameters() as $index => $parameter) {
            $class = $parameter->getClass();

            if ($class !== null) {
                $instance = self::$container->get($class->name);
                array_splice($params, $index, 0, [$instance]);
            }
        }

        return $controller->$methodName(...array_values($params));
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     */
    protected function getControllerNameAndMethod(RequestInterface $request): array
    {
        if ($request->getAction()) {
            return explode('@', $request->getAction());
        } elseif (property_exists(self::$config, 'defaultAction' && !$request->isAjax())) {
            return explode('@', self::$config->defaultAction);
        } else {
            throw new Exception('Can\'t parse request');
        }
    }

    /**
     * @param string $error
     */
    protected static function handleError(string $error = 'Error occurred')
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            http_response_code(400);
            exit();
        } else {
            $view = new View();
            $view->assign(['message' => $error]);
            echo $view->generate('error', 'layout');
            exit;
        }
    }

}