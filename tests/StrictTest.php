<?php

namespace Ayesh\CaseInsensitiveArray\Test;

use Ayesh\CaseInsensitiveArray\Strict;
use PHPUnit_Framework_TestCase;

class StrictTest extends PHPUnit_Framework_TestCase {

  public function testEmptyArrayAccess() {
    $array = new Strict();
    self::assertFalse(isset($array[0]), 'Numeric key isset() should return false on an empty array.');
    self::assertFalse(isset($array['Foo']), 'Non-numeric key isset() should return false on an empty array.');
  }

  public function testMixedCaseArrayGetValue() {
    $array = new Strict();
    $array['Foo'] = 'Bar';

    self::assertNotEmpty($array['Foo'], 'Responded to the exact same key used to set the value.');
    self::assertNotEmpty($array['fOo'], 'Responded to mixed case array access.');
  }

  public function testMixedCaseArraySetValue() {
    $array = new Strict();
    $array['Foo'] = 'Bar';

    self::assertNotEmpty($array['Foo'], 'Responded to the exact same key used to set the value.');

    $array['fOO'] = 'baz';
    self::assertEquals('baz', $array['Foo'], 'Value wasoverritten with mixed case value set.');

    $array['FOO'] = 'Fred';
    self::assertEquals('Fred', $array['Foo'], 'Value wasoverritten with mixed case value set.');
  }

  private static function getSampleTestArray() {
    return [
      'Foo' => 'Bar',
      'Baz' => 'Fred',
      'foo' => 'corge',
      'FOO' => 'gARPly',
      'qux' => ['Test' => 'Test2'],
      234 => 259394,
      '42' => 42,
    ];
  }

  public function testInitializationWithArray() {
    $source = static::getSampleTestArray();

    $array = new Strict($source);
    $array[42] = 'Foo';

    self::assertEquals('Fred', $array['Baz'], 'Array initialization with a source array, exact-key access success.');
    self::assertEquals('gARPly', $array['FoO'], 'Mixed case array initialization returns the same value.');
    self::assertSame(['Test' => 'Test2'], $array['QUX'], 'Array initilization, array value matches.');
    self::assertSame(259394, $array[234], 'Numeric key returns exact same value with same type.');
    self::assertSame('Foo', $array[42], 'Numeric key returns exact same value with same type.');

    $array = new Strict();
    $array['x-frame-options'] = 'DENY';
    $array['X-FRAME-options'] = 'SAMEORIGIN';
    self::assertSame('SAMEORIGIN', $array['X-Frame-Options']);
  }

  public function testNumericArrayAccess() {
    $array = new Strict();
    $array[] = 'Foo';
    $array[] = 'Bar';
    $array[] = 'Fred';

    self::assertEquals('Foo', $array[0]);
    self::assertEquals('Bar', $array["1"]);
    self::assertEquals('Fred', $array[2]);
    self::assertNull($array[3]);

  }

  public function testBasicArrayUnset() {
    $array = new Strict();
    $array['Foo'] = 'Bar';
    $array['FOO'] = 'Baz';
    $array['Fred'] = 14343;
    unset($array['fOO']);

    self::assertNull($array['fOo'], 'Mixed case value unset call properly unsets the value.');
    self::assertSame(14343, $array['FRED'], 'Mixed case value unset call maintained the container data.');

    $source = [];
    $source[] = 'Zero';
    $source[] = 'One';
    $source[] = 'Two';
    $source['FOUR'] = 4;

    $array = new Strict($source);

    self::assertSame('Zero', $array[0], 'Empty array [] operation key starts with zero.');
    self::assertSame('Two', $array[2]);

    unset($array[1]);
    self::assertNull($array[1], 'Numeric key unset properly removes the value.');

    self::assertSame('Two', $array[2], 'Container is not reset on an unset() call.');

    self::assertSame(4, $array['four'], 'Numeric and otherwise mixed key unsets still work after unset calls.');

    unset($array['FOur']);
    self::assertNull($array['FOUR'], 'Mixed case unset calls properly remove the values case insensitively.');

    $array['foUR'] = 4;
    self::assertNotNull($array['FOUR']);
    self::assertSame(4, $array['fouR']);
  }

  public function testCount() {
    $source = static::getSampleTestArray();

    /**
     * Number unique case-insensitive keys in the sample array.
     * @see self::getSampleTestArray();
     */
    $source_count = 5;

    $array = new Strict($source);

    self::assertEquals($source_count, count($array), 'Initial count() call returns same values.');

    unset($array['FOo']);
    $source_count--;
    self::assertEquals($source_count, count($array));

    $array['FOO'] = 'Bar';
    $array['Foo'] = 'Bar';
    $array['foo'] = 'Bar';
    $array['FoO'] = 'Bar';
    $source_count++;
    self::assertEquals($source_count, count($array));

    $array[] = rand(1, 100);
    $array[] = rand(1, 100);
    $array[] = rand(1, 100);
    $source_count += 3;

    self::assertEquals($source_count, count($array));
  }

  public function testForeachIteration() {
    $source = [
      'Foo' => 'Foo',
      'FOO' => 'FooBar',
      'foo' => 'Bar',
    ];

    $array = new Strict($source);

    // Check with the keys.
    foreach ($array as $key => $value) {
      self::assertEquals('foo', $key, 'Has overwritten the existig keys with the last key seen.');
      self::assertEquals('Bar', $value, 'Has overwritten the existig keys with the last key and its value.');
    }
  }


  public function testCustomIteration() {
    $source = [
      'Foo' => 'Foo',
      'FOO' => 'FooBar',
      'foo' => 'Bar',
      'Nothing' => NULL,
      'Soon to loose' => TRUE,
    ];

    $array = new Strict($source);
    self::assertEquals('Bar', $array->current());
    self::assertEquals('foo', $array->key());

    $array->next();
    self::assertNull($array->current());
    self::assertEquals('Nothing', $array->key());

    $array->rewind();
    self::assertEquals('Bar', $array->current());
    self::assertEquals('foo', $array->key());

    $array->next();
    self::assertFalse($array->valid());
  }

}
