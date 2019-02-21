<?php


namespace Tests\Hooks;

use Mockery;
use DI\Container;
use Tests\TestHelpers;
use Tests\MocksGlobals;
use Ink\Hooks\ActionManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Ink\Contracts\Hooks\ActionManager as ActionManagerContract;

require __DIR__ . '/globals.php';

class ActionManagerTest extends MockeryTestCase
{
    use MocksGlobals;

    /**
     * Action manager instance
     *
     * @var Ink\Hooks\ActionManager
     */
    protected $manager;

    /**
     * Default action name to test
     *
     * @var string
     */
    protected $action = 'foo';

    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->clearGlobals();
        $this->container = new Container;
        $this->manager = new ActionManager($this->container);
        $this->manager->name($this->action);
    }

    /**
     * Test if managed action name can be changed
     *
     * @return void
     */
    public function testActionManagerSetsName()
    {
        $this->manager->name('baz');

        $this->assertSame(
            'baz',
            TestHelpers::getProperty($this->manager, 'action')
        );
    }

    /**
     * Test if manager will fail to compile if given non
     * existent controller as responder.
     *
     * @return void
     */
    public function testHandlerCompilationFailsIfGivenBadController()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->respond('NonExistentController@foo');
    }

    /**
     * Test if manager will fail if given non existent method
     * on class object.
     *
     * @return void
     */
    public function testHandlerCompilationFailsIfGivenBadMethod()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager->respond('StubController@foo');
    }

    /**
     * Test if passing proper action string will compile to correct
     * callback handler.
     *
     * @return void
     */
    public function testCreatesCallbackWhenGivenControllerAsAction()
    {
        static::$functions
            ->shouldReceive('add_action')
            ->with(
                $this->action,
                Mockery::on(
                    function ($callback) {
                        return $callback('foo', 'bar') === 'foobarbaz';
                    }
                )
            )
            ->once();

        $this->manager->respond('StubController@handler');
    }

    /**
     * Test if passing callable object will skip compilation
     * part, and just pass it to the wordpress as handler.
     *
     * @return void
     */
    public function testActionIsNotCompiledIfPassedCallable()
    {
        $stub = new StubController;
        $callable = 'add_action';
        $callableObject = [$stub, 'handler'];

        static::$function
            ->shouldReceive('add_action')
            ->with($this->action, $callable)
            ->once();

        static::$function
            ->shouldReceive('add_action')
            ->with($this->action, $callableObject)
            ->once();

        $this->manager
            ->respond($callable)       
            ->respond($callableObject);
    }

    /**
     * Test if passing callable while forcing compilation
     * will bind arguments to it.
     *
     * @return void
     */
    public function testCallableActionIsCompiledIfForced()
    {
        $callable = function ($manager) {
            return $manager instanceof ActionManagerContract;
        };

        static::$functions
            ->shouldReceive('add_action')
            ->with(
                $this->action,
                Mockery::on(
                    function ($callback) {
                        return $callback();
                    }
                )
            );

        $this->manager
            ->forceCompilation()
            ->respond($callable);
    }

    /**
     * Test if manager can handle passing multiple actions 
     * as array, and creates a handler from them
     *
     * @return void
     */
    public function testArrayWithActionsIsCompiledToCallback()
    {
        $integer = 0;

        static::$functions
            ->shouldReceive('add_action')
            ->with(
                $this->action,
                Mockery::on(
                    function ($callback) use ($integer) {
                        $callback($integer);

                        return true;
                    }
                )
            )
            ->once();

        $this->manager
            ->respond(
                [
                    'StubController@increment',
                    'StubController@increment',
                    function (&$object) {
                        $object++;
                    }
                ]
            );

        $this->assertEquals(3, $integer);
    }

    /**
     * Test if manager dispatches actions correctly
     *
     * @return void
     */
    public function testActionIsDispatchedWithArguments()
    {
        static::$functions
            ->shouldReceive('do_action')
            ->with($this->action, 'foo', 'bar')
            ->once();

        $this->manager
            ->dispatch('foo', 'bar');
    }

    /**
     * Test if manager checks action existence correctly
     *
     * @return void
     */
    public function testExistsMethodCallsAndReturnsProperCallbacks()
    {
        $bazFunc = function () {
            return 1;
        };

        $bazzFunc = function () {
            return 2;
        };

        static::$functions
            ->shouldReceive('has_action')
            ->with('baz', $bazFunc)
            ->andReturn(true)
            ->once();

        static::$functions
            ->shouldReceive('has_action')
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
     * Test if calling count method calls correct wp function
     *
     * @return void
     */
    public function testManagerReturnsActionCountCorrectly()
    {
        static::$functions
            ->shouldReceive('did_action')
            ->with($this->action)
            ->andReturn(10)
            ->once();

        $this->assertEquals(10, $this->manager->count());
    }

    /**
     * Test if removing the function is delegated correctly
     *
     * @return void
     */
    public function testFunctionRemovalIsDelegatedCorrectly()
    {   
        $func = function () {
            return 1;
        };

        static::$functions
            ->shouldReceive('remove_action')
            ->with($this->action, $func, 10)
            ->once();

        static::$functions
            ->shouldReceive('remove_action')
            ->with($this->action, $func, 15)
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
    public function testActionIsUnregisteredCorrectly()
    {
        static::$functions
            ->shouldReceive('remove_all_actions')
            ->with($this->action, 10)
            ->once();

        static::$functions
            ->shouldReceive('remove_all_actions')
            ->with($this->action, 15)
            ->once();

        $this->manager->flush();
        $this->manager->flush(15);

    }
}

class StubController
{
    /**
     * Test handler for param concatenation
     *
     * @param string $foo
     * @param string $bar
     * 
     * @return void
     */
    public function handler(string $foo, string $bar) : string 
    {    
        return $foo . $bar . 'baz';
    }

    /**
     * Increment object passed to handler
     *
     * @param int $object
     * 
     * @return void
     */
    public function increment(&$object)
    {
        $object++;
    }
}