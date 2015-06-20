<?php

namespace Danwe\Helpers\Benchmarks;

use Athletic\AthleticEvent;
use Danwe\Helpers\GetterSetterAccessor;
use Danwe\Helpers\Tests\TestHelpers\GetterSetterObject;

class GetterSetterAccessorEvent extends AthleticEvent {

	private $values;
	private $hardCodedSubject;
	private $getterSetterAccessorSubject;
	private $getterSetterAccessorMethods;

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
	}

	/**
	 * @see AthleticEvent::setUp()
	 */
    public function setUp() {
		$this->hardCodedSubject = new GetterSetterObject();
		$this->getterSetterAccessorSubject = new GetterSetterObject();

		$this->getterSetterAccessorMethods = array();
		$getSet = new GetterSetterAccessor( $this->getterSetterAccessorSubject );

		foreach( $this->values as $valueMethod => $someValue ) {
			$this->getterSetterAccessorMethods[ $valueMethod ] =
				function( $value = null ) use( $getSet, $valueMethod ) {
					return $getSet
						->property( $valueMethod )
						->getOrSet( $value );
				};
		}
    }

	private function nextMethodAndValue() {
		if( current( $this->values ) === false ) {
			reset( $this->values );
		}
		$result = each( $this->values );

		return $result;
	}

   /**
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
     * @iterations 6000
     */
    public function setterGetterUsingThisLibrary() {
		$this->pause();

		list( $methodName, $value ) = $this->nextMethodAndValue();
		$method = $this->getterSetterAccessorMethods[ $methodName ];

		$this->resume();
		$method( $value );
		$this->pause();

		assert( '$this->getterSetterAccessorSubject->$methodName() == $value' );

		$this->resume();
    }

}