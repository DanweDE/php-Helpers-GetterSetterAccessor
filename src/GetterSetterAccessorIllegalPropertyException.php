<?php
namespace Danwe\Helpers;

use LogicException;
use InvalidArgumentException;

/**
 * Indicates that a property name used with GetterSetterAccessorPropertyInteractor does not exist
 * on the given object.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorIllegalPropertyException extends LogicException {

	/**
	 * @var string
	 */
	protected $illegalProperty;

	/**
	 * @var object
	 */
	protected $instance;

	/**
	 * @param string $illegalProperty
	 * @param object $instance
	 *
	 * @throws InvalidArgumentException If a value of the wrong type is given to one of the parameters.
	 */
	public function __construct( $illegalProperty, $instance ) {
		if( ! is_string( $illegalProperty ) ) {
			throw new InvalidArgumentException( '$illegalProperty has to be a string' );
		}
		if( ! is_object( $instance ) ) {
			throw new InvalidArgumentException( '$instance has to be an object' );
		}
		parent::__construct(
			"property \"$illegalProperty\" does not exist on given object \$instance"
		);
		$this->illegalProperty = $illegalProperty;
		$this->instance = $instance;
	}

	/**
	 * @since 1.0.1 (always threw an exception in 1.0.0)
	 *
	 * @return string
	 */
	public function getIllegalProperty() {
		return $this->illegalProperty;
	}

	/**
	 * @return object
	 */
	public function getInstance() {
		return $this->instance;
	}
}
