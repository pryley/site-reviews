<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use Closure;
use Exception;
use GeminiLabs\SiteReviews\Providers\ProviderInterface;
use ReflectionClass;
use ReflectionParameter;

abstract class Container
{
	protected static $PROTECTED_PROPERTIES = [
		'instance',
		'services',
		'storage',
	];

	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * The container's bound services
	 *
	 * @var array
	 */
	protected $services = [];

	/**
	 * The container's storage items
	 *
	 * @var array
	 */
	protected $storage = [];

	/**
	 * Set/get the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function load()
	{
		if( empty( static::$instance )) {
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * Dynamically access container properties.
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function __get( $property )
	{
		if( property_exists( $this, $property ) && !in_array( $property, static::$PROTECTED_PROPERTIES )) {
			return $this->$property;
		}
		return isset( $this->storage[$property] )
			? $this->storage[$property]
			: null;
	}

	/**
	 * Dynamically set container properties.
	 *
	 * @param string $property
	 * @param string $value
	 * @return void
	 */
	public function __set( $property, $value )
	{
		if( !property_exists( $this, $property ) || in_array( $property, static::$PROTECTED_PROPERTIES )) {
			$this->storage[$property] = $value;
		}
		else if( !isset( $this->$property )) {
			$this->$property = $value;
		}
		else {
			throw new Exception( sprintf( 'The "%s" property cannot be changed once set.', $property ));
		}
	}

	/**
	 * This is the Application entry point
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * Bind a service to the container.
	 *
	 * @param string $alias
	 * @param mixed $concrete
	 * @return mixed
	 */
	public function bind( $alias, $concrete )
	{
		$this->services[$alias] = $concrete;
	}

	/**
	 * Resolve the given type from the container and allow unbound aliases that omit the
	 * root namespace. i.e. 'Controller' translates to 'GeminiLabs\Pollux\Controller'
	 *
	 * @param mixed $abstract
	 * @return mixed
	 */
	public function make( $abstract )
	{
		if( !isset( $this->services[$abstract] )) {
			$abstract = $this->addNamespace( $abstract );
		}
		if( isset( $this->services[$abstract] )) {
			$abstract = $this->services[$abstract];
		}
		if( is_callable( $abstract )) {
			return call_user_func_array( $abstract, [$this] );
		}
		if( is_object( $abstract )) {
			return $abstract;
		}
		return $this->resolve( $abstract );
	}

	/**
	 * Register a Provider.
	 *
	 * @return void
	 */
	public function register( ProviderInterface $provider )
	{
		$provider->register( $this );
	}

	/**
	 * Register a shared binding in the container.
	 *
	 * @param string $abstract
	 * @param callable|string|null $concrete
	 * @return void
	 */
	public function singleton( $abstract, $concrete )
	{
		$this->bind( $abstract, $this->make( $concrete ));
	}

	/**
	 * Prefix the current namespace to the abstract if absent
	 *
	 * @param string $abstract
	 * @return string
	 */
	protected function addNamespace( $abstract )
	{
		if( strpos( $abstract, __NAMESPACE__ ) === false ) {
			$abstract = __NAMESPACE__ . "\\$abstract";
		}
		return $abstract;
	}

	/**
	 * Throw an exception that the concrete is not instantiable.
	 *
	 * @param string $concrete
	 * @throws Exception
	 */
	protected function notInstantiable( $concrete )
	{
		$message = "Target [$concrete] is not instantiable.";
		throw new Exception( $message );
	}

	/**
	 * Resolve a class based dependency from the container.
	 *
	 * @param mixed $concrete
	 * @return mixed
	 * @throws Exception
	 */
	protected function resolve( $concrete )
	{
		if( $concrete instanceof Closure ) {
			return $concrete( $this );
		}
		$reflector = new ReflectionClass( $concrete );
		if( !$reflector->isInstantiable() ) {
			return $this->notInstantiable( $concrete );
		}
		$constructor = $reflector->getConstructor();
		if( empty( $constructor )) {
			return new $concrete;
		}
		return $reflector->newInstanceArgs(
			$this->resolveDependencies( $constructor->getParameters() )
		);
	}

	/**
	 * Resolve a class based dependency from the container.
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function resolveClass( ReflectionParameter $parameter )
	{
		try {
			return $this->make( $parameter->getClass()->name );
		}
		catch( Exception $error ) {
			if( $parameter->isOptional() ) {
				return $parameter->getDefaultValue();
			}
			throw $error;
		}
	}

	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @return array
	 */
	protected function resolveDependencies( array $dependencies )
	{
		$results = [];
		foreach( $dependencies as $dependency ) {
			$results[] = !is_null( $class = $dependency->getClass() )
				? $this->resolveClass( $dependency )
				: $this->resolveDependency( $dependency );
		}
		return $results;
	}

	/**
	 * Resolve a single ReflectionParameter dependency.
	 *
	 * @return array|null
	 */
	protected function resolveDependency( ReflectionParameter $parameter )
	{
		if( $parameter->isArray() && $parameter->isDefaultValueAvailable() ) {
			return $parameter->getDefaultValue();
		}
		return null;
	}
}
