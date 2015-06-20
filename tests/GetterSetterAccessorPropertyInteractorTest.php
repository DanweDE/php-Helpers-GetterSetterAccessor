<?php
namespace Danwe\Helpers\Tests;

use Danwe\DataProviders\DifferentTypesValues;
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

	/**
	 * @dataProvider typesAndValuesProvider
	 */
	public function testOfType( $type, $validValues, $invalidValues ) {
		$instance = $this->newInstance();
		$this->assertSame( $instance, $instance->ofType( $type ), 'returns self reference' );
	}

	public function testInitiallyWithCallback() {
		$instance = $this->newInstance();
		$this->assertSame( $instance, $instance->initially( function() { return 42; } ) );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testInitiallyWithNonCallback( $value ) {
		$instance = $this->newInstance();
		$this->assertSame( $instance, $instance->initially( $value ), 'returns self reference' );
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testGetOrSetGettingAndSettingWithNonNullValues( $value, $type ) {
		$instance = new GetterSetterTestObject();
		$property = 'some' . ucfirst( $type );
		$getSet = new GetterSetterAccessorPropertyInteractor(
			$instance,
			$property
		);
		$this->assertInternalType( 'null', $getSet->getOrSet( null ),
			"getOrSet( null ) (getter) returns null initially" );

		$this->assertSame( $instance, $getSet->getOrSet( $value ),
			'getOrSet( $value ) (setter) returns self reference' );

		$this->assertSame( $value, $getSet->getOrSet( null ),
			"getOrSet( null ) (getter) returns value previously set" );
	}

	/**
	 * @dataProvider typesAndValuesProvider
	 */
	public function testGetOrSetSettingRespectsOfTypeConfiguration( $type, $validValues, $invalidValues ) {
		$instance = new GetterSetterTestObject();
		$getSet = new GetterSetterAccessorPropertyInteractor( $instance, 'publicValue' );
		$getSet->ofType( $type );

		foreach( $validValues as $validValue ) {
			$this->assertSame( $instance, $getSet->getOrSet( $validValue ),
				'getOrSet( $value ) (setter) returns self reference' );

			$this->assertSame( $validValue, $getSet->getOrSet( null ),
				"getOrSet( null ) (getter) returns value previously set" );
		}

		foreach( $invalidValues as $invalidValue ) {
			$caught = false;
			try {
				$getSet->getOrSet( $invalidValue );
			} catch( \InvalidArgumentException $e ) {
				$caught = true;
			}
			$this->assertTrue( $caught, 'setting value of wrong type \"' . gettype( $invalidValue )
				. '\" throws InvalidArgumentException' );
		}
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 */
	public function testGetOrSetAsGetterGettingDefaultWithNonNullValues( $value, $type ) {
		$instance = new GetterSetterTestObject();
		$property = 'some' . ucfirst( $type );
		$getSet = new GetterSetterAccessorPropertyInteractor(
			$instance,
			$property
		);
		$getSet->initially( function() use( $value ) {
			return $value;
		} );
		$this->assertSame( $value, $getSet->getOrSet( null ),
			"getOrSet( null ) (getter) returns value defined as default" );
	}

	public function testGetOrSetCanAccessPrivateInstanceProperty() {
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

		$this->assertSame( $instance, $getSet->getOrSet( $testValue ),
			'getOrSet( $value ) (setter) returns self reference' );

		$this->assertSame( $testValue, $instance->getPrivateValue() );
	}

	/**
	 * @return GetterSetterAccessorPropertyInteractor
	 */
	protected function newInstance() {
		return new GetterSetterAccessorPropertyInteractor( new GetterSetterTestObject(), 'publicValue' );
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

	public static function typesAndValuesProvider() {
		foreach( DifferentTypesValues::oneOfEachTypeProvider() as $provided ) {
			list( $value, $type ) = $provided;

			$cases[ $type ] = array(
				'type' => $type,
				'valid' => array( $value ),
				'invalid' => self::flattenProviderCasesAndRemoveNull(
					DifferentTypesValues::oneOfEachTypeProvider( "test_with_non_{$type}_values" ) ),
			);
		}
		$cases[ 'int' ] = $cases[ 'integer' ];
		$cases[ 'bool' ] = $cases[ 'boolean' ];
		$cases[ 'float' ] = $cases[ 'double' ];
		$cases[ 'mixed' ] = array(
			'type' => 'mixed',
			'valid' => self::flattenProviderCasesAndRemoveNull(
				DifferentTypesValues::oneOfEachTypeProvider() ),
			'invalid' => array(),
		);
		unset( $cases[ 'NULL' ] ); // wouldn't make much sense since null is reserved to trigger getter

		return $cases;
	}

	private static function flattenProviderCasesAndRemoveNull( $cases ) {
		foreach( $cases as $case ) {
			if( $case[ 0 ] === null ) {
				continue;
			}
			$flattened[] = $case[ 0 ];
		}
		return $flattened;
	}
}

