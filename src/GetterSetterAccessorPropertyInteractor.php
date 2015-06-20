<?php
namespace Danwe\Helpers;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;
use Closure;

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

	protected static $acceptedTypes = array(
		'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'mixed'
	);

	/**
	 * @var mixed
	 */
	protected $instance;

	/**
	 * @var string
	 */
	protected $propertyName;

	/**
	 * @var string
	 */
	protected $expectedValueType = 'mixed';

	/**
	 * @var mixed|callable
	 */
	protected $defaultReturningCallbackOrValue = null;

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
	 * Allows to specify the type a value given to getOrSet() is expected to be of.
	 * Allows types as returned by PHP's gettype() (see http://php.net/manual/en/function.gettype.php)
	 * except for 'NULL' because calling getOrSet( null ) would invoke getter, not setter.
	 * Allows the aliases "float" for "double" and the shorthands "int" and "bool" and the special
	 * type 'mixed' which is the default and stands for any arbitrary type.
	 *
	 * @param string $type
	 * @return $this
	 *
	 * @throws InvalidArgumentException If given type is not known.
	 */
	public function ofType( $type ) {
		if( $type === 'float' ) {
			$type = 'double';
		} else if( $type === 'bool' ) {
			$type = 'boolean';
		} else if( $type === 'int' ) {
			$type = 'integer';
		}
		if( ! in_array( $type, static::$acceptedTypes ) ) {
			throw new InvalidArgumentException( "unknown type $type" );
		}
		$this->expectedValueType = $type;
		return $this;
	}

	/**
	 * Allows to define a value or value returning callback used in case the getter is being called
	 * before the setter, allowing to supply a complex default value.
	 * Performance wise a callback is preferred in case of objects being returned as default since
	 * they will only be constructed once and if the fallback is really required.
	 *
	 * @param mixed|callable $defaultReturningCallbackOrValue
	 * @return $this
	 *
	 * @throws InvalidArgumentException If $defaultReturningCallbackOrValue is not callable.
	 */
	public function initially( $defaultReturningCallbackOrValue ) {
		$this->defaultReturningCallbackOrValue = $defaultReturningCallbackOrValue;
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
		$this->assertValueOfRightType( $value );
		$this->setValue( $value );
		return $this->instance;
	}

	protected function getValue() {
		$returnValue = $this->reflectionProperty->getValue( $this->instance );

		if( $returnValue === null ) {
			$returnValue = $this->getInitialValue();
			if( $returnValue !== null ) {
				$this->setValue( $returnValue );
			}
		}
		return $returnValue;
	}

	protected function getInitialValue() {
		if( $this->defaultReturningCallbackOrValue instanceof Closure ) {
			return call_user_func( $this->defaultReturningCallbackOrValue );
		}
		return $this->defaultReturningCallbackOrValue;
	}

	protected function setValue( $value ) {
		$this->reflectionProperty->setValue( $this->instance, $value );
	}

	protected function assertValueOfRightType( $value ) {
		if( $this->expectedValueType !== 'mixed'
			&& gettype( $value ) !== $this->expectedValueType
		) {
			throw new InvalidArgumentException( "value has to be of type {$this->expectedValueType}" );
		}
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
