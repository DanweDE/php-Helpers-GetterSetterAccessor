<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\GetterSetterAccessorIllegalPropertyException as GSAIPException;
use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject;

/**
 * @covers Danwe\Helpers\GetterSetterAccessorIllegalPropertyException
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorIllegalPropertyExceptionTest extends \PHPUnit_Framework_TestCase {

	protected $someObject;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
		$this->someObject = new GetterSetterObject();
	}

	public function testConstruction() {
		$instance = new GSAIPException(
			'privateValue',
			$this->someObject
		);
		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessorIllegalPropertyException',
			$instance
		);
		return $instance;
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionWithIllegalPropertyArgumentWithNonStringValues( $value ) {
		new GSAIPException(
			$value,
			$this->someObject
		);
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionWithInstanceArgumentWithNonStringValues( $value ) {
		new GSAIPException(
			$value,
			$this->someObject
		);
	}

	/**
	 * @depends testConstruction
	 */
	public function testGetIllegalProperty( GSAIPException $instance ) {
		$this->assertEquals( 'privateValue', $instance->getIllegalProperty() );
	}

	/**
	 * @depends testConstruction
	 */
	public function testGetInstance( GSAIPException $instance ) {
		$exception = new GSAIPException( 'privateValue', $this->someObject );

		$this->assertSame( $this->someObject, $exception->getInstance() );
	}
}

