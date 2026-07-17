<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Exceptions\BindingResolutionException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class Container
{
    /**
     * The container's bindings.
     *
     * @var array[]
     */
    protected array $bindings = [];
    /**
     * The stack of concretions currently being built.
     */
    protected array $buildStack = [];
    /**
     * The container's shared instances.
     *
     * @var object[]
     */
    protected array $instances = [];
    /**
     * The parameter override stack.
     *
     * @var array[]
     */
    protected array $with = [];

    /**
     * @param mixed $concrete
     */
    public function alias(string $alias, $concrete): void
    {
        $this->instances[$alias] = $concrete;
    }

    /**
     * @param mixed $concrete
     */
    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        $this->dropStaleInstances($abstract);
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        if (!$concrete instanceof \Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * @param mixed $abstract
     *
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        if (is_string($abstract) && !class_exists($abstract)) {
            $alias = __NAMESPACE__.'\\'.Str::removePrefix($abstract, __NAMESPACE__);
            if (class_exists($alias)) {
                $abstract = $alias;
            }
        }
        return $this->resolve($abstract, $parameters);
    }

    /**
     * @param mixed $concrete
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        if (isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared']) {
            return; // Singleton already exists
        }
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @param \Closure|string $concrete
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function construct($concrete)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $this->getLastParameterOverride()); // probably a bound closure
        }
        try {
            $reflector = new \ReflectionClass($concrete); // class or classname provided
        } catch (\ReflectionException $e) {
            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
        }
        if (!$reflector->isInstantiable()) {
            $this->throwNotInstantiable($concrete); // not an instantiable class
        }
        $this->buildStack[] = $concrete;
        if (is_null($constructor = $reflector->getConstructor())) {
            array_pop($this->buildStack);
            return new $concrete(); // class has no __construct
        }
        try {
            $instances = $this->resolveDependencies($constructor->getParameters()); // resolve class dependencies
        } catch (BindingResolutionException $e) {
            array_pop($this->buildStack);
            throw $e;
        }
        array_pop($this->buildStack);
        return $reflector->newInstanceArgs($instances); // return a new class
    }

    protected function dropStaleInstances(string $abstract): void
    {
        unset($this->instances[$abstract]);
    }

    /**
     * The plugin's constructor dependencies are always single named classes,
     * never union or intersection types.
     *
     * @return \ReflectionNamedType|null
     */
    protected function getClass(\ReflectionParameter $parameter)
    {
        return $parameter->getType(); // @phpstan-ignore return.type
    }

    protected function getClosure($abstract, $concrete): \Closure
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            return $abstract == $concrete
                ? $container->construct($concrete)
                : $container->resolve($concrete, $parameters);
        };
    }

    /**
     * @return mixed
     */
    protected function getConcrete(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }
        return $abstract;
    }

    protected function getLastParameterOverride(): array
    {
        return Arr::consolidate(end($this->with));
    }

    /**
     * @return mixed
     */
    protected function getParameterOverride(\ReflectionParameter $dependency)
    {
        return $this->getLastParameterOverride()[$dependency->name];
    }

    protected function hasParameterOverride(\ReflectionParameter $dependency): bool
    {
        return array_key_exists($dependency->name, $this->getLastParameterOverride());
    }

    protected function isShared(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || !empty($this->bindings[$abstract]['shared']);
    }

    /**
     * @param mixed $abstract
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function resolve($abstract, array $parameters = [])
    {
        if (isset($this->instances[$abstract]) && empty($parameters)) {
            return $this->instances[$abstract]; // return an existing singleton
        }
        $this->with[] = $parameters;
        // getConcrete() answers a Closure (bind() wraps every concrete in one)
        // or the abstract itself; construct() builds either
        $object = $this->construct($this->getConcrete($abstract));
        if ($this->isShared($abstract) && empty($parameters)) {
            $this->instances[$abstract] = $object; // store as a singleton
        }
        array_pop($this->with);
        return $object;
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function resolveClass(\ReflectionParameter $parameter)
    {
        try {
            return $this->make($this->getClass($parameter)->getName());
        } catch (\Exception $error) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }
            throw $error;
        }
    }

    protected function resolveDependencies(array $dependencies): array
    {
        $results = [];
        foreach ($dependencies as $dependency) {
            if ($this->hasParameterOverride($dependency)) {
                $results[] = $this->getParameterOverride($dependency);
                continue;
            }
            $results[] = is_null($this->getClass($dependency))
                ? $this->resolvePrimitive($dependency)
                : $this->resolveClass($dependency);
        }
        return $results;
    }

    /**
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function resolvePrimitive(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        $this->throwUnresolvablePrimitive($parameter);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function throwNotInstantiable(string $concrete): void
    {
        if (empty($this->buildStack)) {
            $message = "Target [$concrete] is not instantiable.";
        } else {
            $previous = implode(', ', $this->buildStack);
            $message = "Target [$concrete] is not instantiable while building [$previous].";
        }
        throw new BindingResolutionException($message);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function throwUnresolvablePrimitive(\ReflectionParameter $parameter): void
    {
        throw new BindingResolutionException("Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}");
    }
}
