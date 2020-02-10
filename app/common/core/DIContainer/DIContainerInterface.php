<?php


namespace app\common\core\DIContainer;


interface DIContainerInterface
{
    /**
     * @param string $abstract
     * @param string $concrete
     * @return DIContainerInterface
     */
    public function set(string $abstract, $concrete = NULL): DIContainerInterface;

    /**
     * @param string $abstract
     * @param string $concrete
     */
    public function setUniqueInstance(string $abstract, $concrete = NULL);

    /**
     * @param string $abstract
     * @param array $parameters
     *
     * @param null $callingClass
     * @return mixed
     */
    public function get(string $abstract, $parameters = [], $callingClass = null);

    /**
     * @param  $concrete
     * @return DependencyBinderInterface
     */
    public function when(string $concrete): DependencyBinderInterface;

}