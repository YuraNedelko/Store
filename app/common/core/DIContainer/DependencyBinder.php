<?php


namespace app\common\core\DIContainer;


use app\common\helpers\ArrayWrapper;

class DependencyBinder implements DependencyBinderInterface
{
    /**
     * The underlying container instance.
     *
     * @var DIContainerInterface
     */
    protected $container;

    /**
     * The concrete instance.
     *
     * @var string|array
     */
    protected $concrete;

    /**
     * The abstract target.
     *
     * @var string
     */
    protected $needs;

    /**
     * Create a new dependency binder.
     *
     * @param DIContainerInterface $container
     * @param string|array $concrete
     * @return void
     */
    public function __construct(DIContainerInterface $container, $concrete)
    {
        $this->concrete = $concrete;
        $this->container = $container;
    }

    /**
     * Define the abstract target that depends on the context.
     *
     * @param string $abstract
     * @return DependencyBinderInterface
     */
    public function needs(string $abstract): DependencyBinderInterface
    {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * Define the implementation for the contextual binding.
     *
     * @param array|\Closure|string $implementation
     * @return void
     */
    public function give($implementation)
    {
        foreach (ArrayWrapper::wrap($this->concrete) as $concrete) {
            $this->container->addBindings($concrete, $this->needs, $implementation);
        }
    }
}