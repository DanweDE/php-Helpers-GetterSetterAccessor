<?php
namespace Danwe\Helpers;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;

/**
 * Helper for GetterSetterAccessor. Normally GetterSetterAccessor should be used whose "property"
 * method will return an instance of this one, allowing for more expressive code.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorPropertyInteractor {

	/**
	 * @var mixed
	 */
	protected $instance;

	/**
	 * @var string
	 */
	protected $propertyName;

	/**
	 * @var callable
	 */
	protected $defaultReturningCallback;

	/**
	 * @var ReflectionProperty
	 */
	private $reflectionProperty;

	/**
	 * @param mixed $instance The subject object to operate on.
	 * @param string $propertyName Can even be the name of a private property.
	 *
	 * @throws InvalidArgumentException If $instance is no object.
	 * @throws InvalidArgumentException If $propertyName is no string.
	 * @throws GetterSetterAccessorIllegalPropertyException If $instance has no property $propertyName.
	 */
	public function __construct( $instance, $propertyName ) {
		if( ! is_object( $instance ) ) {
			throw new InvalidArgumentException( '$instance has to be an object' );
		}
		if( ! is_string( $propertyName ) ) {
			throw new InvalidArgumentException( '$propertyName has to be a string' );
		}
		$this->instance = $instance;
		$this->propertyName = $propertyName;
		$this->reflectionProperty = $this->newAccessibleReflectionProperty();
	}

	/**
	 * Allows to give a callback called in case the getter is being called before the setter,
	 * allowing to dynamically supply a complex default value.
	 * The function will only be called once, the returned value will then be assigned to the
	 * subject property.
	 *
	 * @param callable $defaultReturningCallback
	 * @return $this
	 *
	 * @throws InvalidArgumentException If $defaultReturningCallback is not callable.
	 */
	public function initially( $defaultReturningCallback ) {
		if( ! is_callable( $defaultReturningCallback ) ) {
			throw new InvalidArgumentException( '$defaultReturningCallback must be callable' );
		}
		$this->defaultReturningCallback = $defaultReturningCallback;
		return $this;
	}

	/**
	 * Runs the getter/setter functionality for the subject property. If a value is given then
	 * the value will be passed to the setter. If the value is null then the getter functionality
	 * will be invoked instead.
	 *
	 * @param mixed $value
	 * @return mixed Getter value or subject instance (initially given to the constructor).
	 */
	public function getOrSet( $value ) {
		if( $value === null ) {
			return $this->getValue();
		}
		$this->setValue( $value );
		return $this->instance;
	}

	protected function getValue() {
		$returnValue = $this->reflectionProperty->getValue( $this->instance );

		if( $returnValue === null && $this->defaultReturningCallback !== null ) {
			$returnValue = call_user_func( $this->defaultReturningCallback );
			if( $returnValue !== null ) {
				$this->getOrSet( $returnValue );
			}
		}
		return $returnValue;
	}

	protected function setValue( $value ) {
		$this->reflectionProperty->setValue( $this->instance, $value );
	}

	/**
	 * @return ReflectionProperty
	 *
	 * @throws GetterSetterAccessorIllegalPropertyException
	 */
	private function newAccessibleReflectionProperty() {
		$className = get_class( $this->instance );
		$reflectionClass = new ReflectionClass( $className );

		try {
			$reflectionProperty = $reflectionClass->getProperty( $this->propertyName );
		} catch( ReflectionException $e ) {
			throw new GetterSetterAccessorIllegalPropertyException(
				$this->propertyName, $this->instance );
		}

		$reflectionProperty->setAccessible( true );
		return $reflectionProperty;
	}
}
