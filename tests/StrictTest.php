<?php

namespace Ayesh\CaseInsensitiveArray\Test;

use Ayesh\CaseInsensitiveArray\Strict;
use PHPUnit\Framework\TestCase;

use function ob_get_clean;
use function ob_start;
use function var_dump;

class StrictTest extends TestCase {

  public function testEmptyArrayAccess(): void {
    $array = new Strict();
    $this->assertFalse(isset($array[0]), 'Numeric key isset() should return false on an empty array.');
    $this->assertFalse(isset($array['Foo']), 'Non-numeric key isset() should return false on an empty array.');
  }

  public function testMixedCaseArrayGetValue(): void {
    $array = new Strict();
    $array['Foo'] = 'Bar';

    $this->assertNotEmpty($array['Foo'], 'Responded to the exact same key used to set the value.');
    $this->assertNotEmpty($array['fOo'], 'Responded to mixed case array access.');
  }

  public function testMixedCaseArraySetValue(): void {
    $array = new Strict();
    $array['Foo'] = 'Bar';

    $this->assertNotEmpty($array['Foo'], 'Responded to the exact same key used to set the value.');

    $array['fOO'] = 'baz';
    $this->assertEquals('baz', $array['Foo'], 'Value was overwritten with mixed case value set.');

    $array['FOO'] = 'Fred';
    $this->assertEquals('Fred', $array['Foo'], 'Value was overwritten with mixed case value set.');
  }

  private static function getSampleTestArray(): array {
    return [
      'Foo' => 'Bar',
      'Baz' => 'Fred',
      'foo' => 'Fred',
      'FOO' => 'gARPly',
      'qux' => ['Test' => 'Test2'],
      234 => 259394,
      '42' => 42,
    ];
  }

  public function testInitializationWithArray(): void {
    $source = static::getSampleTestArray();

    $array = new Strict($source);
    $array[42] = 'Foo';

    $this->assertEquals('Fred', $array['Baz'], 'Array initialization with a source array, exact-key access success.');
    $this->assertEquals('gARPly', $array['FoO'], 'Mixed case array initialization returns the same value.');
    $this->assertSame(['Test' => 'Test2'], $array['QUX'], 'Array instantiate, array value matches.');
    $this->assertSame(259394, $array[234], 'Numeric key returns exact same value with same type.');
    $this->assertSame('Foo', $array[42], 'Numeric key returns exact same value with same type.');

    $array = new Strict();
    $array['x-frame-options'] = 'DENY';
    $array['X-FRAME-options'] = 'SAMEORIGIN';
    $this->assertSame('SAMEORIGIN', $array['X-Frame-Options']);
  }

  public function testNumericArrayAccess(): void {
    $array = new Strict();
    $array[] = 'Foo';
    $array[] = 'Bar';
    $array[] = 'Fred';

    $this->assertEquals('Foo', $array[0]);
    $this->assertEquals('Bar', $array['1']);
    $this->assertEquals('Fred', $array[2]);
    $this->assertNull($array[3]);

  }

  public function testBasicArrayUnset(): void {
    $array = new Strict();
    $array['Foo'] = 'Bar';
    $array['FOO'] = 'Baz';
    $array['Fred'] = 14343;
    unset($array['fOO']);

    $this->assertNull($array['fOo'], 'Mixed case value unset call properly unset the value.');
    $this->assertSame(14343, $array['FRED'], 'Mixed case value unset call maintained the container data.');

    $source = [];
    $source[] = 'Zero';
    $source[] = 'One';
    $source[] = 'Two';
    $source['FOUR'] = 4;

    $array = new Strict($source);

    $this->assertSame('Zero', $array[0], 'Empty array [] operation key starts with zero.');
    $this->assertSame('Two', $array[2]);

    unset($array[1]);
    $this->assertNull($array[1], 'Numeric key unset properly removes the value.');

    $this->assertSame('Two', $array[2], 'Container is not reset on an unset() call.');

    $this->assertSame(4, $array['four'], 'Numeric and otherwise mixed key unset still work after unset calls.');

    unset($array['FOur']);
    $this->assertNull($array['FOUR'], 'Mixed case unset calls properly remove the values case insensitively.');

    $array['foUR'] = 4;
    $this->assertNotNull($array['FOUR']);
    $this->assertSame(4, $array['fouR']);
  }

  public function testCount(): void {
    $source = static::getSampleTestArray();

    /**
     * Number unique case-insensitive keys in the sample array.
     * @see $this->getSampleTestArray();
     */
    $source_count = 5;

    $array = new Strict($source);

    $this->assertCount($source_count, $array, 'Initial count() call returns same values.');

    unset($array['FOo']);
    $source_count--;
    $this->assertCount($source_count, $array);

    $array['FOO'] = 'Bar';
    $array['Foo'] = 'Bar';
    $array['foo'] = 'Bar';
    $array['FoO'] = 'Bar';
    $source_count++;
    $this->assertCount($source_count, $array);

    $array[] = random_int(1, 100);
    $array[] = random_int(1, 100);
    $array[] = random_int(1, 100);
    $source_count += 3;

    $this->assertCount($source_count, $array);
  }

  public function testForeachIteration(): void {
    $source = [
      'Foo' => 'Foo',
      'FOO' => 'FooBar',
      'foo' => 'Bar',
    ];

    $array = new Strict($source);

    // Check with the keys.
    foreach ($array as $key => $value) {
      $this->assertEquals('foo', $key, 'Has overwritten the existing keys with the last key seen.');
      $this->assertEquals('Bar', $value, 'Has overwritten the existing keys with the last key and its value.');
      if ($value === 'H') {
        $this->assertEquals('Fred', $key, 'Key case is preserved.');
      }
    }

    $source = [
      'Foo' => 'Foo',
      'FOO' => 'FooBar',
      'FreD' => 'Bar',
    ];

    $array = new Strict($source);

    // Check with the keys.
    foreach ($array as $key => $value) {
      if ($value === 'Bar') {
        $this->assertEquals('FreD', $key, 'Key case is preserved.');
      }
    }

    $source = [1, 2, 3, 4, 5];
    $array = new Strict($source);
    foreach ($array as $key => $value) {
      if ($value === 5) {
        $this->assertEquals(4, $key);
      }
    }
  }


  public function testCustomIteration(): void {
    $source = [
      'Foo' => 'Foo',
      'FOO' => 'FooBar',
      'foo' => 'Bar',
      'Nothing' => NULL,
      'Soon to loose' => TRUE,
    ];

    $array = new Strict($source);
    $this->assertEquals('Bar', $array->current());
    $this->assertEquals('foo', $array->key());

    $array->next();
    $this->assertNull($array->current());
    $this->assertEquals('Nothing', $array->key());

    $array->rewind();
    $this->assertEquals('Bar', $array->current());
    $this->assertEquals('foo', $array->key());

    $array->next();
    $this->assertFalse($array->valid());
  }

  public function testDebugInfo(): void {
    $array = new Strict();
    $array[] = 'One';
    $array[2] = 'Two';
    /** @noinspection SpellCheckingInspection */
    $array['Thuna'] = '2';
    $array['ThuNA'] = '3';

    ob_start();
    var_dump($array);
    $dump = ob_get_clean();
    $this->assertStringContainsString('ThuNA', $dump, 'Checking the var_dump return value to contain the overridden header.');
  }

}
