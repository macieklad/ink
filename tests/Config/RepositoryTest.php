<?php

use Ink\Config\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * Repository class instance
     *
     * @var Ink\Config\Repository
     */
    protected $repository = null;

    protected function setUp(): void
    {
        $this->repository = new Repository;
    }

    /**
     * Test, if repository returns full config when no key
     * is provided for it while retrieving a value
     *
     * @return void
     */
    public function testReturnFullConfig()
    {
        $this->assertSame([], $this->repository->get());
    }

    /**
     * Check if basic key set inside repository works properly
     *
     * @return void
     */
    public function testSimpleKeySet()
    {
        $this->repository->set('foo', 'bar');

        $this->assertSame('bar', $this->repository->get('foo'));
    }

    /**
     * Test if nested key setting works properly
     *
     * @return void
     */
    public function testNestedKeySet()
    {
        $this->repository->set('foo.bar', 'bar');

        $this->assertSame([
            'foo' => [
                'bar' => 'bar'
            ]
        ], $this->repository->get());
        $this->assertSame('bar', $this->repository->get('foo.bar'));

        $this->repository->set('foo.baz', 'baz');

        $this->assertSame([
            'foo' => [
                'bar' => 'bar',
                'baz' => 'baz'
            ]
        ], $this->repository->get());
    }

    /**
     * Test, if setting nested key replaces the non array
     * ones within array, if they were parent keys for
     * the element that was going to be set.
     *
     * @return void
     */
    public function testNestedKeySetOverwrite()
    {
        $this->repository->set('foo.bar', 'baz');

        $this->assertSame([
            'foo' => [
                'bar' => 'baz'
            ]
        ], $this->repository->get());

        $this->repository->set('foo.bar.baz', 'baz');

        $this->assertSame([
            'foo' => [ 
                'bar' => [ 
                    'baz' => 'baz'
                ]   
            ]
        ], $this->repository->get());
    }

    /**
     * Check if the test returns assoc array when retrieving
     * mutiple keys from it.
     *
     * @return void
     */
    public function testBatchKeyRetrieval()
    {
        $this->repository->set('foo.bar', 'baz');
        $this->repository->set('qux', 'quux');
        $this->repository->set('quuz', 'coorge');

        $this->assertSame([
            'foo.bar' => 'baz',
            'quuz' => 'coorge'
        ], $this->repository->get(['foo.bar', 'quuz']));
    }
}