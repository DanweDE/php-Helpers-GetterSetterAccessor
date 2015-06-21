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
	 * @dataProvider objectsAndObjectsPropertyProvider
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
	 * @dataProvider objectsAndObjectsPropertyProvider
	 */
	public function testProperty( $object, $objectsProperty ) {
		$getSet = new GetterSetterAccessor( $object );

		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessorPropertyInteractor',
			$getSet->property( $objectsProperty ),
			"property( '$objectsProperty' ) returns a GetterSetterAccessorPropertyInteractor instance"
		);
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testPropertyWithNonStringValues( $value ) {
		$getSet = new GetterSetterAccessor( new \DateTime() );

		$this->setExpectedException( 'InvalidArgumentException' );

		$getSet->property( $value );
	}

	/**
	 * @dataProvider objectsAndObjectsPropertyProvider
	 */
	public function testAccess( $object, $objectsProperty ) {
		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessor',
			GetterSetterAccessor::access( $object )
		);
	}

	/**
	 * Returns a random object for a case's first parameter and a property of that object as second.
	 *
	 * @return array( array( object $object, $objectsProperty ), ... )
	 */
	public static function objectsAndObjectsPropertyProvider() {
		return array(
			'public property' => array( new GetterSetterTestObject(), 'someDouble' ),
			'protected property' => array( new GetterSetterTestObject(), 'protectedValue' ),
			'private property' => array( new GetterSetterTestObject(), 'privateValue' ),
		);
	}
}

