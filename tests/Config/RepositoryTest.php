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

    /**
     * Default repository state
     * 
     * @var array
     */
    protected static $defaults = [
        'x' => 'one',
        'assoc' => [
            'x' => 'one',
            'y' => 'two',
            'z' => 'three',
            'arr' => [
                'w' => 'four'
            ]
        ]
    ];

    protected function setUp(): void
    {
        $this->repository = new Repository(self::$defaults);

        parent::setUp();
    }

    /**
     * Test, if repository returns full config when no key
     * is provided for it while retrieving a value
     *
     * @return void
     */
    public function testReturnFullConfig()
    {
        $this->assertSame(static::$defaults, $this->repository->all());
    }

    /**
     * Test that key exists
     *
     * @return void
     */
    public function testKeyExists()
    {
        $this->assertTrue($this->repository->has('assoc.x'));
    }

    /**
     * Test that key does not exist
     *
     * @return void
     */
    public function testKeyDoesNotExist()
    {
        $this->assertFalse($this->repository->has('nill'));
    }

    /**
     * Test if repository returns default values when the keys are not present
     *
     * @return void
     */
    public function testDefaultValueRetrieval()
    {
        $defaultValues = [
            'foo' => 'bar',
            'baz' => function () {
            }
        ];

        $this->assertNull($this->repository->get('none'));
        $this->assertSame($defaultValues, $this->repository->getMultiple($defaultValues));
    }

    /**
     * Test if repository returns signle configuration values 
     *
     * @return void
     */
    public function testSingleValueRetrieval()
    {
        $this->assertSame('one', $this->repository->get('x'));
        $this->assertSame(static::$defaults['assoc'], $this->repository->get('assoc'));
        $this->assertSame('two', $this->repository->get('assoc.y'));
        $this->assertSame('four', $this->repository->get('assoc.arr.w'));
    }

    /**
     * Test whether batch retrieval from repository works correctly
     *
     * @return void
     */
    public function testBatchValueRetrieval()
    {
        $this->assertSame(
            [
                'x' => 'one',
                'assoc.z' => 'three',
                'assoc.arr.w' => 'four'
            ], 
            $this->repository->getMultiple(
                [
                    'x', 'assoc.z', 'assoc.arr.w'
                ]
            )
        );
    }

    /**
     * Test if key setting works properly
     *
     * @return void
     */
    public function testKeySet()
    {
        $this->repository->set('foo', 'bar');
        $this->assertSame('bar', $this->repository->get('foo'));
     
        $this->repository->set('foo.bar', 'bar');
        $this->assertSame('bar', $this->repository->get('foo.bar'));

        $this->repository->setMultiple(
            [
                'baz' => 'five',
                'assoc.bazz' => 'six'
            ]
        );
        $this->assertSame(
            [
                'baz' => 'five',
                'assoc.bazz' => 'six'
            ], 
            $this->repository->getMultiple(['baz', 'assoc.bazz'])
        );
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
        $this->assertSame('baz', $this->repository->get('foo.bar'));

        $this->repository->set('foo.bar.baz', 'baz');
        $this->assertSame([ 'baz' => 'baz' ], $this->repository->get('foo.bar'));
    }


}