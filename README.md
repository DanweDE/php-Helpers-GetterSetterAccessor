# php-helpers-settergetteraccessor
Helper class offering functionality to define setter and getter class methods with a minimum of expressive code.

[![Latest Stable Version](https://poser.pugx.org/danwe/helpers-settergetteraccessor/version.png)](https://packagist.org/packages/danwe/helpers-settergetteraccessor)
[![Build Status](https://travis-ci.org/DanweDE/php-Helpers-SetterGetterAccessor.svg)](https://travis-ci.org/DanweDE/php-Helpers-SetterGetterAccessor)
[![Coverage Status](https://coveralls.io/repos/DanweDE/php-Helpers-SetterGetterAccessor/badge.svg)](https://coveralls.io/r/DanweDE/php-Helpers-SetterGetterAccessor)
[![Download count](https://poser.pugx.org/danwe/helpers-settergetteraccessor/d/total.png)](https://packagist.org/packages/danwe/helpers-settergetteraccessor)

## Usage
See the following usage example:

```php
<?php

use Danwe\Helpers\GetterSetterAccessor;

class Options {
  $protected $formatter;
  $protected $name = 'Php';
  $protected $length;

  public function formatter( Formatter $value = null ) {
    return $this->getterSetter( __FUNCTION__ )
      ->initially( function() {
        return new Formatter();
      } )
      ->getOrSet( $value );
  }

  public function name( $value = null ) {
    return $this->getterSetter( __FUNCTION__ )
      ->ofType( 'string' )
      ->getOrSet( $value );
  }

  public function length( $value = null ) {
    return $this->getterSetter( __FUNCTION__ )
      ->ofType( 'int' )
      ->initially( 42 ) // Equivalent to setting 42 for Options::$length above as done for "name".
      ->getOrSet( $value );
  }

  protected function getterSetter( $accessProperty ) {
    if( !$this->getterSetter ) {
      $this->getterSetter = new GetterSetterAccessor( $this );
    }
    return $this->getterSetter->property( $accessProperty );
  }
}
```

## Performance Implications
You should be aware that `GetterSetterAccessor` is using PHP's reflection facilities internally
to set/get property values. This is expected to be a few times slower than setting the property
directly but shouldn't be of any real concern unless you are expecting a method to be called
several hundred thousand times.

## TODOs
* Allow to set more than one type for `ofType`.
* Allow setting `null` values by defining another value as the one triggering the getter.
