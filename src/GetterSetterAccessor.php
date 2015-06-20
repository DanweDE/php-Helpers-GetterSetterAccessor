<?php
namespace Danwe\Helpers;

use InvalidArgumentException;

/**
 * Helper class offering functionality to define setter and getter class methods with a minimum of
 * expressive code.
 *
 * @example <code>
 * class Foo {
 *   public function length( Length $value = null ) {
 *     ( new GetterSetterAccessor( $this ) )
 *       ->property( 'length' )
 *       ->initially( function() {
 *           return new Length( 20 );
 *       } )
 *       ->getOrSet( $value )
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
	 * @param object $instance The subject object to operate on.
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
	public function property( $propertyName ) {
		return new GetterSetterAccessorPropertyInteractor( $this->instance, $propertyName );
	}

	/**
	 * Equivalent to
	 *   ( new GetterSetterAccessor( $instance ) )->property( $propertyName ) ...
	 * but usable with PHP 5.3 while the above is not.
	 *
	 * @param object $instance
	 * @param string $propertyName
	 *
	 * @return mixed
	 */
	public static function access( $instance, $propertyName ) {
		$accessor = new static( $instance );
		return $accessor->property( $propertyName );
	}
}
