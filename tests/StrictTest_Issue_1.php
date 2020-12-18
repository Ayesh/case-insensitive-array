<?php

namespace Ayesh\CaseInsensitiveArray\Test;

use Ayesh\CaseInsensitiveArray\Strict;
use PHPUnit\Framework\TestCase;

class StrictTest_Issue_1 extends TestCase {
    public function testMixCasedItemsNotSkipped(): void {
        $container = new Strict();
        $container['foo'] = 'Foo';
        $container['Foo'] = 'Foo';
        $container['BAR'] = 'BAR';
        $container['bar'] = 'bar';

        $count = 0;
        foreach ($container as $key => $value) {
            $count++;
        }

        self::assertSame(2, $count);
        self::assertCount(2, $container);
        self::assertTrue(isset($container['fOO']));
        self::assertTrue(isset($container['baR']));
    }
}
