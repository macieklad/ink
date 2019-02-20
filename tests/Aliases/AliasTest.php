<?php

namespace Tests\Aliases;

use Ink\Aliases\Alias;
use Ink\Aliases\AliasLoader;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AliasTest extends MockeryTestCase
{
    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp() : void
    {
        StubAlias::clearResolvedInstances();
        StubAliasBeta::clearResolvedInstances();

        $this->container = \Mockery::mock(ContainerInterface::class);
        Alias::setAliasContainer($this->container);

        $this->mock = \Mockery::mock();
    }

    /**
     * Make sure that alias throws exception without accessor
     *
     * @return void
     */
    public function testAliasShouldFailWithoutAccessor()
    {
        $this->expectException(\RuntimeException::class);
        BadStub::foo();
    }

    /**
     * Test alias helper function
     *
     * @return void
     */
    public function testAliasHelperFunctions()
    {
        $this->container->shouldReceive('get')
            ->with('stub')
            ->andReturn($this->mock);

        $this->assertSame(
            $this->container,
            StubAlias::getAliasContainer()
        );
        $this->assertSame(
            get_class($this->mock),
            StubAlias::getMockableClass()
        );
    }

    /**
     * Make sure that alias fails if no underlaying
     * container entry is found while resolving.
     *
     * @return void
     */
    public function testAliasShouldFailWithoutImplementation()
    {
        $this->expectException(\RuntimeException::class);
        $this->container->shouldReceive('get')
            ->with('stub')
            ->andReturn(null);

        StubAlias::foo('bar');
    }

    /**
     * Check if binding object directly as accessor 
     * resolves alias properly
     *
     * @return void
     */
    public function testAliasShouldResolveObjectTypeAccessorDirectly()
    {
        $this->assertEquals('bar', ObjectAlias::foo());
    }

    /**
     * Test normal resolving of classes set inside
     * container by alias
     *
     * @return void
     */
    public function testConcreteAliasCallResolvesProperly()
    {
        $this->container->shouldReceive('get')
            ->with('stub')
            ->andReturn($this->mock);
        
        $this->mock->shouldReceive('foo')
            ->with('bar')
            ->once();

        StubAlias::foo('bar');
    }

    /**
     * Test if multiple aliases do point to the
     * single implementation correctly
     *
     * @return void
     */
    public function testConcreteAliasCallResolvesForMultipleAliases()
    {
        $this->container->shouldReceive('get')
            ->with('stub')
            ->andReturn($this->mock);

        $this->mock->shouldReceive('foo')
            ->with('bar');

        StubAlias::foo('bar');
        StubAliasBeta::foo('bar');
    }

    /**
     * Test if aliases resolve properly after automatic loading.
     * Test loads the first alias incorrectly, then merges 
     * second one with it. Then it corrects such aliases
     * with signle aliasing call.
     *
     * @return void
     */
    public function testAliasResolvesProperlyAfterLoading()
    {
        AliasLoader::getInstance(
            [
                'StubClass' => get_class($this->container),
            ]
        )->register();

        AliasLoader::getInstance(
            [
                'StubBetaClass' => get_class($this->mock)
            ]
        )->register();

        AliasLoader::getInstance()->alias('StubClass', get_class($this->mock));

        $this->mock->shouldReceive('foo')
            ->withAnyArgs()
            ->twice();
        
        \StubClass::foo();
        \StubBetaClass::foo();
    }
    
}

class StubAlias extends Alias
{
    /**
     * Return stub alias accessor inside container
     *
     * @return void
     */
    public static function getAliasAccessor()
    {
        return 'stub';
    }
}

class StubAliasBeta extends Alias
{
    /**
     * Return stub alias accessor inside container
     *
     * @return void
     */   
    public static function getAliasAccessor()
    {
        return 'stub';
    }
}

class ObjectAlias extends Alias
{
    /**
     * Return conrecte object as accessor
     *
     * @return void
     */
    public static function getAliasAccessor()
    {
        return new ObjectAccessor;
    }
}

class ObjectAccessor
{
    /**
     * Example function to test
     *
     * @return void
     */
    public function foo()
    {
        return 'bar';
    }
}

class BadStub extends Alias
{

}

