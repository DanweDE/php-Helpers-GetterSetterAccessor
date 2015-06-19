<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject as GetterSetterTestObject;
use Danwe\Helpers\GetterSetterAccessorPropertyInteractor;

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
	 * @dataProvider objectsAndObjectsPropertyProvider
	 */
	public function testConstruction( $object, $property ) {
		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessorPropertyInteractor',
			new GetterSetterAccessorPropertyInteractor( $object, $property )
		);
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionFirstParameterWithNonObjectValues( $nonObject ) {
		new GetterSetterAccessorPropertyInteractor( $nonObject, 'someProperty' );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionSecondParameterWithNonStringValues( $nonExistentProperty ) {
		$object = new \DateTime();
		new GetterSetterAccessorPropertyInteractor( $object, $nonExistentProperty );
	}

	public function testInitially() {
		$instance = $this->newInstance();
		$this->assertSame( $instance, $instance->initially( function() { return 42; } ) );
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
		return new GetterSetterAccessorPropertyInteractor( new GetterSetterTestObject(), 'someString' );
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

