<?php
namespace Danwe\Helpers\Tests;

use Danwe\Helpers\GetterSetterAccessorIllegalPropertyException as GSAIPException;
use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject;

/**
 * @covers Danwe\Helpers\GetterSetterAccessor
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorIllegalPropertyExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstruction() {
		$instance = new GSAIPException(
			'privateValue',
			new GetterSetterObject()
		);
		$this->assertInstanceOf(
			'Danwe\Helpers\GetterSetterAccessorIllegalPropertyException',
			$instance
		);
		return $instance;
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
		$instance = new GetterSetterObject();
		$exception = new GSAIPException( 'privateValue', $instance );

		$this->assertSame( $instance, $exception->getInstance() );
	}
}

