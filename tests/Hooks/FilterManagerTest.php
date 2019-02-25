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
        $this->manager->setMutatorNamespace('Tests\Hooks');
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
     * Test that mutator namespace is settable inside action
     * manager, from which it can infer their namespaces.
     *
     * @return void
     */
    public function testMutatorNamespaceIsSetCorrectly()
    {
        $this->manager->setMutatorNamespace('Test\Hooks');

        $this->assertSame(
            'Test\Hooks', 
            TestHelpers::getProperty($this->manager, 'mutatorNamespace')
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
     * existent mutator as responder.
     *
     * @return void
     */
    public function testHandlerCompilationFailsIfGivenBadMutator()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->use('NonExistentMutator@foo');
    }

    /**
     * Test if manager will fail if given non existent method
     * on class object.
     *
     * @return void
     */
    public function testMutatorCompilationFailsIfGivenBadMutatorMethod()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->use(
            $this->mockMutatorString('foo')
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

        $this->manager->use(null);
    }

    /**
     * Test if passing callable object will skip compilation
     * part, and just pass it to the wordpress as handler.
     *
     * @return void
     */
    public function testMutatorIsNotCompiledIfPassedCallable()
    {
        $stub = new StubMutator;
        $callable = 'Ink\Hooks\add_action';
        $callableObject = [$stub, 'addBar'];

        $this->mockAddFilterWithMutator($callable);
        $this->mockAddFilterWithMutator($callableObject);

        $this->manager
            ->use($callable);
        $this->manager
            ->use($callableObject);
    }

    /**
     * Test if passing callable while forcing compilation
     * will bind arguments from service conntainer.
     *
     * @return void
     */
    public function testCallableMutatorIsCompiledIfForced()
    {
        $callable = function ($value, StubMutator $stub) {
            return $stub instanceof StubMutator;
        };

        $this->mockAddFilterWithMutator(
            Mockery::on(
                function ($callback) {
                    return $callback('foo');
                }
            )
        );

        $this->manager
            ->forceCompilation()
            ->use($callable);
    }

    /**
     * Test if passing proper mutator string will compile
     * to correct callback handler.
     *
     * @return void
     */
    public function testCreatesCallbackWhenGivenMutatorString()
    {
        $this->mockAddFilterWithMutator(
            Mockery::on(
                function ($callback) {
                    return $callback('foo') === 'foobar';
                }
            )
        );

        $this->manager->use(
            $this->mockMutatorString('addBar')
        );
    }

    /**
     * Test if manager can handle passing multiple mutators 
     * as array, and creates a closure from them
     *
     * @return void
     */
    public function testArrayWithMutatorsIsCompiledToCallback()
    {
        $this->mockAddFilterWithMutator(
            Mockery::on(
                function ($callback) {
                    return $callback('foo') == 'foobarbaz';
                }
            )
        );

        $this->manager
            ->forceCompilation()
            ->use(
                [
                    $this->mockMutatorString('addBar'),
                    function ($value, StubMutator $mutator) {
                        if ($mutator instanceof StubMutator) {
                            return $value . 'baz';
                        }
                    }
                ]
            );
    }
    
    /**
     * Test if manager checks filters existence correctly for given
     * mutator.
     *
     * @return void
     */
    public function testManagerChecksFilterExistanceForMutator()
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
    public function testMutatorRemovalIsDelegatedCorrectly()
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
     * except custom function that mutates the passed value.
     *
     * @param mixed $mutator
     * 
     * @return void
     */
    protected function mockAddFilterWithMutator($mutator)
    {
        TestHelpers::functions()
            ->shouldReceive('add_filter')
            ->with(
                $this->filter,
                $mutator,
                10,
                1
            )
            ->once();
    }

    /**
     * Return mock mutator string that can be compiled
     * by action manager.
     *
     * @param string $method
     * 
     * @return string
     */
    protected function mockMutatorString(string $method = '')
    {
        return 'StubMutator' . ($method != '' ? '@' . $method : $method);
    }
}

class StubMutator
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