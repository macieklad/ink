<?php

namespace Tests\Hooks;

use Mockery;
use DI\Container;
use Tests\TestHelpers;
use Tests\MocksGlobals;
use Ink\Hooks\FilterManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;

require_once __DIR__ . '/globals.php';

class TestName extends MockeryTestCase
{
    use MocksGlobals;

    /**
     * Default filter name
     *
     * @var string
     */
    protected $filter = 'foo';

    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->clearGlobals();
        $this->manager = new FilterManager(new Container);
        $this->manager->name($this->filter);
        $this->manager->setTransformerNamespace('Tests\Hooks');
    }

     /**
      * Test if managed filter name can be changed
      *
      * @return void
      */
    public function testActionManagerSetsName()
    {
        $this->manager->name('baz');

        $this->assertSame(
            'baz',
            TestHelpers::getProperty($this->manager, 'filter')
        );
    }

    
    /**
     * Test that transformer namespace is settable inside action
     * manager, from which it can infer their namespaces.
     *
     * @return void
     */
    public function testTransformerNamespaceIsSetCorrectly()
    {
        $this->manager->setTransformerNamespace('Test\Hooks');

        $this->assertSame(
            'Test\Hooks', 
            TestHelpers::getProperty($this->manager, 'transformerNamespace')
        );
    }

    /**
     * Check if manager applies filter by calling required
     * wordpress functions first.
     *
     * @return void
     */
    public function testFilterIsAppliedWithCorrectArgs()
    {
        TestHelpers::functions()
            ->shouldReceive('apply_filters')
            ->with(
                $this->filter,
                'foo',
                'bar',
                'baz'
            );

        $this->manager->apply('foo', 'bar', 'baz');
    }

    /**
     * Test if manager will fail to compile if given non
     * existent transformer as responder.
     *
     * @return void
     */
    public function testHandlerCompilationFailsIfGivenBadTransformer()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->add('NonExistentTransformer@foo');
    }

    /**
     * Test if manager will fail if given non existent method
     * on class object.
     *
     * @return void
     */
    public function testTransformerCompilationFailsIfGivenBadTransfomerMethod()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->add(
            $this->mockTransfomerString('foo')
        );
    }

    /**
     * Test if passing invalid handler, like null, will fail
     * callback handler compilation.
     *
     * @return void
     */
    public function testHandlerCompilationFailsIfGivenUnsupportedArgument()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->add(null);
    }

    /**
     * Test if passing callable object will skip compilation
     * part, and just pass it to the wordpress as handler.
     *
     * @return void
     */
    public function testTransformerIsNotCompiledIfPassedCallable()
    {
        $stub = new StubTransformer;
        $callable = 'Ink\Hooks\add_action';
        $callableObject = [$stub, 'addBar'];

        $this->mockAddFilterWithTransformer($callable);
        $this->mockAddFilterWithTransformer($callableObject);

        $this->manager
            ->add($callable);
        $this->manager
            ->add($callableObject);
    }

    /**
     * Test if passing callable while forcing compilation
     * will bind arguments from service conntainer.
     *
     * @return void
     */
    public function testCallableTransformerIsCompiledIfForced()
    {
        $callable = function ($value, StubTransformer $stub) {
            return $stub instanceof StubTransformer;
        };

        $this->mockAddFilterWithTransformer(
            Mockery::on(
                function ($callback) {
                    return $callback('foo');
                }
            )
        );

        $this->manager
            ->forceCompilation()
            ->add($callable);
    }

    /**
     * Test if passing proper transformer string will compile
     * to correct callback handler.
     *
     * @return void
     */
    public function testCreatesCallbackWhenGivenTransformerString()
    {
        $this->mockAddFilterWithTransformer(
            Mockery::on(
                function ($callback) {
                    return $callback('foo') === 'foobar';
                }
            )
        );

        $this->manager->add(
            $this->mockTransfomerString('addBar')
        );
    }

    /**
     * Test if manager can handle passing multiple transformers 
     * as array, and creates a closure from them
     *
     * @return void
     */
    public function testArrayWithTransformersIsCompiledToCallback()
    {
        $this->mockAddFilterWithTransformer(
            Mockery::on(
                function ($callback) {
                    return $callback('foo') == 'foobarbaz';
                }
            )
        );

        $this->manager
            ->forceCompilation()
            ->add(
                [
                    $this->mockTransfomerString('addBar'),
                    function ($value, StubTransformer $transfomer) {
                        if ($transfomer instanceof StubTransformer) {
                            return $value . 'baz';
                        }
                    }
                ]
            );
    }
    
    /**
     * Test if manager checks filters existence correctly for given
     * transformer.
     *
     * @return void
     */
    public function testManagerChecksFilterExistanceForTransformer()
    {
        $bazFunc = function () {
            return 1;
        };

        $bazzFunc = function () {
            return 2;
        };

        TestHelpers::functions()
            ->shouldReceive('has_filter')
            ->with('baz', $bazFunc)
            ->andReturn(true)
            ->once();

        TestHelpers::functions()
            ->shouldReceive('has_filter')
            ->with('bazz', $bazzFunc)
            ->andReturn(false)
            ->once();

        $this->assertTrue(
            $this->manager
                ->name('baz')
                ->exists($bazFunc)
        );
        $this->assertFalse(
            $this->manager
                ->name('bazz')
                ->exists($bazzFunc)
        );
    }

    /**
     * Test if removing the function is delegated correctly
     *
     * @return void
     */
    public function testTransformerRemovalIsDelegatedCorrectly()
    {   
        $func = function () {
            return 1;
        };

        TestHelpers::functions()
            ->shouldReceive('remove_filter')
            ->with($this->filter, $func, 10)
            ->once();

        TestHelpers::functions()
            ->shouldReceive('remove_filter')
            ->with($this->filter, $func, 15)
            ->once();

        $this->manager->detach($func);
        $this->manager->detach($func, 15);
    }

    /**
     * Test if manager calls wp function that removes all
     * handlers from action.
     *
     * @return void
     */
    public function testFilterIsUnregisteredCorrectly()
    {
        TestHelpers::functions()
            ->shouldReceive('remove_all_filters')
            ->with($this->filter, 10)
            ->once();

        TestHelpers::functions()
            ->shouldReceive('remove_all_filters')
            ->with($this->filter, 15)
            ->once();

        $this->manager->flush();
        $this->manager->flush(15);
    }

    /**
     * Mock wordpress add_filter function with correct arguments,
     * except custom function that transforms the passed value.
     *
     * @param mixed $transformer
     * 
     * @return void
     */
    protected function mockAddFilterWithTransformer($transformer)
    {
        TestHelpers::functions()
            ->shouldReceive('add_filter')
            ->with(
                $this->filter,
                $transformer,
                10,
                1
            )
            ->once();
    }

    /**
     * Return mock transformer string that can be compiled
     * by action manager.
     *
     * @param string $method
     * 
     * @return string
     */
    protected function mockTransfomerString(string $method = '')
    {
        return 'StubTransformer' . ($method != '' ? '@' . $method : $method);
    }
}

class StubTransformer
{
    /**
     * Add 'bar' string to another one
     *
     * @param string $to
     * 
     * @return string
     */
    public function addBar(string $to)
    {
        return $to . 'bar';
    }
}