<?php

use WP_Mock\Tools\TestCase;

class ActionManagerTest extends TestCase
{
    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp() : void
    {
        \WP_Mock::setUp();
    }

    /**
     * Clean up after each test
     *
     * @return void
     */
    public function tearDown() : void
    {
        \WP_Mock::tearDown();
    }
}