<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\GetterSetterAccessorIllegalPropertyException as GSAIPException;
use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject;

/**
 * @covers Danwe\Helpers\GetterSetterAccessorIllegalPropertyException
 *
 * @since 1.0.1
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorIllegalPropertyExceptionTest extends \PHPUnit_Framework_TestCase {

	/** @var object */
	protected static $someObject;

	/** @var string */
	protected static $somePropertyOfSomeObject;

	/**
	 * @see PHPUnit_Framework_TestCase::setUpBeforeClass()
	 */
	public static function setUpBeforeClass() {
		static::$someObject = new GetterSetterObject();
		static::$somePropertyOfSomeObject = 'privateValue';
	}

	public function testConstruction() {
		$instance = new GSAIPException(
			static::$somePropertyOfSomeObject,
			static::$someObject
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
			static::$someObject
		);
	}

	/**
	 * @dataProvider Danwe\DataProviders\DifferentTypesValues::oneOfEachTypeProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionWithInstanceArgumentWithNonObjectValues( $value ) {
		new GSAIPException(
			'someProperty',
			$value
		);
	}

	/**
	 * @depends testConstruction
	 */
	public function testGetIllegalProperty( GSAIPException $exception ) {
		$this->assertEquals(
			static::$somePropertyOfSomeObject,
			$exception->getIllegalProperty()
		);
	}

	/**
	 * @depends testConstruction
	 */
	public function testGetInstance( GSAIPException $exception ) {
		$this->assertSame(
			static::$someObject,
			$exception->getInstance()
		);
	}
}

