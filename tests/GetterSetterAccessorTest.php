<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject as GetterSetterTestObject;
use Danwe\Helpers\GetterSetterAccessor;

/**
 * @covers Danwe\Helpers\GetterSetterAccessor
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider objectsProvider
	 */
	public function testConstruction( $object ) {
		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessor',
			new GetterSetterAccessor( $object )
		);
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionWithNonObjectValues( $value ) {
		new GetterSetterAccessor( $value );
	}

	/**
	 * @dataProvider objectsProvider
	 */
	public function testAccess( $object ) {
		$getSet = new GetterSetterAccessor( $object );

		foreach( static::propertyNamesProvider() as $case ) {
			list( $propertyName ) = $case;

			$this->assertInstanceOf(
				'Danwe\Helpers\GetterSetterAccessorPropertyInteractor',
				$getSet->access( $propertyName ),
				"access( '$propertyName' ) returns a GetterSetterAccessorPropertyInteractor instance"
			);
		}
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testAccessWithNonStringValues( $value ) {
		$getSet = new GetterSetterAccessor( new \DateTime() );

		$this->setExpectedException( 'InvalidArgumentException' );

		$getSet->access( $value );
	}

	/**
	 * Returns a random object for a case's first parameter.
	 *
	 * @return array( array( mixed $object ), ... )
	 */
	public static function objectsProvider() {
		return array_chunk( array(
			new GetterSetterTestObject(),
			new \DateTime(),
			new \ReflectionClass( __CLASS__ )
		), 1 );
	}

	/**
	 * @return array( array( string $propertyName ), ... )
	 */
	public static function propertyNamesProvider() {
		return array_chunk( array(
			'foo',
			'bar',
			'anotherProperty'
		), 1 );
	}
}

