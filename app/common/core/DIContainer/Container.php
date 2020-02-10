<?php


namespace app\common\core\DIContainer;


use Closure;
use Exception;
use ReflectionClass;

class Container implements DIContainerInterface
{
    /**
     * @var int
     */
    protected $nestingLevel = 0;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var array
     */
    protected $uniqueInstances = [];


    /**
     * @param string $abstract
     * @param $concrete
     * @return DIContainerInterface
     */
    public function set(string $abstract, $concrete = NULL): DIContainerInterface
    {
        if ($concrete === NULL) {
            $concrete = $abstract;
        }
        $this->instances[$abstract] = $concrete;
        return $this;
    }

    /**
     * @param string $abstract
     * @param $concrete
     * @return void
     * @throws \Exception
     */
    public function setUniqueInstance(string $abstract, $concrete = NULL)
    {
        if ($concrete === NULL) {
            $concrete = $abstract;
        }
        $this->uniqueInstances[$abstract] = $this->resolve($concrete);
    }

    /**
     * @param string $abstract
     * @param array $parameters
     * @param string $callingClass
     *
     * @return mixed
     * @throws \Exception
     */
    public function get(string $abstract, $parameters = [], $callingClass = null)
    {
        if ($this->nestingLevel > 20) {
            throw new Exception('Object\'s dependencies are too deeply nested');
        } else {
            $this->nestingLevel++;
        }

        if ($callingClass && isset($this->bindings[$callingClass][$abstract])) {
            return $this->bindings[$callingClass][$abstract];
        } elseif (isset($this->uniqueInstances[$abstract])) {
            return $this->uniqueInstances[$abstract];
        }

        // if we don't have it, just register it
        if (!isset($this->instances[$abstract])) {
            $this->set($abstract);
        }
        return $this->resolve($this->instances[$abstract], $parameters);
    }

    /**
     * resolve single
     *
     * @param $concrete
     * @param array $parameters
     *
     * @return mixed
     * @throws \Exception
     */
    protected function resolve($concrete, $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);

        // check if class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable");
        }
        // get class constructor
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            // get new instance from class
            return $reflector->newInstance();
        }
        // get constructor params
        $constructorParameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($constructorParameters, $concrete);
        // get new instance with dependencies resolved
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * get all dependencies resolved
     *
     * @param $callingClass
     * @param $constructorParameters
     *
     * @return mixed[]
     * @throws \Exception
     */
    protected function getDependencies($constructorParameters, $callingClass = null): array
    {
        $dependencies = [];
        foreach ($constructorParameters as $parameter) {
            // get the type hinted class
            $dependency = $parameter->getClass();

            if ($dependency === NULL) {
                // check if default value for a parameter is available
                if ($parameter->isDefaultValueAvailable()) {
                    // get default value of parameter
                    $dependencies[] = $parameter->getDefaultValue();
                } elseif ($callingClass && isset($this->bindings[$callingClass][$parameter->name])) {
                    $dependencies[] = $this->get($parameter->name, [], $callingClass);
                } else {
                    throw new Exception("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                // get dependency resolved
                $dependencies[] = $this->get($dependency->name, [], $callingClass);
            }
        }
        return $dependencies;
    }

    /**
     * @param string $concrete
     * @return DependencyBinderInterface
     */
    public function when(string $concrete): DependencyBinderInterface
    {
        return (new DependencyBinder($this, $concrete));
    }

    /**
     * @param string|array $concrete
     * @param string $needs
     * @param mixed $implementation
     */
    public function addBindings($concrete, string $needs, $implementation)
    {
        $this->bindings[$concrete][$needs] = $implementation;
    }
}