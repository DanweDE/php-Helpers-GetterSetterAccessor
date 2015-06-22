# php-Helpers-GetterSetterAccessor
Helper class offering functionality to define combined getter and setter class methods with a minimum of expressive code. An example for a "combined getter and setter" would be `User::age( $age=null )` for getting or setting an user's age instead of having two separate methods `User::getAge()` and `User::setAge( $age )`.

[![Latest Stable Version](https://poser.pugx.org/danwe/helpers-gettersetteraccessor/version.png)](https://packagist.org/packages/danwe/helpers-gettersetteraccessor)
[![Build Status](https://travis-ci.org/DanweDE/php-Helpers-GetterSetterAccessor.svg)](https://travis-ci.org/DanweDE/php-Helpers-SetterGetterAccessor)
[![Coverage Status](https://coveralls.io/repos/DanweDE/php-Helpers-GetterSetterAccessor/badge.svg)](https://coveralls.io/r/DanweDE/php-Helpers-SetterGetterAccessor)
[![Download count](https://poser.pugx.org/danwe/helpers-gettersetteraccessor/d/total.png)](https://packagist.org/packages/danwe/helpers-gettersetteraccessor)

## Disclaimer
This library is in no way suggesting that combined getters and setters are superior to having separate methods. There might be use cases where each way has certain advantages and disatvantages. Choosing one might often just be a question of the developer's own taste of style.

## Usage
See the following usage example:

```php
<?php

use Danwe\Helpers\GetterSetterAccessor;

class Options {
  protected $formatter;
  protected $name = 'Php';
  protected $length;

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
      ->initially( 42 ) // Equivalent to declaring "protected $length = 42" on top
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
to set/get property values.

This package contains a benchmark to test a class using this library versus a simple means implementation. It can be run via:
```
php vendor/bin/athletic -p benchmarks
```
Example output:
```
Method Name                                 Iterations   Average Time      Ops/sec   
------------------------------------------- ----------- ----------------- ---------
hardCodedSetterGetter                      : [   6,000] [0.0000072908004] [137,159]
setterGetterUsingThisLibrary_normalUsage   : [   6,000] [0.0000241236687] [ 41,453]
setterGetterUsingThisLibrary_extensiveUsage: [   6,000] [0.0000284811258] [ 35,110]
```

The benchmark result indicated that using `GetterSetterAccessor` is three to four times slower than
using hard coded methods doing the same thing (this might vary depending on the PHP version and
the system). If a method is expected to be called several thousand times than this could become an
actual concern.

## TODOs
* Allow to set more than one type for `ofType`.
* Allow setting `null` values by defining another value as the one triggering the getter.
