<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject as GetterSetterTestObject;
use Danwe\Helpers\GetterSetterAccessorPropertyInteractor;
use Danwe\Helpers\GetterSetterAccessor;

/**
 * @covers Danwe\Helpers\GetterSetterAccessorPropertyInteractor
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorPropertyInteractorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider objectsProvider
	 */
	public function testConstruction( $object ) {
		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessorPropertyInteractor',
			new GetterSetterAccessorPropertyInteractor( $object, 'someProperty' )
		);
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionFirstParameterWithNonObjectValues( $value ) {
		new GetterSetterAccessorPropertyInteractor( $value, 'someProperty' );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionSecondParameterWithNonStringValues( $value ) {
		new GetterSetterAccessorPropertyInteractor( new \DateTime(), $value );
	}

	public function testInitially() {
		$this->newInstance()->initially( function() { return 42; } );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testInitiallyWithInvalidValues( $value ) {
		$this->newInstance()->initially( $value );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testRunGettingAndSettingWithNonNullValues( $value, $type ) {
		$instance = new GetterSetterTestObject();
		$property = 'some' . ucfirst( $type );
		$getSet = new GetterSetterAccessorPropertyInteractor(
			$instance,
			$property
		);
		$this->assertInternalType( 'null', $getSet->run( null ),
			"run( null ) (getter) returns null initially" );

		$this->assertSame( $instance, $getSet->run( $value ),
			'run( $value ) (setter) returns self reference' );

		$this->assertSame( $value, $getSet->run( null ),
			"run( null ) (getter) returns value previously set" );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testRunAsGetterGettingDefaultWithNonNullValues( $value, $type ) {
		$instance = new GetterSetterTestObject();
		$property = 'some' . ucfirst( $type );
		$getSet = new GetterSetterAccessorPropertyInteractor(
			$instance,
			$property
		);
		$getSet->initially( function() use( $value ) {
			return $value;
		} );
		$this->assertSame( $value, $getSet->run( null ),
			"run( null ) (getter) returns value defined as default" );
	}

	public function testRunCanAccessPrivateInstanceProperty() {
		$instance = new GetterSetterTestObject();
		$privateProperty = 'privateValue';
		$getSet = new GetterSetterAccessorPropertyInteractor(
			$instance,
			$privateProperty // Private property of GetterSetterTestObject
		);
		$testValue = 'some value';

		$reflect = new \ReflectionClass( $instance );
		$this->assertTrue(
			$reflect->getProperty( $privateProperty )->isPrivate(),
			"Test subject's \"$privateProperty\" property is private"
		);

		$this->assertSame( $instance, $getSet->run( $testValue ),
			'run( $value ) (setter) returns self reference' );

		$this->assertSame( $testValue, $instance->getPrivateValue() );
	}

	/**
	 * @return GetterSetterAccessorPropertyInteractor
	 */
	protected function newInstance() {
		return new GetterSetterAccessorPropertyInteractor( new GetterSetterTestObject(), 'someInt' );
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
}

