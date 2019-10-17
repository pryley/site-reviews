<?php

namespace GeminiLabs\SiteReviews;

use Closure;
use Exception;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;
use ReflectionParameter;

abstract class Container
{
    const PROTECTED_PROPERTIES = [
        'instance',
        'services',
        'session',
        'storage',
    ];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * The container's bound services.
     * @var array
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $session = [];

    /**
     * The container's storage items.
     * @var array
     */
    protected $storage = [];

    /**
     * @return static
     */
    public static function load()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property) && !in_array($property, static::PROTECTED_PROPERTIES)) {
            return $this->$property;
        }
        $constant = 'static::'.strtoupper(Str::snakeCase($property));
        if (defined($constant)) {
            return constant($constant);
        }
        return Arr::get($this->storage, $property, null);
    }

    /**
     * @param string $property
     * @param string $value
     * @return void
     */
    public function __set($property, $value)
    {
        if (!property_exists($this, $property) || in_array($property, static::PROTECTED_PROPERTIES)) {
            $this->storage[$property] = $value;
        } elseif (!isset($this->$property)) {
            $this->$property = $value;
        } else {
            throw new Exception(sprintf('The "%s" property cannot be changed once set.', $property));
        }
    }

    /**
     * Bind a service to the container.
     * @param string $alias
     * @param mixed $concrete
     * @return mixed
     */
    public function bind($alias, $concrete)
    {
        $this->services[$alias] = $concrete;
    }

    /**
     * Request a service from the container.
     * @param mixed $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        if (!isset($this->services[$abstract])) {
            $abstract = $this->addNamespace($abstract);
        }
        if (isset($this->services[$abstract])) {
            $abstract = $this->services[$abstract];
        }
        if (is_callable($abstract)) {
            return call_user_func_array($abstract, [$this]);
        }
        if (is_object($abstract)) {
            return $abstract;
        }
        return $this->resolve($abstract);
    }

    /**
     * @return void
     */
    public function sessionClear()
    {
        $this->session = [];
    }

    /**
     * @return mixed
     */
    public function sessionGet($key, $fallback = '')
    {
        $value = Arr::get($this->session, $key, $fallback);
        unset($this->session[$key]);
        return $value;
    }

    /**
     * @return void
     */
    public function sessionSet($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * Bind a singleton instance to the container.
     * @param string $alias
     * @param callable|string|null $binding
     * @return void
     */
    public function singleton($alias, $binding)
    {
        $this->bind($alias, $this->make($binding));
    }

    /**
     * Prefix the current namespace to the abstract if absent.
     * @param string $abstract
     * @return string
     */
    protected function addNamespace($abstract)
    {
        if (!Str::contains($abstract, __NAMESPACE__) && !class_exists($abstract)) {
            $abstract = __NAMESPACE__.'\\'.$abstract;
        }
        return $abstract;
    }

    /**
     * Resolve a service from the container.
     * @param mixed $concrete
     * @return mixed
     * @throws Exception
     */
    protected function resolve($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            throw new Exception('Target ['.$concrete.'] is not instantiable.');
        }
        $constructor = $reflector->getConstructor();
        if (empty($constructor)) {
            return new $concrete();
        }
        return $reflector->newInstanceArgs(
            $this->resolveDependencies($constructor->getParameters())
        );
    }

    /**
     * Resolve a class based dependency from the container.
     * @return mixed
     * @throws Exception
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            return $this->make($parameter->getClass()->name);
        } catch (Exception $error) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }
            throw $error;
        }
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     * @return array
     */
    protected function resolveDependencies(array $dependencies)
    {
        $results = [];
        foreach ($dependencies as $dependency) {
            $results[] = !is_null($class = $dependency->getClass())
                ? $this->resolveClass($dependency)
                : $this->resolveDependency($dependency);
        }
        return $results;
    }

    /**
     * Resolve a single ReflectionParameter dependency.
     * @return array|null
     */
    protected function resolveDependency(ReflectionParameter $parameter)
    {
        if ($parameter->isArray() && $parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        return null;
    }
}
