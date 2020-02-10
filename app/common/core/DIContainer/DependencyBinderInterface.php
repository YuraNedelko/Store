<?php


namespace app\common\core\DIContainer;


interface DependencyBinderInterface
{
    /**
     * Define the abstract target that depends on the context.
     *
     * @param string $abstract
     * @return DependencyBinderInterface
     */
    public function needs(string $abstract): DependencyBinderInterface;

    /**
     * Define the implementation for the contextual binding.
     *
     * @param \Closure|string $implementation
     * @return void
     */
    public function give($implementation);
}