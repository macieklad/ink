<?php

namespace Tests\Foundation;

use DI\Container;
use Ink\Foundation\Kernel;
use Ink\Foundation\Support;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SupportTest extends MockeryTestCase
{
    /**
     * Test if helper function joins path correctly
     *
     * @return void
     */
    public function testPathJoinFunctionWorksProperly()
    {
        $this->assertSame(
            'foo' . DIRECTORY_SEPARATOR . 'bar',
            Support::joinPaths('foo', 'bar')
        );
        $this->assertSame(
            'baz' . DIRECTORY_SEPARATOR . 'bazz',
            Support::joinPaths('baz', 'bazz')
        );
        $this->assertSame(
            '/baz' . DIRECTORY_SEPARATOR . 'bazz',
            Support::joinPaths('/baz', 'bazz')
        );
    }
}