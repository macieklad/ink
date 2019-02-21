<?php

namespace Ink\Hooks;

use Tests\Hooks\ActionManagerTest;

/**
 * Mock wordpress has_action
 *
 * @return void
 */
function has_action()
{
    $args = \func_get_args();

    return ActionManagerTest::$functions->has_action(...$args);
}

/**
 * Mock wordpress add_action
 *
 * @return void
 */
function add_action()
{
    $args = \func_get_args();

    return ActionManagerTest::$functions->add_action(...$args);
}

/**
 * Mock wordpress do_action
 *
 * @return void
 */
function do_action()
{
    $args = \func_get_args();

    return ActionManagerTest::$functions->do_action(...$args);
}

/**
 * Mock wordpress did_action
 *
 * @return void
 */
function did_action()
{
    $args = \func_get_args();

    return ActionManagerTest::$functions->did_action(...$args);
}

/**
 * Mock wordpress remove_action
 *
 * @return void
 */
function remove_action()
{
    $args = \func_get_args();

    return ActionManagerTest::$functions->remove_action(...$args);
}

/**
 * Mock wordpress remove_all_actions
 *
 * @return void
 */
function remove_all_actions()
{
    $args = \func_get_args();

    return ActionManagerTest::$functions->remove_all_actions(...$args);
}

