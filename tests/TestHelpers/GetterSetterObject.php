<?php
namespace Danwe\Helpers\Tests\TestHelpers;

use Doctrine\Instantiator\Exception\InvalidArgumentException;

/**
 * Class designed mainly for GetterSetterAccessorIntegrationTest.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterObject {

	// Properties are at least protected so they are accessible in a mocks "returnsCallback":
	protected $someBoolean;
	protected $someInteger;
	public $someDouble;
	protected $someString;
	protected $someObject;
	public $someArray;

	// For testing accessibility of privates:
	private $privateValue;
	protected $protectedValue;
	public $publicValue;

	public function __construct( $defaultForSomeObjectGetter = null ) {
		$this->someObject = $defaultForSomeObjectGetter;
	}

	public function someBoolean( $value = null ) {
		return $this->getAndSet( __FUNCTION__, $value, 'is_bool' );
	}

	public function someInteger( $value = null ) {
		return $this->getAndSet( __FUNCTION__, $value, 'is_integer' );
	}

	public function someDouble( $value = null ) {
		return $this->getAndSet( __FUNCTION__, $value, 'is_double' );
	}

	public function someString( $value = null ) {
		return $this->getAndSet( __FUNCTION__, $value, 'is_string' );
	}

	public function someObject( $value = null ) {
		return $this->getAndSet( __FUNCTION__, $value, 'is_object' );
	}

	public function someArray( $value = null ) {
		return $this->getAndSet( __FUNCTION__, $value, 'is_array' );
	}

	public function getPrivateValue() {
		return $this->privateValue;
	}

	private function getAndSet( $property, $value, $typeCheck ) {
		if( $value === null ) { // GETTER:
			return $this->{ $property };
		}
		// SETTER:
		if( ! $typeCheck( $value ) ) {
			throw new InvalidArgumentException( "$value does not pass $typeCheck" );
		}
		$this->{ $property } = $value;
		return $this;
	}
}
