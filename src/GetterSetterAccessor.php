<?php
namespace Danwe\Helpers;

use InvalidArgumentException;

/**
 * Helper class offering functionality to define setter and getter class methods with a minimum of
 * expressive code.
 *
 * @example <code>
 * class Foo {
 *   public function length( $value = null ) {
 *     ( new GetterSetterAccessor( $this ) )
 *       ->access( 'length' )
 *       ->defaultValue( function() {
 *           return new Length( 20 );
 *       } )
 *       ->run( $value )
 *   }
 * }
 *
 * </code>
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessor {

	/**
	 * @var mixed
	 */
	protected $instance;

	/**
	 * @param mixed $instance The subject object to operate on.
	 *
	 * @throws InvalidArgumentException If $instance is no object.
	 */
	public function __construct( $instance ) {
		if( ! is_object( $instance ) ) {
			throw new InvalidArgumentException( '$instance has to be an object' );
		}
		$this->instance = $instance;
	}

	/**
	 * @param string $propertyName The property used for value interacting with the getter/setter.
	 *        Should be the name of property of the class whose instance has been given to the
	 *        constructor. Might even be a private property.
	 * @return GetterSetterAccessorPropertyInteractor
	 */
	public function access( $propertyName ) {
		return new GetterSetterAccessorPropertyInteractor( $this->instance, $propertyName );
	}
}
