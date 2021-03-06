<?php
namespace Danwe\Helpers\Tests;

use Danwe\DataProviders\DifferentTypesValues;
use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject as GetterSetterTestObject;
use Danwe\Helpers\GetterSetterAccessor;

/**
 * Tests whether GetterSetterAccessor can be used in designing a class with combined getter/setter
 * members.
 * Is using the Danwe\Helpers\Tests\TestHelpers\GetterSetterObject class and integrates the
 * GetterSetterAccessor functionality via PhpUnit's mocking facility.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessor_IntegrationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider implementationsOfCombinedSetterGetterMethodsProvider
	 */
	public function testRunAsGetterWithNonNullValues( GetterSetterTestObject $instance, $defaultsPerMethod ) {
		foreach( DifferentTypesValues::oneOfEachTypeProvider( 'test_with_non_null_values' ) as $case ) {
			list( $value, $type ) = $case;

			$method = 'some' . ucfirst( $type );
			$default = $defaultsPerMethod[ $method ];

			$this->assertSame( $default, $instance->$method(),
				"\$instance->$method() (getter) returns expected initial value" );

			$this->assertSame( $instance, $instance->$method( $value ),
				"\$instance->$method( \$value ) (setter) return self reference" );

			$this->assertSame( $value, $instance->$method(),
				"\$instance->$method() ( getter) returns value previously set" );
		}
	}

	/**
	 * @return array( array( GetterSetterTestObject $instance, mixed[] $defaultsPerMethod ), ... )
	 */
	public function implementationsOfCombinedSetterGetterMethodsProvider() {
		$initialValues = array(
			'someBoolean' => null,
			'someInteger' => null,
			'someDouble' => 3.14,
			'someString' => null,
			'someObject' => new GetterSetterTestObject(),
			'someArray' => array( 'rab' => 'bar', true, 42, 'test' ),
		);

		$originalObject = new GetterSetterTestObject( $initialValues[ 'someObject' ] );
		$originalObject->someDouble = $initialValues[ 'someDouble' ];
		$originalObject->someArray = $initialValues[ 'someArray' ];

		$mockedObject = $this->getMockBuilder(
			'Danwe\Helpers\Tests\TestHelpers\GetterSetterObject' )->getMock();

		$getSet = new GetterSetterAccessor( $mockedObject );

		foreach( $initialValues as $valueMethod => $initialValue ) {
			$getOrSetHandler =
				function( $value = null ) use( $getSet, $valueMethod, $initialValue ) {
					$propGetSet = $getSet->property( $valueMethod );

					if( $initialValue ) {
						$propGetSet->initially( function() use( $initialValue ) {
							return $initialValue;
						} );
					}
					return $propGetSet->getOrSet( $value );
				};

			$mockedObject->method( $valueMethod )
				->will( $this->returnCallback( $getOrSetHandler ) );
		}

		return array(
			'original object without GetterSetterAccessor' =>
				array( $originalObject, $initialValues
			),
			'mocked object using GetterSetterAccessor' =>
				array( $mockedObject, $initialValues
			),
		);
	}
}

