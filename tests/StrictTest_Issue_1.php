<?php

namespace Ayesh\CaseInsensitiveArray\Test;

use Ayesh\CaseInsensitiveArray\Strict;

class StrictTest_Issue_1 extends \PHPUnit_Framework_TestCase {
  public function testMixCasedItemsNotSkipped() {
    $container  = new Strict();
    $container['foo'] = 'Foo';
    $container['Foo'] = 'Foo';
    $container['BAR'] = 'BAR';
    $container['bar'] = 'bar';

    $count = 0;
    foreach ($container as $key => $value) {
      $count++;
    }

    $this->assertSame(2, $count);
    $this->assertCount(2, $container);
    $this->assertTrue(isset($container['fOO']));
    $this->assertTrue(isset($container['baR']));
  }
}
