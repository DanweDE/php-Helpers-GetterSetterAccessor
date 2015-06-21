<?php

namespace Danwe\Helpers\Benchmarks;

use Athletic\AthleticEvent;
use Danwe\Helpers\GetterSetterAccessor;
use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject;

/**
 * Benchmarks for different combined setter/getter method implementations. Compares implementations
 * using this library's GetterSetterAccessor with a basic implementation using standard PHP means.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class GetterSetterAccessorEvent extends AthleticEvent {

	private $values;
	private $nextValuesKey;

	private $hardCodedSubject;
	private $getterSetterAccessorSubject;
	private $getterSetterAccessorWorstCaseSubject;

	private $getterSetterAccessorMethods;
	private $getterSetterAccessorWorstCaseMethods;

	/**
	 * @see AthleticEvent::classSetUp()
	 */
    public function classSetUp() {
		$this->values = array(
			'someBoolean' => true,
			'someInteger' => 1337,
			'someDouble' => 3.14159,
			'someString' => 'Some string not too short, not too long but at least a few characters',
			'someObject' => new \DateTime(),
			'someArray' => array( 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D' ),
		);
		$this->nextValuesKey = 'someBoolean';
	}

	/**
	 * @see AthleticEvent::setUp()
	 */
    public function setUp() {
		$this->hardCodedSubject = new GetterSetterObject();
		$this->getterSetterAccessorSubject = new GetterSetterObject();
		$this->getterSetterAccessorWorstCaseSubject = new GetterSetterObject();

		$this->getterSetterAccessorMethods = array();
		$getSet = new GetterSetterAccessor( $this->getterSetterAccessorSubject );

		foreach( $this->values as $valueMethod => $someValue ) {
			$valueType = str_replace( 'some', '', strtolower( $valueMethod ) );
			$this->getterSetterAccessorMethods[ $valueMethod ] =
				function( $value = null ) use( $getSet, $valueMethod, $valueType ) {
					return $getSet
						->property( $valueMethod )
						->ofType( $valueType )
						->getOrSet( $value );
				};
		}

		$this->getterSetterAccessorWorstCaseMethods = array();
		$worstCaseSubject = $this->getterSetterAccessorWorstCaseSubject;

		foreach( $this->values as $valueMethod => $someValue ) {
			$valueType = str_replace( 'some', '', strtolower( $valueMethod ) );
			$this->getterSetterAccessorWorstCaseMethods[ $valueMethod ] =
				function( $value = null ) use( $worstCaseSubject, $valueMethod, $valueType ) {
					$getSet = new GetterSetterAccessor( $worstCaseSubject );
					$getSet
						->property( $valueMethod )
						->ofType( $valueType )
						->initially( function() { return null; } )
						->getOrSet( $value );
				};
		}
    }

	/**
	 * Returns a method name and a value to set for that method. Iterates over $this->values so
	 * each benchmark iteration is changing the getter/setter to be used.
	 *
	 * @return array
	 */
	private function nextMethodAndValue() {
		$result = array( $this->nextValuesKey, $this->values[ $this->nextValuesKey ] );

		$firstKey = false;
		$nextIsNextValuesKey = false;
		foreach( $this->values as $key => $value ) {
			if( !$firstKey ) {
				$firstKey = $key;
			}
			if( $nextIsNextValuesKey ) {
				$this->nextValuesKey = $key;
				return $result;
			}
			if( $key === $this->nextValuesKey ) {
				$nextIsNextValuesKey = true;
			}
		}
		$this->nextValuesKey = $firstKey;
		return $result;
	}

	/**
	 * Benchmarks setter/getter methods implemented in pure PHP without GetterSetterAccessor helper.
	 *
	 * @iterations 6000
	 */
	public function hardCodedSetterGetter() {
		$this->pause();

		list( $methodName, $value ) = $this->nextMethodAndValue();

		$subject = $this->hardCodedSubject;

		$this->resume();
		$subject->$methodName( $value );
		$this->pause();

		assert( '$subject->$methodName() == $value' );

		$this->resume();
	}

	/**
	 * Benchmarks setter/getter methods implemented with the help of this library's
	 * GetterSetterAccessor class. The implementation is done in a performant way, only constructing
	 * the GetterSetterAccessor object once and not defining a default value.
	 *
	 * @iterations 6000
	 */
	public function setterGetterUsingThisLibrary_normalUsage() {
		$this->pause();

		list( $methodName, $value ) = $this->nextMethodAndValue();
		$method = $this->getterSetterAccessorMethods[ $methodName ];

		$this->resume();
		$method( $value );
		$this->pause();

		assert( '$this->getterSetterAccessorSubject->$methodName() == $value' );

		$this->resume();
	}

	/**
	 * In this benchmark a new GetterSetterAccessor is constructed per getter/setter method call
	 * while the first benchmark is simulating the case where the object is created only once and
	 * then reused for subsequent calls.
	 * Furthermore, GetterSetterAccessor's default value functionality is used for the getter/setter
	 * methods, which is creating additional overhead per method call.
	 *
	 * @iterations 6000
	 */
	public function setterGetterUsingThisLibrary_extensiveUsage() {
		$this->pause();

		list( $methodName, $value ) = $this->nextMethodAndValue();
		$method = $this->getterSetterAccessorWorstCaseMethods[ $methodName ];

		$this->resume();
		$method( $value );
		$this->pause();

		assert( '$this->getterSetterAccessorWorstCaseSubject->$methodName() == $value' );

		$this->resume();
	}

}