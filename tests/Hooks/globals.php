<?php

namespace Ink\Hooks;

use Tests\TestHelpers;
use Tests\Hooks\ActionManagerTest;

/**
 * Mock wordpress has_action
 *
 * @return void
 */
function has_action()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('has_action', $args);
}

/**
 * Mock wordpress add_action
 *
 * @return void
 */
function add_action()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('add_action', $args);
}

/**
 * Mock wordpress do_action
 *
 * @return void
 */
function do_action()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('do_action', $args);
}

/**
 * Mock wordpress did_action
 *
 * @return void
 */
function did_action()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('did_action', $args);
}

/**
 * Mock wordpress remove_action
 *
 * @return void
 */
function remove_action()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('remove_action', $args);
}

/**
 * Mock wordpress remove_all_actions
 *
 * @return void
 */
function remove_all_actions()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('remove_all_actions', $args);
}

/**
 * Mock wordpress has_filter
 *
 * @return void
 */
function has_filter()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('has_filter', $args);
}

/**
 * Mock wordpress add_filter
 *
 * @return void
 */
function add_filter()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('add_filter', $args);
}

/**
 * Mock wordpress apply_filters
 *
 * @return void
 */
function apply_filters()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('apply_filters', $args);
}

/**
 * Mock wordpress remove_filter
 *
 * @return void
 */
function remove_filter()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('remove_filter', $args);
}

/**
 * Mock wordpress remove_all_filters
 *
 * @return void
 */
function remove_all_filters()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('remove_all_filters', $args);
}

/**
 * Mock wordpress doing_filter
 *
 * @return void
 */
function doing_filter()
{
    $args = \func_get_args();

    return TestHelpers::passGlobalCall('doing_filter', $args);
}
