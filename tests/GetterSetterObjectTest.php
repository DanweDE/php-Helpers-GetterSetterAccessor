<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject;
use DateTime;
use InvalidArgumentException;

/**
 * @covers Danwe\Helpers\Tests\TestHelpers\GetterSetterObject
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterObjectTest extends \PHPUnit_Framework_TestCase {

	public function testConstruction( $arg1 = null ) {
		$instance = new GetterSetterObject( $arg1 );
		$this->assertInstanceOf(
			'Danwe\Helpers\Tests\TestHelpers\GetterSetterObject',
			$instance
		);
		return $instance;
	}

	public function testConstructionWithDefaultValueForObjectGetter() {
		$defaultValue = new DateTime();
		return $this->testConstruction( $defaultValue );
	}

	/**
	 * @depends testConstructionWithDefaultValueForObjectGetter
	 */
	public function testSomeObjectGetterWithConstructorDefinedDefault( GetterSetterObject $instance ) {
		$this->assertTrue( $instance->someObject() instanceof DateTime );
	}

	/**
	 * @dataProvider getterSetterValuesProvider
	 */
	public function testGetterSetterMethods( $setterGetter, $someValue ) {
		$instance = new GetterSetterObject();

		$this->assertNull( $instance->$setterGetter(), 'null initially' );

		$this->assertSame( $instance, $instance->$setterGetter( $someValue ),
			'setter returns self reference' );

		$this->assertSame( $someValue, $instance->$setterGetter(),
			'getter returns value previously set vai setter' );
	}

	/**
	 * @dataProvider getterSetterValuesProvider
	 */
	public function testGetterSetterMethodSettingInvalidValue( $setterGetter, $someValue ) {
		$instance = new GetterSetterObject();

		foreach( $this->getterSetterValuesProvider() as $case ) {
			list( $caseSetterGetter, $value ) = $case;

			if( $caseSetterGetter === $setterGetter ) {
				continue;
			}

			$caughtException = false;
			try {
				$instance->$setterGetter( $value );
			} catch( InvalidArgumentException $e ) {
				$caughtException = true;
			}
			$this->assertTrue( $caughtException,
				"$setterGetter() with invalid value threw an exception" );
		}
	}

	public function testGetPrivateValue() {
		$instance = new GetterSetterObject();
		$this->assertNull( $instance->getPrivateValue() );
	}

	/**
	 * @return array( array( string $setterGetterName, mixed $someValue ), ... )
	 */
	public static function getterSetterValuesProvider() {
		return array(
			array( 'someBoolean', false ),
			array( 'someInteger', 42 ),
			array( 'someDouble', 3.14 ),
			array( 'someString', 'test string' ),
			array( 'someObject', new DateTime() ),
			array( 'someArray', array( 'foo' => 'bar', true, 42, 'test' ) ),
		);
	}

}

